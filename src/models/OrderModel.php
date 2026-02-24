<?php

require_once 'Model.php';
require_once 'CartModel.php';
require_once 'ProductModel.php';
require_once __DIR__ . '/../Core/Exceptions/StockExceededException.php';

class OrderModel extends Model {

    private $cartModel;
    private $productModel;

    function __construct($db)
    {
        parent::__construct($db);
        $this->cartModel = new CartModel($db);
        $this->productModel = new ProductModel($db);
    }

    //Trägt neue Order in die entsprechenden Tabellen ein 
    public function createOrder($data) {
        $user_data = $data['user_data'];
        $address_data = $data['address_data']; 
        $cart_items = $data['cart_items'] ?? []; 
        
        $errors = [];

        //prüft für jedes Item in der Bestellung, ob es den Lagerbestand überschreitet
        foreach ($cart_items as $item) {
            $pid = (int) $item['product_id'];
            $wanted = (int) $item['product_amount'];
            
            $product = $this->productModel->getByID($pid);
            $stock = (int) ($product['inventory'] ?? 0);

            if ($stock < $wanted) {
                //Fehler werden gesammelt
                $errors[] = [
                    'name' => $product['product_name'],
                    'stock' => $stock,
                    'wanted' => $wanted
                ];
            }
        }

        //wirfst exception mit allen Produkten, die Lagerbestand überschreiten
        if (!empty($errors)) {
            throw new StockExceededException($errors);
        }

        $pdo=$this->conn->getConnection();

        try {

            //Transaction damit keine Inkonsistenzen zwischen Orders und Order_Addresses entstehen
            $pdo->beginTransaction();
            
            //Legt Adresse in Datenbank an
            $sqlAddress = "INSERT INTO Order_Addresses
                        (house_number, street, zip_code)
                        VALUES
                        (:house_number, :street, :zip_code)";
            
            $stmtAddr = $this->conn->prepare($sqlAddress);
            $stmtAddr->bindValue(':house_number', $address_data['house_number'] ?? null, PDO::PARAM_STR);
            $stmtAddr->bindValue(':street', $address_data['street'] ?? null, PDO::PARAM_STR);
            $stmtAddr->bindValue(':zip_code', $address_data['zip_code'] ?? null, PDO::PARAM_STR);
            $stmtAddr->execute();        

            $addressId = (int) $this->conn->lastInsertId();

            //Legt Order in Datenbank an
            $sqlOrder = "INSERT INTO Orders 
                         (user_id, order_address_id, first_name, last_name, guest_email, state) 
                         VALUES 
                         (:uid, :addr_id, :fname, :lname, :mail, 'pending')";
            
            $stmtOrder = $this->conn->prepare($sqlOrder);
            
            //Wenn userId Null ist, ist das kein Fehler sondern es handelt sich um einen Gast
            $userId = $user_data['user_id'] ?? null;    
            if (empty($userId)) {
                $userId = null;
            }

            $stmtOrder->bindValue(':uid', $userId, ($userId === null) ? PDO::PARAM_NULL : PDO::PARAM_INT);            
            $stmtOrder->bindValue(':addr_id', $addressId, PDO::PARAM_INT);
            $stmtOrder->bindValue(':fname', $user_data['first_name'], PDO::PARAM_STR);
            $stmtOrder->bindValue(':lname', $user_data['last_name'], PDO::PARAM_STR);
            $stmtOrder->bindValue(':mail', $user_data['email'], PDO::PARAM_STR);
            $stmtOrder->execute();

            $orderId = (int) $this->conn->lastInsertId();

            //SQL Template für Order-Items
            $sqlItem = "INSERT INTO OrderItems (order_id, product_id, product_amount, order_total) 
                        VALUES (:oid, :pid, :amount, :total)";
            
            //SQL Template für Stock-Update
            $sqlUpdateStock = "UPDATE Products 
                               SET inventory = inventory - :amount, 
                                   units_sold = units_sold + :amount 
                               WHERE product_id = :pid AND inventory >= :amount";
            
            $stmtItem = $this->conn->prepare($sqlItem);
            $stmtStock = $this->conn->prepare($sqlUpdateStock);

            foreach ($cart_items as $i) {
                $pid = (int) $i['product_id']; 
                $amount = (int) $i['product_amount']; 
                $price = (int) $i['price']; 

                $totalPrice = $price * $amount; 

                //Ersetzt Placeholder im Template mit tatsächlichen Werten für das aktuelle Produkt
                $stmtStock->bindValue(':amount', $amount, PDO::PARAM_INT);
                $stmtStock->bindValue(':pid', $pid, PDO::PARAM_INT);
                $stmtStock->execute();
                
                //Trägt Item in Orderitems ein
                $stmtItem->bindValue(':oid', $orderId, PDO::PARAM_INT);
                $stmtItem->bindValue(':pid', $pid, PDO::PARAM_INT);
                $stmtItem->bindValue(':amount', $amount, PDO::PARAM_INT);
                $stmtItem->bindValue(':total', $totalPrice, PDO::PARAM_STR); 
                $stmtItem->execute();
            }

            //Wenn User eingeloggt war, wird Warenkorb gelöscht.
            if ($userId) {
                $this->cartModel->clear($userId); 
            }

            $pdo->commit();
            return $orderId; 
        
        } catch (PDOException $e) {
            //rollback bei Fehler
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e; 
        }
    }
    //Holt alle bestellungen von User für USerPAge
    public function getOrderByUserId($userId)
    {
        $sql = "SELECT o.*, oa.street, oa.house_number, oa.zip_code 
            FROM Orders o
            JOIN Order_Addresses oa ON o.order_address_id = oa.address_id
            WHERE o.user_id = :uid
            ORDER BY o.date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as &$order){
            $order['items'] = $this->getOrderItems($order['order_id']);
        }

        return $orders;
    }
//Hilfsmethode für getOrderByUserId
    public function getOrderItems($orderId){
        $sql = "SELECT oi.*, p.product_name 
            FROM OrderItems oi
            JOIN Products p ON oi.product_id = p.product_id
            WHERE oi.order_id = :oid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':oid', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
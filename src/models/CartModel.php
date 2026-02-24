<?php

require_once 'Model.php';
require_once __DIR__ . '/../Core/Exceptions/StockExceededException.php';

class CartModel extends Model {
    
    function __construct($db)
    {
        parent::__construct($db);
    }


    //prüft Lagerbestand 
    public function checkStockLimit($productId, $newAmount) {        
        $sql = "SELECT inventory FROM Products WHERE product_id = :productId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":productId", $productId, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new Exception("Produkt nicht gefunden.");
        }
        $stock = $data['inventory'];

        if ($newAmount > $stock) {
            throw new StockExceededException("überschreitet Lagerbestand", $stock);
        }
        return true;
    }

    //holt den Warenkorb für einen User aus der Datenbank
    public function loadCartFromDatabase($userId) {
        $sql = "SELECT product_id, product_amount FROM Cart WHERE user_id = :userId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $cart = [];
        foreach ($results as $row) {
            $cart[$row['product_id']] = $row['product_amount'];
        }
        
        return $cart;
    }

    //liefert Daten für Produkte, die im Session Warenkorb gespeichert sind
    public function resolveSessionCart(array $sessionCart) {
        if (empty($sessionCart)) {
            return ['items' => [], 'total' => 0.0];
        }
        $ids = array_keys($sessionCart);

        //Erstellt Placeholder für jede product ID im Warenkorb (? , ? , ...)
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "SELECT product_id, product_name, price, inventory, asset_path, flavour 
                FROM Products 
                WHERE product_id IN ($placeholders)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $items = [];
        $grandTotal = 0.0;

        foreach ($products as $product) {
            $pId = $product['product_id'];
            
            //holt Menge aus Session
            $amount = $sessionCart[$pId] ?? 0; 
            $product['product_amount'] = $amount;
            $lineTotal = $product['price'] * $amount;
            $grandTotal += $lineTotal;
            $items[] = $product;
        }

        return [
            'items' => $items,
            'total' => $grandTotal
        ];
    }

    //synchronisiert Session Warenkorb mit Datenbank
    public function syncCartToDatabase($userId, array $sessionCart) {
        if (empty($sessionCart)) return;

        try {
            $pdo = $this->conn->getConnection();
            $pdo->beginTransaction();

            //löscht alten Warenkorb für clean reset
            $clearSql = "DELETE FROM Cart WHERE user_id = :userId";
            $stmt = $this->conn->prepare($clearSql);
            $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
            $stmt->execute();

            $insertSql = "INSERT INTO Cart (user_id, product_id, product_amount) VALUES (:userId, :productId, :amount)";
            $stmt = $this->conn->prepare($insertSql);

            foreach ($sessionCart as $productId => $amount) {
                $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
                $stmt->bindValue(":productId", $productId, PDO::PARAM_INT);
                $stmt->bindValue(":amount", $amount, PDO::PARAM_INT);
                $stmt->execute();
            }

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
    
    //löscht den gesamten Warenkorb eines Users
    public function clear($userId) {
        $sql = "DELETE FROM Cart WHERE user_id = :userId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
        $stmt->execute();
    }
}
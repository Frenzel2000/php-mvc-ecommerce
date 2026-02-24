<?php

require_once 'Model.php';

class ProductModel extends Model {

    function __construct($db)
    {
        parent::__construct($db);
    }

    //holt alle Produkte
    public function getAll() 
    {
        $sql = "SELECT * FROM Products";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    //Produkte inkl. Kategorie-Name (für Admin-Liste)
    public function getAllWithCategory(): array
    {
        $sql = "
            SELECT 
                p.*,
                c.category_name
            FROM Products p
            JOIN Categories c ON c.category_id = p.category_id
            ORDER BY p.product_id ASC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //holt Produkte nach ID 
    public function getByID($id)
    {
        $sql = "SELECT * FROM Products WHERE product_id = :id";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    //holt Produkte nach Kategorie
    public function getByCategory($categoryId) 
    {
        $sql = "SELECT * FROM Products WHERE category_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //holt Bestseller
    public function getBestsellers($limit) 
    {
        $sql = "SELECT * FROM Products ORDER BY units_sold DESC LIMIT :lim";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);        
    }

    //legt neues Produkt an
    public function create($data)
    {
        $sql = "INSERT INTO Products 
        (product_name, price, category_id, inventory, flavour, size, description_short, description_long, asset_path) 
        VALUES 
        (:product_name, :price, :category_id, :inventory, :flavour, :size, :description_short, :description_long, :asset_path)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':product_name',$data['product_name'],PDO::PARAM_STR);
        $stmt->bindValue(':price',$data['price'],PDO::PARAM_STR);
        $stmt->bindValue(':category_id',$data['category_id'],PDO::PARAM_INT);
        $stmt->bindValue(':inventory',$data['inventory'],PDO::PARAM_INT);
        $stmt->bindValue(':flavour',$data['flavour'],PDO::PARAM_STR);
        $stmt->bindValue(':size',$data['size'],PDO::PARAM_STR);
        $stmt->bindValue(':description_short',$data['description_short'],PDO::PARAM_STR);
        $stmt->bindValue(':description_long',$data['description_long'],PDO::PARAM_STR);
        $stmt->bindValue(':asset_path',$data['asset_path'],PDO::PARAM_STR);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    //sucht ähnliche Produkte basierend auf keyword (searchbar preview)
    public function searchProducts($keyword) 
    {
        $placeholder = "%{$keyword}%";
        $sql = "SELECT product_id, asset_path, product_name, description_short, price FROM Products WHERE product_name LIKE :placeholder LIMIT 6";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':placeholder', $placeholder, PDO::PARAM_STR); 
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    //holt ähnliche Produkte basierend auf keyword (suche komplett)
    public function searchProductsFull($keyword) 
    {
        $placeholder = "%{$keyword}%";
        $sql = "SELECT * FROM Products WHERE product_name LIKE :placeholder";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':placeholder', $placeholder, PDO::PARAM_STR); 
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    //löscht Produkt
    public function remove($id)
    {
        $sql = "DELETE FROM Products WHERE product_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

      //Update für product_admin
    public function update(int $id, array $data): void
    {
        $sql = "
            UPDATE Products SET
                product_name = :product_name,
                price = :price,
                category_id = :category_id,
                inventory = :inventory,
                units_sold = :units_sold,
                flavour = :flavour,
                size = :size,
                description_short = :description_short,
                description_long = :description_long,
                asset_path = :asset_path
            WHERE product_id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->bindValue(':product_name', $data['product_name'], PDO::PARAM_STR);
        $stmt->bindValue(':price', $data['price'], PDO::PARAM_STR);
        $stmt->bindValue(':category_id', (int)$data['category_id'], PDO::PARAM_INT);
        $stmt->bindValue(':inventory', (int)$data['inventory'], PDO::PARAM_INT);
        $stmt->bindValue(':units_sold', (int)$data['units_sold'], PDO::PARAM_INT);

        $this->bindOptional($stmt, ':flavour', $data['flavour'] ?? null);
        $this->bindOptional($stmt, ':size', $data['size'] ?? null);
        $this->bindOptional($stmt, ':description_short', $data['description_short'] ?? null);
        $this->bindOptional($stmt, ':description_long', $data['description_long'] ?? null);
        $this->bindOptional($stmt, ':asset_path', $data['asset_path'] ?? null);

        $stmt->execute();
    }

    //löschen eines Produktes für den product_manager mit alle verbundenen Tabellen
    public function removeAdmin(int $id): void
    {
        $pdo = $this->conn->getConnection();
        $pdo->beginTransaction();

        try {
            $stmt = $this->conn->prepare("DELETE FROM Cart WHERE product_id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->conn->prepare("DELETE FROM Ratings WHERE product_id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->conn->prepare("DELETE FROM OrderItems WHERE product_id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->conn->prepare("DELETE FROM Products WHERE product_id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $pdo->commit();
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    //Helper für Nullable-Felder
    private function bindOptional(PDOStatement $stmt, string $param, $value): void
    {
        if ($value === '' || $value === null) {
            $stmt->bindValue($param, null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue($param, (string)$value, PDO::PARAM_STR);
        }
    }

    //Filtert Produkte
    public function getFilteredProducts($params) {

        $keyword = $params['term'];

        //Basis SQL ist immer gleich
        $sql = "SELECT * FROM Products 
                WHERE price >= :min 
                AND price <= :max";

        //Filter nach category, wenn vorhanden
        if ($params['catId'] !== null) {
            $sql .= " AND category_id = :cid";
        }

        //Filter nach suchbegriff, wenn vorhanden
        if ($keyword !== null) {
            $sql .= " AND product_name LIKE :keyword";
        }

        //Filtert verfügbarkeit
        if ($params['onlyAvailable'] === 'true' || $params['onlyAvailable'] === 1 || $params['onlyAvailable'] === true) {
            $sql .= " AND inventory > 0";
        }

        //sortiert ergebnisse
        if ($params['sortByBestseller'] === 'true' || $params['sortByBestseller'] === 1 || $params['sortByBestseller'] === true) {
            $sql .= " ORDER BY units_sold DESC";
        } else {
            $sql .= " ORDER BY product_id ASC";
        }

        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);

        //Basis Bindings sind immer gleich
        $stmt->bindValue(':min', $params['min']);
        $stmt->bindValue(':max', $params['max']);
        $stmt->bindValue(':limit', (int)$params['limit'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$params['offset'], PDO::PARAM_INT);

        //category binding, wenn vorhanden
        if ($params['catId'] !== null) {
            $stmt->bindValue(':cid', $params['catId'], PDO::PARAM_INT);
        }

        //keyword binding, wenn vorhanden
        if ($keyword !== null) {
            $stmt->bindValue(':keyword', "%{$keyword}%", PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


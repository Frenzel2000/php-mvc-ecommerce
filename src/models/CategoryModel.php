<?php

require_once 'Model.php';

class CategoryModel extends Model {

    function __construct($db)
    {
        parent::__construct($db);
    }

    //holt alle Kategorien
    public function getAll() 
    {
        $sql = "SELECT * FROM Categories";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //holt Kategorie nach ID 
    public function getByID($id)
    {
        $sql = "SELECT * FROM Categories WHERE category_id = :id";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    //holt alle Kategorien mit ihren dazugehörigen Produkten
    public function getAllWithProducts()
    {
        $sql = "
            SELECT 
                c.category_id,
                c.category_name AS name,
                p.product_id,
                p.product_name AS product_name
            FROM Categories c
            LEFT JOIN Products p ON c.category_id = p.category_id
            ORDER BY c.category_id, p.product_id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $categories = [];

        //baut verschachteltes Categories-Produkt Array auf
        foreach ($rows as $row) {
            $cid = $row['category_id'];

            //Falls Kategorie noch nicht im Array ist, initialisieren
            if (!isset($categories[$cid])) {
                $categories[$cid] = [
                    'category_id' => $cid,
                    'name'        => $row['name'],      
                    'products'    => []
                ];
            }

            //Wenn ein Produkt zur Kategorie existiert (weil LEFT JOIN kann auch NULL liefern)        
            if (!empty($row['product_id'])) {
                $categories[$cid]['products'][] = [
                    'product_id'   => $row['product_id'],
                    'name'         => $row['product_name']
                ];
            }
        }

        //array_values entfernt assoziative Keys (category_id)
        //gibt "sauberes" numerisch indiziertes Array zurück:
        return array_values($categories);
    }

    //holt alle Produkte einer Kategorie
    public function getProductsByCategory($categoryId) {
        $sql = "SELECT * FROM Products WHERE category_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
<?php

class RatingModel extends Model
{
    function __construct($db)
    {
        parent::__construct($db);
    }

    //holt Rating-Score
    public function getRatingScore($productId) {
        $sql = "SELECT COUNT(*) as count, AVG(rating_score) as average 
                FROM Ratings 
                WHERE product_id = :pid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //holt alle Ratings mit Nutzer-Informationen
    public function getFullRating($productId) {
        $sql = "SELECT r.*, u.first_name, u.last_name 
                FROM Ratings r 
                LEFT JOIN Users u ON r.user_id = u.user_id 
                WHERE r.product_id = :pid 
                ORDER BY r.date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*erstellt neues Rating
    TODO:
    1. Logik einfügen, wenn User bereits ein Rating abgegeben hat
    */
    public function create($productId, $data) 
    {
        //prüft ob Nutzer schon ein Rating für das Produkt hat
        $checkSql = "SELECT rating_id FROM Ratings WHERE product_id = :pid AND user_id = :uid";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->bindValue(':pid', $productId, PDO::PARAM_INT);
        $checkStmt->bindValue(':uid', $data['user_id'], PDO::PARAM_INT);
        $checkStmt->execute();

        //wenn user schon eine Bewertung abgegeben hat (LOGIK HIER ERGÄNZEN!!!)
        if ($checkStmt->fetch()) {
            return;
        } 
        //wenn user noch keine Bewertung abgegeben hat, neue Bewertung anlegen
        else {
            $sql = "INSERT INTO Ratings (product_id, user_id, rating_score, comment) 
            VALUES (:pid, :uid, :score, :comment)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
            $stmt->bindValue(':uid', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':score', $data['rating_score'], PDO::PARAM_INT);
            $stmt->bindValue(':comment', $data['comment'], PDO::PARAM_STR);

            try {
                $stmt->execute();
            } 
            catch (PDOException $e) {
                echo "Error: " . $e->getMessage(); 
            }
        }
    }

    //entfertn Rating 
    public function remove($ratingId)
    {
        $sql = "DELETE FROM Ratings WHERE rating_id = :rid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':rid', $ratingId, PDO::PARAM_INT);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    //Holt alle Bewertungen von User, damit wir die bei USer Page zeigen können.
    public function getRatingsByUSerId($userId)
    {
        $sql = "SELECT r.rating_id, r.rating_score, r.comment, r.date, p.product_name 
            FROM Ratings r 
            JOIN Products p ON r.product_id = p.product_id 
            WHERE r.user_id = :uid 
            ORDER BY r.date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
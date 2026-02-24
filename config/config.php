<?php
//Database Objekt wird in jedem Controller erstellt
class Database
{

    private $host = 'localhost';
    private $db_name = 'sport_shop';
    private $username = 'root';
    private $password = '';
    private $conn;



    public function connect()
    {
        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            echo "Connection Error: ".$e->getMessage();
        }
        return $this->conn;
    }


    public function closeConnection(){
        $this->conn = null;
    }
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    public function lastInsertId()
    {
        return $this->conn->lastInsertId();
    }

    public function getConnection()
    {
        return $this->conn;
    }
    
}
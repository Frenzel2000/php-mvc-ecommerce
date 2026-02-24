<?php 

abstract class Model {

    protected $conn; 

    function __construct($db) {
        $this->conn = $db; 
    }
}
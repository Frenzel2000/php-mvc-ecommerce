<?php

//custom StockExceededException für separate Behandlung
class StockExceededException extends Exception {

    //array mit Liste von Produkten, die den Lagerbestand überschreiten
    private $failedProducts = []; 

    public function __construct($messageOrList, $singleStock = null)
    {
        if (is_array($messageOrList)) {
            $this->failedProducts = $messageOrList;
            parent::__construct("Einige Artikel sind nicht mehr verfügbar.");
        } else {
            $this->failedProducts = [[
                'name' => $messageOrList, 
                'stock' => $singleStock
            ]];
            parent::__construct($messageOrList);
        }
    }

    public function getFailedProducts() {
        return $this->failedProducts;
    }
    
    public function getAvailableStock() {
        return $this->failedProducts[0]['stock'] ?? 0;
    }
}
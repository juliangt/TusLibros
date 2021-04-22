<?php

require_once "Cart.php";
require_once 'SupermarketTestObjects.php';

class Api
{
    const NOT_AUTHORIZED = "Client is not authorized";

    protected $testObjects;

    public function __construct()
    {
        $this->testObjects = new SupermarketTestObjects();
    }

    public function createCart($clientId,$pass){

        if (!$this->isAuthorized($clientId,$pass))
            throw new InvalidArgumentException(self::NOT_AUTHORIZED);

        return $this->testObjects->createCartWithCatalog();
    }

    private function isAuthorized($clientId,$pass){
        if($clientId != 24 && $pass != "zxczxc"){
            return false;
        }
        return true;
    }
}
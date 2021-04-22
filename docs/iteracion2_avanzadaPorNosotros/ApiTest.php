<?php

require_once 'Cart.php';
require_once 'Api.php';
require_once 'SupermarketTestObjects.php';
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    function testCanNotCreateCartWithInvalidCredentials()
    {
        $clientId = 23;
        $pass = 'zxc';

        try {
            $api = new Api();
            $api->createCart($clientId,$pass);
            $this->fail();
        } catch (InvalidArgumentException $error) {
            $this->assertEquals(Api::NOT_AUTHORIZED, $error->getMessage());
        }
    }

    function testCanCreateCart()
    {
        $clientId = 24;
        $pass = 'zxczxc';

        $api = new Api();
        $cart = $api->createCart($clientId,$pass);
        $this->assertInstanceOf(Cart::class, $cart);
    }
}
<?php

require_once 'Cart.php';
require_once 'SupermarketTestObjects.php';
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    protected $testObjects;

    protected function setUp():void {
        $this->testObjects = new SupermarketTestObjects();
    }

    public function testNewCartIsEmpty(){
        $cart = $this->testObjects->createCartWithCatalog();

        $this->assertTrue($cart->isEmpty());
    }

    public function testCartIsNotEmptyAfterAddingAProduct(){
        $cart = $this->testObjects->createCartWithCatalog();

        $cart->add($this->testObjects->validProduct());

        $this->assertFalse($cart->isEmpty());
    }

    public function testCartIncludesAddedProducts(){
        $cart = $this->testObjects->createCartWithCatalog();

        $cart->add($this->testObjects->validProduct());

        $this->assertTrue($cart->includes($this->testObjects->validProduct()));
    }

    public function testCartDoesNotIncludeNotAddedProducts(){
        $cart = $this->testObjects->createCartWithCatalog();

        $this->assertFalse($cart->includes($this->testObjects->validProduct()));
    }

    public function testCanNotAddProductsNotInCatalog(){
        $cart = $this->testObjects->createCartWithCatalog();

        try {
            $cart->add($this->testObjects->invalidProduct());
            $this->fail();
        } catch (Error $exception){
            $this->assertEquals(Cart::INVALID_PRODUCT,$exception->getMessage());
            $this->assertTrue($cart->isEmpty());
        }
    }

    public function testCanNotAddNoPositiveNumberOfProducts(){
        $cart = $this->testObjects->createCartWithCatalog();

        try {
            $cart->add($this->testObjects->validProduct(),0);
            $this->fail();
        } catch (Error $exception){
            $this->assertEquals(Cart::INVALID_NUMBER_OF_PRODUCTS,$exception->getMessage());
            $this->assertTrue($cart->isEmpty());
        }
    }

    public function testCanNotAddNonIntegerNumberOfProducts(){
        $cart = $this->testObjects->createCartWithCatalog();

        try {
            $cart->add($this->testObjects->validProduct(),1.1);
            $this->fail();
        } catch (Error $exception){
            $this->assertEquals(Cart::INVALID_NUMBER_OF_PRODUCTS,$exception->getMessage());
            $this->assertTrue($cart->isEmpty());
        }
    }

    public function testCanAddManyProductsAtTheSameTime(){
        $cart = $this->testObjects->createCartWithCatalog();

        $cart->add($this->testObjects->validProduct(),2);

        $this->assertEquals(2,$cart->numberOf($this->testObjects->validProduct()));
    }

    public function testNumberOfProductsOfNotAddedProductIsZero(){
        $cart = $this->testObjects->createCartWithCatalog();

        $this->assertEquals(0,$cart->numberOf($this->testObjects->validProduct()));
    }
}
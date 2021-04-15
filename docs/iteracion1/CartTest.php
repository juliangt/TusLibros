<?php

require_once 'Cart.php';
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{

    public function testNewCartIsEmpty(){
        $cart = $this->createCartWithCatalog();

        $this->assertTrue($cart->isEmpty());
    }

    public function testCartIsNotEmptyAfterAddingAProduct(){
        $cart = $this->createCartWithCatalog();

        $cart->add($this->validProduct());

        $this->assertFalse($cart->isEmpty());
    }

    public function testCartIncludesAddedProducts(){
        $cart = $this->createCartWithCatalog();

        $aProduct = $this->validProduct();
        $cart->add($aProduct);

        $this->assertTrue($cart->includes($aProduct));
    }

    public function testCartDoesNotIncludeNotAddedProducts(){
        $cart = $this->createCartWithCatalog();

        $aProduct = $this->validProduct();

        $this->assertFalse($cart->includes($aProduct));
    }

    public function testCanNotAddProductsNotInCatalog(){
        $cart = $this->createCartWithCatalog();

        try {
            $cart->add($this->invalidProduct());
            $this->fail();
        } catch (Error $error){
            $this->assertEquals(Cart::INVALID_PRODUCT,$error->getMessage());
            $this->assertTrue($cart->isEmpty());
        }
    }

    public function testCanNotAddNoPositiveNumberOfProducts(){
        $cart = $this->createCartWithCatalog();

        try {
            $cart->add($this->validProduct(),0);
            $this->fail();
        } catch (Error $error){
            $this->assertEquals(Cart::INVALID_NUMBER_OF_PRODUCTS,$error->getMessage());
            $this->assertTrue($cart->isEmpty());
        }
    }

    public function testCanNotAddNonIntegerNumberOfProducts(){
        $cart = $this->createCartWithCatalog();

        try {
            $cart->add($this->validProduct(),1.1);
            $this->fail();
        } catch (Error $exception){
            $this->assertEquals(Cart::INVALID_NUMBER_OF_PRODUCTS,$exception->getMessage());
            $this->assertTrue($cart->isEmpty());
        }
    }

    public function testCanAddManyProductsAtTheSameTime(){
        $cart = $this->createCartWithCatalog();

        $cart->add($this->validProduct(),2);

        $this->assertEquals(2,$cart->numberOf($this->validProduct()));
    }

    public function testNumberOfProductsOfNotAddedProductIsZero(){
        $cart = $this->createCartWithCatalog();
        $cart->add($this->validProduct());

        $this->assertEquals(0,$cart->numberOf($this->invalidProduct()));
    }

    /**
     * @return string[]
     */
    public function catalog(): array
    {
        return array($this->validProduct());
    }

    /**
     * @return string
     */
    public function validProduct(): string
    {
        return "ISBN1";
    }

    /**
     * @return string
     */
    public function invalidProduct(): string
    {
        return "ISBN2";
    }

    /**
     * @return Cart
     */
    public function createCartWithCatalog(): Cart
    {
        return new Cart($this->catalog());
    }
}
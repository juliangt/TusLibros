<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Entity\Cart;

class CartTest extends TestCase
{
    public function testNewCartIsEmpty()
    {
        $cart = $this->createCartWithCatalog();

        $this->assertTrue($cart->isEmpty());
    }

    public function testCartIsNotEmptyAfterAddingAProduct(){

        $cart = $this->createCartWithCatalog();

        $cart->add($this->validProduct(), 1);

        $this->assertFalse($cart->isEmpty());
    }

    public function testCanGetAddedBook()
    {
        $cart = $this->createCartWithCatalog();

        $cart->add($this->validProduct(),1);

        $this->assertTrue( $cart->includes($this->validProduct()));
    }

    public function testCartDoesNotIncludeNotAddedProducts(){
        $cart = $this->createCartWithCatalog();

        $cart->add($this->validProduct(),1);

        $this->assertFalse( $cart->includes($this->invalidProduct() ));
    }

    public function testCanGetMoreThanOneBook(){
        $cart = $this->createCartWithCatalog();

        $cart->add($this->validProduct(), 2);

        $this->assertTrue( $cart->includes($this->validProduct()));
        $this->assertEquals( 2 , $cart->numberOf($this->validProduct()) );
    }

    public function testCanAddManyProductsAtTheSameTime(){
        $cart = $this->createCartWithCatalog();
        $cart->add($this->validProduct(),2);
        $this->assertEquals(2,$cart->numberOf($this->validProduct()));
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
     * @return string[]
     */
    public function catalog(): array
    {
        return array($this->validProduct());
    }

    /**
     * @return Cart
     */
    public function createCartWithCatalog(): Cart
    {
        return new Cart($this->catalog());
    }

}
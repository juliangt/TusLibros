<?php

namespace Tests\Unit;

use App\Entity\Cashier;
use PHPUnit\Framework\TestCase;
use App\Entity\Cart;
use App\Entity\Book;

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

    public function testCheckoutEmptyCart(){
        $cart = $this->createCartWithCatalog();

        try {
            $this->checkoutCartSuccess($cart);
            $this->fail();
        } catch (\Exception $exception) {
            $this->assertEquals(Cashier::INVALID_CART_STATUS_IN_CHECKOUT, $exception->getMessage());
        }
    }

    public function testCheckoutWithExpiredCC(){
        $cart = $this->createCartWithCatalog();
        $cart->add($this->validProduct(),2);
        try {
            $this->checkoutCartExpiredCC($cart);
            $this->fail();
        } catch (\Exception $exception) {
            $this->assertEquals(Cashier::INVALID_CARD_IN_CHECKOUT, $exception->getMessage());
        }
    }










    private function checkoutCartSuccess($cart){
        $cashier = new Cashier();
        $cashier->checkout($cart,'4234231111231234','122021','Test Successfull');
    }

    private function checkoutCartExpiredCC($cart){
        $cashier = new Cashier();
        $cashier->checkout($cart,'4234231111231234','012021','Test Failed');
    }

    /**
     * @return Book
     */
    public function validProduct(): Book
    {
        return new Book('ISBN1', 'Mi libro', 100.00);
        //return "ISBN1";
    }

    /**
     * @return Book
     */
    public function invalidProduct(): Book
    {
        return new Book('ISBN2', 'Mi segundo libro', 200.00);
    }

    /**
     * @return Book[]
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
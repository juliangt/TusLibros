<?php

require_once 'TusLibrosRestInterface.php';
require_once 'AuthenticationSystem.php';
require_once 'SupermarketTestObjects.php';
require_once 'MerchantProcessor.php';
require_once 'ManualClock.php';

use PHPUnit\Framework\TestCase;

class TusLibrosRestInterfaceTest extends TestCase implements AuthenticationSystem, MerchantProcessor
{

    private $testObjects;
    private TusLibrosRestInterface $interface;
    private array $salesBook;
    private ManualClock $manualClock;

    protected function setUp():void {
        $this->testObjects = new SupermarketTestObjects();
        $this->salesBook = array();
        $this->manualClock = new ManualClock($this->testObjects->today());
        $this->interface = new TusLibrosRestInterface($this,
            $this->testObjects->catalog(), $this->salesBook, $this,
            $this->manualClock);
    }

    public function testCanNotCreateCartWithInvalidUserId(){

        try {
            $this->interface->createCart($this->invalidClientId(), $this->validPassword());
            $this->fail();
        } catch (InvalidArgumentException $error){
            $this->assertEquals(
                TusLibrosRestInterface::INVALID_CLIENT_ID_OR_PASSWORD,
                $error->getMessage());
        }
    }

    public function testCanCreateCartWithValidUserIdAndPassword(){
        $cartId = $this->interface->createCart($this->validClientId(), $this->validPassword());
        $this->assertCount(0,$this->interface->listCart($cartId));
    }

    public function testCanNotCreateCartWithInvalidPassword(){
        try {
            $this->interface->createCart($this->validClientId(), $this->invalidPassword());
            $this->fail();
        } catch (InvalidArgumentException $error){
            $this->assertEquals(
                TusLibrosRestInterface::INVALID_CLIENT_ID_OR_PASSWORD,
                $error->getMessage());
        }
    }

    public function testUserCanCreateMoreThanOneCart(){
        $firstCartId = $this->interface->createCart($this->validClientId(), $this->validPassword());
        $secondCartId = $this->interface->createCart($this->validClientId(), $this->validPassword());
        $this->assertNotEquals($firstCartId,$secondCartId);
    }

    public function testAddedBooksAreListed(){
        $cartId = $this->interface->createCart($this->validClientId(), $this->validPassword());
        $this->interface->addToCart($cartId,$this->testObjects->validProduct(),1);
        $cartContents = $this->interface->listCart($cartId);

        $this->assertCount(1,$cartContents);
        $this->assertEquals(1,$cartContents[$this->testObjects->validProduct()]);
    }

    public function testCanNotAddToCartWithInvalidCartId(){
        try {
            $this->interface->addToCart($this->invalidCartid(),$this->testObjects->validProduct(),1);
            $this->fail();
        } catch (InvalidArgumentException $error) {
            $this->assertEquals(
                TusLibrosRestInterface::INVALID_CART_ID,
                $error->getMessage());
        }
    }

    public function testCanNotListCartWithInvalidCartId(){
        try {
            $this->interface->listCart($this->invalidCartid());
            $this->fail();
        } catch (InvalidArgumentException $error) {
            $this->assertEquals(
                TusLibrosRestInterface::INVALID_CART_ID,
                $error->getMessage());
        }
    }

    public function testCanNotCheckOutWithInvalidCartId(){
        try {
            $this->interface->checkOutCart($this->invalidCartid(),
                $this->testObjects->validCreditCardNumber(),
                $this->testObjects->validCreditCardNumber(),
                $this->testObjects->notExpiredMonthOfYear());
            $this->fail();
        } catch (InvalidArgumentException $error) {
            $this->assertEquals(
                TusLibrosRestInterface::INVALID_CART_ID,
                $error->getMessage());
        }
    }

    public function testCheckOutAffectsClientPurchases(){
        $cartId = $this->interface->createCart($this->validClientId(),$this->validPassword());
        $this->interface->addToCart($cartId,$this->testObjects->validProduct(),1);
        $this->interface->checkOutCart($cartId,
            $this->testObjects->validCreditCardNumber(),
            $this->testObjects->validCreditCardNumber(),
            $this->testObjects->notExpiredMonthOfYear());

        $purchases = $this->interface->listPurchases($this->validClientId(),$this->validPassword());

        $this->assertCount(1,$purchases->products());
        $this->assertEquals(1,($purchases->products())[$this->testObjects->validProduct()]);
        $this->assertEquals($this->testObjects->validProductPrice(),$purchases->total());
    }

    public function testCanNotListPurchasesWithInvalidClientId(){
        try {
            $this->interface->listPurchases($this->invalidClientId(),$this->validPassword());
        } catch (InvalidArgumentException $error){
            $this->assertEquals(
                TusLibrosRestInterface::INVALID_CLIENT_ID_OR_PASSWORD,
                $error->getMessage());
        }
    }

    public function testCanNotListPurchasesWithInvalidPassword(){
        try {
            $this->interface->listPurchases($this->validClientId(), $this->invalidPassword());
        } catch (InvalidArgumentException $error){
            $this->assertEquals(
                TusLibrosRestInterface::INVALID_CLIENT_ID_OR_PASSWORD,
                $error->getMessage());
        }
    }

    public function testAllClientPurchasesAreListed(){
        $cartId = $this->interface->createCart($this->validClientId(),$this->validPassword());
        $this->interface->addToCart($cartId,$this->testObjects->validProduct(),1);
        $this->interface->checkOutCart($cartId,
            $this->testObjects->validCreditCardNumber(),
            $this->testObjects->validCreditCardNumber(),
            $this->testObjects->notExpiredMonthOfYear());

        $cartId = $this->interface->createCart($this->validClientId(),$this->validPassword());
        $this->interface->addToCart($cartId,$this->testObjects->validProduct(),1);
        $this->interface->checkOutCart($cartId,
            $this->testObjects->validCreditCardNumber(),
            $this->testObjects->validCreditCardNumber(),
            $this->testObjects->notExpiredMonthOfYear());

        $purchases = $this->interface->listPurchases($this->validClientId(),$this->validPassword());

        $this->assertCount(1,$purchases->products());
        $this->assertEquals(2,($purchases->products())[$this->testObjects->validProduct()]);
        $this->assertEquals($this->testObjects->validProductPrice()*2,$purchases->total());
    }

    public function testPurchasesFromDifferentClientsAreNotMixed(){
        $cartId = $this->interface->createCart($this->validClientId(),$this->validPassword());
        $this->interface->addToCart($cartId,$this->testObjects->validProduct(),1);
        $this->interface->checkOutCart($cartId,
            $this->testObjects->validCreditCardNumber(),
            $this->testObjects->validCreditCardNumber(),
            $this->testObjects->notExpiredMonthOfYear());

        $cartId = $this->interface->createCart($this->anotherValidClientId(),$this->validPassword());
        $this->interface->addToCart($cartId,$this->testObjects->validProduct(),2);
        $this->interface->checkOutCart($cartId,
            $this->testObjects->validCreditCardNumber(),
            $this->testObjects->validCreditCardNumber(),
            $this->testObjects->notExpiredMonthOfYear());

        $purchases = $this->interface->listPurchases($this->validClientId(),$this->validPassword());

        $this->assertCount(1,$purchases->products());
        $this->assertEquals(1,($purchases->products())[$this->testObjects->validProduct()]);
        $this->assertEquals($this->testObjects->validProductPrice(),$purchases->total());

        $purchases = $this->interface->listPurchases($this->anotherValidClientId(),$this->validPassword());

        $this->assertCount(1,$purchases->products());
        $this->assertEquals(2,($purchases->products())[$this->testObjects->validProduct()]);
        $this->assertEquals($this->testObjects->validProductPrice()*2,$purchases->total());
    }

    public function testCanNotCheckOutSameCartMoreThanOnce(){
        $cartId = $this->interface->createCart($this->validClientId(),$this->validPassword());
        $this->interface->addToCart($cartId,$this->testObjects->validProduct(),1);
        $this->interface->checkOutCart($cartId,
            $this->testObjects->validCreditCardNumber(),
            $this->testObjects->validCreditCardNumber(),
            $this->testObjects->notExpiredMonthOfYear());

        try {
            $this->interface->checkOutCart($cartId,
                $this->testObjects->validCreditCardNumber(),
                $this->testObjects->validCreditCardNumber(),
                $this->testObjects->notExpiredMonthOfYear());
            $this->fail();
        } catch (InvalidArgumentException $error){
            $this->assertEquals(TusLibrosRestInterface::INVALID_CART_ID,$error->getMessage());
        }
    }

    public function testCanNotListCartAfter30MinutesOfLastUse()
    {
        $this->createCartAndAssertCartSessionIsExpiredWhen(
            fn($cartId) => $this->interface->listCart($cartId));
    }

    public function testCanNotAddToCartCartAfter30MinutesOfLastUse(){
        $this->createCartAndAssertCartSessionIsExpiredWhen(
            fn($cartId) => $this->interface->addToCart($cartId,$this->testObjects->validProduct(),1));
    }

    public function testCanNotCheckOutCartCartAfter30MinutesOfLastUse(){
        $cartId = $this->interface->createCart($this->validClientId(),$this->validPassword());
        $this->interface->addToCart($cartId,$this->testObjects->validProduct(),1);
        $this->manualClock->advance(new DateInterval("PT31M"));

        $this->asssertCartSessionIsExpiredWhen(
            fn ($cartId) => $this->interface->checkOutCart($cartId,
                $this->testObjects->validCreditCardNumber(),
                $this->testObjects->validCreditCardName(),
                $this->testObjects->notExpiredMonthOfYear()), $cartId);
    }

    public function testUsingCartUpdatesItsLastUsedTime(){
        $errorThrow = false;
        try {
            $cartId = $this->interface->createCart($this->validClientId(), $this->validPassword());
            $this->manualClock->advance(new DateInterval("PT29M"));
            $this->interface->addToCart($cartId, $this->testObjects->validProduct(), 1);
            $this->manualClock->advance(new DateInterval("PT29M"));
            $this->interface->listCart($cartId);
            $this->manualClock->advance(new DateInterval("PT29M"));
            $this->interface->checkOutCart($cartId,
                $this->testObjects->validCreditCardNumber(),
                $this->testObjects->validCreditCardName(),
                $this->testObjects->notExpiredMonthOfYear());
        } catch (Error $error){
            $errorThrow = true;
        }

        $this->assertFalse($errorThrow);
    }


    public function createCartAndAssertCartSessionIsExpiredWhen($aClosure) {
        $cartId = $this->interface->createCart($this->validClientId(),$this->validPassword());
        $this->manualClock->advance(new DateInterval("PT31M"));

        $this->asssertCartSessionIsExpiredWhen($aClosure, $cartId);
    }

    /**
     * @param $aClosure
     * @param int $cartId
     */
    private function asssertCartSessionIsExpiredWhen($aClosure, int $cartId): void
    {
        try {
            $aClosure($cartId);
            $this->fail();
        } catch (Error $error) {
            $this->assertEquals(
                TusLibrosRestInterface::CART_SESSION_TIMED_OUT,
                $error->getMessage());
        }
    }

    public function doesAuthenticate($clientId, $password)
    {
        return ($clientId==$this->validClientId() or $clientId==$this->anotherValidClientId())
            and $password==$this->validPassword();
    }

    /**
     * @return string
     */
    private function validClientId(): string
    {
        return "validClientId";
    }

    /**
     * @return string
     */
    private function validPassword(): string
    {
        return "validPassword";
    }

    function debit($total, $creditCard)
    {
        //It will debit always
    }

    /**
     * @return string
     */
    private function invalidClientId(): string
    {
        return "";
    }

    /**
     * @return string
     */
    private function invalidPassword(): string
    {
        return "";
    }

    /**
     * @return int
     */
    private function invalidCartid(): int
    {
        return 1;
    }

    private function anotherValidClientId()
    {
        return "anotherValidClientId";
    }

}
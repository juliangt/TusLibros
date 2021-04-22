<?php

require_once 'CartTest.php';
require_once 'CashierAsPerson.php';
require_once 'SupermarketTestObjects.php';
require_once 'MerchantProcessor.php';

use PHPUnit\Framework\TestCase;


class CashierAsPersonTest extends TestCase implements MerchantProcessor
{
    protected $testObjects;
    private $merchantProcessorBehavior;

    protected function setUp():void {
        $this->testObjects = new SupermarketTestObjects();
        $this->merchantProcessorBehavior = function ($total,$creditCard) {};
    }

    public function testCanNotCheckoutAnEmptyCart()
    {
        $cart = $this->testObjects->createCartWithCatalog();
        $salesBook = array();
        $cashier = new CashierAsPerson($salesBook, $this->testObjects->today(), $this);

        try {
            $cashier->checkOut($cart,$this->testObjects->notExpiredCreditCard());
            $this->fail();
        } catch (Error $error){
            $this->assertEquals(CashierAsPerson::CAN_NOT_CHECKOUT_EMPTY_CART,$error->getMessage());
            $this->assertTrue(empty($salesBook));
        }
    }

    public function testCalculatesSalesTotalCorrectly()
    {
        $cart = $this->testObjects->createCartWithCatalog();
        $cart->add($this->testObjects->validProduct());

        $salesBook = array();
        $cashier = new CashierAsPerson($salesBook, $this->testObjects->today(), $this);

        $total = $cashier->checkOut($cart,$this->testObjects->notExpiredCreditCard());

        $this->assertEquals($this->testObjects->validProductPrice(),$total);
        $this->assertEquals(1,count($salesBook));
        $this->assertEquals($this->testObjects->validProductPrice(),$salesBook[0]);
    }

    public function testCalculatesSalesTotalCorrectlyForMoreThanOneProduct()
    {
        $cart = $this->testObjects->createCartWithCatalog();
        $cart->add($this->testObjects->validProduct(),2);

        $salesBook = array();
        $cashier = new CashierAsPerson($salesBook, $this->testObjects->today(), $this);

        $total = $cashier->checkOut($cart,$this->testObjects->notExpiredCreditCard());

        $this->assertEquals($this->testObjects->validProductPrice()*2,$total);
        $this->assertEquals(1,count($salesBook));
        $this->assertEquals($this->testObjects->validProductPrice()*2,$salesBook[0]);
    }

    public function testCanNotCheckoutWithExpiredCreditCard()
    {
        $cart = $this->testObjects->createCartWithCatalog();
        $cart->add($this->testObjects->validProduct());
        $salesBook = array();
        $this->merchantProcessorBehavior = function ($total,$creditCard) {
            //Merchant processor should not be used if credit card is expired
            $this->fail();
        };
        $cashier = new CashierAsPerson($salesBook, $this->testObjects->today(), $this);

        try {
            $cashier->checkOut($cart, $this->testObjects->expiredCreditCard());
            $this->fail();
        } catch (Error $error){
            $this->assertEquals(CashierAsPerson::CAN_NOT_CHECKOUT_WITH_EXPIRED_CREDIT_CARD,$error->getMessage());
            $this->assertTrue(empty($salesBook));
        }
    }

    public function testCanNotCheckoutWithStolenCreditCard()
    {
        $cart = $this->testObjects->createCartWithCatalog();
        $cart->add($this->testObjects->validProduct());
        $salesBook = array();
        $this->merchantProcessorBehavior = function ($total,$creditCard) {
            throw new Error(MerchantProcessor::STOLEN_CREDIT_CARD);
        };

        $cashier = new CashierAsPerson(
            $salesBook, $this->testObjects->today(), $this);

        try {
            $cashier->checkOut($cart, $this->testObjects->notExpiredCreditCard());
            $this->fail();
        } catch (Error $error){
            $this->assertEquals(MerchantProcessor::STOLEN_CREDIT_CARD,$error->getMessage());
            $this->assertTrue(empty($salesBook));
        }
    }

    public function testCanNotCheckoutCreditCardWithoutCredit()
    {
        $cart = $this->testObjects->createCartWithCatalog();
        $cart->add($this->testObjects->validProduct());
        $salesBook = array();
        $this->merchantProcessorBehavior = function ($total,$creditCard) {
            throw new Error(MerchantProcessor::CREDIT_CARD_WITHOUT_CREDIT);
        };

        $cashier = new CashierAsPerson(
            $salesBook, $this->testObjects->today(), $this);

        try {
            $cashier->checkOut($cart, $this->testObjects->notExpiredCreditCard());
            $this->fail();
        } catch (Error $error){
            $this->assertEquals(MerchantProcessor::CREDIT_CARD_WITHOUT_CREDIT,$error->getMessage());
            $this->assertTrue(empty($salesBook));
        }
    }

    public function testCashierInformsRightTotalAndCreditCardToMerchantProcessor()
    {
        $cart = $this->testObjects->createCartWithCatalog();
        $cart->add($this->testObjects->validProduct());
        $salesBook = array();
        $notExpiredCreditCard = $this->testObjects->notExpiredCreditCard();

        $passedTotal = null;
        $passedCreditCard = null;
        $this->merchantProcessorBehavior = function ($total,$creditCard) use (&$passedTotal, &$passedCreditCard) {
            //I could have the assertions here but I put them at the end to kee the
            //test format in the text although in the time axis would be ok to put them here - Hernan
            $passedTotal = $total;
            $passedCreditCard = $creditCard;
        };

        $cashier = new CashierAsPerson(
            $salesBook, $this->testObjects->today(), $this);

        $cashier->checkOut($cart, $notExpiredCreditCard);

        $this->assertEquals($this->testObjects->validProductPrice(),$passedTotal);
        $this->assertEquals($notExpiredCreditCard,$passedCreditCard);
    }

    public function debit($total,$creditCard) {
        ($this->merchantProcessorBehavior)($total,$creditCard);
    }
}
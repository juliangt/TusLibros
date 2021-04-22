<?php

require_once 'CartTest.php';
require_once 'CashierPerSale.php';
use PHPUnit\Framework\TestCase;


class CashierPerSaleTest extends TestCase implements MerchantProcessor
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

        try {
            new CashierPerSale($salesBook, $cart, $this->testObjects->notExpiredCreditCard(), $this->testObjects->today(), $this, $this->validClient());
            $this->fail();
        } catch (Error $error){
            $this->assertEquals(CashierPerSale::CAN_NOT_CHECKOUT_EMPTY_CART,$error->getMessage());
        }
    }

    public function testCalculatesSalesTotalCorrectly()
    {
        $cart = $this->testObjects->createCartWithCatalog();
        $cart->add($this->testObjects->validProduct());

        $salesBook = array();
        $cashier = new CashierPerSale($salesBook, $cart, $this->testObjects->notExpiredCreditCard(), $this->testObjects->today(), $this, $this->validClient());

        $total = $cashier->checkOut();

        $this->assertEquals($this->testObjects->validProductPrice(),$total);
        $this->assertEquals(1,count($salesBook));
        $this->assertEquals($this->testObjects->validProductPrice(),$salesBook[0]->total());
    }

    public function testCalculatesSalesTotalCorrectlyForMoreThanOneProduct()
    {
        $cart = $this->testObjects->createCartWithCatalog();
        $cart->add($this->testObjects->validProduct(),2);
        $salesBook = array();

        $cashier = new CashierPerSale($salesBook, $cart, $this->testObjects->notExpiredCreditCard(), $this->testObjects->today(), $this, $this->validClient());

        $total = $cashier->checkOut();

        $this->assertEquals($this->testObjects->validProductPrice()*2,$total);
        $this->assertEquals(1,count($salesBook));
        $this->assertEquals($this->testObjects->validProductPrice()*2,$salesBook[0]->total());
    }

    public function testCanNotCheckoutWithExpiredCreditCard()
    {
        $cart = $this->testObjects->createCartWithCatalog();
        $cart->add($this->testObjects->validProduct());
        $salesBook = array();

        try {
            new CashierPerSale($salesBook, $cart, $this->testObjects->expiredCreditCard(), $this->testObjects->today(), $this, $this->validClient());
            $this->fail();
        } catch (Error $error){
            $this->assertEquals(CashierPerSale::CAN_NOT_CHECKOUT_WITH_EXPIRED_CREDIT_CARD,$error->getMessage());
        }
    }

    public function testCanCheckOutOnlyOnce()
    {
        $cart = $this->testObjects->createCartWithCatalog();
        $cart->add($this->testObjects->validProduct(),2);
        $salesBook = array();

        $cashier = new CashierPerSale($salesBook, $cart, $this->testObjects->notExpiredCreditCard(), $this->testObjects->today(), $this, $this->validClient());

        $cashier->checkOut();

        try {
            $cashier->checkOut();
            $this->fail();
        } catch (Error $error) {
            $this->assertEquals(CashierPerSale::CAN_CHECKOUT_ONLY_ONCE,$error->getMessage());
            $this->assertEquals(1,count($salesBook));
            $this->assertEquals($this->testObjects->validProductPrice()*2,$salesBook[0]->total());
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

        $cashier = new CashierPerSale($salesBook, $cart, $this->testObjects->notExpiredCreditCard(), $this->testObjects->today(), $this, $this->validClient());

        try {
            $cashier->checkOut();
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

        $cashier = new CashierPerSale($salesBook, $cart, $this->testObjects->notExpiredCreditCard(), $this->testObjects->today(), $this, $this->validClient());

        try {
            $cashier->checkOut();
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

        $cashier = new CashierPerSale($salesBook, $cart, $notExpiredCreditCard, $this->testObjects->today(), $this, $this->validClient());

        $cashier->checkOut();

        $this->assertEquals($this->testObjects->validProductPrice(),$passedTotal);
        $this->assertEquals($notExpiredCreditCard,$passedCreditCard);
    }

    public function debit($total,$creditCard) {
        ($this->merchantProcessorBehavior)($total,$creditCard);
    }

    private function validClient()
    {
        return "validClient";
    }

}
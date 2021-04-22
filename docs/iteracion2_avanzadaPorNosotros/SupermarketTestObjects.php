<?php

require_once 'Cart.php';
require_once 'CreditCard.php';
require_once 'MonthOfYear.php';

class SupermarketTestObjects
{
    /**
     * @return Cart
     */
    public function createCartWithCatalog() : Cart
    {
        $catalog = array($this->validProduct() => $this->validProductPrice());
        return new Cart($catalog);
    }

    /**
     * @return string
     */
    public function validProduct()
    {
        return "ISBN1";
    }

    /**
     * @return string
     */
    public function invalidProduct()
    {
        return "ISBN2";
    }

    public function validProductPrice()
    {
        return 20;
    }

    public function expiredCreditCard()
    {
        return new CreditCard($this->validCreditCardNumber(), $this->validCreditCardName(), $this->expiredMonthOfYear());
    }

    public function today()
    {
        return new DateTime();
    }

    public function notExpiredCreditCard()
    {
        return new CreditCard($this->validCreditCardNumber(), $this->validCreditCardName(), $this->notExpiredMonthOfYear());
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    public function expiredMonthOfYear(): MonthOfYear
    {
        return MonthOfYear::from($this->today()->sub(new DateInterval("P1Y")));
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    public function notExpiredMonthOfYear(): MonthOfYear
    {
        return MonthOfYear::from($this->today()->add(new DateInterval("P1Y")));
    }

    /**
     * @return string
     */
    public function validCreditCardNumber(): string
    {
        return "1234567890123456";
    }

    /**
     * @return string
     */
    public function validCreditCardName(): string
    {
        return "Pepe Sanchez";
    }
}
<?php


class CashierAsPerson
{
    const CAN_NOT_CHECKOUT_EMPTY_CART = "Can not checkout an empty cart";
    const CAN_NOT_CHECKOUT_WITH_EXPIRED_CREDIT_CARD = "Can not checkout with expired credit card";
    /**
     * @var array
     */
    private $salesBook;
    private $today;
    private $merchantProcessor;

    /**
     * CashierAsPerson constructor.
     * @param array $salesBook
     * @param DateTime $today
     * @param MerchantProcessor $merchantProcessor
     */
    public function __construct(array &$salesBook, DateTime $today,
                                MerchantProcessor $merchantProcessor)
    {
        $this->salesBook = &$salesBook;
        $this->today = $today;
        $this->merchantProcessor = $merchantProcessor;
    }

    public function checkOut(Cart $cart,CreditCard $creditCard)
    {
        $this->assertCartIsNotEmpty($cart);
        $this->assertCreditCardIsNotExpired($creditCard);

        $total = $cart->total();
        $this->merchantProcessor->debit($total,$creditCard);

        array_push($this->salesBook, $total);

        return $total;
    }

    /**
     * @param Cart $cart
     */
    public function assertCartIsNotEmpty(Cart $cart): void
    {
        if ($cart->isEmpty())
            throw new Error(self::CAN_NOT_CHECKOUT_EMPTY_CART);
    }

    /**
     * @param $creditCard
     */
    public function assertCreditCardIsNotExpired(CreditCard $creditCard): void
    {
        if ($creditCard->isExpiredOn($this->today))
            throw new Error((self::CAN_NOT_CHECKOUT_WITH_EXPIRED_CREDIT_CARD));
    }
}
<?php


class CashierPerSale
{
    const CAN_NOT_CHECKOUT_EMPTY_CART = "Can not checkout an empty cart";
    const CAN_NOT_CHECKOUT_WITH_EXPIRED_CREDIT_CARD = "Can not checkout with expired credit card";
    const CAN_CHECKOUT_ONLY_ONCE = "Can checkout only once";
    private Cart $cart;
    private bool $checkedOut;
    private CreditCard $creditCard;
    private MerchantProcessor $merchantProcessor;
    private array $salesBook;

    /**
     * CashierPerSale constructor.
     * @param array $salesBook
     * @param Cart $cart
     * @param CreditCard $creditCard
     * @param DateTime $today
     * @param MerchantProcessor $merchantProcessor
     */
    public function __construct(array &$salesBook, Cart $cart, CreditCard $creditCard, DateTime $today, MerchantProcessor $merchantProcessor)
    {
        $this->assertCartIsNotEmpty($cart);
        $this->assertCreditCardIsNotExpired($creditCard, $today);

        $this->cart = $cart;
        $this->checkedOut = false;
        $this->creditCard = $creditCard;
        $this->merchantProcessor = $merchantProcessor;
        $this->salesBook = &$salesBook;
    }

    public function checkOut()
    {
        $this->assertHasNotCheckedOut();

        $total = $this->cart->total();
        $this->merchantProcessor->debit($total,$this->creditCard);
        array_push($this->salesBook,$total);

        $this->checkedOut = true;
        return $total;
    }

    /**
     * @param $cart
     */
    public function assertCartIsNotEmpty(Cart $cart): void
    {
        if ($cart->isEmpty())
            throw new Error(self::CAN_NOT_CHECKOUT_EMPTY_CART);
    }

    /**
     * @param $creditCard
     * @param $today
     */
    public function assertCreditCardIsNotExpired(CreditCard $creditCard, DateTime $today): void
    {
        if ($creditCard->isExpiredOn($today))
            throw new Error((self::CAN_NOT_CHECKOUT_WITH_EXPIRED_CREDIT_CARD));
    }

    public function assertHasNotCheckedOut(): void
    {
        if ($this->checkedOut)
            throw new Error(self::CAN_CHECKOUT_ONLY_ONCE);
    }
}
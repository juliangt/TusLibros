<?php

require_once 'CashierPerSale.php';
require_once 'CreditCard.php';
require_once 'ClientSalesSummary.php';
require_once 'CartSession.php';

class TusLibrosRestInterface
{
    const INVALID_CLIENT_ID_OR_PASSWORD = "Invalid client id or password";
    const INVALID_CART_ID = "Invalid cart id";
    const CART_SESSION_TIMED_OUT = "Can not use cart after 30 minutes of last use";
    private AuthenticationSystem $authenticationSystem;
    private int $lastCartId;
    private array $catalog;
    private array $cartSessions;
    private array $salesBook;
    private MerchantProcessor $merchantProcessor;
    private Clock $clock;

    /**
     * TusLibrosRestInterface constructor.
     * @param AuthenticationSystem $authenticationSystem
     * @param array $catalog
     * @param array $salesBook
     * @param MerchantProcessor $merchantProcessor
     * @param Clock $aClock
     */
    public function __construct(AuthenticationSystem $authenticationSystem, array $catalog, array $salesBook, MerchantProcessor $merchantProcessor, Clock $aClock)
    {
        $this->authenticationSystem = $authenticationSystem;
        $this->catalog = $catalog;
        $this->salesBook = $salesBook;
        $this->merchantProcessor = $merchantProcessor;

        $this->lastCartId = 0;
        $this->cartSessions = array();
        $this->clock = $aClock;
    }

    public function createCart($aClientId, $aPassword)
    {
        $this->assertCanAuthenticate($aClientId, $aPassword);

        $cartId = $this->lastCartId++;
        $this->cartSessions[$cartId] = new CartSession($aClientId,
            new Cart($this->catalog),$this->clock);
        
        return $cartId;
    }

    public function listCart($cartId)
    {
        return $this->withCartSessionDo(
            $cartId,
            fn($cartSession) => $cartSession->cartContents());
    }

    public function addToCart($cartId, string $aProduct, int $aQuantity)
    {
        $this->withCartSessionDo(
            $cartId,
            fn($cartSession) => $cartSession->addToCart($aProduct,$aQuantity));
    }

    public function checkOutCart(int $cartId, $creditCardNumber,$creditCardName,$creditCardExpiration)
    {
        $this->withCartSessionDo(
            $cartId,
            fn($cartSession) => $this->doCheckOut($creditCardNumber, $creditCardName,
                $creditCardExpiration, $cartSession));

        unset($this->cartSessions[$cartId]);
    }

    /**
     * @param $creditCardNumber
     * @param $creditCardName
     * @param $creditCardExpiration
     * @param $cartSession
     * @return float|int
     */
    private function doCheckOut($creditCardNumber, $creditCardName, $creditCardExpiration, $cartSession)
    {
        $creditCard = new CreditCard($creditCardNumber, $creditCardName, $creditCardExpiration);
        $cashier = new CashierPerSale($this->salesBook, $cartSession->cart(),
            $creditCard, $this->clock->now(), $this->merchantProcessor, $cartSession->clientId());

        return $cashier->checkOut();
    }

    public function withCartSessionDo($cartId, $closure){
        $cartSession = $this->cartSessionOf($cartId);
        return $cartSession->ifNotExpiredDo($closure);
    }

    public function listPurchases(string $aClientId, string $aPassword)
    {
        $this->assertCanAuthenticate($aClientId,$aPassword);

        return new ClientSalesSummary($aClientId,$this->salesBook);
    }

    /**
     * @param $cartId
     * @return Cart
     */
    private function cartSessionOf($cartId): CartSession
    {
        if (!array_key_exists($cartId, $this->cartSessions))
            throw new InvalidArgumentException(self::INVALID_CART_ID);

        return $this->cartSessions[$cartId];
    }

    /**
     * @param $aClientId
     * @param $aPassword
     */
    private function assertCanAuthenticate($aClientId, $aPassword): void
    {
        if (!$this->authenticationSystem->doesAuthenticate($aClientId, $aPassword))
            throw new InvalidArgumentException(self::INVALID_CLIENT_ID_OR_PASSWORD);
    }


}
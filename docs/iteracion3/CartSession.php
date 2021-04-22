<?php


class CartSession
{
    private $clientId;
    private Cart $cart;
    private $clock;
    private $lastUsedTime;

    /**
     * CartSession constructor.
     * @param $aClientId
     * @param Cart $aCart
     */
    public function __construct($aClientId, Cart $aCart, $clock)
    {
        $this->clientId = $aClientId;
        $this->cart = $aCart;
        $this->clock = $clock;
        $this->lastUsedTime = $clock->now();
    }

    public function cart(){
        return $this->cart;
    }

    public function clientId(){
        return $this->clientId;
    }

    public function isExpired(){
        $lastUsedCloned = clone $this->lastUsedTime;
        return $lastUsedCloned->add(new DateInterval("PT30M")) < $this->clock->now();
    }

    public function cartContents(){
        return $this->cart->contents();
    }

    public function addToCart($aProduct,$aQuantity){
        $this->cart->add($aProduct, $aQuantity);
    }

    public function ifNotExpiredDo($aClosure){
        if($this->isExpired($this->clock->now()))
            throw new Error(TusLibrosRestInterface::CART_SESSION_TIMED_OUT);

        try {
            return $aClosure($this);
        } finally {
            $this->lastUsedTime = $this->clock->now();
        }
    }
}
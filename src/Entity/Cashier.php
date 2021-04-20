<?php

namespace App\Entity;

class Cashier
{
    const INVALID_CART_STATUS_IN_CHECKOUT = "Cart status is invalid, can not process this cart";

    /**
     * @param $cart
     * @throws Exception
     */
    public function checkout(Cart $cart,Card $card){
        $this->cartIsValidToCheckout($cart);
        $card->isValidCC();

    }

    /**
     * @param $cart
     * @throws \Exception
     */
    private function cartIsValidToCheckout(Cart $cart){
        if ($cart->isEmpty()){
            throw new \Exception(self::INVALID_CART_STATUS_IN_CHECKOUT);
        }
    }
}
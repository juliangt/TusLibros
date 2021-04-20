<?php

namespace App\Entity;

class Cashier
{
    const INVALID_CART_STATUS_IN_CHECKOUT = "Cart status is invalid, can not process this cart";
    const INVALID_CARD_IN_CHECKOUT = "Card is expired";

    /**
     * @param $cart
     * @throws Exception
     */
    public function checkout(Cart $cart,int $cc, string $cced,string $cco){
        $this->cartIsValidToCheckout($cart,$cc,$cced,$cco);
    }

    /**
     * @param $cart
     * @throws Exception
     */
    private function cartIsValidToCheckout(Cart $cart,int $cc, string $cced,string $cco){
        if ($cart->isEmpty()){
            throw new \Exception(self::INVALID_CART_STATUS_IN_CHECKOUT);
        }

        $fecha = \DateTime::createFromFormat('mY', $cced);
        if ($fecha < new \DateTime()){
            throw new \Exception(self::INVALID_CARD_IN_CHECKOUT);
        }
    }
}
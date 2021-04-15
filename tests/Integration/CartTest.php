<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Entity\Cart;

class CartTest extends TestCase
{
    public function testNewCartIsEmpty()
    {
        $cart = new Cart();

        $this->assertTrue($cart->isEmpty());

    }
}
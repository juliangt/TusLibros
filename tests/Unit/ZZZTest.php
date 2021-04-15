<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Entity\Cart;

class ZZZTest extends TestCase
{
    public function testNewCartIsEmpty()
    {
        $cart = new Cart();

        $this::assertInstanceOf(CART::class, $cart);
        $this::assertTrue(0 == $cart->getItems());

    }

}
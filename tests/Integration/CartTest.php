<?php

namespace Tests\Unit;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;
use App\Entity\Cart;

class CartTest extends TestCase
{
    public function testNewCartIsEmpty()
    {
        $cart = new Cart();

        $this->assertTrue($cart->isEmpty());
    }

    public function testCanAddBookToCart(){
        $cart = new Cart();

        $book = new Book();

        $cart->addBook($book);

        $this->assertInstanceOf(Book::class,$cart->getBook());

    }

}
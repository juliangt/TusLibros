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

    public function testCartIsNotEmptyAfterAddingAProduct(){
        $cart = new Cart();

        $book = new Book(1);

        $cart->addBook($book, 1);

        $this->assertFalse($cart->isEmpty());
    }

    public function testCanAddBookToCart(){
        $cart = new Cart();

        $book = new Book(1);

        $cart->addBook($book, 1);

        $this->assertInstanceOf(Book::class, $cart->getItems()[0]->getBook());
    }

    public function testCanGetAddedBook()
    {
        $cart = new Cart();

        $book1 = new Book(1);
        $book2 = new Book(2);

        $cart->addBook($book1,1);

        $this->assertEquals( $book1->getId(), $cart->getItems()[0]->getBook()->getId() );
        $this->assertNotEquals( $book2->getId(), $cart->getItems()[0]->getBook()->getId() );
    }

    public function testCartDoesNotIncludeNotAddedProducts(){
        $cart = new Cart();

        $book1 = new Book(1);

        $this->assertTrue( $cart->isEmpty());
    }

    public function testCanGetMoreThanOneBook(){
        $cart = new Cart();

        $book1 = new Book(1);
        $book2 = new Book(2);

        $cart->addBook($book1, 1);

        $cart->addBook($book2, 1);

        $this->assertEquals( $book1->getId(), $cart->getItems()[0]->getBook()->getId() );
        $this->assertEquals( $book2->getId(), $cart->getItems()[1]->getBook()->getId() );

    }

    public function testCanAddBookWithQuantity()
    {
        $cart = new Cart();

        $book1 = new Book(1);

        $cart->addBook($book1, 10);

        $items = $cart->getItems();

        $this->assertEquals( 10, $items[0]->getQuantity() );

    }

}
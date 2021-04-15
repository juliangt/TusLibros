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

        $book = new Book(1);

        $cart->addBook($book);

        $this->assertInstanceOf(Book::class,$cart->getBooks()[0]);

    }

    public function testCanGetAddedBook()
    {
        $cart = new Cart();

        $book1 = new Book(1);
        $book2 = new Book(2);

        $cart->addBook($book1);

        $this->assertEquals( $book1->getId(), $cart->getBooks()[0]->getId() );
        $this->assertNotEquals( $book2->getId(), $cart->getBooks()[0]->getId() );
    }

    public function testCanGetMoreThanOneBook(){
        $cart = new Cart();

        $book1 = new Book(1);
        $book2 = new Book(2);

        $cart->addBook($book1);

        $cart->addBook($book2);

        $books = $cart->getBooks();

        $this->assertEquals( $book1->getId(), $books[0]->getId() );
        $this->assertEquals( $book2->getId(), $books[1]->getId() );

    }

}
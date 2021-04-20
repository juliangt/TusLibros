<?php

namespace App\Entity;

use App\Entity\Book;

class CartItem {

    /**
     * @var Book
     */
    private $book;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var float
     */
    private $price;

    public function __construct(Book $book, int $quantity)
    {
        $this->setBook($book);
        $this->setQuantity($quantity);
        $this->setPrice( $book->getPrice() );

    }

    /**
     * @return Book
     */
    public function getBook(): Book
    {
        return $this->book;
    }

    /**
     * @param Book $book
     */
    public function setBook(Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSubtotal() : float
    {
        return $this->getPrice() * $this->getQuantity();
    }
}
<?php

namespace App\Entity;

class CartItem
{

    private $book;
    private $quantity;

    public function __construct(Book $book, int $quantity)
    {
        $this->setBook($book);
        $this->setQuantity($quantity);
    }

    public function getBook() : Book
    {
        return $this->book;
    }

    public function setBook(Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getQuantity() : int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }




}
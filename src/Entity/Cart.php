<?php

namespace App\Entity;
use App\Entity\Book;

class Cart
{
    private $items = [];

    public function isEmpty()
    {
        return count($this->items) == 0;
    }

    public function addBook(Book $book, int $quantity)
    {
        $cartItem = new CartItem($book, $quantity);
        $this->items[] = $cartItem;
    }

    public function getItems() : array
    {
        return $this->items;
    }

}
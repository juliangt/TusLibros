<?php

namespace App\Entity;
use App\Entity\Book;

class Cart
{
    private $books = [];

    public function isEmpty()
    {
        return count($this->books) == 0;
    }

    public function addBook(Book $book)
    {
        $this->books[] = $book;
    }

    public function getBooks() :array
    {
        return $this->books;
    }

}
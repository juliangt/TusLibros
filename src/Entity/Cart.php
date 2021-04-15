<?php

namespace App\Entity;
use App\Entity\Book;

class Cart
{
    private $book;

    public function isEmpty() {
        return true;
    }

    public function addBook(Book $book) {
        $this->book = $book;
    }

    public function getBook(){
        return $this->book;
    }

}
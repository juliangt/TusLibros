<?php

namespace App\Entity;

class Cart
{

    public function isEmpty() {
        return true;
    }

    public function addBook($book) {

    }

    public function getBook(){
        return new Book();
    }

}
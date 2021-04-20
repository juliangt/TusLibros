<?php

namespace App\Entity;

class Book {

    /**
     * @var string
     */
    private $isbn;

    /**
     * @var string
     */
    private $title;

    /**
     * @var float
     */
    private $price;

    public function __construct($isbn, $title, $price)
    {
        $this->setIsbn($isbn);
        $this->setTitle($title);
        $this->setPrice($price);
    }

    /**
     * @return string
     */
    public function getIsbn() : string
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     */
    public function setIsbn($isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice() : float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }

}
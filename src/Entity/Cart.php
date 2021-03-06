<?php

namespace App\Entity;

use App\Entity\Book;
use App\Entity\CartItem;

class Cart
{
    const INVALID_PRODUCT = "Product not in catalog";
    const INVALID_NUMBER_OF_PRODUCTS = "Number of products must be a positive integer";

    private $contents = [];
    private $catalog = [];
    private $prices = [];

    /**
     * Cart constructor.
     * @param array $catalog
     */
    public function __construct(array $catalog)
    {
        $this->contents = array();
        $this->catalog = $catalog;
    }

    public function isEmpty()
    {
        return count($this->contents) == 0;
    }

    public function add(Book $aBook, int $quantity)
    {

        $this->assertProductIsInCatalog($aBook);

        $this->assertValidNumberOfProducts($quantity);

        if ($this->includes($aBook) ) {
            $bookItem = $this->get($aBook);
            $bookItem->setQuantity( $bookItem->getQuantity() + $quantity );

        } else {
            $cartItem = new CartItem($aBook, $quantity);
            $this->contents[] = $cartItem;
        }

    }

    public function setPrice($aProduct, float $price)
    {
        if ($this->includes($aProduct)) {
            $this->prices[$aProduct] = $price;
        }
    }

    public function get($aBook)
    {
        foreach ($this->contents as $bookItem) {
            if ($bookItem->getBook()->getIsbn() == $aBook->getIsbn()) {
                return $bookItem;
            }
        }
    }

    public function includes($aBook)
    {
        foreach ($this->contents as $bookItem) {
            if ($bookItem->getBook()->getIsbn() == $aBook->getIsbn()) {
                return true;
            }
        }

        return false;
    }

    public function numberOf($aBook) : int
    {
        foreach ($this->contents as $bookItem) {
            if ($bookItem->getBook()->getIsbn() == $aBook->getIsbn()) {
                return $bookItem->getQuantity();
            }
        }

        return 0;
    }

    /**
     * @param $aBook
     * @throws Exception
     */
    public function assertProductIsInCatalog(Book $aBook): void
    {
        $flag = false;

        foreach ($this->catalog as $bookInCatalog) {
            if ($bookInCatalog->getIsbn() == $aBook->getIsbn()) {
                $flag = true;
            }
        }

        if (!$flag)
            throw new \Exception((self::INVALID_PRODUCT));
    }

    /**
     * @param $aNumberOfProducts
     */
    // Es importante que el tipo de $aNumberOfProducts no sea int porque sino
    // realiza un cast autom??tico por ej. cuando se pasa un float
    // y el error no se detecta - Hernan
    public function assertValidNumberOfProducts($aNumberOfProducts): void
    {
        if (!is_integer($aNumberOfProducts) or $aNumberOfProducts < 1)
            throw new \Exception(self::INVALID_NUMBER_OF_PRODUCTS);
    }
}
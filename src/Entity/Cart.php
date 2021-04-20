<?php

namespace App\Entity;

class Cart
{
    const INVALID_PRODUCT = "Product not in catalog";
    const INVALID_NUMBER_OF_PRODUCTS = "Number of products must be a positive integer";

    private $contents = [];
    private $catalog = [];

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

    public function add($book, $quantity)
    {

        $this->assertProductIsInCatalog($book);

        $this->assertValidNumberOfProducts($quantity);

        for($i=0;$i<$quantity;$i++)
            array_push($this->contents, $book);

    }

    public function includes($aProduct)
    {
        return in_array($aProduct,$this->contents);
    }

    public function numberOf($aProduct)
    {
        $productNumbers = array_count_values($this->contents);
        if(array_key_exists($aProduct,$productNumbers))
            return $productNumbers[$aProduct];
        else
            return 0;
    }

    /**
     * @param $aProduct
     * @throws Exception
     */
    public function assertProductIsInCatalog($aProduct): void
    {
        if (!in_array($aProduct, $this->catalog))
            throw new \Exception((self::INVALID_PRODUCT));
    }

    /**
     * @param $aNumberOfProducts
     */
    // Es importante que el tipo de $aNumberOfProducts no sea int porque sino
    // realiza un cast automático por ej. cuando se pasa un float
    // y el error no se detecta - Hernan
    public function assertValidNumberOfProducts($aNumberOfProducts): void
    {
        if (!is_integer($aNumberOfProducts) or $aNumberOfProducts < 1)
            throw new \Exception(self::INVALID_NUMBER_OF_PRODUCTS);
    }
}
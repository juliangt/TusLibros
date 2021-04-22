<?php


class Cart
{
    const INVALID_PRODUCT = "Product not in catalog";
    const INVALID_NUMBER_OF_PRODUCTS = "Number of products must be a positive integer";

    private $contents = array();
    private $catalog;

    /**
     * Cart constructor.
     * @param $catalog
     */
    public function __construct($catalog)
    {
        $this->catalog = $catalog;
    }

    public function isEmpty()
    {
        return empty($this->contents);
    }

    public function add($aProduct,$aNumberOfProduct=1)
    {
        $this->assertProductIsInCatalog($aProduct);
        $this->assertNumberOfProductsIsPositiveInteger($aNumberOfProduct);

        for($i=0; $i<$aNumberOfProduct; $i++)
            array_push($this->contents, $aProduct);
    }

    public function includes($aProduct)
    {
        return in_array($aProduct,$this->contents);
    }

    /**
     * @param $aProduct
     * @throws Exception
     */
    public function assertProductIsInCatalog($aProduct)
    {
        if (!array_key_exists($aProduct, $this->catalog))
            throw new Error(self::INVALID_PRODUCT);
    }

    /**
     * @param $aNumberOfProduct
     */
    public function assertNumberOfProductsIsPositiveInteger($aNumberOfProduct)
    {
        if ($aNumberOfProduct < 1 or !is_integer($aNumberOfProduct))
            throw new Error(self::INVALID_NUMBER_OF_PRODUCTS);
    }

    public function numberOf($aProduct)
    {
        $productNumbers = array_count_values($this->contents);
        if(array_key_exists($aProduct,$productNumbers))
            return $productNumbers[$aProduct];
        else
            return 0;
    }

    public function total()
    {
        $productPrices = array_map(
            function ($aProduct) { return $this->catalog[$aProduct]; },
            $this->contents);

        $total = array_sum($productPrices);
        return $total;
    }

}
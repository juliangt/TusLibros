<?php


class Cart
{
    const INVALID_PRODUCT = "Product not in catalog";
    const INVALID_NUMBER_OF_PRODUCTS = "Number of products must be a positive integer";

    private array $contents;
    private array $catalog;

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
        return empty($this->contents);
    }

    public function add($aProduct,$aNumberOfProducts = 1)
    {
        $this->assertProductIsInCatalog($aProduct);
        $this->assertValidNumberOfProducts($aNumberOfProducts);

        for($i=0;$i<$aNumberOfProducts;$i++)
            array_push($this->contents, $aProduct);
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
            throw new Error((self::INVALID_PRODUCT));
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
            throw new Error(self::INVALID_NUMBER_OF_PRODUCTS);
    }
}
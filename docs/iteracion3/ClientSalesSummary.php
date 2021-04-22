<?php


class ClientSalesSummary
{
    private array $clientSales;
    private $products;
    private $total;

    /**
     * ClientSalesSummary constructor.
     * @param array $clientSales
     */
    public function __construct($aClientId, $salesBook)
    {
        $this->clientSales = array_filter(
            $salesBook, fn($aSale) => $aSale->isOf($aClientId));

        $this->products = null;
    }

    public function products()
    {
        if(is_null($this->products))
            $this->initializeProducts();

        return $this->products;
    }

    public function total()
    {
        if(is_null($this->total))
            $this->initializeTotal();

        return $this->total;
    }

    private function initializeProducts(): void
    {
        $this->products = array();

        foreach ($this->clientSales as $sale) {
            $this->addCartContentsToProducts($sale->cartContents());
        }
    }

    private function initializeTotal()
    {
        $this->total = array_sum(
            array_map(fn($aSale) => $aSale->total(),$this->clientSales));
    }

    /**
     * @param $cartContents
     */
    private function addCartContentsToProducts($cartContents): void
    {
        foreach ($cartContents as $product => $quantity) {
            if (array_key_exists($product, $this->products))
                $this->products[$product] += $quantity;
            else
                $this->products[$product] = $quantity;
        }
    }
}
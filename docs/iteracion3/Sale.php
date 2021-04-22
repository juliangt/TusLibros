<?php


class Sale
{
    private CashierPerSale $cashier;
    private $total;

    /**
     * Sale constructor.
     * @param CashierPerSale $aCashier
     * @param array $salesBook
     * @param float|int $total
     */
    public function __construct(CashierPerSale $aCashier, $aTotal)
    {
        $this->cashier = $aCashier;
        $this->total = $aTotal;
    }

    public function isOf($aClient){
        return $this->cashier->isClient($aClient);
    }

    public function total(){
        return $this->total;
    }

    public function cartContents(){
        return $this->cashier->cartContents();
    }
}
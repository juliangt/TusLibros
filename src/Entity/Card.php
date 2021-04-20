<?php


namespace App\Entity;


use Carbon\Carbon;

class Card
{
    const INVALID_CARD_IN_CHECKOUT = "Card is expired";

    private $ccNum;
    private $ccExpDate;
    private $ccName;

    public function __construct(string $ccNum, string $ccExpDate, string $ccName)
    {
        $this->setCcNum($ccNum);
        $this->setCcExpDate($ccExpDate);
        $this->setCcName($ccName);
    }

    public function isValidCC()
    {
        $ccMonth = new Carbon($this->getCcExpDate());
        $monthNow = Carbon::now()->firstOfMonth()->addMonth(1);

        if ($ccMonth < $monthNow){
            throw new \Exception(self::INVALID_CARD_IN_CHECKOUT);
        }
    }
    /**
     * @return mixed
     */
    public function getCcNum()
    {
        return $this->ccNum;
    }

    /**
     * @param mixed $ccNum
     */
    public function setCcNum($ccNum): void
    {
        $this->ccNum = $ccNum;
    }

    /**
     * @return mixed
     */
    public function getCcExpDate()
    {
        return $this->ccExpDate;
    }

    /**
     * @param mixed $ccExpDate
     */
    public function setCcExpDate($ccExpDate): void
    {
        $this->ccExpDate = $ccExpDate;
    }

    /**
     * @return mixed
     */
    public function getCcName()
    {
        return $this->ccName;
    }

    /**
     * @param mixed $ccName
     */
    public function setCcName($ccName): void
    {
        $this->ccName = $ccName;
    }


}
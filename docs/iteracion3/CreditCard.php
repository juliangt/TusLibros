<?php

require_once "MonthOfYear.php";

class CreditCard
{
    const INVALID_NUMBER = "Number must be 16 digits";
    const INVALID_NAME = "Name can not be empty";
    private MonthOfYear $expirationDate;
    private string $number;
    private string $name;

    /**
     * CreditCard constructor.
     * @param string $number
     * @param string $name
     * @param MonthOfYear $expirationDate
     */
    public function __construct(string $number, string $name, MonthOfYear $expirationDate)
    {
        $this->assertNumberIsValid($number);
        $this->assertNameIsValid($name);

        $this->expirationDate = $expirationDate;
        $this->number = $number;
        $this->name = $name;
    }

    public function isExpiredOn($aDate){
        return $this->expirationDate->isBefore(MonthOfYear::from($aDate));
    }

    /**
     * @param string $number
     */
    public function assertNumberIsValid(string $number): void
    {
        if (strlen($number) != 16)
            throw new InvalidArgumentException(self::INVALID_NUMBER);
        if (!is_numeric($number))
            throw new InvalidArgumentException(self::INVALID_NUMBER);
        if (strpos($number, "."))
            throw new InvalidArgumentException(self::INVALID_NUMBER);
    }

    /**
     * @param string $name
     */
    public function assertNameIsValid(string $name): void
    {
        if (empty($name))
            throw new InvalidArgumentException(self::INVALID_NAME);
    }

    public function number()
    {
        return $this->number;
    }

    public function name()
    {
        return $this->name;
    }
}
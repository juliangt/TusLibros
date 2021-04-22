<?php


class MonthOfYear
{
    const INVALID_MONTH_NUMBER = "Month number must be an integer between 1 and 12";
    const INVALID_YEAR_NUMBER = "Year number must be integer different to 0";
    private $monthNumber;
    private $yearNumber;

    /**
     * MonthOfYear constructor.
     * @param $aMonthNumber
     * @param $aYearNumber
     */
    public function __construct($aMonthNumber, $aYearNumber)
    {
        $this->assertMonthNumberIsValid($aMonthNumber);
        $this->assertYearNumberIsValid($aYearNumber);

        $this->monthNumber = $aMonthNumber;
        $this->yearNumber = $aYearNumber;
    }

    public static function from(DateTime $date)
    {
        $monthNumberAsString = $date->format("m");
        $yearNumberAsString = $date->format("Y");

        return new MonthOfYear(intval($monthNumberAsString),intval($yearNumberAsString));
    }

    public function monthNumber()
    {
        return $this->monthNumber;
    }

    public function yearNumber()
    {
        return $this->yearNumber;
    }

    /**
     * @param $aMonthNumber
     */
    public function assertMonthNumberIsValid($aMonthNumber): void
    {
        if ($aMonthNumber < 1 or $aMonthNumber > 12 or !is_integer($aMonthNumber))
            throw new InvalidArgumentException(self::INVALID_MONTH_NUMBER);
    }

    /**
     * @param $aYearNumber
     */
    public function assertYearNumberIsValid($aYearNumber): void
    {
        if ($aYearNumber == 0 or !is_integer($aYearNumber))
            throw new InvalidArgumentException(self::INVALID_YEAR_NUMBER);
    }

    public function isBefore(MonthOfYear $aMonthOfYear)
    {
        if ($this->isYearBefore($aMonthOfYear)) return true;
        if ($this->isYearAfter($aMonthOfYear)) return false;

        return $this->isMonthBefore($aMonthOfYear);
    }

    /**
     * @param MonthOfYear $aMonthOfYear
     * @return bool
     */
    public function isYearBefore(MonthOfYear $aMonthOfYear): bool
    {
        return $this->yearNumber < $aMonthOfYear->yearNumber();
    }

    /**
     * @param MonthOfYear $aMonthOfYear
     * @return bool
     */
    public function isMonthBefore(MonthOfYear $aMonthOfYear): bool
    {
        return $this->monthNumber < $aMonthOfYear->monthNumber();
    }

    /**
     * @param MonthOfYear $aMonthOfYear
     * @return bool
     */
    public function isYearAfter(MonthOfYear $aMonthOfYear): bool
    {
        return $this->yearNumber > $aMonthOfYear->yearNumber();
    }
}
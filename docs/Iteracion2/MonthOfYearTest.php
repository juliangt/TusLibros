<?php

require_once "MonthOfYear.php";
use PHPUnit\Framework\TestCase;

class MonthOfYearTest extends TestCase
{

    public function testMonthNumberCanNotBeLessThanOne(){
        $this->assertCanNotCreateWithInvalidMonthNumber(0);
    }

    public function testMonthNumberMustBeBetweenOneAndTwelve(){
        $monthOfYear = new MonthOfYear(1,2020);
        $this->assertEquals(1,$monthOfYear->monthNumber());
    }

    public function testMonthNumberCanNotBeBiggerThanTwelve(){
        $this->assertCanNotCreateWithInvalidMonthNumber(13);
    }

    public function testMonthNumberMustBeInteger(){
        $this->assertCanNotCreateWithInvalidMonthNumber(1.1);
    }

    public function testYearNumberCanNotBeZero()
    {
        $this->assertCanNotCreateWithInvalidYearNumber(0);
    }

    public function testYearNumberCanBeAnyIntegerDifferentToZero(){
        $monthOfYear = new MonthOfYear(1,2020);
        $this->assertEquals(2020,$monthOfYear->yearNumber());
    }

    public function testYearNumberMustBeInteger()
    {
        $this->assertCanNotCreateWithInvalidYearNumber(2020.1);
    }

    public function testIsBeforeIfYearIsBefore(){
        $smaller = new MonthOfYear(1,2019);
        $bigger = new MonthOfYear(1,2020);

        $this->assertTrue($smaller->isBefore($bigger));
    }

    public function testIsNotBeforeIfYearIsAfter(){
        $smaller = new MonthOfYear(1,2019);
        $bigger = new MonthOfYear(1,2020);

        $this->assertFalse($bigger->isBefore($smaller));
    }

    public function testIsBeforeWithSmallerMonthAndSameYear(){
        $smaller = new MonthOfYear(1,2019);
        $bigger = new MonthOfYear(2,2019);

        $this->assertTrue($smaller->isBefore($bigger));
    }

    public function testIsNotBeforeWithSmallerMonthAndBiggerYear(){
        $smaller = new MonthOfYear(2,2020);
        $bigger = new MonthOfYear(1,2021);

        $this->assertFalse($bigger->isBefore($smaller));
    }

    public function testCanCreateMonthOfYearFromDate(){
        $date = new DateTime("2020-05-30");
        $monthOfYear =  MonthOfYear::from($date);

        $this->assertEquals(5,$monthOfYear->monthNumber());
        $this->assertEquals(2020,$monthOfYear->yearNumber());
    }

    public function assertCanNotCreateWithInvalidMonthNumber($aMonthNumber): void
    {
        $this->assertCanNotCreateMonthOfYear($aMonthNumber, 2020, MonthOfYear::INVALID_MONTH_NUMBER);
    }

    public function assertCanNotCreateWithInvalidYearNumber($aYearNumber): void
    {
        $this->assertCanNotCreateMonthOfYear(1,$aYearNumber,MonthOfYear::INVALID_YEAR_NUMBER);
    }

    /**
     * @param $aMonthNumber
     * @param $aYearNumber
     * @param $anErrorMessage
     */
    public function assertCanNotCreateMonthOfYear($aMonthNumber, $aYearNumber, $anErrorMessage): void
    {
        try {
            new MonthOfYear($aMonthNumber, $aYearNumber);
            $this->fail();
        } catch (InvalidArgumentException $error) {
            $this->assertEquals($anErrorMessage, $error->getMessage());
        }
    }

}
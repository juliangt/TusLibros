<?php

require_once "CreditCard.php";
use PHPUnit\Framework\TestCase;


class CreditCardTest extends TestCase
{
    public function testNumberCanNotBeLessThan16Digits(){
        $this->assertNumberIsInvalid("123456789012345");
    }

    public function testNumberCanNotBeBiggerThan16Digits(){
        $this->assertNumberIsInvalid("12345678901234567");
    }

    public function testNumberMustContainDigitsOnly(){
        $this->assertNumberIsInvalid("123456789012345A");
    }

    public function testNumberCanNotContainDot(){
        $this->assertNumberIsInvalid("1.34567890123456");
    }

    public function testNameCanNotBeEmpty(){
        $this->assertCanNotCreateCreditCard(
            $this->validCreditCardNumber(), "", CreditCard::INVALID_NAME);
    }

    public function assertNumberIsInvalid($number): void
    {
        $this->assertCanNotCreateCreditCard(
            $number,"Pepe Sanchez",CreditCard::INVALID_NUMBER);
    }

    public function assertCanNotCreateCreditCard($number, $name, $errorMessage): void
    {
        try {
            new CreditCard($number, $name, MonthOfYear::from(new DateTime()));
            $this->fail();
        } catch (InvalidArgumentException $error) {
            $this->assertEquals($errorMessage, $error->getMessage());
        }
    }

    public function testNumberIsKept(){
        $creditCard = new CreditCard($this->validCreditCardNumber(),"Name",MonthOfYear::from(new DateTime()));
        $this->assertEquals($this->validCreditCardNumber(),$creditCard->number());
    }

    public function testNameIsKept(){
        $name = "Name";
        $creditCard = new CreditCard($this->validCreditCardNumber(), $name,MonthOfYear::from(new DateTime()));
        $this->assertEquals($name,$creditCard->name());
    }

    /**
     * @return string
     */
    private function validCreditCardNumber(): string
    {
        return "1234567890123456";
    }
}
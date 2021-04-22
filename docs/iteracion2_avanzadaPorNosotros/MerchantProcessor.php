<?php


interface MerchantProcessor
{

    const STOLEN_CREDIT_CARD = "Stolen credit card";
    const CREDIT_CARD_WITHOUT_CREDIT = "Credit card without credit";

    function debit($total,$creditCard);
}
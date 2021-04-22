<?php


interface AuthenticationSystem
{
    public function doesAuthenticate($clientId, $password);
}
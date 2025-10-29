<?php

namespace iutnc\deefy\exception;

class AuthException extends \Exception{
    public function __construct(string $message){
        parent::__construct($message);
    }
}
<?php

namespace iutnc\deefy\exception;

class InvalidPropertyValueException extends \Exception{
    public function __construct(string $message){
        parent::__construct($message);
    }
}
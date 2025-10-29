<?php

namespace iutnc\deefy\exception;

class InvalidProprietyException extends \Exception {
    public function __construct(string $message){
        parent::__construct($message);
    }
}
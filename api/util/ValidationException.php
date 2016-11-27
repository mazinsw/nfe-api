<?php

class ValidationException extends Exception 
{
    private $errors = array();

    public function __construct($errors = array()) { 
        $this->errors = $errors;
        reset($errors);
        parent::__construct(current($errors));
    } 

    public function getErrors() {
        return $this->errors; 
    } 
}
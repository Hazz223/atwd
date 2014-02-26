<?php

/**
 * Description of FieldNotFoundException
 * 
 * @author hlp2-winser
 */
class FieldNotFoundException extends Exception{
    
    public function __construct($message) {
        
        parent::__construct($message, 404);
    }
    
    public function __toString() {
        return "[".$this->code."]: ".$this->message;
    }
}

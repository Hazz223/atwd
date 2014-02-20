<?php

class InvalidCrimeData extends Exception{
    
    public function __construct($message) {
        
        parent::__construct($message, 602);
    }
    
    public function __toString() {
        return "[".$this->code."]: ".$this->message;
    }
}
?>

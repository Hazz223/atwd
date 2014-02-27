<?php

/**
 * Description of AreaAlreadyExists
 * 
 * @author hlp2-winser
 */
class AreaAlreadyExists extends Exception{
    
    public function __construct($message) {
        
        parent::__construct($message, 603);
    }
    
    public function __toString() {
        return "[".$this->code."]: ".$this->message;
    }
}

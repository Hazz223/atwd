<?php

/**
 * Description of XMLDataNotFound
 * 
 * @author hlp2-winser
 */

class XMLDataNotFound extends Exception{
    
    public function __construct($message) {
        
        parent::__construct($message, 601);
    }
    
    public function __toString() {
        return "[".$this->code."]: ".$this->message;
    }
}
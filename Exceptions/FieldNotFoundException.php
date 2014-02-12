<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FieldNotFound
 *
 * @author Harry
 */
class FieldNotFoundException extends Exception{
    
    public function __construct($message) {
        
        parent::__construct($message, 404);
    }
    
    public function __toString() {
        return "[".$this->code."]: ".$this->message;
    }
}

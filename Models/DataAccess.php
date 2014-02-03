<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DataAccess
 *
 * @author Harry
 */
class DataAccess {
    private $xmlFile = "../Data/CrimeStats.xml";
    private $xml;

    function __construct() {
        $this->xml = new DOMDocument();
        $this->xml->load($this->xmlFile);
    }

    public function getCrimeXML() {
        return $this->xml;
    }
    
    public function saveData($xml){
        $xml->save("../Data/CrimeStats.xml");
    }
}

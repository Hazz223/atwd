<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of XmlAccess
 *
 * @author Harry
 */
class XmlAccess {
    
    const xmlFile = "../Data/CrimeStats.xml";
    private $xml;
    
    private function __construct() {
        // New dom document. 
        $this->xml = new DOMDocument();
        $this->xml->load(XmlAccess::xmlFile);
    }
}

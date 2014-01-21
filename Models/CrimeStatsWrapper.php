<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CrimeStatsWrapper
 *
 * @author Harry
 */
require_once '../Entities/Region.php';
require_once '../Entities/County.php';

class CrimeStatsWrapper {

    // We will be accessing the XML here, and then calling functions on this to get the information. 

    private $xmlFile = "../Data/CrimeStats.xml";
    private $xml;

    function __construct() {
        $this->xml = new DOMDocument();
        $this->xml->load($this->xmlFile);
    }

    public function getRegionByName() {
        // Locates a region from within the xml file, and creates the region object, and then passes this information back 
        // Need to watch out for title list and stuff too.
        
    }

}

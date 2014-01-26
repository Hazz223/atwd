<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Counties
 *
 * @author Harry
 */

require_once 'CrimeStatsWrapper.php';
require_once '../Entities/County.php';

class Counties {
    
    private $xml, $crimeData;

    function __construct() {
        $this->crimeData = new CrimeStatsWrapper();
        $this->xml = $this->crimeData->getCrimeXML(); // sets the 'database'        
    }
    
    public function GetCountyByName($name){
        // Returns a county by name
    }
}

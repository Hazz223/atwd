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

    public function GetCountyByName($name) {  
        
        $xpathDoc = new DOMXPath($this->xml);
        
        //http://stackoverflow.com/questions/3443701/domdocument-need-to-search-for-an-element-that-has-attribute-class-something
        foreach ($xpathDoc->query('//County[contains(@Name, \''.$name.'\')]') as $county) {
            $newCounty = new County();
            $newCounty->setName($name);
            
            $crimeArray = array();
            $crimes = $county->getElementsByTagName("Crime");
            foreach($crimes as $crime){
                $crimeArray[$crime->getAttribute("Type")] = $crime->textContent;
            }
            $newCounty->setCrimeList($crimeArray);
            
            $parent = $county->parentNode;
            
            $newCounty->setParentRegionName($parent->getAttribute("Name"));
            
            
            return $newCounty;
        }
    }

}

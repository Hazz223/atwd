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
require_once '../Entities/Area.php';
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
        // this will return a region object, with a bunch of info in it.
    }

    public function getTotalsForAll() {
        
    }

    public function getTotalsForAllAreas() {
        
    }

    public function getTotalsForAllRegions() {
        // This will build a sod off big object. Don't know if I'm happy about that...
        $areaObjectArray = array();
        $areas = $this->xml->getElementsByTagName("Area");

        foreach ($areas as $area) {
            $areaObj = new Area();
            $areaObj->setName($area->getAttribute("Name"));
            
            $totals = $area->getElementsByTagName("Totals");
            // Total for each area
            foreach ($totals as $total) { // should only be one of these anyway
                $areaTotals = $total->getElementsByTagName("AreaTotal");
                $stats = $this->GetCrimeStats($areaTotals);
                $areaObj->setTotals($stats);
            }
            $areaObjectArray[] = $areaObj;
        }
        
        return $areaObjectArray;
    }

    private function GetCrimeStats($parentNode) {
        
        $dataArray = array();
        
        foreach ($parentNode as $child) {
            $dataArray[$child->getAttribute("Name")] = $child->textContent;
        }
        
        return $dataArray;
    }
    
   

}

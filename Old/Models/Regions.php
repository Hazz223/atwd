<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Regions
 *
 * @author Harry
 */
require_once 'CrimeStatsWrapper.php';
require_once '../Entities/Region.php';

class Regions {

    private $xml, $crimeData;

    function __construct() {
        $this->crimeData = new CrimeStatsWrapper();
        $this->xml = $this->crimeData->getCrimeXML(); // sets the 'database'        
    }

    public function GetRegions() {
        $regionObjectArray = array();
        $regions = $this->xml->getElementsByTagName("Region");

        foreach ($regions as $region) {
            $regionObj = new Region();
            $regionObj->setName($region->getAttribute("Name"));

            $totals = $region->getElementsByTagName("RegionTotals");
            $totalArray = array();
            foreach ($totals as $total) {
                $crimeTotals = $total->getElementsByTagName("Crime");

                foreach ($crimeTotals as $cTotal) {
                    $totalArray[$cTotal->getAttribute("Type")] = $cTotal->textContent;
                }
            }
            $regionObj->setTotals($totalArray);
            
            $counties = $region->getElementsByTagName("County");

            foreach ($counties as $county) {
                $countyName = $county->getAttribute("Name");
                $regionObj->addCountyName($countyName);
            }

            $regionObjectArray[] = $regionObj;
        }

        return $regionObjectArray;
    }

    public function GetRegionByName($name) {
        $regions = $this->xml->getElementsByTagName("Region");

        foreach ($regions as $region) {
            $regionName = $region->getAttribute("Name");

            if (strtolower($regionName) == strtolower($name)) {
                return $this->_createRegionOnNode($region, $regionName);
            }
        }
    }

    public function addNewRegion($name) {
        //Adds a new area. - includes the actual xml access
        // return boolean yay or nay. 
    }

    public function UpdateRegion($name) {
        // Updates an Area- the actual xml. 
        // returns a boolean for yay or nay
    }

    private function _createRegionOnNode($node, $name) {
        // So we have the singular node

        $newRegion = new Region();
        $newRegion->setName($name);

        $counties = $node->getElementsByTagName("County");

        foreach ($counties as $county) {
            $newRegion->addCountyName($county->getAttribute("Name"));
        }

        // Get the titles
        $totals = $node->getElementsByTagName("RegionTotals");
        $totalArray = array();
        foreach ($totals as $total) {
            $crimeTotals = $total->getElementsByTagName("Crime");

            foreach ($crimeTotals as $cTotal) {
                $totalArray[$cTotal->getAttribute("Type")] = $cTotal->textContent;
            }
        }
        $newRegion->setTotals($totalArray);

        return $newRegion;
    }

}

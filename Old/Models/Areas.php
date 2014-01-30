<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Areas
 *
 * @author Harry
 */
require_once 'CrimeStatsWrapper.php';
require_once '../Entities/Area.php';

class Areas { // apprently Areas isn't found??? WhaT THE FUCK>!

    private $xml, $crimeData;

    function __construct() {
        $this->crimeData = new CrimeStatsWrapper();
        $this->xml = $this->crimeData->getCrimeXML(); // sets the 'database'        
    }

    public function GetAreas() {
        $areaObjectArray = array();
        $areas = $this->xml->getElementsByTagName("Area");

        foreach ($areas as $area) {
            $areaObj = new Area();
            $areaObj->setName($area->getAttribute("Name"));

            $totals = $area->getElementsByTagName("Totals");
            // Total for each area
            foreach ($totals as $total) {
                $areaTotals = $total->getElementsByTagName("AreaTotal");
                $stats = $this->crimeData->GetCrimeStats($areaTotals); // that returns an array
                $areaObj->setTotals($stats);
            }

            $regions = $area->getElementsByTagName("Region");

            foreach ($regions as $region) {
                $regionName = $region->getAttribute("Name");
                $areaObj->addRegionName($regionName);
            }

            $areaObjectArray[] = $areaObj;
        }

        return $areaObjectArray;
    }

    public function GetAreaByName($name) {
        $areas = $this->xml->getElementsByTagName("Area");

        foreach ($areas as $areaNode) {
            $areaName = $areaNode->getAttribute("Name");

            if ($areaName == $name) {
                return $this->_createAreaOnNode($areaNode, $name);
            }
        }
        
        return "false";
        
    }

    public function addNewArea($name) {
        //Adds a new area. - includes the actual xml access
        // return boolean yay or nay. 
    }

    public function UpdateArea($name) {
        // Updates an Area- the actual xml. 
        // returns a boolean for yay or nay
    }

    private function _createAreaOnNode($node, $name) {
        $newArea = new Area();
        $newArea->setName($name);

        // Create region name list
        $regions = $node->getElementsByTagName("Region");

        foreach ($regions as $region) {
            $newArea->addRegionName($region->getAttribute("Name"));
        }

        // Get the titles
        $totals = $node->getElementsByTagName("Totals");
        $totalArray = array();
        
        foreach ($totals as $total) {
            $areaTotals = $total->getElementsByTagName("AreaTotal");
            $totalArray = $this->crimeData->GetCrimeStats($areaTotals); // that returns an array
        }

        $newArea->setTotals($totalArray);

        return $newArea;
    }

}

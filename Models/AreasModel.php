<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AreasModel
 *
 * @author Harry
 */
require_once 'DataAccess.php';
require_once '../Entities/Area.php';

class AreasModel {

    private $xml, $dataAccess;

    function __construct() {
        $this->dataAccess = new DataAccess();
        $this->xml = $this->dataAccess->getCrimeXML(); // gives me access to the xml
    }

    public function getAllAreas() {
        
    }

    public function getAreaByName($name) {
        $xpath = new DOMXpath($this->xml);
        $area = $xpath->query("Country/Region/area [@name='" . $name . "']")->item(0); // This is VERY ridgid. Doesdn't work with the wrong capitals etc.

        $newArea = new Area();

        $newArea->setName($area->getAttribute("name"));

        $crimes = $area->getElementsByTagName("Crime");
        $crimeDataArray = array();

        foreach ($crimes as $crime) {
            $crimeDataArray[$crime->getAttribute("name")] = $crime->textContent;
        }

        $totalNode = $xpath->query("CrimeType/CrimeCatagory [@name='Total recorded crime - including fraud']", $area)->item(0);

        $newArea->setTotal($totalNode->getAttribute("total"));
        $newArea->setCrimeData($crimeDataArray);

        return $newArea;
    }

    public function UpdateAreaTotal($name, $value) {

        $oldRegion = $this->getAreaByName($name);

        $xpath = new DOMXpath($this->xml);
        $area = $xpath->query("Country/Region/area [@name='" . $name . "']")->item(0);
        

        $totalNode = $xpath->query("CrimeType/CrimeCatagory [@name='Total recorded crime - including fraud']", $area);
        $totalNode->item(0)->setAttribute("total", $value);
        $this->dataAccess->saveData($this->xml);
        
        $newRegion = $this->getAreaByName($name);
        
        return array($oldRegion, $newRegion);
    }

}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RegionsModel
 *
 * @author Harry
 */
require_once 'DataAccess.php';
require_once '../Entities/Region.php';

class RegionsModel {

    private $xml, $dataAccess;

    function __construct() {
//        $this->dataAccess = new DataAccess();
//        $this->xml = $this->dataAccess->getCrimeXML(); // gives me access to the xml
    }

    public function getAllRegions() {
        //Get elements by county, then create a region object. The county can then be fed to it. 
        $newRegionList = array();

        $countries = DataAccess::GetInstance()->getCrimeXML()->getElementsByTagName("Country");

        foreach ($countries as $country) { // somehow this leaves out wales!
            $countryName = $country->getAttribute("name");
            $regions = $country->getElementsByTagName("Region");

            foreach ($regions as $region) {
                $newRegion = new Region();
                $regionName = $region->getAttribute("name");

                $regionName = str_replace("Region", "", $regionName);

                $newRegion->setName($regionName);
                $newRegion->setCountry($countryName);
                $newRegion->setAreaNames($this->_populateAreaNames($region));
                $newRegion->setTotal($this->_getEnglishRegionTotal($region));

                $newRegionList[] = $newRegion;
            }
        }

        return $newRegionList;
    }

    public function getRegionByName($name) {

        $region = $this->_getRegionNodeByName($name);
        $regionObj = $this->_createRegionObject($region);
        return $regionObj;
    }

    public function addAreaToRegion(Area $areaObj) {
        if (!$this->_areaExists($areaObj->getName())) {
            $newAreaNode = DataAccess::GetInstance()->getCrimeXML()->createElement("area");
            $newAreaNode->setAttribute("name", $areaObj->getName());

            $regionNode = $this->_getRegionNodeByName($areaObj->getRegionName());

            $regionNode->appendChild($newAreaNode);

            DataAccess::GetInstance()->saveXML();
        }
    }

    public function isRegion($name) {
        $cleanedName = str_replace("_", " ", $name);
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $regionNode = $xpath->query("Country/Region [@name='" . $cleanedName . "']")->item(0);

        return isset($regionNode);
    }

    public function getRegionsByCountry($countryName) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $countryNode = $xpath->query("Country [@name='" . $countryName . "']")->item(0);

        // need to return a list of region objects that are associated with this country!
        
        $regionNodeList = $countryNode->getElementsByTagName("Region");
        
        $regionObjList = array();
        
        foreach($regionNodeList as $regionNode){
            $regionObjList[] = $this->_createRegionObject($regionNode);
        }
        return $regionObjList;
    }

    private function _areaExists($name) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $exists = $xpath->query("Country/Region/area [@name='" . $name . "']")->item(0);
        return isset($exists);
    }

    private function _getEnglishRegionTotal($region) {

        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $areas = $region->getElementsByTagName("area");

        $regionTotal = 0;
        foreach ($areas as $area) {
            $totalNode = $xpath->query("CrimeCatagory [@name='Total recorded crime - including fraud']", $area)->item(0);

            $regionTotal = $regionTotal + intval($totalNode->getAttribute("total"));
        }

        return $regionTotal;
    }

    private function _getWalesRegionTotal($region) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $totalNode = $xpath->query("CrimeType/CrimeCatagory [@name='Total recorded crime - including fraud']", $region)->item(0);
        return intval($totalNode->getAttribute("total"));
    }

    private function _getRegionTotal(DOMNode $region) {
        $areas = $region->getElementsByTagName("area");

        $regionTotal = 0;
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        foreach ($areas as $area) {
            $totalNode = $xpath->query("CrimeCatagory [@name='Total recorded crime - including fraud']", $area)->item(0);

            $regionTotal = $regionTotal + intval($totalNode->getAttribute("total"));
        }

        return $regionTotal;
    }

    private function _populateAreaNames($region) {
        $areaNames = array();
        $areas = $region->getElementsByTagName("area");

        foreach ($areas as $area) {

            $areaNames[] = $area->getAttribute("name");
        }

        return $areaNames;
    }

    public function _getRegionNodeByName($regionName) {
        $name = str_replace("_", " ", $regionName);
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $regionNode = $xpath->query("Country/Region [@name='" . $name . "']")->item(0);
        return $regionNode;
    }

    private function _createCrimeNodes($crimeData, $newCrimeCatNode) {
        if (isset($crimeData)) {
            foreach ($crimeData as $crime) {
                $newCrimeNode = DataAccess::GetInstance()->getCrimeXML()->createElement("Crime");
                $newCrimeNode->setAttribute("name", $crime->getName());
                $text = DataAccess::GetInstance()->getCrimeXML()->createTextNode($crime->getValue());
                $newCrimeNode->appendChild($text);

                $newCrimeCatNode->appendChild($newCrimeNode);
            }
        }

        return $newCrimeCatNode;
    }

    private function _createRegionObject(DOMNode $regionNode) {
        $newRegion = new Region();
        $newRegion->setName($regionNode->getAttribute("name"));

        $countryName = $regionNode->parentNode->getAttribute("name"); 
        $newRegion->setCountry($countryName);

        $areas = $regionNode->getElementsByTagName("area");

        foreach ($areas as $area) {
            $newRegion->addAreaName($area->getAttribute("name"));
        }

//        if ($countryName === "ENGLAND") {
//            $newRegion->setTotal($this->_getEnglishRegionTotal($regionNode));
//        } else {
//            $newRegion->setTotal($this->_getWalesRegionTotal($regionNode));
//        }
        
        $newRegion->setTotal($this->_getRegionTotal($regionNode));
        
        return $newRegion;
    }

}

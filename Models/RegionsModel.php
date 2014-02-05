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
        $this->dataAccess = new DataAccess();
        $this->xml = $this->dataAccess->getCrimeXML(); // gives me access to the xml
    }

    public function getAllRegions() {
        //Get elements by county, then create a region object. The county can then be fed to it. 
        $newRegionList = array();

        $countries = $this->xml->getElementsByTagName("Country");

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
        // need to clean up the name.
        $name = str_replace("_", " ", $name);

        $xpath = new DOMXpath($this->xml);
        $region = $xpath->query("Country/Region [@name='" . $name . "']")->item(0); // this screws up on Welsh

        $newRegion = new Region();
        $countryName = $region->parentNode->getAttribute("name");
        $newRegion->setCountry($countryName);
        $newRegion->setName($region->getAttribute("name"));

        $areas = $region->getElementsByTagName("area");

        foreach ($areas as $area) {
            $newRegion->addAreaName($area->getAttribute("name"));
        }

        if ($countryName === "ENGLAND") {
            $newRegion->setTotal($this->_getEnglishRegionTotal($region));
        } else {
            $newRegion->setTotal($this->_getWalesRegionTotal($region));
        }

        return $newRegion;
    }

    private function _getEnglishRegionTotal($region) {
        $xpath = new DOMXpath($this->xml);
        $areas = $region->getElementsByTagName("area");

        $regionTotal = 0;
        foreach ($areas as $area) {
            $totalNode = $xpath->query("CrimeType/CrimeCatagory [@name='Total recorded crime - including fraud']", $area)->item(0);

            $regionTotal = $regionTotal + intval($totalNode->getAttribute("total"));
        }

        return $regionTotal;
    }

    private function _getWalesRegionTotal($region) {
        $xpath = new DOMXpath($this->xml);
        $totalNode = $xpath->query("CrimeType/CrimeCatagory [@name='Total recorded crime - including fraud']", $region)->item(0);
        return intval($totalNode->getAttribute("total"));
    }

    private function _populateAreaNames($region) {
        $areaNames = array();
        $areas = $region->getElementsByTagName("area");

        foreach ($areas as $area) {

            $areaNames[] = $area->getAttribute("name");
        }

        return $areaNames;
    }

}

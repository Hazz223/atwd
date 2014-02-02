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

    private $xml;

    function __construct() {
        $dataAccess = new DataAccess();
        $this->xml = $dataAccess->getCrimeXML(); // gives me access to the xml
    }

    public function getAllRegions() {
        //Get elements by county, then create a region object. The county can then be fed to it. 
        $newRegionList = array();

        $countries = $this->xml->getElementsByTagName("Country");

        foreach ($countries as $country) { // somehow this leaves out wales!
            $countryName = $country->getAttribute("name");
            $regions = $country->getElementsByTagName("Region");
            // Need to do a check for wales, due to the different way I've built it. I blame the CSV

            if ($countryName === "WALES") {
                foreach ($regions as $region) {
                    $newRegion = new Region();
                    $regionName = $region->getAttribute("name");
                    $newRegion->setName($regionName);
                    $newRegion->setCountry($countryName);
                    
                    $newRegion->setTotal($this->_getWalesRegionTotal($region));
                    
                    
                    $newRegionList[] = $newRegion;
                }
                
            } else {
                foreach ($regions as $region) {
                    $newRegion = new Region();
                    $regionName = $region->getAttribute("name");
                    
                    $regionName = str_replace("Region", "", $regionName);
                    
                    $newRegion->setName($regionName);
                    $newRegion->setCountry($countryName); // set country
                    //$totalNode = $xpath->query("area//CrimeCatagory [@name='Total recorded crime - including fraud']", $region); // this now works! Huzzar!
                    $newRegion->setAreaNames($this->_populateAreaNames($region));
                    $newRegion->setTotal($this->_getEnglishRegionTotal($region));

                    $newRegionList[] = $newRegion;
                }
            }
        }

        return $newRegionList;
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
    
    private function _getWalesRegionTotal($region){
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

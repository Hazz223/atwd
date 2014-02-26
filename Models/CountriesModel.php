<?php

/**
 * Description of CountriesModel
 * Model that allows access to Country information from the XML data
 * Mostly returns Country objects
 * 
 * @author hlp2-winser
 */

require_once 'DataAccess.php';
require_once 'RegionsModel.php';
require_once '../Entities/Country.php';

class CountriesModel {
    // Returns an array of all Countries in the data
    // Each item in the array is a country object
    function getAllCountries(){
        $regionModel = new RegionsModel();
        
        $allRegions = $regionModel->GetAllRegions();
        $countryList = array();

        $countries = DataAccess::GetInstance()->getCrimeXML()->getElementsByTagName("Country");
        
        foreach($countries as $country){
            $newCountry = new Country();
            
            $countryName = $country->getAttribute("name");
            $newCountry->setName($countryName);
            
            $total = 0;
            $regionNames = array();
            // Store region names, which is lighter than 
            // storing multiple region objects.
            // Region names can be used with the Region model to actually get
            // There full information
            foreach($allRegions as $region){
                if($region->getCountry() === $countryName ){
                    $total = $total + $region->getTotal();
                    $regionNames[] = $region->getName();
                }
            }
           
            $newCountry->setTotal($total);
            $newCountry->setRegionNames($regionNames);
            $countryList[] = $newCountry;
        }
        
        return $countryList;
    }
   // Get a country by the name given
    public function getCountryByName($countryName){
        $regionModel = new RegionsModel();
        
        $countryNode = $this->_getCountryNode($countryName);
        
        $countryObj = new Country();
        $countryObj->setName($countryNode->getAttribute("name"));
        $countryObj->setProperName($countryNode->getAttribute("proper_name"));
      
        
        $regions = $regionModel->getRegionsByCountry($countryObj->getName());
        
        $regionNamesList = array();
        $countryTotal = 0;
        foreach($regions as $region){
            $regionNamesList[] = $region->getName();
            $countryTotal = $countryTotal + $region->getTotal();
        }

        $countryObj->setRegionNames($regionNamesList);
        $countryObj->setTotal($countryTotal);
        
        return $countryObj;
    }
    
    //Quick function to check if the country name given is actually a country
    public function isCountry($name){
        $countryNode = $this->_getCountryNode($name);
        return isset($countryNode);
    }
    
    //Gets a country node based on the name given.
    private function _getCountryNode($name){
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        return $xpath->query("Country [@name='" . $name . "']")->item(0);
    }
}

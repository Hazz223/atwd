<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'DataAccess.php';
require_once 'RegionsModel.php';
require_once '../Entities/Country.php';

class CountriesModel {
    
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
            foreach($allRegions as $region){
                if($region->getCountry() === $countryName ){
                    $total = $total + $region->getTotal(); // should get the total for the country
                    $regionNames[] = $region->getName();
                }
            }
           
            $newCountry->setTotal($total);
            $newCountry->setRegionNames($regionNames);
            $countryList[] = $newCountry;
        }
        
        return $countryList;
    }
   
    public function getCountryByName($countryName, $regionList){
        $countryNode = $this->_getCountryNode($countryName);
                
        $countryObj = new Country();
        $countryObj->setName($countryNode->getAttribute("name"));
       

        $regionNamesList = array();
        $countryTotal = 0;
        foreach($regionList as $regionObj){
            $regionNamesList[] = $regionObj->getName();
            $countryTotal = $regionObj->getTotal();
        }
        
        $countryObj->setRegionNames($regionNamesList);
        $countryObj->setTotal($countryTotal);
        
        return $countryObj;
    }
    
    public function isCountry($name){
        $countryNode = $this->_getCountryNode($name);
        return isset($countryNode);
    }
    
    private function _getCountryNode($name){
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        return $xpath->query("Country [@name='" . $name . "']")->item(0);
    }
}

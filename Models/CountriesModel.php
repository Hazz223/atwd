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
    private $xml;

    function __construct() {
        $dataAccess = new DataAccess();
        $this->xml = $dataAccess->getCrimeXML(); // gives me access to the xml
    }
    
    function getAllCounties(){
        $regionModel = new RegionsModel();
        
        $allRegions = $regionModel->GetAllRegions();
        $countryList = array();

        $countries = $this->xml->getElementsByTagName("Country");
        
        foreach($countries as $country){
            $newCountry = new Country();
            
            $countryName = $country->getAttribute("name");
            $newCountry->setName($countryName);
            
            // I could get all regions, and then split them based on if their parent is England or Wales...? 
            
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
}

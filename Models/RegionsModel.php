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

        foreach ($countries as $country) {

            $regions = $country->getElementsByTagName("Region");
            $countryName = $country->getAttribute("name");

            foreach ($regions as $region) {
                $newRegion = new Region();

                $regionName = $region->getAttribute("name");
                $newRegion->setName($regionName);
                $newRegion->setCountry($countryName); // set country


                
                //$totalNode = $xpath->query("area//CrimeCatagory [@name='Total recorded crime - including fraud']", $region); // this now works! Huzzar!
                // What i need to do is access this data per area. Then add it up really. 
                
                $total = 0;
                
                $xpath = new DOMXpath($this->xml);
                $areas = $region->getElementsByTagName("area"); // area no areas getting through?

                foreach ($areas as $area) {
                    
                    echo $area->getAttribute("name"); // is finding areas
                    
                    $totalNode = $xpath->query("//@name='Total recorded crime - including fraud'");
                    
                    var_dump($totalNode->item(0));
                    
                    foreach($totalNode as $tn) {
                        
                        echo "test";
//                        $total + $tn->getAttribute("total"); // this ruins the whole bloody thing... lame!
//                        //$total = $total + 1;
//                        break;
                    }
                }






                $newRegion->setTotal($total);





                $newRegionList[] = $newRegion;
            }
        }

        return $newRegionList;
    }

}

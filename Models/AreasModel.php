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
require_once '../Entities/Crime.php';
require_once '../Entities/CrimeCatagory.php';

class AreasModel {

    private $xml, $dataAccess;

    function __construct() {
        $this->dataAccess = new DataAccess();
        $this->xml = $this->dataAccess->getCrimeXML(); // gives me access to the xml
    }

    public function getAreaByName($name) {
        $area = $this->_getAreaNode($name);

        $newArea = new Area();
        $newArea->setName($area->getAttribute("name"));
        $newArea->setRegionName($area->parentNode->getAttribute("name"));
        
        $crimeCategories = $area->getElementsByTagName("CrimeCatagory"); 
        $crimeDataArray = array();

        foreach ($crimeCategories as $crimeCat) {
            $crimeDataArray[] = $this->_createCrimeCatagoryObject($crimeCat);
        }

        $totalNode = $xpath->query("CrimeType/CrimeCatagory [@name='Total recorded crime - including fraud']", $area)->item(0);

        $newArea->setTotal($totalNode->getAttribute("total"));
        $newArea->setCrimeData($crimeDataArray);

        return $newArea;
    }
    
    public function addCrimeCatagories(CrimeCatagory $crimeCat,  $areaName){
        $areaNode = $this->_getAreaNode($areaName);
        // need to check if the catagory already exits.
        
        // Basically need to create the catagory here, so that we don't need to worry about it in the area spot.
        // Private function for totals i think!
        
    }

    public function addCrimeToArea(Crime $crime, $areaName){

        $areaNode = $this->_getAreaNode($areaName);
        
        // now need to check for a catagory and if it exists
        $catNode = $xpath->query("CrimeType/CrimeCatagory [@name='".$crime->getCrimeCatagory()."']", $areaNode)->item(0);
        if(isset($catNode)){
            // Means there's already a node of this type! Woo!
            $newCrimeNode = $this->xml->createElement("crime");
            $newCrimeNode->setAttribute("name", $crime->getName());
            $textNode = $this->xml->createTextNode($crime->getValue());
            $newCrimeNode->appendChild($textNode);
            $catNode->appendChild($newCrimeNode);  
        }
        
        // at the end, we need to update the totals. If they don't exist, make them.
        $this->dataAccess->saveData($this->xml);
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

    private function _createCrimeCatagoryObject($node) {
        
        $crimeCatObj = new CrimeCatagory();
        
        $crimeCatName = $node->getAttribute("name");
        $crimeCatObj->setName($node->getAttribute("name"));
        $crimeCatObj->setTotal($node->getAttribute("total"));
        $crimeCatObj->setCrimeType($node->getAttribute("type"));

        if ($node->hasChildNodes()) {
            $crimeArray = array();
            $crimes = $node->childNodes;
            foreach ($crimes as $crime) {
                $crimeObj = new Crime();
                $crimeObj->setName($crime->getAttribute("name"));
                $crimeObj->setValue($crime->textContent);
                $crimeObj->setCrimeCatagory($crimeCatName);
                $crimeArray[] = $crimeObj;
            }
            $crimeCatObj->setCrimeList($crimeArray);
        }
        
        return $crimeCatObj;
    }
    
    private function _getAreaNode($name){
        $xpath = new DOMXpath($this->xml);
        return $xpath->query("Country/Region/area [@name='" . $name . "']")->item(0);
    }
}

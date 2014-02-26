<?php

/**
 * Description of RegionsModel
 * Model for accessing Region data from the data xml.
 *
 * @author hlp2-winser
 */

require_once 'DataAccess.php';
require_once '../Entities/Region.php';
require_once '../Exceptions/FieldNotFoundException.php';

class RegionsModel {

    //Get elements by county, then create a region object. The county can then be fed to it. 
    public function getAllRegions() {
        $newRegionList = array();

        $countries = DataAccess::GetInstance()->getCrimeXML()->getElementsByTagName("Country");

        foreach ($countries as $country) {
            
            $regions = $country->getElementsByTagName("Region");
            foreach ($regions as $region) {
                $newRegionList[] = $this->_createRegionObject($region);
            }
        }

        return $newRegionList;
    }

    // Gets a region by the name given.
    public function getRegionByName($name) {
        $region = $this->_getRegionNode($name);
       
        if (isset($region)) {
            $regionObj = $this->_createRegionObject($region);
            return $regionObj;
        } else {
            throw new FieldNotFoundException("Could not find region: ".$name, 404);
        }
    }

    // Adds an area to a region, by giving it an area object
    // Area object knows what it's region is
    public function addAreaToRegion(Area $areaObj) {
        if (!$this->_areaExists($areaObj->getName())) { // create a whole new area
            $newAreaNode = DataAccess::GetInstance()->getCrimeXML()->createElement("Area");
            $newAreaNode->setAttribute("name", $areaObj->getName());
            $newAreaNode->setAttribute("proper_name", $areaObj->getProperName());

            $regionNode = $this->_getRegionNode($areaObj->getRegionName());

            $regionNode->appendChild($newAreaNode);

            DataAccess::GetInstance()->saveXML();
        }else{
            // Need to throw an error here really!
        }
    }

    // quick check to see if the region exists
    public function isRegion($name) {
        $cleanedName = str_replace(" ", "_", $name);
        $cleanedName = strtolower($cleanedName);
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $regionNode = $xpath->query("Country/Region [@name='" . $cleanedName . "']")->item(0);
        return isset($regionNode);
    }

    // Gets a list of region objects based on the country name
    public function getRegionsByCountry($countryName) {
        try{
            $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
            $countryNode = $xpath->query("Country [@name='" . $countryName . "']")->item(0);
        } catch (FieldNotFoundException $ex) {
            throw new FieldNotFoundException("Failed to find country with name: ".$countyName);
        }
        
        $regionNodeList = $countryNode->getElementsByTagName("Region");

        $regionObjList = array();

        foreach ($regionNodeList as $regionNode) {
            $regionObjList[] = $this->_createRegionObject($regionNode);
        }
        return $regionObjList;
    }
    
    // Deletes a region based on a name given
    public function DeleteRegion($name){
        $regionNode = $this->_getRegionNode($name);
        $parent = $regionNode->parentNode;
        $parent->removeChild($regionNode);
        DataAccess::GetInstance()->saveXML();
    }

    // quick check to see if an area already exists
    private function _areaExists($name) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $exists = $xpath->query("Country/Region/area [@name='" . $name . "']")->item(0);
        return isset($exists);
    }
    
    // returns the total for a region
    private function _getRegionTotal(DOMNode $region) {
        $areas = $region->getElementsByTagName("Area");

        $regionTotal = 0;
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        // cycles through all of the area total nodes, and add them up to create the region total
        foreach ($areas as $area) {
            $totalNode = $xpath->query("CrimeCatagory [@name='Total recorded crime - including fraud']", $area)->item(0);

            $regionTotal = $regionTotal + intval($totalNode->getAttribute("total")); // not working?
        }

        return $regionTotal;
    }

    // Gets a region node based on a name.
    public function _getRegionNode($regionName) {
        $name = str_replace(" ", "_", $regionName);
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $regionNode = $xpath->query("Country/Region [@name='" .strtolower($name). "']")->item(0);
        if(isset($regionNode)){
            return $regionNode;
        }
        else{
            throw new FieldNotFoundException("Region not found with name ". $regionName);
        }
    }

    // Populates a crime catagory node based on an array of crime objects given. 
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

    // Creates a region object from a node given. Populates the object from the 
    // Attributes of the node, as well as areas within.
    private function _createRegionObject(DOMNode $regionNode) {
        $newRegion = new Region();
        $newRegion->setName($regionNode->getAttribute("name"));
        $newRegion->setProperName($regionNode->getAttribute("proper_name"));
        
        $countryName = $regionNode->parentNode->getAttribute("name");
        $newRegion->setCountry($countryName);

        $areas = $regionNode->getElementsByTagName("Area");
        
        // Only attribute names, as storing the full area objects would be wasteful. 
        foreach ($areas as $area) {
            $newRegion->addAreaName($area->getAttribute("name"));
        }

        $newRegion->setTotal($this->_getRegionTotal($regionNode));

        return $newRegion;
    }
}

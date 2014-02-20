<?php

require_once 'DataAccess.php';
require_once '../Entities/Region.php';
require_once '../Exceptions/FieldNotFoundException.php';

class RegionsModel {

    public function getAllRegions() {
        //Get elements by county, then create a region object. The county can then be fed to it. 
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

    public function getRegionByName($name) {
        $region = $this->_getRegionNode($name);

        if (isset($region)) {
            $regionObj = $this->_createRegionObject($region);
            return $regionObj;
        } else {
            throw new FieldNotFoundException("Could not find region: ".$name, 404);
        }
    }

    public function addAreaToRegion(Area $areaObj) {
        if (!$this->_areaExists($areaObj->getName())) { // create a whole new area
            $newAreaNode = DataAccess::GetInstance()->getCrimeXML()->createElement("Area");
            $newAreaNode->setAttribute("name", $areaObj->getName());
            $newAreaNode->setAttribute("proper_name", $areaObj->getProperName());

            $regionNode = $this->_getRegionNode($areaObj->getRegionName());

            $regionNode->appendChild($newAreaNode);

            DataAccess::GetInstance()->saveXML();
        }
    }

    public function isRegion($name) {
        $cleanedName = str_replace(" ", "_", $name);
        $cleanedName = strtolower($cleanedName);
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $regionNode = $xpath->query("Country/Region [@name='" . $cleanedName . "']")->item(0);
        return isset($regionNode);
    }

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
    
    public function DeleteRegion($name){
        $regionNode = $this->_getRegionNode($name);
        $parent = $regionNode->parentNode;
        $parent->removeChild($regionNode);
        DataAccess::GetInstance()->saveXML();
    }

    private function _areaExists($name) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $exists = $xpath->query("Country/Region/area [@name='" . $name . "']")->item(0);
        return isset($exists);
    }

    private function _getRegionTotal(DOMNode $region) {
        $areas = $region->getElementsByTagName("Area");

        $regionTotal = 0;
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        foreach ($areas as $area) {
            $totalNode = $xpath->query("CrimeCatagory [@name='Total recorded crime - including fraud']", $area)->item(0);

            $regionTotal = $regionTotal + intval($totalNode->getAttribute("total")); // not working?
        }

        return $regionTotal;
    }

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
        $newRegion->setProperName($regionNode->getAttribute("proper_name"));
        
        $countryName = $regionNode->parentNode->getAttribute("name");
        $newRegion->setCountry($countryName);

        $areas = $regionNode->getElementsByTagName("Area");

        foreach ($areas as $area) {
            $newRegion->addAreaName($area->getAttribute("name"));
        }

        $newRegion->setTotal($this->_getRegionTotal($regionNode));

        return $newRegion;
    }
}

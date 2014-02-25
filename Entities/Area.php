<?php

/**
 * Description of Area
 * This class represents an Area in the data
 * It also stores crime data
 * 
 * @author hlp2-winser
 */

class Area {

    private $name, $total, $crimeData = array(), $regionName, $properName;

    public function getName() {
        return $this->name;
    }

    public function getTotal() {
        return intval($this->total);
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setTotal($total) {
        $this->total = $total;
    }
    
    public function getCrimeData() {
        return $this->crimeData;
    }

    public function setCrimeData($crimeData) {
        $this->crimeData = $crimeData;
    }
    
    public function getCrimeValueByName($name){
        return  $this->crimeData[$name];
    }
    
    public function getRegionName() {
        return $this->regionName;
    }

    public function setRegionName($regionName) {
        $this->regionName = $regionName;
    }
    
    public function getProperName() {
        return $this->properName;
    }

    public function setProperName($properName) {
        $this->properName = $properName;
    }


}

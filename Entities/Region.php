<?php

/**
 * Description of Region
 * This class represents a Region
 * It stores a list of Area names
 * 
 * @author hlp2-winser
 */


class Region {
    private $total, $name, $country, $areaNames = array(), $properName;
    
    public function getTotal() {
        return intval($this->total);
    }

    public function getName() {
        return $this->name;
    }

    public function getCountry() {
        return $this->country;
    }

    public function getAreaNames() {
        return $this->areaNames;
    }

    public function setTotal($total) {
        $this->total = $total;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setCountry($country) {
        $this->country = $country;
    }

    public function setAreaNames($areaNames) {
        $this->areaNames = $areaNames;
    }
    
    public function addAreaName($areaName){
        $this->areaNames[] = $areaName;
    }
    public function getProperName() {
        return $this->properName;
    }

    public function setProperName($properName) {
        $this->properName = $properName;
    }
}

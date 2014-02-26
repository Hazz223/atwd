<?php

/**
 * Description of Country
 * This class represents a Country in the data
 * It stores a list of Region Names, not their objects.
 * 
 * @author hlp2-winser
 */

class Country {
    private $name, $countryTotal, $regionNames = array(), $properName;
    
    public function getName() {
        return $this->name;
    }

    public function getTotal() {
        return $this->countryTotal;
    }

    public function getRegionNames() {
        return $this->regionNames;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setTotal($countryTotal) {
        $this->countryTotal = $countryTotal;
    }

    public function setRegionNames($regionNames) {
        $this->regionNames = $regionNames;
    }
    
    public function getCountryTotal() {
        return $this->countryTotal;
    }

    public function getProperName() {
        return $this->properName;
    }

    public function setCountryTotal($countryTotal) {
        $this->countryTotal = $countryTotal;
    }

    public function setProperName($properName) {
        $this->properName = $properName;
    }
}

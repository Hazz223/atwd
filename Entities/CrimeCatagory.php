<?php

/**
 * Description of CrimeCatagory
 * This class represents a Crime Catagory object. It stores a list of Crime objects
 * 
 * @author hlp2-winser
 */

class CrimeCatagory {
    private $name, $crimeType, $total, $crimeList = array();
    
    public function getName() {
        return $this->name;
    }

    public function getCrimeType() {
        return $this->crimeType;
    }

    public function getTotal() {
        return intval($this->total);
    }

    public function getCrimeList() {
        return $this->crimeList;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setCrimeType($crimeType) {
        $this->crimeType = $crimeType;
    }

    public function setTotal($total) {
        $this->total = $total;
    }

    public function setCrimeList($crimeList) {
        $this->crimeList = $crimeList;
    }


}

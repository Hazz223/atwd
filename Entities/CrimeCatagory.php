<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CrimeCatagory
 *
 * @author Harry
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
        return $this->total;
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

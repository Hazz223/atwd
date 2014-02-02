<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Country
 *
 * @author Harry
 */
class Country {
    private $name, $countryTotal, $regionNames = array();
    
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
}

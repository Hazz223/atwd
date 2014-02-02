<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Region
 *
 * @author Harry
 */
class Region {
    private $total, $name, $country, $areaNames = array();
    
    public function getTotal() {
        return $this->total;
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


}

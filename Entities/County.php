<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of County
 *
 * @author Harry
 */
class County {

    private $name, $crimeList = array(), $parentRegionName;

    public function getName() {
        return $this->name;
    }

    public function getCrimeList() {
        return $this->crimeList;
    }

    public function getParentRegionName() { // need to set this!
        return $this->parentRegionName;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setCrimeList($crimeList) {
        $this->crimeList = $crimeList;
    }

    public function setParentRegionName($parentRegion) {
        $this->parentRegionName = $parentRegion;
    }
    
    public function getCrimeStatByName($name){
        return $this->crimeList[$name];
    }

}

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
    private $name, $totals, $countyNames = array();
    
    public function getName() {
        return $this->name;
    }

    public function getTotals() {
        return $this->totals;
    }

    public function getCountyNames() {
        return $this->countyNames;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setTotals($totals) {
        $this->totals = $totals;
    }

    public function setCountyNames($countyNames) {
        $this->countyNames = $countyNames;
    }

    public function addCountyName($name){
        $this->countyNames[] = $name;
    }
    
    public function getCrimeStatByName($name){
        return $this->totals[$name];
        
    }
}

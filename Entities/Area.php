<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Area
 *
 * @author Harry
 */
class Area {

    // I need to be careful of updating this information...
    private $name, $totals, $regionNames = array();

    public function getName() {
        return $this->name;
    }

    public function getTotals() {
        return $this->totals;
    }

    public function getRegionNames() {
        return $this->regionNames;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setTotals($totals) {
        $this->totals = $totals;
    }

    public function setRegionNames($names) {
        $this->regionNames = $names;
    }

    public function addRegionName($name) {
        $this->regionNames[] = $name;
    }

    public function getCrimeStatByName($name) {
        foreach ($this->totals as $key => $value) {
            if ($key == $name) {
                return $value;
            }
        }
    }

}

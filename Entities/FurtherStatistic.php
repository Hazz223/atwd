<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FurtherStatistic
 *
 * @author Harry
 */


class FurtherStatistic {
    private $name, $total, $crimeData = array();
    
    public function getName() {
        return $this->name;
    }

    public function getTotal() {
        return $this->total;
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


}
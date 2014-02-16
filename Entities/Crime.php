<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Crime
 *
 * @author Harry
 */
class Crime {
    private $name, $crimeType,$crimeCatagory, $crimeCatagoryTotal, $value;
    
    public function getName() {
        return $this->name;
    }

    public function getCrimeType() {
        return $this->crimeType;
    }

    public function getCrimeCatagory() {
        return $this->crimeCatagory;
    }

    public function getCrimeCatagoryTotal() {
        return $this->crimeCatagoryTotal;
    }

    public function getValue() {
        return intval($this->value);
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setCrimeType($crimeType) {
        $this->crimeType = $crimeType;
    }

    public function setCrimeCatagory($crimeCatagory) {
        $this->crimeCatagory = $crimeCatagory;
    }

    public function setCrimeCatagoryTotal($crimeCatagoryTotal) {
        $this->crimeCatagoryTotal = $crimeCatagoryTotal;
    }

    public function setValue($value) {
        $this->value = $value;
    }


}

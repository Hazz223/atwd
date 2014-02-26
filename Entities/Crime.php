<?php

/**
 * Description of Crime
 * This class represents a crime
 * It has a crime catagory, which is a Crime Catagory object
 * 
 * @author hlp2-winser
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

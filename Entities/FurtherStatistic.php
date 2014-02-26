<?php

/**
 * Description of FurtherStatistic
 * This class represents a National Statistic, which i refer to as FurtherStatisitc
 * It contains an array of Crimes.
 * 
 * @author hlp2-winser
 */


class FurtherStatistic {
    private $name, $total, $crimeData = array(), $properName;
    
    public function getName() {
        return $this->name;
    }

    public function getTotal() {
        return intval($this->total);
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
    public function getProperName() {
        return $this->properName;
    }

    public function setProperName($properName) {
        $this->properName = $properName;
    }

}

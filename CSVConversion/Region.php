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
require 'ProvinceInterface.php';

class Region implements ProvinceInterface {

    private $name, $data, $countyList;

    function __construct($name, $data) {
        $this->name = $name;
        $this->data = new ArrayObject($data);
        $this->countyList = array();
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function getStats() {
        return $this->data;
    }

    public function setStats($stat) {
        $this->data = $stat;
    }
    
    public function addCounty($county){
        $this->countyList[] = $county;
    }
   
    public function getCounties(){
        return $this->countyList;
    }

}

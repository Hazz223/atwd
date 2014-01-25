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

class County implements ProvinceInterface {

    private $name, $data;
    
    function __construct($name, $data) {
        $this->name = $name;
        $this->data = $data;
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

}

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
require_once 'ProvinceInterface.php';

class Region implements ProvinceInterface {

    private $name, $data = array(), $countyList, $headers = array();

    function __construct($name, $data, $headers) {
        $this->name = $name;
        $this->data = new ArrayObject($data);
        $this->countyList = array();
        $this->headers = $headers;
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

    public function addCounty($county) {
        $this->countyList[] = $county;
    }

    public function getCounties() {
        return $this->countyList;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function setHeaders($headers) {
        $this->headers = $headers;
    }

}

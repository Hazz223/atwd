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
require_once "IArea.php";

class Area implements IArea {
private $name, $stats, $totals;
    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function getStats() {
        return $this->stats;
    }

    public function setStats($stat) {
        $this->stats = $stat;
    }

    public function getTotal() {
        
    }

    public function setStatisticByName($name, $data) {
        
    }

    public function getStatisticByName($name) {
        
    }
    public function getTotals() {
        return $this->totals;
    }

    public function setTotals($totals) {
        $this->totals = $totals;
    }

}

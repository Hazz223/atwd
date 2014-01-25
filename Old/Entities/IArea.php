<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IArea
 *
 * @author Harry
 */
interface IArea {
    public function setName($name);
    public function getName();
    public function getStats();
    public function setStats($stat);
    public function getTotal();
    public function setStatisticByName($name, $data);
    public function getStatisticByName($name);
}

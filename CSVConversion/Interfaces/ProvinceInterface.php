<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProvinceInterface
 *
 * @author Harry
 */

interface ProvinceInterface {
    public function __construct($name, $stats);
    public function setName($name);
    public function getName();
    public function getStats();
    public function setStats($stat);
    
}

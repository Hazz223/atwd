<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CrimeConfig
 *
 * @author Harry
 */
class CrimeConfig {
    
    private $configXml, $xpath;
    
    function __construct() {
        $this->configXml = new DOMDocument();
        $this->configXml->load("../Config/CrimeConfig.xml");
        $this->xpath = new DOMXpath($this->configXml);
    }
    
    function GetCrimeName($abName){
        $crimeNode = $this->_getNodeOnAbName($abName);
        
        return $crimeNode->getAttribute("name");
    }
    
    function CheckIfCrimeCategory($abName){
        $crimeNode = $this->_getNodeOnAbName($abName);
        
        if($crimeNode->getAttribute("iscrimecatagory") === "true"){
            return true;
        }
        else
        {
            return false;
        }
    }
    
    function GetCrimeCatagory($abName){
        $crimeNode = $this->_getNodeOnAbName($abName);
        
        return $crimeNode->getAttribute("crimecatagory");
    }
    
    private function _getNodeOnAbName($abName){
        return $this->xpath->query("CrimeAbriviations/Crime [@abrivated='".$abName."']", $area)->item(0);
    }

}

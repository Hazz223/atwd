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
    
    public function __construct() {
        $this->configXml = new DOMDocument();
        $this->configXml->load("../Config/CrimeConfig.xml");
        $this->xpath = new DOMXpath($this->configXml);
    }
    
    public function GetCrimeName($abName){
        $crimeNode = $this->_getNodeOnAbName($abName);
        
        return $crimeNode->getAttribute("name");
    }
    
    public function CheckIfCrimeCategory($abName){
        $crimeNode = $this->_getNodeOnAbName($abName);
        $isTrue = $crimeNode->getAttribute("iscrimecatagory");
        if($crimeNode->getAttribute("iscrimecatagory") === "true"){ // not finding the node.. Balls!
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function GetCrimeCatagory($abName){
        $crimeNode = $this->_getNodeOnAbName($abName);
        
        return $crimeNode->getAttribute("crimecatagory");
    }
    
    public function GetCrimeType($abName){
        $crimeNode = $this->_getNodeOnAbName($abName);
        return $crimeNode->getAttribute("type");
    }
    
    private function _getNodeOnAbName($abName){
        return $this->xpath->query("CrimeAbriviations/Crime [@abrivated='".$abName."']")->item(0);
    }
    

}

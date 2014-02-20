<?php

class CrimeConfig {

    private $configXml, $xpath;

    public function __construct() {
        $this->configXml = new DOMDocument();
        $this->configXml->load("../Config/CrimeConfig.xml");
        $this->xpath = new DOMXpath($this->configXml);
    }

    public function getCrimeName($abName) {
        $crimeNode = $this->_getNodeOnAbName($abName);

        return $crimeNode->getAttribute("name");
    }
    
    public function getAllNamesAndAbvNames(){
        $abvNames = $this->getAllCrimeNamesAbrivated();
        
        $nameArray = array();
        
        foreach($abvNames as $abvName){
            $nameArray[$abvName] = $this->getCrimeName($abvName);
        }
        
        return $nameArray;
    }

    public function CheckIfCrimeCategory($abName) {
        $crimeNode = $this->_getNodeOnAbName($abName);
        if ($crimeNode->getAttribute("iscrimecatagory") === "true") {
            return true;
        } else {
            return false;
        }
    }

    public function GetCrimeCatagory($abName) {
        $crimeNode = $this->_getNodeOnAbName($abName);

        return $crimeNode->getAttribute("crimecatagory");
    }

    public function GetCrimeType($abName) {
        $crimeNode = $this->_getNodeOnAbName($abName);
        return $crimeNode->getAttribute("type");
    }

    public function getAllCrimeNames() {
        $crimes = $this->configXml->getElementsByTagName("Crime");
        $crimeNames = array();

        foreach ($crimes as $crime) {
            $crimeNames[] = $crime->getAttribute("name");
        }

        return $crimeNames;
    }

    public function getAllCrimeNamesAbrivated() {
        $crimes = $this->configXml->getElementsByTagName("Crime");
        $crimeNames = array();

        foreach ($crimes as $crime) {
            $crimeNames[] = $crime->getAttribute("abrivated");
        }

        return $crimeNames;
    }

    private function _getNodeOnAbName($abName) {
        return $this->xpath->query("CrimeAbriviations/Crime [@abrivated='" . $abName . "']")->item(0);
    }
}

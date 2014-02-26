<?php

require_once '../Exceptions/SchemaValidationException.php';
class CrimeConfig {

    private $configXml, $xpath;

    public function __construct() {
        $this->configXml = new DOMDocument();
        $this->configXml->load("../Config/CrimeConfig.xml");
        
        if($this->configXml->schemaValidate("../Config/CrimeConfig.xsd")){ // doesn't validate
            $this->xpath = new DOMXpath($this->configXml);
        }
        else{
           throw new SchemaValidationError("CrimeConfig XML failed to validate");
        }
        
        $this->xpath = new DOMXpath($this->configXml);
        
    }

    public function getCrimeName($abName) {
        $crimeNode = $this->_getNodeOnAbName($abName); // not returning a node

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
    
    public function GetDataXMLName(){
       
        $nameNode = $this->xpath->query("cd:crime_data/cd:stored_xml_location")->item(0); // should get the node.
        
        return $nameNode->textContent; // Doesn't bloody work!
    }
    
    public function GetCacheLocation(){
        $nameNode = $this->xpath->query("cd:cache_data_location/cd:stored_cache_location")->item(0);
        return $nameNode->textContent;
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
        return $this->xpath->query("cd:CrimeAbriviations/cd:Crime [@abrivated='" . $abName . "']")->item(0);
    }
}

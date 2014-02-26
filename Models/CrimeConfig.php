<?php
/**
 * Description of CrimeConfig
 * Class that allows access to the crime config data
 * Used so you can manually change how the application works, without needing to 
 * change any code. 
 * The config XML is auto generated when running the Conversion Script
 * 
 * @author hlp2-winser
 */

class CrimeConfig {

    private $configXml, $xpath;

    // Loads the crime config xml and an xpath object to be used.
    public function __construct() {
        $this->configXml = new DOMDocument();
        $this->configXml->load("../Config/CrimeConfig.xml");
        $this->xpath = new DOMXpath($this->configXml);
    }
    
    //Get a crime name based on it's abbreviation
    public function getCrimeName($abName) {
        $crimeNode = $this->_getNodeOnAbName($abName);

        return $crimeNode->getAttribute("name");
    }
    
    // Returns an key value array - key is the abbreviated name of a crime
    // value is the full name of a crime
    public function getAllNamesAndAbvNames(){
        $abvNames = $this->getAllCrimeNamesAbrivated();
        
        $nameArray = array();
        
        foreach($abvNames as $abvName){
            $nameArray[$abvName] = $this->getCrimeName($abvName);
        }
        
        return $nameArray;
    }

    // Checks to see if the abbreviated name is a crime catagory
    public function CheckIfCrimeCategory($abName) {
        $crimeNode = $this->_getNodeOnAbName($abName);
        if ($crimeNode->getAttribute("iscrimecatagory") === "true") {
            return true;
        } else {
            return false;
        }
    }
    
    // Gets the location of the overal XML data
    public function getDataXMLName(){
        $nameNode = $this->xpath->query("crime_data/stored_xml_location")->item(0); // should get the node.
        
        return $nameNode->textContent; // returns the DataXML name!
    }
    
    // Gets where the cache is being stored
    public function getCacheLocation(){
        $nameNode = $this->xpath->query("cache_data_location/stored_cache_location")->item(0);
        return $nameNode->textContent;
    }

    // Gets the crime catagory name based on its abbreviated name
    public function getCrimeCatagory($abName) {
        $crimeNode = $this->_getNodeOnAbName($abName);

        return $crimeNode->getAttribute("crimecatagory");
    }

    // Gets the crime type based on the abbreviated name
    public function getCrimeType($abName) {
        $crimeNode = $this->_getNodeOnAbName($abName);
        return $crimeNode->getAttribute("type");
    }

    // returns a list of full crime names
    public function getAllCrimeNames() {
        $crimes = $this->configXml->getElementsByTagName("Crime");
        $crimeNames = array();

        foreach ($crimes as $crime) {
            $crimeNames[] = $crime->getAttribute("name");
        }

        return $crimeNames;
    }

    // returns a list of abbreviated  crime names
    public function getAllCrimeNamesAbrivated() {
        $crimes = $this->configXml->getElementsByTagName("Crime");
        $crimeNames = array();

        foreach ($crimes as $crime) {
            $crimeNames[] = $crime->getAttribute("abrivated");
        }

        return $crimeNames;
    }
    
    // gets a crime node based on the abbreviated name
    private function _getNodeOnAbName($abName) {
        return $this->xpath->query("CrimeAbriviations/Crime [@abrivated='" . $abName . "']")->item(0);
    }
}

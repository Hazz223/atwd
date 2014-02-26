<?php

/**
 * Description of DataAccess
 * This grants access to the XML
 * Uses the singlton pattern to get around issues where differnet objects had
 * different instances of the xml, and as such could have out dated information
 * 
 * @author hlp2-winser
 */

require_once 'CrimeConfig.php';
require_once '../Exceptions/XMLDataNotFound.php';

class DataAccess {

    private $xmlFileName;
    private $xml;
    private static $instance;

    private function __construct() {
            $crimeConf = new CrimeConfig();
            $this->xmlFileName = $crimeConf->getDataXMLName();
            $this->xml = new DOMDocument();
            $this->xml->load($this->xmlFileName); // gets the name from the config
            
            // I have a schema to validate against, but didn't have the time to 
            // Implement it. Real shame.
    }
    
    //Creates/gets the only instance of this data
    public static function GetInstance() {
        if (self::$instance === null) {
            self::$instance = new DataAccess();
        }
        return self::$instance;
    }
    
    // Gets the dom object
    public function getCrimeXML() {
        return $this->xml;
    }
    
    //Saves the dom object to a file
    public function saveXML() {
        $this->xml->save($this->xmlFileName);
    }
    
    // Removes a given node.
    public function RemoveNode($node) {
        $this->xml->removeChild($node);
        $this->saveXML();
    }

}

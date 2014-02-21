<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DataAccess
 *
 * @author Harry
 */
require_once 'CrimeConfig.php';
require_once '../Exceptions/XMLDataNotFound.php';

class DataAccess {

    const xmlFile = "../Data/CrimeStats.xml";

    private $xml;
    private static $instance;

    private function __construct() {
        
        $crimeConf = new CrimeConfig();
        
        $this->xml = new DOMDocument();
        $this->xml->load($crimeConf->GetDataXMLName()); // gets the name from the config
    }

    public static function GetInstance() { // solves the issue of he file not updating fast enough
        if (self::$instance === null) {
            self::$instance = new DataAccess();
        }
        return self::$instance;
    }

    public function getCrimeXML() {
        return $this->xml;
    }

    public function saveXML() {
        $this->xml->save(DataAccess::xmlFile);
    }

    public function RemoveNode($node) {
        $this->xml->removeChild($node);
        $this->saveXML();
    }

}

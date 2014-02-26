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
require_once '../Exceptions/SchemaValidationException.php';

class DataAccess {

    private $xmlFileName;
    private $xml;
    private static $instance;

    private function __construct() {
        $crimeConf = new CrimeConfig();
        $this->xmlFileName = $crimeConf->GetDataXMLName(); 
        $this->xml = new DOMDocument();
        $this->xml->load($this->xmlFileName); // gets the name from the config

        if (! $this->xml->schemaValidate("../Data/CrimeStats.xsd")){ // Doesn't validate. 
            throw new SchemaValidationError("CrimeStats XML failed to validate");
        } 
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
        $this->xml->save($this->xmlFileName);
    }

    public function RemoveNode($node) {
        $this->xml->removeChild($node);
        $this->saveXML();
    }

}

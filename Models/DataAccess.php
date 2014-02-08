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
class DataAccess {
    const xmlFile = "../Data/CrimeStats.xml";
    private $xml;
    private static $instance;
    
    private function __construct() {
        $this->xml = new DOMDocument();
        $this->xml->load(DataAccess::xmlFile);
    }

    private static function GetInstance(){ // Should change it so there is only one XML item.
        if(self::$instance === null){
            self::$instance = new DataAccess();
        }
        return self::$instance;
    }
    
    public function getCrimeXML() {
        return $this->xml;
    }
    
    public function saveData($xml){
        $xml->save(DataAccess::xmlFile);
    }
    
}

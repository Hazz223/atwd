<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AreasModel
 *
 * @author Harry
 */
require_once 'DataAccess.php';
require_once '../Entities/Area.php';
require_once '../Entities/Crime.php';
require_once '../Entities/CrimeCatagory.php';

class AreasModel {

    private $xml, $dataAccess;
    const totalInFraudTitle = "Total recorded crime - including fraud"; // set these so i dont have to keep refering to them.
    const totalNoFraudTitle = "Total recorded crime - excluding fraud";
    
    function __construct() {
        //$this->dataAccess = new DataAccess();
    }

    public function getAreaByName($name) {
        $area = $this->_getAreaNode($name);

        $newArea = new Area();
        $newArea->setName($area->getAttribute("name"));
        $newArea->setRegionName($area->parentNode->getAttribute("name"));

        $crimeCategories = $area->getElementsByTagName("CrimeCatagory");
        $crimeDataArray = array();

        foreach ($crimeCategories as $crimeCat) {
            $crimeDataArray[] = $this->_createCrimeCatagoryObject($crimeCat);
        }

        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $totalNode = $xpath->query("CrimeCatagory [@name='".AreasModel::totalInFraudTitle."']", $area)->item(0);

        $newArea->setTotal($totalNode->getAttribute("total"));
        $newArea->setCrimeData($crimeDataArray);

        return $newArea;
    }
    
    public function isArea($name){
        // if it is an area, return true
        // else return false.
        // Use xpath to find out?
        
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $result = $xpath->query("Country/Region/area [@name='" . $name . "']")->item(0);
        
        return isset($result);
    }

    public function AddCrimeCategory(CrimeCatagory $crimeCat, $areaName) {
        $areaNode = $this->_getAreaNode($areaName);
        
        if (!$this->_crimeCategoryExists($crimeCat->getName(), $areaNode)) {
            $this->_createCrimeCatagoryNode($crimeCat, $areaNode);
        } else {
            $crimeCatNode = $this->_getCrimeCategoryNode($crimeCat->getName(), $areaNode); // not finding the node!
            $crimeCatNode->setAttribute("total", $crimeCat->getTotal());
            DataAccess::GetInstance()->saveXML();

            $this->_updateTotalsNodes($areaNode);
        }
    }

    public function addCrimeToArea(Crime $crime, $areaName) {
        $areaNode = $this->_getAreaNode($areaName);

        if (!$this->_crimeExists($crime->getName(), $areaNode)) {
            $this->_createCrimeNode($crime, $areaNode);
            $this->_updateCrimeCategoryTotal($crime->getCrimeCatagory(), $areaNode);
            
            $this->_updateTotalsNodes($areaNode);

        } else {
            $crimeNode = $this->_getCrimeNode($crime->getName(), $areaNode);
            $crimeNode->nodeValue = $crime->getValue();

            $this->_updateCrimeCategoryTotal($crime->getCrimeCatagory(), $areaNode);
            
            $this->_updateTotalsNodes($areaNode);
            DataAccess::GetInstance()->saveXML();
        }
    }

    public function UpdateAreaTotal($name, $value) {

        $oldRegion = $this->getAreaByName($name);

        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $area = $xpath->query("Country/Region/area [@name='" . $name . "']")->item(0);


        $totalNode = $xpath->query("CrimeType/CrimeCatagory [@name='".AreasModel::totalInFraudTitle."']", $area);
        $totalNode->item(0)->setAttribute("total", $value);
        $this->dataAccess->saveData(DataAccess::GetInstance()->getCrimeXML());

        $newRegion = $this->getAreaByName($name);

        return array($oldRegion, $newRegion);
    }

    public function DeleteArea($areaName){
        $areaNode = $this->_getAreaNode($areaName);
        $parent = $areaNode->parentNode;
        $parent->removeChild($areaNode);
        DataAccess::GetInstance()->saveXML();
    }
    
    private function _createCrimeCatagoryNode($crimeCat, $areaNode) {
        $newCatNode = DataAccess::GetInstance()->getCrimeXML()->createElement("CrimeCatagory");
        $newCatNode->setAttribute("name", $crimeCat->getName());
        $newCatNode->setAttribute("type", $crimeCat->getCrimeType());
        $newCatNode->setAttribute("total", $crimeCat->getTotal());
        $areaNode->appendChild($newCatNode);

        DataAccess::GetInstance()->saveXML();
    }

    private function _createCrimeNode(Crime $crime, $areaNode) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $catNode = $xpath->query("CrimeCatagory [@name='" . $crime->getCrimeCatagory() . "']", $areaNode)->item(0); // not finding the correct node at all
        $areaName = $areaNode->getAttribute("name");

        if (isset($catNode)) {
            $newCrimeNode = DataAccess::GetInstance()->getCrimeXML()->createElement("Crime");
            $newCrimeNode->setAttribute("name", $crime->getName());
            $textNode = DataAccess::GetInstance()->getCrimeXML()->createTextNode($crime->getValue());
            $newCrimeNode->appendChild($textNode);
            $catNode->appendChild($newCrimeNode);
        } else {
            // need to build the crime cat node based off the crime object
            $newCrimeCatObj = new CrimeCatagory();
            $newCrimeCatObj->setName($crime->getCrimeCatagory());
            $newCrimeCatObj->setTotal($crime->getValue());
            $newCrimeCatObj->setCrimeType($crime->getCrimeType());

            $this->AddCrimeCategory($newCrimeCatObj, $areaName);

            $this->addCrimeToArea($crime, $areaName);
        }
        DataAccess::GetInstance()->saveXML();

        $this->_updateTotalsNodes($areaNode);
    }

    private function _createCrimeCatagoryObject($node) {

        $crimeCatObj = new CrimeCatagory();

        $crimeCatName = $node->getAttribute("name");
        $crimeCatObj->setName($node->getAttribute("name"));
        $crimeCatObj->setTotal($node->getAttribute("total"));
        $crimeCatObj->setCrimeType($node->getAttribute("type"));

        if ($node->hasChildNodes()) {
            $crimeArray = array();
            $crimes = $node->childNodes;
            foreach ($crimes as $crime) {
                $crimeObj = new Crime();
                $crimeObj->setName($crime->getAttribute("name"));
                $crimeObj->setValue($crime->textContent);
                $crimeObj->setCrimeCatagory($crimeCatName);
                $crimeArray[] = $crimeObj;
            }
            $crimeCatObj->setCrimeList($crimeArray);
        }

        return $crimeCatObj;
    }

    private function _getAreaNode($name) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        return $xpath->query("Country/Region/area [@name='" . $name . "']")->item(0);
    }

    private function _crimeCategoryExists($crimeCatName, $areaNode) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $node = $xpath->query("CrimeCatagory [@name='" . $crimeCatName . "']", $areaNode)->item(0);

        return isset($node);
    }

    private function _crimeExists($crimeName, $areaNode) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $exists = $xpath->query("CrimeCatagory/Crime [@name='" . $crimeName . "']", $areaNode)->item(0);

        return isset($exists);
    }

    private function _getCrimeNode($crimeName, $areaNode) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $node = $xpath->query("CrimeCatagory/Crime [@name='" . $crimeName . "']", $areaNode)->item(0);

        return $node;
    }

    private function _getCrimeCategoryNode($crimeCatagoryName, $areaNode) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $node = $xpath->query("CrimeCatagory [@name='" . $crimeCatagoryName . "']", $areaNode)->item(0);

        return $node;
    }

    private function _updateCrimeCategoryTotal($crimeCatagoryName, $areaNode) {
        $crimeCatNode = $this->_getCrimeCategoryNode($crimeCatagoryName, $areaNode);

        $crimeList = $crimeCatNode->getElementsByTagName("Crime");

        $total = 0;
        foreach ($crimeList as $crime) {
            $total = $total + intval($crime->nodeValue);
        }

        $crimeCatNode->setAttribute("total", $total);

        DataAccess::GetInstance()->saveXML(); // save the changes
    }

    private function _updateTotalsNodes($areaNode) {
        if ($this->_crimeCategoryExists(AreasModel::totalInFraudTitle, $areaNode)) {
            $crimeCats = $areaNode->getElementsByTagName("CrimeCatagory");
            
            
            $total = 0;
            $fraudTotal = 0;

            foreach ($crimeCats as $crimeCat) {
                if (($crimeCat->getAttribute("name") != AreasModel::totalInFraudTitle) // needed, else it will try to add itself up!
                        && ($crimeCat->getAttribute("name") != AreasModel::totalNoFraudTitle)) {
                    $value = intval($crimeCat->getAttribute("total"));

                    if ($crimeCat->getAttribute("name") === "Fraud") {
                        $fraudTotal = $value;
                    }
                    $test = $total + $value;
                }
            }

            $totalInFraud = $this->_getCrimeCategoryNode(AreasModel::totalInFraudTitle, $areaNode);
            $totalInFraud->setAttribute("total", $test);

            $totalNoFraud = $this->_getCrimeCategoryNode(AreasModel::totalNoFraudTitle, $areaNode);
            $totalNoFraud->setAttribute("total", ($test - $fraudTotal));

            DataAccess::GetInstance()->saveXML();
        } else {
            $this->_createTotalsNodes($areaNode);

            $this->_updateTotalsNodes($areaNode);
        }
    }

    private function _createTotalsNodes($areaNode) {
        $totalInFraud = new CrimeCatagory();

        $totalInFraud->setName(AreasModel::totalInFraudTitle);
        $totalInFraud->setTotal(0);
        $totalInFraud->setCrimeType("Total");
        $this->_createCrimeCatagoryNode($totalInFraud, $areaNode);

        $totalNoFraud = new CrimeCatagory();
        $totalNoFraud->setName(AreasModel::totalNoFraudTitle);
        $totalNoFraud->setTotal(0);
        $totalNoFraud->setCrimeType("Total");
        $this->_createCrimeCatagoryNode($totalNoFraud, $areaNode);
    }

}

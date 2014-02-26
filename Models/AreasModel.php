<?php

/**
 * Description of AreasModel
 * This model gives access to the Data when dealing with areas.
 * Populates and returns Area entity objects to be used by the controller
 * As well as other functions. 
 * Areas contain Crime Catagories, which in turn contain Crimes
 * @author hlp2-winser
 */

require_once 'DataAccess.php';
require_once '../Entities/Area.php';
require_once '../Entities/Crime.php';
require_once '../Entities/CrimeCatagory.php';
require_once '../Exceptions/FieldNotFoundException.php';

class AreasModel {

    const totalInFraudTitle = "Total recorded crime - including fraud"; // set these so i dont have to keep refering to them.
    const totalNoFraudTitle = "Total recorded crime - excluding fraud";

    // Searches for an Area by name, and returns an area object of that name.
    public function getAreaByName($name) {

        $area = $this->_getAreaNode($name);
        
        $newArea = new Area();
        $newArea->setName($area->getAttribute("name"));
        $newArea->setProperName($area->getAttribute("proper_name"));
        $newArea->setRegionName($area->parentNode->getAttribute("name"));

        $crimeCategories = $area->getElementsByTagName("CrimeCatagory");
        $crimeDataArray = array();

        foreach ($crimeCategories as $crimeCat) {
            $crimeDataArray[] = $this->_createCrimeCatagoryObject($crimeCat);
        }

        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $totalNode = $xpath->query("CrimeCatagory [@name='" . AreasModel::totalInFraudTitle . "']", $area)->item(0);

        $newArea->setTotal($totalNode->getAttribute("total"));
        $newArea->setCrimeData($crimeDataArray);

        return $newArea;
    }

    //  Checks if the area name given is already an area that exists.
    public function isArea($name) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        // xpath which gets the first node found. Usually enough to find the correct node.
        $result = $xpath->query("Country/Region/Area [@name='" . $name . "']")->item(0); 
        return isset($result);
    }

    // Adds a crime catagory to an area
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
        
        $this->_updateTotalsNodes($areaNode);
    }

    // Adds a single crime to an area
    public function addCrimeToArea(Crime $crime, $areaName) {
        $areaNode = $this->_getAreaNode($areaName);

        // Checks if crime exits - Yes: Updates the value, No: Creates a new Crime node
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

    // Updates the Area Total nodes based on an area name
    public function UpdateAreaTotal($name, $value) {
        $area = $this->_getAreaNode($name);

        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $totalNode = $xpath->query(" CrimeCatagory [@name='" . AreasModel::totalInFraudTitle . "']", $area)->item(0);
        $totalNode->setAttribute("total", $value);
        DataAccess::GetInstance()->saveXML();
    }

    // Removes an Area based on it's name
    public function DeleteArea($areaName) {
        $areaNode = $this->_getAreaNode($areaName);
        $parent = $areaNode->parentNode;
        $parent->removeChild($areaNode);
        DataAccess::GetInstance()->saveXML();
    }

    // creates a crime Catagory node using a CrimeCatagory object and a given node
    private function _createCrimeCatagoryNode($crimeCat, $areaNode) {
        $newCatNode = DataAccess::GetInstance()->getCrimeXML()->createElement("CrimeCatagory");
        $newCatNode->setAttribute("name", $crimeCat->getName());
        $newCatNode->setAttribute("type", $crimeCat->getCrimeType());
        $newCatNode->setAttribute("total", $crimeCat->getTotal());
        $areaNode->appendChild($newCatNode);

        DataAccess::GetInstance()->saveXML();
    }

    //Creates a crime node for an node given, using a crime object
    private function _createCrimeNode(Crime $crime, $areaNode) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        // Searches for the Crime Catagory Node to append it to
        $catNode = $xpath->query("CrimeCatagory [@name='" . $crime->getCrimeCatagory() . "']", $areaNode)->item(0);
        $areaName = $areaNode->getAttribute("name");

        // checks if tehre is a crime catagory for this already
        if (isset($catNode)) {
            // There is one, and so it is appended.
            $newCrimeNode = DataAccess::GetInstance()->getCrimeXML()->createElement("Crime");
            $newCrimeNode->setAttribute("name", $crime->getName());
            $textNode = DataAccess::GetInstance()->getCrimeXML()->createTextNode($crime->getValue());
            $newCrimeNode->appendChild($textNode);
            $catNode->appendChild($newCrimeNode);
        } else {
            // No crime catagory node available. Creates one
            $newCrimeCatObj = new CrimeCatagory();
            $newCrimeCatObj->setName($crime->getCrimeCatagory());
            $newCrimeCatObj->setTotal($crime->getValue());
            $newCrimeCatObj->setCrimeType($crime->getCrimeType());
            
            // Adds it to the data
            $this->AddCrimeCategory($newCrimeCatObj, $areaName);
            // Sort of recursion, as it calls the function that calls this one.
            $this->addCrimeToArea($crime, $areaName);
        }
        DataAccess::GetInstance()->saveXML();

        $this->_updateTotalsNodes($areaNode);
    }

    //Takes in a node and creates a crime catagory object from it. Needs to be
    // A node that is a crime catagory
    private function _createCrimeCatagoryObject($node) {

        $crimeCatObj = new CrimeCatagory();

        $crimeCatName = $node->getAttribute("name");
        $crimeCatObj->setName($node->getAttribute("name"));
        $crimeCatObj->setTotal($node->getAttribute("total"));
        $crimeCatObj->setCrimeType($node->getAttribute("type"));

        //If it has children, we create crime objects
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

    // Gets the node that an area belogs too.
    // Allows access to all of its information and Attributes etc.
    private function _getAreaNode($name) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $node = $xpath->query("Country/Region/Area [@name='" . $name . "']")->item(0);
        if (isset($node)) {
            return $node;
        } else {
            throw new FieldNotFoundException("Could not find area with name ".$name, 404);
        }
    }

    //Quick boolean to check if a crime catagory exists
    private function _crimeCategoryExists($crimeCatName, $areaNode) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $node = $xpath->query("CrimeCatagory [@name='" . $crimeCatName . "']", $areaNode)->item(0);

        return isset($node);
    }
    
    //Quick boolean to check if a crime exists
    private function _crimeExists($crimeName, $areaNode) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $exists = $xpath->query("CrimeCatagory/Crime [@name='" . $crimeName . "']", $areaNode)->item(0);

        return isset($exists);
    }

    // gets a crime node based on a name and an area node
    private function _getCrimeNode($crimeName, $areaNode) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $node = $xpath->query("CrimeCatagory/Crime [@name='" . $crimeName . "']", $areaNode)->item(0);

        return $node;
    }
    
    // gets a crime catagory node based on a crime catagory name and an area node
    private function _getCrimeCategoryNode($crimeCatagoryName, $areaNode) {
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $node = $xpath->query("CrimeCatagory [@name='" . $crimeCatagoryName . "']", $areaNode)->item(0);

        return $node;
    }

    //Cycles through all the named Crime catagory children, and adds them up. Stores the Total 
    // As an Attribute
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

    //This updates the Fraud and No fraud nodes with all the data contained within the area node
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
                    $total = $total + $value;
                }
            }

            $totalInFraud = $this->_getCrimeCategoryNode(AreasModel::totalInFraudTitle, $areaNode);
            $totalInFraud->setAttribute("total", $total);

            $totalNoFraud = $this->_getCrimeCategoryNode(AreasModel::totalNoFraudTitle, $areaNode);
            $totalNoFraud->setAttribute("total", ($total - $fraudTotal));

            DataAccess::GetInstance()->saveXML();
        } else {
            $this->_createTotalsNodes($areaNode);

            // Recursion so that it updates the nodes once it's created the nodes
            $this->_updateTotalsNodes($areaNode);
        }
    }

    // Creates the total nodes needed for an areas crime data
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

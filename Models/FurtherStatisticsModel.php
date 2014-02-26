<?php

/**
 * Description of FurtherStatisticsModel
 * Model for accessing the FurtherStatistic data in the XML data
 *
 * @author hlp2-winser
 */
require_once 'DataAccess.php';
require_once '../Entities/FurtherStatistic.php';

class FurtherStatisticsModel {

    // returns a list of FurtherStatistic objects
    public function getAllFurtherStatistics() {
        $newFStatsList = array();
        $fStats = DataAccess::GetInstance()->getCrimeXML()->getElementsByTagName("FurtherStatistics");

        // This never creates all of the crime data? Why?
        foreach ($fStats as $fStat) {
            $newFurtherStat = new FurtherStatistic();

            $newFurtherStat->setName($fStat->getAttribute("name"));
            $newFurtherStat->setProperName($fStat->getAttribute("proper_name"));
            
            $totalNode = $this->_getTotalNode($fStat);
            $total = intval($totalNode->getAttribute("total"));
            $newFurtherStat->setTotal($total);

            $newFStatsList[] = $newFurtherStat;
        }

        return $newFStatsList;
    }

    // returns a FurtherStatistic based on the name
    public function getFurtherStatisticsByName($name) {

        $furtherStatNode = $this->_findFurtherStatNode($name);
        
        $furtherStatObj = new FurtherStatistic();
        
        $furtherStatObj->setName($furtherStatNode->getAttribute("name"));
        $furtherStatObj->setProperName($furtherStatNode->getAttribute("proper_name"));
        
        $totalNode = $this->_getTotalNode($furtherStatNode);
        $total = intval($totalNode->getAttribute("total")); // comes through as text - needs to be converted to an int
        $furtherStatObj->setTotal($total);

        $crimes = $furtherStatNode->getElementsByTagName("Crime");

        // Creates an array - key = Crime name, value is crime value... Does this then not include crime catagories?  
        $crimeStatsArray = array();
        foreach ($crimes as $crime) {
            $crimeStatsArray[$crime->getAttribute("name")] = $crime->textContent;
        }

        $furtherStatObj->setCrimeData($crimeStatsArray);

        return $furtherStatObj;
    }

    // quick check to see if this further statistic exists.
    public function isFurtherStat($name) {
        $cleanedName = str_replace(" ", "_", $name);
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $statNode = $xpath->query("FurtherStatistics [@name='" . $cleanedName . "']")->item(0);
        return isset($statNode);
    }
    
    // Updates the total node of further stat
    public function updateTotal($name, $data) {
        $furtherStatNode = $this->_findFurtherStatNode($name);
        
        $totalNode = $this->_getTotalNode($furtherStatNode);
        $totalNode->setAttribute("total", $data);
        
        DataAccess::GetInstance()->saveXML();
    }

    // Finds a further stat node using Xpath
    private function _findFurtherStatNode($name) {
        $name = str_replace(" ", "_", $name);

        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $furtherStat = $xpath->query("FurtherStatistics [@name='" . strtolower($name) . "']")->item(0);
        
        if(isset($furtherStat)){
            return $furtherStat;
        }
        else{
            throw new FieldNotFoundException("Could not find National stat: ".$name);
        }
    }
    
    // Gets the total node for a Further Stat
    private function _getTotalNode($node){
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $totalNode = $xpath->query("CrimeCatagory [@name='Total recorded crime - including fraud']", $node)->item(0);
        
        return $totalNode;
    }
}

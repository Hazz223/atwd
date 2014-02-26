<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FurtherStatisticsModel
 *
 * @author Harry
 */
require_once 'DataAccess.php';
require_once '../Entities/FurtherStatistic.php';

class FurtherStatisticsModel {

    public function getAllFurtherStatistics() {
        $newFStatsList = array();
        $fStats = DataAccess::GetInstance()->getCrimeXML()->getElementsByTagName("FurtherStatistics");

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

    public function getFurtherStatisticsByName($name) {

        $furtherStatNode = $this->_findFurtherStatNode($name);
        
        $furtherStatObj = new FurtherStatistic();
        
        $furtherStatObj->setName($furtherStatNode->getAttribute("name"));
        $furtherStatObj->setProperName($furtherStatNode->getAttribute("proper_name"));
        
        $totalNode = $this->_getTotalNode($furtherStatNode);
        $total = intval($totalNode->getAttribute("total"));
        $furtherStatObj->setTotal($total);

        $crimes = $furtherStatNode->getElementsByTagName("Crime");

        $crimeStatsArray = array();
        foreach ($crimes as $crime) {
            $crimeStatsArray[$crime->getAttribute("name")] = $crime->textContent;
        }

        $furtherStatObj->setCrimeData($crimeStatsArray);

        return $furtherStatObj;
    }

    public function isFurtherStat($name) {
        $cleanedName = str_replace(" ", "_", $name);
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $statNode = $xpath->query("cd:FurtherStatistics [@name='" . $cleanedName . "']")->item(0);
        return isset($statNode);
    }

    public function updateTotal($name, $data) {
        $furtherStatNode = $this->_findFurtherStatNode($name);
        
        $totalNode = $this->_getTotalNode($furtherStatNode);
        $totalNode->setAttribute("total", $data);
        
        DataAccess::GetInstance()->saveXML();
    }

    private function _findFurtherStatNode($name) {
        $name = str_replace(" ", "_", $name);

        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $furtherStat = $xpath->query("cd:FurtherStatistics [@name='" . strtolower($name) . "']")->item(0);
        
        if(isset($furtherStat)){
            return $furtherStat;
        }
        else{
            throw new FieldNotFoundException("Could not find National stat: ".$name);
        }
    }
    
    private function _getTotalNode($node){
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $totalNode = $xpath->query("cd:CrimeCatagory [@name='Total recorded crime - including fraud']", $node)->item(0);
        
        return $totalNode;
    }
}

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

            $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
            $totalNode = $xpath->query("CrimeCatagory [@name='Total recorded crime - including fraud']", $fStat)->item(0);
            $total = intval($totalNode->getAttribute("total"));
            $newFurtherStat->setTotal($total);

            $newFStatsList[] = $newFurtherStat;
        }

        return $newFStatsList;
    }

    public function getFurtherStatisticsByName($name) {
        // find this using xpath
        $name = str_replace("_", " ", $name); // remove any annoying underscores

        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $furtherStat = $xpath->query("FurtherStatistics[@name='" . $name . "']")->item(0);
        //British Transport Police
        
        //$furtherStat = $xpath->query("FurtherStatistics[@name='British Transport Police']")->item(0);
        $furtherStatObj = new FurtherStatistic();

        $furtherStatObj->setName($furtherStat->getAttribute("name"));

        $totalNode = $xpath->query("CrimeType/CrimeCatagory [@name='Total recorded crime - including fraud']", $furtherStat)->item(0);
        $total = intval($totalNode->getAttribute("total"));
        $furtherStatObj->setTotal($total);
        
        $crimes = $furtherStat->getElementsByTagName("Crime");
        
        $crimeStatsArray = array();
        foreach($crimes as $crime){
            $crimeStatsArray[$crime->getAttribute("name")] = $crime->textContent;
        }
        
        $furtherStatObj->setCrimeData($crimeStatsArray);

        return $furtherStatObj;
    }
    
    public function isFurtherStat($name){
        $cleanedName = str_replace("_", " ", $name);
        $xpath = new DOMXpath(DataAccess::GetInstance()->getCrimeXML());
        $statNode = $xpath->query("FurtherStatistics [@name='" . $cleanedName . "']")->item(0);
        return isset($statNode);
    }

}

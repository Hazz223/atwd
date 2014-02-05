<?php

// We can cache this by looking at the request, and then store it in a text file. 
// check age of text file against the age of the XML file. If younger, serve it, if not, then delete and refresh.

require_once '../Models/RegionsModel.php';
require_once '../Models/CountriesModel.php';
require_once '../Models/FurtherStatisticsModel.php';
require_once '../Models/AreasModel.php';

$countryModel = new CountriesModel();
$regionModel = new RegionsModel();
$fStatsModel = new FurtherStatisticsModel();
$areasModel = new AreasModel();

$responseXML = new DOMDocument();
$base = $responseXML->createElement("reponse");

$base->setAttribute("timestamp", date("YmdHi"));
$crime = $responseXML->createElement("crimes");
$crime->setAttribute("year", "6-2013");

if (isset($_GET["region"])) {
    $givenRegionName = $_GET["region"];
    // Need to check for Action Fraud/Transport

    if (strtolower($givenRegionName) === "british_transport_police" || strtolower($givenRegionName) === "action_fraud") {
        $obj = $fStatsModel->getFurtherStatisticsByName($givenRegionName); // gets the object. We then do something similar to the below.
        // This needs to be all chanegd, as this comes through as a region... Apprently?!

        $furtehrStatNode = $responseXML->createElement("national");
        $furtehrStatNode->setAttribute("id", $furtherStat->getName());
        $furtehrStatNode->setAttribute("total", $furtherStat->getTotal());

        $crime->appendChild($furtehrStatNode);
    } else {
        $region = $regionModel->getRegionByName($givenRegionName);

        $name = $region->getName();

        $regionNode = $responseXML->createElement("region");
        $regionNode->setAttribute("id", $name);
        $regionNode->setAttribute("total", $region->getTotal());

        $areaNames = $region->getAreaNames();
        foreach ($areaNames as $areaName) {

            $areaObj = $areasModel->getAreaByName($areaName);

            $areaNode = $responseXML->createElement("area");
            $areaNode->setAttribute("id", $areaObj->getName());
            $areaNode->setAttribute("total", $areaObj->getTotal());
            $regionNode->appendChild($areaNode);
        }

        $crime->appendChild($regionNode);
    }
} else {

    $regions = $regionModel->getAllRegions();
    $countries = $countryModel->getAllCounties();
    $fStats = $fStatsModel->getAllFurtherStatistics();



    foreach ($regions as $region) {
        $name = $region->getName();
        if ($name != "WALES") {
            $regionNode = $responseXML->createElement("region");
            $regionNode->setAttribute("id", $name);

            $regionNode->setAttribute("total", $region->getTotal());
            $crime->appendChild($regionNode);
        }
    }

    foreach ($countries as $country) {
        $name = $country->getName();
        $countryNode = $responseXML->createElement(strtolower($name));
        $countryNode->setAttribute("total", $country->getTotal());

        $crime->appendChild($countryNode);
    }

    foreach ($fStats as $stat) {
        $statNode = $responseXML->createElement("national");
        $statNode->setAttribute("id", $stat->getName());
        $statNode->setAttribute("total", $stat->getTotal());
        $crime->appendChild($statNode);
    }



    //Could i not just do an include as a view?
}


header("Content-type: text/xml");
$base->appendChild($crime);
$responseXML->appendChild($base);
echo $responseXML->saveXML();

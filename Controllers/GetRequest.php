<?php

// If the URL is: 
// http://www.cems.uwe.ac.uk/~<yourusername>/atwd/crimes/6-2013/south_west/xml
// or:
// http://www.cems.uwe.ac.uk/~<yourusername>/atwd/crimes/6-2013/xml
// don't actually need to worry about if it is either xml or json. I can throw an error in this instead. 
// Need to use Regex to point it to this file. 
// It should look in this get request file. I should then break this down, and then return the correct information.
require_once '../Models/Areas.php';
require_once '../Models/Regions.php';
require_once '../Models/Counties.php';
$areasModel = new Areas();
$regionsModel = new Regions();
$countiesModel = new Counties();

header("Content-type: text/xml");
$responseXML = new DOMDocument();
$base = $responseXML->createElement("reponse");

$base->setAttribute("timestamp", date("YmdHi"));
$crime = $responseXML->createElement("crime");
$crime->setAttribute("year", "6-2013");

if (isset($_GET["region"])) {

    // need to watch out for the term region, and the term Region. His request doesn't have it, while some parts of my xml does. Wonderful

    $region = $regionsModel->GetRegionByName(str_replace("_", " ", $_GET["region"])); // southwest doesn't seem to exis?

    $regionNode = $responseXML->createElement("region");
    $regionNode->setAttribute("id", $region->getName());
    $regionNode->setAttribute("total", $region->getCrimeStatByName("Total recorded crime - including fraud"));

    foreach ($region->getCountyNames() as $countyName) {
        $county = $countiesModel->GetCountyByName($countyName);
        $countyNode = $responseXML->createElement("area");
        $countyNode->setAttribute("id", $county->getName());
        $countyNode->setAttribute("total", $county->getCrimeStatByName("Total recorded crime - including fraud"));

        $regionNode->appendChild($countyNode);
    }

    $crime->appendChild($regionNode);
} else {

    $areas = $areasModel->GetAreas();
    $regions = $regionsModel->GetRegions();

    foreach ($regions as $region) {
        $regionNode = $responseXML->createElement("region");
        $regionNode->setAttribute("id", $region->getName());
        $regionNode->setAttribute("total", $region->getCrimeStatByName("Total recorded crime - excluding fraud")); // this doesn't work yet... woo
        $crime->appendChild($regionNode);
    }

    $areaArray = array();

    foreach ($areas as $area) {
        $areaStats = $area->getCrimeStatByName("Total recorded crime - including fraud");

        $name = $area->getName();
        if (($name == "ENGLAND") || ($name == "WALES")) {
            $areaNode = $responseXML->createElement(strtolower($name));
            $areaNode->setAttribute("total", $areaStats);

            $areaArray[$name] = $areaNode;
        }

        if ($name == "British Transport Police") {
            $areaNode = $responseXML->createElement("national");
            $areaNode->setAttribute("id", $name);
            $areaNode->setAttribute("total", $areaStats);
            $areaArray[$name] = $areaNode;
        }

        if ($name == "Action Fraud1") {
            $areaNode = $responseXML->createElement("national");
            $areaNode->setAttribute("id", "Action Fraud");
            $areaNode->setAttribute("total", $areaStats);
            $areaArray[$name] = $areaNode;
        }
    }

    // due to the formatting, i had to put it in this order. Pain in the ass reall.
    $crime->appendChild($areaArray["British Transport Police"]);
    $crime->appendChild($areaArray["Action Fraud1"]);
    $crime->appendChild($areaArray["ENGLAND"]);
    $crime->appendChild($areaArray["WALES"]);
}

$base->appendChild($crime);
$responseXML->appendChild($base);
echo $responseXML->saveXML();
?>



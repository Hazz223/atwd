<?php

/**
 * Description of FullGetRequestView
 * View for the Full Get request - both xml and json varities. 
 * 
 * @author hlp2-winser
 */
require_once '../Cache/Cache.php';

$regions = $_SESSION["regions"]; // List of region objects
$countries = $_SESSION["countries"]; // List of County objects
$fStats = $_SESSION["fStats"]; // List of FurtherStatistics objects
$type = $_SESSION["type"];

$cache = new Cache(); // cache used to store the response

if ($type === "xml") {
    $responseXML = new DOMDocument();
    $base = $responseXML->createElement("reponse");

    $base->setAttribute("timestamp", time());
    $crime = $responseXML->createElement("crimes");
    $crime->setAttribute("year", "6-2013");

    foreach ($regions as $region) {
        $name = $region->getProperName();
        // need to check for wales, as it's both a region and a country.
        // So we can't include it in the Region part.
        if ($name !== "Wales") {
            $regionNode = $responseXML->createElement("region");
            $regionNode->setAttribute("id", $name);

            $regionNode->setAttribute("total", $region->getTotal());
            $crime->appendChild($regionNode);
        }
    }

    // All the country information 
    foreach ($countries as $country) {
        $name = $country->getName();
        $countryNode = $responseXML->createElement(strtolower($name));
        $countryNode->setAttribute("total", $country->getTotal());

        $crime->appendChild($countryNode);
    }

    // All the Further Statis information for the request
    foreach ($fStats as $stat) {
        $statNode = $responseXML->createElement("national");
        $statNode->setAttribute("id", $stat->getProperName());
        $statNode->setAttribute("total", $stat->getTotal());
        $crime->appendChild($statNode);
    }

    $base->appendChild($crime);
    $responseXML->appendChild($base);

    // creates the new cache file for the request - this is controlled in the controller
    $cache->createCacheFile("all-get", $responseXML, $type); 


    header("Content-type: text/xml"); // content type needed
    echo $responseXML->saveXML();
} else {
    $regionArray = array();
    foreach ($regions as $region) {
        // need to ignore wales, as it's both a region and a country. We don't include it here
        if ($region->getProperName() !== "Wales") {
            $array = array("id" => $region->getProperName(), "total" => $region->getTotal());
            $regionArray[] = $array;
        }
    }

    // All of the Further Stats information
    $fStatArray = array();
    foreach ($fStats as $stat) {
        $array = array("id" => $stat->getProperName(), "total" => $stat->getTotal());
        $fStatArray[] = $array;
    }

    $crimeData = array("year" => "6-2013");
    $crimeData["region"] = $regionArray;

    $dataArray = array();
    $dataArray["timestamp"] = time();
    $crimeData["national"] = $fStatArray;

    // All of the country information
    foreach ($countries as $country) {
        $crimeData[strtolower($country->getName())] = $country->getTotal();
    }
    $dataArray["crimes"] = $crimeData;

    $base = array();
    $base["response"] = $dataArray;

    header("Content-type: application/json"); // content type is needed
    $fullJson = json_encode($base, JSON_PRETTY_PRINT); // pretty print is pretty

    // creates the new cache file for the request - this is controlled in the controller
    $cache->createCacheFile("all-get", $fullJson, $type);
    echo $fullJson;
}


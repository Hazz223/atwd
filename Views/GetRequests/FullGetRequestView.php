<?php

require_once '../Cache/Cache.php';

$regions = $_SESSION["regions"];
$countries = $_SESSION["countries"];
$fStats = $_SESSION["fStats"];
$type = $_SESSION["type"];
$cache = new Cache();

if ($type === "xml") {
    $responseXML = new DOMDocument();
    $base = $responseXML->createElement("reponse");

    $base->setAttribute("timestamp", time());
    $crime = $responseXML->createElement("crimes");
    $crime->setAttribute("year", "6-2013");

    foreach ($regions as $region) {
        $name = $region->getProperName();
        if ($name !== "Wales") {
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
        $statNode->setAttribute("id", $stat->getProperName());
        $statNode->setAttribute("total", $stat->getTotal());
        $crime->appendChild($statNode);
    }

    $base->appendChild($crime);
    $responseXML->appendChild($base);

    $cache->createCacheFile("all-get", $responseXML, $type);


    header("Content-type: text/xml");
    echo $responseXML->saveXML();
} else {
    $regionArray = array();
    foreach ($regions as $region) {
        $array = array("id" => $region->getProperName(), "total" => $region->getTotal());
        $regionArray[] = $array;
    }

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

    foreach ($countries as $country) {
        $crimeData[strtolower($country->getName())] = $country->getTotal();
    }
    $dataArray["crimes"] = $crimeData;

    $base = array();
    $base["response"] = $dataArray;

    header("Content-type: application/json");
    $fullJson = json_encode($base, JSON_PRETTY_PRINT);

    $cache->createCacheFile("all-get", $fullJson, $type);
    echo $fullJson;
}


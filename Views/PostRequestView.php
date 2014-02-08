<?php

$area = $_SESSION["area"];
$region = $_SESSION["region"];

$responseXML = new DOMDocument();
$base = $responseXML->createElement("reponse");
$base->setAttribute("timestamp", date("YmdHi"));
$crimeDataNode = $responseXML->createElement("crimes");
$crimeDataNode->setAttribute("year", "6-2013");

$regionNode = $responseXML->createElement("region");
$regionNode->setAttribute("id", $region->getName());
$regionNode->setAttribute("total", $region->getTotal());

$areaNode = $responseXML->createElement("area");
$areaNode->setAttribute("name", $area->getName());
$areaNode->setAttribute("total", $area->getTotal());

foreach ($area->getCrimeData() as $crimeCats) {
    if ($crimeCats->getCrimeType() != "Total") {
        $crimeList = $crimeCats->getCrimeList();
        if (isset($crimeList)) {
            foreach ($crimeList as $crime) {
                $crimeNode = $responseXML->createElement("recorded");
                $crimeNode->setAttribute("id", $crime->getName());
                $crimeNode->setAttribute("total", $crime->getValue());

                $areaNode->appendChild($crimeNode);
            }
        } else {
            $crimeCatNode = $responseXML->createElement("recorded");
            $crimeCatNode->setAttribute("id", $crimeCats->getName());
            $crimeCatNode->setAttribute("total", $crimeCats->getTotal());

            $areaNode->appendChild($crimeCatNode);
        }
    }
}

$regionNode->appendChild($areaNode);

$crimeDataNode->appendChild($regionNode);

$base->appendChild($crimeDataNode);
$responseXML->appendChild($base);
header("Content-type: text/xml");
echo $responseXML->saveXML();

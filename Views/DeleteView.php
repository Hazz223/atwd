<?php

$area = $_SESSION["area"];
$englandTotal = $_SESSION["englandTotal"];
$combinedTotal = $_SESSION["combinedTotal"];

$responseXML = new DOMDocument();
$base = $responseXML->createElement("reponse");
$base->setAttribute("timestamp", date("YmdHi"));
$crimeDataNode = $responseXML->createElement("crimes");
$crimeDataNode->setAttribute("year", "6-2013");

$areaNode = $responseXML->createElement("area");
$areaNode->setAttribute("name", $area->getName());
$areaNode->setAttribute("total", $area->getTotal());

foreach ($area->getCrimeData() as $crimeCats) {
    if ($crimeCats->getCrimeType() != "Total") {
        $crimeList = $crimeCats->getCrimeList();
        if (isset($crimeList)) {
            foreach ($crimeList as $crime) {
                $crimeNode = $responseXML->createElement("deleted");
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

$crimeDataNode->appendChild($areaNode);

$englandTotalNode = $responseXML->createElement("england");
$englandTotalNode->setAttribute("total", $englandTotal);

$combinedTotalNode = $responseXML->createElement("england_wales");
$combinedTotalNode->setAttribute("total", $combinedTotal);

$crimeDataNode->appendChild($englandTotalNode);
$crimeDataNode->appendChild($combinedTotalNode);

$base->appendChild($crimeDataNode);
$responseXML->appendChild($base);
header("Content-type: text/xml");
echo $responseXML->saveXML();
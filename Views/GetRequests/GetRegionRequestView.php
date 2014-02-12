<?php

$region = $_SESSION["region"];
$areas = $_SESSION["areas"];
$type = $_SESSION["type"];

if ($type === "xml") {
    $responseXML = new DOMDocument();
    $base = $responseXML->createElement("reponse");

    $base->setAttribute("timestamp", date("YmdHi"));
    $crime = $responseXML->createElement("crimes");
    $crime->setAttribute("year", "6-2013");

    $regionNode = $responseXML->createElement("region");
    $regionNode->setAttribute("id", $region->getProperName());
    $regionNode->setAttribute("total", $region->getTotal());

    foreach ($areas as $area) {
        $areaNode = $responseXML->createElement("area");
        $areaNode->setAttribute("id", $area->getProperName());
        $areaNode->setAttribute("total", $area->getTotal());
        $regionNode->appendChild($areaNode);
    }

    $crime->appendChild($regionNode);


    $base->appendChild($crime);
    $responseXML->appendChild($base);

    header("Content-type: text/xml");
    echo $responseXML->saveXML();
} else {
    $regionArray = array();

    $areasArray = array();
    foreach ($areas as $area) {
        $array = array("id" => $area->getProperName(), "total" => $area->getTotal());
        $areasArray[] = $array;
    }


    $crimeData = array("year" => "6-2013");
    $crimeData["region"] = array("id" => $region->getProperName(), "total" => $region->getTotal(), "areas" => $areasArray);

    $dataArray = array();
    $dataArray["timestamp"] = date("YmdHi");

    $dataArray["crimes"] = $crimeData;

    $base = array();
    $base["response"] = $dataArray;
    header("Content-type: application/json");
    echo json_encode($base);
}



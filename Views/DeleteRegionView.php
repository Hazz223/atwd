<?php

$region = $_SESSION["region"];
$areaList = $_SESSION["areaList"];
$englandTotal = $_SESSION["englandTotal"];
$combinedTotal = $_SESSION["combinedTotal"];
$type = $_SESSION["type"];

if ($type === "xml") {
    $responseXML = new DOMDocument();
    $base = $responseXML->createElement("reponse");
    $base->setAttribute("timestamp", date("YmdHi"));
    $crimeDataNode = $responseXML->createElement("crimes");
    $crimeDataNode->setAttribute("year", "6-2013");

    $regionNode = $responseXML->createElement("region");
    $regionNode->setAttribute("name", $region->getProperName());
    $regionNode->setAttribute("total", $region->getTotal());

    foreach ($areaList as $area) {
        $areaNode = $responseXML->createElement("deleted");
        $areaNode->setAttribute("id", $area->getProperName());
        $areaNode->setAttribute("total", $area->getTotal());

        $regionNode->appendChild($areaNode);
    }

    $crimeDataNode->appendChild($regionNode);

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
} else {

    $deletedArray = array();

    foreach ($areaList as $area) {
        $array = array("id" => $area->getProperName(), "total" => $area->getTotal());
        $deletedArray[] = $array;
    }

    $crimesData = array("year" => "6-2013");
    $crimesData["region"] = array("id" => $region->getProperName(), "total" => $region->getTotal(), "deleted" => $deletedArray);

    $dataArray = array();
    $dataArray["timestamp"] = date("YmdHi");

    $dataArray["crimes"] = $crimesData;

    $base = array();
    $base["response"] = $dataArray;
    header("Content-type: application/json");
    echo json_encode($base, JSON_PRETTY_PRINT);
}


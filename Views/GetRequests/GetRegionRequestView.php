<?php


$region = $_SESSION["region"];

$responseXML = new DOMDocument();
$base = $responseXML->createElement("reponse");

$base->setAttribute("timestamp", date("YmdHi"));
$crime = $responseXML->createElement("crimes");
$crime->setAttribute("year", "6-2013");

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


$base->appendChild($crime);
$responseXML->appendChild($base);

header("Content-type: text/xml");
echo $responseXML->saveXML();


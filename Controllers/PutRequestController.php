<?php

// This only works with areas
// This doesn't work with wales.

require_once '../Models/AreasModel.php';
$area = $_GET["area"];
$data =  $_GET["data"];
$areasModel = new AreasModel();

$results = $areasModel->UpdateAreaTotal($area, $data); // That's because it's areas are classed as regions, not areas. Wonderful.
// Perhapes a rewrite so that wales has a region of wales within it, and within those are areas?

// So wales is also a region and a country. Just to make shit real confusing!

$oldArea = $results[0];
$newAreaName = $results[1];

$responseXML = new DOMDocument();
$base = $responseXML->createElement("reponse");
$base->setAttribute("timestamp", date("YmdHi"));
$crime = $responseXML->createElement("crimes");
$crime->setAttribute("year", "6-2013");

$regionNode = $responseXML->createElement("region");

$regionNode->setAttribute("id", $oldArea->getName());
$regionNode->setAttribute("previous", $oldArea->getTotal());
$regionNode->setAttribute("total", $newAreaName->getTotal());

$crime->appendChild($regionNode);
header("Content-type: text/xml");
$base->appendChild($crime);
$responseXML->appendChild($base);
echo $responseXML->saveXML();


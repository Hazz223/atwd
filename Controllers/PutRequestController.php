<?php


// echo "This will put something."

// I need to get the change - which will be stored as number and region
// I need to get the region, or area, and then update the value. Wonderful... 
// Looks as though we can actually only update the totals. Pain in the arse, but that's life. 
// Only areas and special cases can be updated, such as wales... Fooking brilliant.
// Do i create a crime model that deals with this?
// I can update a crime based on a name, and a value instead. This should save me from confusion and code??


require_once '../Models/AreasModel.php';
$area = $_GET["area"];
$data =  $_GET["data"];
$areasModel = new AreasModel();

$results = $areasModel->UpdateAreaTotal($area, $data);

$oldArea = $results[0];
$newArea = $results[1];

$responseXML = new DOMDocument();
$base = $responseXML->createElement("reponse");
$base->setAttribute("timestamp", date("YmdHi"));
$crime = $responseXML->createElement("crimes");
$crime->setAttribute("year", "6-2013");

$regionNode = $responseXML->createElement("region");

$regionNode->setAttribute("id", $oldArea->getName());
$regionNode->setAttribute("previous", $oldArea->getTotal());
$regionNode->setAttribute("total", $newArea->getTotal());

$crime->appendChild($regionNode);
header("Content-type: text/xml");
$base->appendChild($crime);
$responseXML->appendChild($base);
echo $responseXML->saveXML();


<?php

$furtherStat = $_SESSION["fStat"];

$responseXML = new DOMDocument();
$base = $responseXML->createElement("reponse");

$base->setAttribute("timestamp", date("YmdHi"));
$crime = $responseXML->createElement("crimes");
$crime->setAttribute("year", "6-2013");

$furtehrStatNode = $responseXML->createElement("national");
$furtehrStatNode->setAttribute("id", $furtherStat->getName());
$furtehrStatNode->setAttribute("total", $furtherStat->getTotal());

$crime->appendChild($furtehrStatNode);

$base->appendChild($crime);
$responseXML->appendChild($base);

header("Content-type: text/xml");
echo $responseXML->saveXML();

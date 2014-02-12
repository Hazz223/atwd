<?php


$responseXML = new DOMDocument();
$base = $responseXML->createElement("reponse");
$base->setAttribute("timestamp", date("YmdHi"));

$errorNode = $responseXML->createElement("error");
$errorNode->setAttribute("code", "501");
$errorNode->setAttribute("desc", "URL Pattern Not Recognised");

$base->appendChild($errorNode);
$responseXML->appendChild($base);

header("Content-type: text/xml");
echo $responseXML->saveXML();
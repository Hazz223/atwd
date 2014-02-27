<?php
/**
 * Description of ErrorView
 * Used for displaying errors from the code
 *
 * @author hlp2-winser
 */
$errorMessage = $_SESSION["errorMessage"];
$errorCode = $_SESSION["errorCode"];

$responseXML = new DOMDocument("1.0");
$base = $responseXML->createElement("reponse");
$base->setAttribute("timestamp", time());

$errorNode = $responseXML->createElement("error");
$errorNode->setAttribute("code", $errorCode);
$errorNode->setAttribute("desc", $errorMessage);

$base->appendChild($errorNode);
$responseXML->appendChild($base);

header("Content-type: text/xml");
echo $responseXML->saveXML();

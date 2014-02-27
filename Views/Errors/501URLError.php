<?php
/**
 * Description of Error 501
 * Used to display a url error
 * All errrors are displayed as XML
 *
 * @author hlp2-winser
 */

$responseXML = new DOMDocument();
$base = $responseXML->createElement("reponse");
$base->setAttribute("timestamp", time());

$errorNode = $responseXML->createElement("error");
$errorNode->setAttribute("code", "501");
$errorNode->setAttribute("desc", "URL Pattern Not Recognised");

$base->appendChild($errorNode);
$responseXML->appendChild($base);

header("Content-type: text/xml");
echo $responseXML->saveXML();
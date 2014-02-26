<?php

/**
 * Description of PutRequestView
 * View for the Put request for both xml and JSON
 * 
 * @author hlp2-winser
 */

$old = $_SESSION["old"]; // Could be either an area or a Further Stats
$new = $_SESSION["new"]; // Could be either an area or a Further Stats
$type = $_SESSION["type"];

if ($type === "xml") {
    $responseXML = new DOMDocument();
    $base = $responseXML->createElement("reponse");
    $base->setAttribute("timestamp", time());
    $crime = $responseXML->createElement("crimes");
    $crime->setAttribute("year", "6-2013");
    $regionNode = $responseXML->createElement("region");

    $regionNode->setAttribute("id", $old->getName());
    $regionNode->setAttribute("previous", $old->getTotal());
    $regionNode->setAttribute("total", $new->getTotal());

    $crime->appendChild($regionNode);
    $base->appendChild($crime);
    $responseXML->appendChild($base);

    header("Content-type: text/xml"); // content type needed
    echo $responseXML->saveXML();
} else { 
    $crimesData = array("year" => "6-2013");
    $crimesData["region"] = array("id" => $new->getName(), "previous" =>$old->getTotal(), "total" => $new->getTotal());

    $dataArray = array();
    $dataArray["timestamp"] = time();

    $dataArray["crimes"] = $crimesData;

    $base = array();
    $base["response"] = $dataArray;
    header("Content-type: application/json");
    echo json_encode($base,JSON_PRETTY_PRINT); // pretty print makes json pretty
}



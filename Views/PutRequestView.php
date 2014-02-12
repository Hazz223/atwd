<?php

$old = $_SESSION["old"];
$new = $_SESSION["new"];
$type = $_SESSION["type"];

if ($type === "xml") {
    $responseXML = new DOMDocument();
    $base = $responseXML->createElement("reponse");
    $base->setAttribute("timestamp", date("YmdHi"));
    $crime = $responseXML->createElement("crimes");
    $crime->setAttribute("year", "6-2013");

    $regionNode = $responseXML->createElement("region");

    $regionNode->setAttribute("id", $old->getName());
    $regionNode->setAttribute("previous", $old->getTotal());
    $regionNode->setAttribute("total", $new->getTotal());

    $crime->appendChild($regionNode);
    $base->appendChild($crime);
    $responseXML->appendChild($base);

    header("Content-type: text/xml");
    echo $responseXML->saveXML();
} else {
    
    $crimesData = array("year" => "6-2013");
    $crimesData["region"] = array("id" => $new->getName(), "previous" =>$old->getTotal(), "total" => $new->getTotal());

    $dataArray = array();
    $dataArray["timestamp"] = date("YmdHi");

    $dataArray["crimes"] = $crimesData;

    $base = array();
    $base["response"] = $dataArray;
    header("Content-type: application/json");
    echo json_encode($base);
}



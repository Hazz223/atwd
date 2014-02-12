<?php

$furtherStat = $_SESSION["fStat"];
$type = $_SESSION["type"];

if ($type === "xml") {
    $responseXML = new DOMDocument();
    $base = $responseXML->createElement("reponse");

    $base->setAttribute("timestamp", date("YmdHi"));
    $crime = $responseXML->createElement("crimes");
    $crime->setAttribute("year", "6-2013");

    $furtehrStatNode = $responseXML->createElement("national");
    $furtehrStatNode->setAttribute("id", $furtherStat->getProperName());
    $furtehrStatNode->setAttribute("total", $furtherStat->getTotal());

    $crime->appendChild($furtehrStatNode);

    $base->appendChild($crime);
    $responseXML->appendChild($base);

    header("Content-type: text/xml");
    echo $responseXML->saveXML();
}
else{
    $crimesData = array("year" => "6-2013");
    $crimesData["national"] = array("id" => $furtherStat->getProperName(), "total" => $furtherStat->getTotal());

    $dataArray = array();
    $dataArray["timestamp"] = date("YmdHi");
    $dataArray["crimes"] = $crimesData;

    $base = array();
    $base["response"] = $dataArray;
    header("Content-type: application/json");
    echo json_encode($base);
}


<?php

/**
 * Description of GetFutherStatRequestView
 * The view just for Futher statis information
 * 
 * @author hlp2-winser
 */

$furtherStat = $_SESSION["fStat"]; // further statistic object
$type = $_SESSION["type"];
$cache = new Cache();

if ($type === "xml") {
    $responseXML = new DOMDocument();
    $base = $responseXML->createElement("response");

    $base->setAttribute("timestamp", date("YmdHi"));
    $crime = $responseXML->createElement("crimes");
    $crime->setAttribute("year", "6-2013");

    $furtehrStatNode = $responseXML->createElement("national");
    $furtehrStatNode->setAttribute("id", $furtherStat->getProperName());
    $furtehrStatNode->setAttribute("total", $furtherStat->getTotal());

    $crime->appendChild($furtehrStatNode);

    $base->appendChild($crime);
    $responseXML->appendChild($base);

    // Create new cache file
    $cache->createCacheFile($furtherStat->getName()."-cache", $responseXML, $type);
    header("Content-type: text/xml"); // content type needed
    echo $responseXML->saveXML();
}
else{
    $crimesData = array("year" => "6-2013");
    $crimesData["national"] = array("id" => $furtherStat->getProperName(), "total" => $furtherStat->getTotal());

    $dataArray = array();
    $dataArray["timestamp"] = time();
    $dataArray["crimes"] = $crimesData;

    $base = array();
    $base["response"] = $dataArray;
    header("Content-type: application/json"); // content type needed
    $fullJson = json_encode($base, JSON_PRETTY_PRINT); // pretty is the pretty print
    
    // creates the cache file
    $cache->createCacheFile($furtherStat->getName()."-cache", $fullJson, $type); 
    echo $fullJson;
}


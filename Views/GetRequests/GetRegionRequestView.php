<?php
/**
 * Description of GetRegionRequestView
 * View for the Region get request.
 *
 * You can search for wales, as it's also region!
 * @author hlp2-winser
 */

$region = $_SESSION["region"]; // region object
$areas = $_SESSION["areas"]; // area object list
$type = $_SESSION["type"];
$cache = new Cache();

if ($type === "xml") {
    $responseXML = new DOMDocument();
    $base = $responseXML->createElement("response");

    $base->setAttribute("timestamp", time());
    $crime = $responseXML->createElement("crimes");
    $crime->setAttribute("year", "6-2013");

    $regionNode = $responseXML->createElement("region");
    $regionNode->setAttribute("id", $region->getProperName());
    $regionNode->setAttribute("total", $region->getTotal());

    foreach ($areas as $area) { // Adds all of the area nodes
        $areaNode = $responseXML->createElement("area");
        $areaNode->setAttribute("id", $area->getProperName());
        $areaNode->setAttribute("total", $area->getTotal());
        $regionNode->appendChild($areaNode);
    }

    $crime->appendChild($regionNode);
    
    $base->appendChild($crime);
    $responseXML->appendChild($base);
    
    // Create the cache for this request
    $cache->createCacheFile($region->getName()."-cache", $responseXML, $type);
    header("Content-type: text/xml"); // need this!
    echo $responseXML->saveXML();
} else {

    $areasArray = array();
    foreach ($areas as $area) { // cycle through the areas and add it to the json
        $array = array("id" => $area->getProperName(), "total" => $area->getTotal());
        $areasArray[] = $array;
    }
    
    $crimeData = array("year" => "6-2013");
    $crimeData["region"] = array("id" => $region->getProperName(), "total" => $region->getTotal(), "areas" => $areasArray);

    $dataArray = array();
    $dataArray["timestamp"] = time(); // unix time stamp

    $dataArray["crimes"] = $crimeData;

    $base = array();
    $base["response"] = $dataArray;
    header("Content-type: application/json");
    $fullJson = json_encode($base, JSON_PRETTY_PRINT); // pretty print is pretty
    
    $cache->createCacheFile($region->getName()."-cache", $fullJson, $type); // create the cache file
    echo $fullJson;
}



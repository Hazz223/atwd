<?php
/**
 * Description of PostRequestView
 * View for the Post requests. 
 * @author hlp2-winser
 */

$area = $_SESSION["area"]; // area object
$region = $_SESSION["region"]; // region object
$type = $_SESSION["type"];

$englandTotal = $_SESSION["englandTotal"]; // int
$combinedTotal = $_SESSION["combinedTotal"]; // int

if ($type === "xml") {
    $responseXML = new DOMDocument("1.0");
    $base = $responseXML->createElement("reponse");
    $base->setAttribute("timestamp", time()); // unix time
    $crimeDataNode = $responseXML->createElement("crimes");
    $crimeDataNode->setAttribute("year", "6-2013");

    $regionNode = $responseXML->createElement("region");
    $regionNode->setAttribute("id", $region->getProperName());
    $regionNode->setAttribute("total", $region->getTotal());

    $areaNode = $responseXML->createElement("area");
    $areaNode->setAttribute("name", $area->getProperName());
    $areaNode->setAttribute("total", $area->getTotal());

    // foreach crime catagory contained inside an area
    foreach ($area->getCrimeData() as $crimeCats) {
        if ($crimeCats->getCrimeType() != "Total") {
            $crimeList = $crimeCats->getCrimeList();
            if (isset($crimeList)) { // if it has a crimes
                foreach ($crimeList as $crime) {
                    $crimeNode = $responseXML->createElement("recorded");
                    $crimeNode->setAttribute("id", $crime->getName());
                    $crimeNode->setAttribute("total", $crime->getValue());

                    $areaNode->appendChild($crimeNode);
                }
            } else { // just append the crime catagory
                $crimeCatNode = $responseXML->createElement("recorded");
                $crimeCatNode->setAttribute("id", $crimeCats->getProperName());
                $crimeCatNode->setAttribute("total", $crimeCats->getTotal());

                $areaNode->appendChild($crimeCatNode);
            }
        }
    }

    $regionNode->appendChild($areaNode);

    $crimeDataNode->appendChild($regionNode);

    $englandTotalNode = $responseXML->createElement("england");
    $englandTotalNode->setAttribute("total", $englandTotal);

    $combinedTotalNode = $responseXML->createElement("england_wales");
    $combinedTotalNode->setAttribute("total", $combinedTotal);

    $crimeDataNode->appendChild($englandTotalNode);
    $crimeDataNode->appendChild($combinedTotalNode);
    
    $base->appendChild($crimeDataNode);
    $responseXML->appendChild($base);
    header("Content-type: text/xml"); // content type needed for correct output
    echo $responseXML->saveXML();
} else {
   
    $addedArray = array();
    foreach ($area->getCrimeData() as $crimeCat) { // crime catagory list
        if ($crimeCat->getCrimeType() != "Total") {
            $crimeList = $crimeCat->getCrimeList();
            if (count($crimeList) > 0) {
                foreach ($crimeList as $crime) { // if crimes, create that information
                    $array = array("id" => $crime->getName(), "total" => $crime->getValue());
                    $addedArray[] = $array;
                }
            } else { // just continue as normal if no crimes available
                $array = array("id" => $crimeCat->getName(), "total" => $crimeCat->getTotal());
                $addedArray[] = $array;
            }
        }
    }


    $crimesData = array("year" => "6-2013");
    $areaArray = array("id" => $area->getProperName(), "total" => $area->getTotal(), "recorded" => $addedArray);
    $crimesData["region"] = array("id" => $region->getProperName(), "total" => $region->getTotal(), "area" => $areaArray);

    $dataArray = array();
    $dataArray["timestamp"] = time(); // unix time
    $dataArray["crimes"] = $crimesData;

    $base = array();
    $base["response"] = $dataArray;
    header("Content-type: application/json"); // content type needed
    echo json_encode($base, JSON_PRETTY_PRINT); // pretty print is pretty
}


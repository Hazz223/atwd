<?php
/**
 * Description of DeleteAreaView
 * Displays the view for deleting an area
 *
 * @author hlp2-winser
 */

$area = $_SESSION["area"]; // area object
$englandTotal = $_SESSION["englandTotal"]; // int total
$combinedTotal = $_SESSION["combinedTotal"]; // int total
$type = $_SESSION["type"];

if ($type === "xml") {
    // Create the intial part of the xml
    $responseXML = new DOMDocument("1.0");
    $base = $responseXML->createElement("response");
    $base->setAttribute("timestamp", time());
    $crimeDataNode = $responseXML->createElement("crimes");
    $crimeDataNode->setAttribute("year", "6-2013");

    $areaNode = $responseXML->createElement("area");
    $areaNode->setAttribute("name", strtolower($area->getProperName()));
    $areaNode->setAttribute("total", $area->getTotal());
    
    // Populate the deleted information
    foreach ($area->getCrimeData() as $crimeCats) {
        if ($crimeCats->getCrimeType() != "Total") {
            $crimeList = $crimeCats->getCrimeList();
            if (count($crimeList) > 0) {
                foreach ($crimeList as $crime) {
                    $crimeNode = $responseXML->createElement("deleted");
                    $crimeNode->setAttribute("id", $crime->getName());
                    $crimeNode->setAttribute("total", $crime->getValue());

                    $areaNode->appendChild($crimeNode);
                }
            } else {
                $crimeCatNode = $responseXML->createElement("deleted");
                $crimeCatNode->setAttribute("id", $crimeCats->getName());
                $crimeCatNode->setAttribute("total", $crimeCats->getTotal());

                $areaNode->appendChild($crimeCatNode);
            }
        }
    }
    
    //append the area information, as well as the country totals etc
    $crimeDataNode->appendChild($areaNode);

    $englandTotalNode = $responseXML->createElement("england");
    $englandTotalNode->setAttribute("total", $englandTotal);

    $combinedTotalNode = $responseXML->createElement("england_wales");
    $combinedTotalNode->setAttribute("total", $combinedTotal);

    $crimeDataNode->appendChild($englandTotalNode);
    $crimeDataNode->appendChild($combinedTotalNode);

    $base->appendChild($crimeDataNode);
    $responseXML->appendChild($base);
    header("Content-type: text/xml"); // output the correct content type
    echo $responseXML->saveXML();
} else {

    // Create the data array, which then becomes the json
    $deletedArray = array();
    foreach ($area->getCrimeData() as $crimeCat) { // each crime cat
        if ($crimeCat->getCrimeType() != "Total") {
            $crimeList = $crimeCat->getCrimeList();
            if (count($crimeList) > 0) { // Crime catagory might not have crimes associated with it. So we just output this catagory
                foreach ($crimeList as $crime) {
                    $array = array("id" => $crime->getName(), "total" => $crime->getValue());
                    $deletedArray[] = $array;
                }
            } else {
                $array = array("id" => $crimeCat->getName(), "total" => $crimeCat->getTotal());
                $deletedArray[] = $array;
            }
        }
    }

    $crimesData = array("year" => "6-2013");
    $crimesData["area"] = array("id" => strtolower($area->getProperName()), "total" => $area->getTotal(), "deleted" => $deletedArray); // storing the data
    
    $crimesData["england"] = array("total"=> $englandTotal); 
    $crimesData["england_wales"] = array("total"=> $combinedTotal); 
    
    $dataArray = array();
    $dataArray["timestamp"] = time();

    $dataArray["crimes"] = $crimesData;

    $base = array();
    $base["response"] = $dataArray;
    header("Content-type: application/json"); // content type needed
    echo json_encode($base, JSON_PRETTY_PRINT); // pretty print makes it look pretty.
}

<?php
/**
 * Description of DeleteRegionView
 * Displays the information for deleting a region.
 * Very similar to the Area view one.
 *
 * @author hlp2-winser
 */

$region = $_SESSION["region"]; // region object
$areaList = $_SESSION["areaList"]; // list of area objects
$englandTotal = $_SESSION["englandTotal"]; // int
$combinedTotal = $_SESSION["combinedTotal"]; // int
$type = $_SESSION["type"];

if ($type === "xml") {
    $responseXML = new DOMDocument();
    $base = $responseXML->createElement("reponse");
    $base->setAttribute("timestamp", time()); // unix time
    $crimeDataNode = $responseXML->createElement("crimes");
    $crimeDataNode->setAttribute("year", "6-2013");

    $regionNode = $responseXML->createElement("region");
    $regionNode->setAttribute("name", $region->getProperName());
    $regionNode->setAttribute("total", $region->getTotal());

    foreach ($areaList as $area) { // goes through each area, and states that it was deleted
        $areaNode = $responseXML->createElement("deleted");
        $areaNode->setAttribute("id", $area->getProperName());
        $areaNode->setAttribute("total", $area->getTotal());

        $regionNode->appendChild($areaNode);
    }

    $crimeDataNode->appendChild($regionNode);

    // Adds the extra bits of information - country total etc
    $englandTotalNode = $responseXML->createElement("england");
    $englandTotalNode->setAttribute("total", $englandTotal);

    $combinedTotalNode = $responseXML->createElement("england_wales");
    $combinedTotalNode->setAttribute("total", $combinedTotal);

    $crimeDataNode->appendChild($englandTotalNode);
    $crimeDataNode->appendChild($combinedTotalNode);

    $base->appendChild($crimeDataNode);
    $responseXML->appendChild($base);
    header("Content-type: text/xml"); // content type needed
    echo $responseXML->saveXML(); // outputs it to screen
} else {

    $deletedArray = array();

    foreach ($areaList as $area) { // creates array for areas deleted
        $array = array("id" => $area->getProperName(), "total" => $area->getTotal());
        $deletedArray[] = $array;
    }

    $crimesData = array("year" => "6-2013");
    // region array for region information, includes area stuff
    $crimesData["region"] = array("id" => $region->getProperName(), "total" => $region->getTotal(), "deleted" => $deletedArray);

    $dataArray = array();
    $dataArray["timestamp"] = time(); // unix time

    $dataArray["crimes"] = $crimesData;

    $base = array();
    $base["response"] = $dataArray;
    header("Content-type: application/json");
    echo json_encode($base, JSON_PRETTY_PRINT); // pretty print looks pretty
}


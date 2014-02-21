<?php

$area = $_SESSION["area"];
$region = $_SESSION["region"];
$type = $_SESSION["type"];
$englandTotal = $_SESSION["englandTotal"];
$combinedTotal = $_SESSION["combinedTotal"];

if ($type === "xml") {
    $responseXML = new DOMDocument();
    $base = $responseXML->createElement("reponse");
    $base->setAttribute("timestamp", time());
    $crimeDataNode = $responseXML->createElement("crimes");
    $crimeDataNode->setAttribute("year", "6-2013");

    $regionNode = $responseXML->createElement("region");
    $regionNode->setAttribute("id", $region->getProperName());
    $regionNode->setAttribute("total", $region->getTotal());

    $areaNode = $responseXML->createElement("area");
    $areaNode->setAttribute("name", $area->getProperName());
    $areaNode->setAttribute("total", $area->getTotal());

    foreach ($area->getCrimeData() as $crimeCats) {
        if ($crimeCats->getCrimeType() != "Total") {
            $crimeList = $crimeCats->getCrimeList();
            if (isset($crimeList)) {
                foreach ($crimeList as $crime) {
                    $crimeNode = $responseXML->createElement("recorded");
                    $crimeNode->setAttribute("id", $crime->getName());
                    $crimeNode->setAttribute("total", $crime->getValue());

                    $areaNode->appendChild($crimeNode);
                }
            } else {
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
    header("Content-type: text/xml");
    echo $responseXML->saveXML();
} else {
   
    $addedArray = array();
    foreach ($area->getCrimeData() as $crimeCat) {
        if ($crimeCat->getCrimeType() != "Total") {
            $crimeList = $crimeCat->getCrimeList();
            if (count($crimeList) > 0) {
                foreach ($crimeList as $crime) {
                    $array = array("id" => $crime->getName(), "total" => $crime->getValue());
                    $addedArray[] = $array;
                }
            } else {
                $array = array("id" => $crimeCat->getName(), "total" => $crimeCat->getTotal());
                $addedArray[] = $array;
            }
        }
    }


    $crimesData = array("year" => "6-2013");
    $areaArray = array("id" => $area->getProperName(), "total" => $area->getTotal(), "recorded" => $addedArray);
    $crimesData["region"] = array("id" => $region->getProperName(), "total" => $region->getTotal(), "area" => $areaArray);

    $dataArray = array();
    $dataArray["timestamp"] = time();
    $dataArray["crimes"] = $crimesData;

    $base = array();
    $base["response"] = $dataArray;
    header("Content-type: application/json");
    echo json_encode($base, JSON_PRETTY_PRINT);
}


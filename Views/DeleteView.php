<?php

$area = $_SESSION["area"];
$englandTotal = $_SESSION["englandTotal"];
$combinedTotal = $_SESSION["combinedTotal"];
$type = $_SESSION["type"];
if ($type === "xml") {
    $responseXML = new DOMDocument();
    $base = $responseXML->createElement("reponse");
    $base->setAttribute("timestamp", date("YmdHi"));
    $crimeDataNode = $responseXML->createElement("crimes");
    $crimeDataNode->setAttribute("year", "6-2013");

    $areaNode = $responseXML->createElement("area");
    $areaNode->setAttribute("name", $area->getProperName());
    $areaNode->setAttribute("total", $area->getTotal());

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

    $crimeDataNode->appendChild($areaNode);

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
    $regionArray = array();

    $deletedArray = array();
    foreach ($area->getCrimeData() as $crimeCat) {
        if ($crimeCat->getCrimeType() != "Total") {
            $crimeList = $crimeCat->getCrimeList();
            if (count($crimeList) > 0) {
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
    $crimesData["area"] = array("id" => $area->getProperName(), "total" => $area->getTotal(), "deleted" => $deletedArray);

    $dataArray = array();
    $dataArray["timestamp"] = date("YmdHi");

    $dataArray["crimes"] = $crimesData;

    $base = array();
    $base["response"] = $dataArray;
    header("Content-type: application/json");
    echo json_encode($base);
}

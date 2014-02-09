<?php

$regions = $_SESSION["regions"];
$countries = $_SESSION["countries"];
$fStats = $_SESSION["fStats"];

$responseXML = new DOMDocument();
$base = $responseXML->createElement("reponse");

$base->setAttribute("timestamp", date("YmdHi"));
$crime = $responseXML->createElement("crimes");
$crime->setAttribute("year", "6-2013");


foreach ($regions as $region) {
    $name = $region->getName();
    if ($name != "WALES") {
        $regionNode = $responseXML->createElement("region");
        $regionNode->setAttribute("id", $name);

        $regionNode->setAttribute("total", $region->getTotal());
        $crime->appendChild($regionNode);
    }
}

foreach ($countries as $country) {
    $name = $country->getName();
    $countryNode = $responseXML->createElement(strtolower($name));
    $countryNode->setAttribute("total", $country->getTotal());

    $crime->appendChild($countryNode);
}

foreach ($fStats as $stat) {
    $statNode = $responseXML->createElement("national");
    $statNode->setAttribute("id", $stat->getName());
    $statNode->setAttribute("total", $stat->getTotal());
    $crime->appendChild($statNode);
}


$base->appendChild($crime);
$responseXML->appendChild($base);

header("Content-type: text/xml");
echo $responseXML->saveXML();


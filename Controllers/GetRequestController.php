<?php

if (isset($_GET["region"])) {
    $regionNamesasdas = $_GET["region"]; // this needs to be changed to the safer version

    echo $regionNamesasdas . "</br>";
} else {
    require_once '../Models/RegionsModel.php';
    require_once '../Models/CountriesModel.php';

    $countryModel = new CountriesModel();
    $regionModel = new RegionsModel();

    $regions = $regionModel->getAllRegions(); // gets all regions correctly.

    $countries = $countryModel->GetAllCounties();

    $responseXML = new DOMDocument();
    $base = $responseXML->createElement("reponse");

    $base->setAttribute("timestamp", date("YmdHi"));
    $crime = $responseXML->createElement("crime");
    $crime->setAttribute("year", "6-2013");

    foreach ($regions as $region) {
        $name = $region->getName();
        $regionNode = $responseXML->createElement("region");
        $regionNode->setAttribute("id", $name);

        $regionNode->setAttribute("total", $region->getTotal());
        $crime->appendChild($regionNode);
    }
    
    foreach($countries as $country){
        $name = $country->getName();
        $countryNode = $responseXML->createElement(strtolower($name));
        $countryNode->setAttribute("total", $country->getTotal());
        
        $crime->appendChild($countryNode);
    }
    
    
    

    header("Content-type: text/xml");
    $base->appendChild($crime);
    $responseXML->appendChild($base);
    echo $responseXML->saveXML();
}




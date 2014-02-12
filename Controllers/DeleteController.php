<?php

$deleteItem = $_GET["data"];
//I need to do a check to see if this is an area, region, country or furtherStat
require_once '../Models/CountriesModel.php';
require_once '../Models/RegionsModel.php';
require_once '../Models/AreasModel.php';
require_once '../Models/FurtherStatisticsModel.php';
require_once '../Entities/Area.php';
require_once '../Entities/Region.php';
require_once '../Entities/Country.php';

$countryModel = new CountriesModel();
$regionModel = new RegionsModel();
$areaModel = new AreasModel();
$fStatsModel = new FurtherStatisticsModel();

if ($areaModel->isArea($deleteItem)) {
    
    $deletedArea = $areaModel->getAreaByName($deleteItem);
    //$areaNode = $areaModel->DeleteArea($deleteItem);

    $englishRegions = $regionModel->getRegionsByCountry("ENGLAND");
    $welshRegions = $regionModel->getRegionsByCountry("WALES");
    
    $england = $countryModel->getCountryByName("ENGLAND", $englishRegions);
    $wales = $countryModel->getCountryByName("WALES", $welshRegions);
    
    $_SESSION["area"] =  $deletedArea;
    $_SESSION["englandTotal"] = $england->getTotal();
    $combinedTotal = $wales->getTotal() + $england->getTotal();
    $_SESSION["combinedTotal"] = $combinedTotal;
    
    $_SESSION["type"] = $_GET["type"];
    // send all this stuff to the view.
    include "../Views/DeleteView.php";
}
else{
    $_SESSION["errorCode"] = 404;
    $_SESSION["errorMessage"] = "could not find area with name: ".$deleteItem;
    include "../Views/Errors/ErrorView.php";
}








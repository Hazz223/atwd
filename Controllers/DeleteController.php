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

$hasDeleted = false;

if ($areaModel->isArea($deleteItem)) {
    $deletedArea = $areaModel->getAreaByName($deleteItem);
    $areaModel->DeleteArea($deleteItem);

    $_SESSION["area"] = $deletedArea;
    $englishRegions = $regionModel->getRegionsByCountry("ENGLAND");
    $welshRegions = $regionModel->getRegionsByCountry("WALES");

    $england = $countryModel->getCountryByName("ENGLAND");
    $wales = $countryModel->getCountryByName("WALES");
    $combinedTotal = $wales->getTotal() + $england->getTotal();

    $_SESSION["englandTotal"] = $england->getTotal();
    $_SESSION["combinedTotal"] = $combinedTotal;
    $_SESSION["type"] = $_GET["type"];

    $hasDeleted = true;
    include "../Views/DeleteAreaView.php";
}

if ($regionModel->isRegion($deleteItem)) {
    $deletedRegion = $regionModel->getRegionByName($deleteItem);
    $areaNames = $deletedRegion->getAreaNames();
   
    $areaData = array();
    foreach ($areaNames as $areaName) {
        $areaData[] = $areaModel->getAreaByName($areaName);
    }

    $regionModel->DeleteRegion($deleteItem);

    $england = $countryModel->getCountryByName("ENGLAND");
    $wales = $countryModel->getCountryByName("WALES");
    $combinedTotal = $wales->getTotal() + $england->getTotal();

    $_SESSION["englandTotal"] = $england->getTotal();
    $_SESSION["combinedTotal"] = $combinedTotal;
    $_SESSION["type"] = $_GET["type"];
    $_SESSION["region"] = $deletedRegion;
    $_SESSION["areaList"] = $areaData;

    $hasDeleted = true;
    include "../Views/DeleteRegionView.php";
}

if (!$hasDeleted) {


    $_SESSION["errorCode"] = 404;
    $_SESSION["errorMessage"] = "could not find area with name: " . $deleteItem;
    include "../Views/Errors/ErrorView.php";
}


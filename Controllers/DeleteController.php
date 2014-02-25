<?php
/**
 * Description of DeleteController
 * The controller to Delete either a Region or Area from the data
 * Based on their name
 * 
 * @author hlp2-winser
 */

require_once '../Models/CountriesModel.php';
require_once '../Models/RegionsModel.php';
require_once '../Models/AreasModel.php';
require_once '../Models/FurtherStatisticsModel.php';
require_once '../Entities/Area.php';
require_once '../Entities/Region.php';
require_once '../Entities/Country.php';

$deleteItem = $_GET["data"];

$countryModel = new CountriesModel();
$regionModel = new RegionsModel();
$areaModel = new AreasModel();

$hasDeleted = false;

if ($areaModel->isArea($deleteItem)) {
    // if deleting an area
    
    $deletedArea = $areaModel->getAreaByName($deleteItem); // object before
    
    $areaModel->DeleteArea($deleteItem);

    $england = $countryModel->getCountryByName("ENGLAND"); // get country objects
    $wales = $countryModel->getCountryByName("WALES");
    
    $combinedTotal = $wales->getTotal() + $england->getTotal();

    $_SESSION["area"] = $deletedArea;
    $_SESSION["englandTotal"] = $england->getTotal();
    $_SESSION["combinedTotal"] = $combinedTotal;
    $_SESSION["type"] = $_GET["type"];

    $hasDeleted = true;
    include "../Views/DeleteAreaView.php";
}

if ($regionModel->isRegion($deleteItem)) {
    // If region
    
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
    // throw error if you can't find that area or region!
    $_SESSION["errorCode"] = 404;
    $_SESSION["errorMessage"] = "could not find area with name: " . $deleteItem;
    include "../Views/Errors/ErrorView.php";
}


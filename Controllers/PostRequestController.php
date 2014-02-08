<?php


require_once '../Models/RegionsModel.php';
require_once '../Models/AreasModel.php';
require_once '../Models/CrimeConfig.php';
require_once '../Entities/Area.php';
require_once '../Entities/CrimeCatagory.php';
require_once '../Entities/Crime.php';

$uriArray = DecodeRequestURI($_SERVER['REQUEST_URI']);

$region = $uriArray[5];
$newAreaName = $uriArray[6];
$data = $uriArray[7];
$viewType = $uriArray[8];

$crimeDataAbrivated = DecodeCrimeData($data);

$regionModel = new RegionsModel();


$newArea = new Area();
$newArea->setRegionName($region);
$newArea->setName($newAreaName);

$regionModel->addAreaToRegion($newArea);


$areaModel = new AreasModel();

$crimeCats = createCrimeCategoryData($crimeDataAbrivated);
$crimes = createCrimeData($crimeDataAbrivated);

foreach ($crimeCats as $crime) {
    $areaModel->AddCrimeCategory($crime, $newAreaName);
}

foreach ($crimes as $crime) {
    $areaModel->addCrimeToArea($crime, $newAreaName);
}


$_SESSION["area"] = $areaModel->getAreaByName($newAreaName);
$_SESSION["region"] = $regionModel->getRegionByName($region); 
include "../Views/PostRequestView.php";

function DecodeRequestURI($uri) {
    return explode("/", $uri);
}

function DecodeCrimeData($data) {
    $splitData = explode("-", $data);

    $crimeDataArray = array();

    foreach ($splitData as $crime) {

        $crimeArray = explode(":", $crime);
        $crimeDataArray[$crimeArray[0]] = $crimeArray[1];
    }

    return $crimeDataArray;
}

function createCrimeData($crimeDataArray) {
    $crimeConfigModel = new CrimeConfig();

    $crimeArray = array(); // If catagory object, then we need to store it in here. 
    // If it's not, we need to find the catagory it belongs too, then append it to that one.

    foreach ($crimeDataArray as $name => $value) {
        if (!$crimeConfigModel->CheckIfCrimeCategory($name)) { 
            
            $newCrime = new Crime();
            $newCrime->setName($crimeConfigModel->GetCrimeName($name));
            $newCrime->setValue($value);
            $newCrime->setCrimeCatagory($crimeConfigModel->GetCrimeCatagory($name));
            $newCrime->setCrimeType($crimeConfigModel->GetCrimeType($name));
            $crimeArray[] = $newCrime;
        }
    }

    return $crimeArray;
}

function createCrimeCategoryData($crimeDataArray) {
    $crimeConfigModel = new CrimeConfig();

    $crimeCatArray = array();
    
    foreach ($crimeDataArray as $name => $value) {
        if ($crimeConfigModel->CheckIfCrimeCategory($name)) { // Can't find the node. Lame!
            
            $newCrimeCat = new CrimeCatagory();
            $newCrimeCat->setTotal($value);
            $newCrimeCat->setName($crimeConfigModel->GetCrimeName($name));
            $newCrimeCat->setCrimeType($crimeConfigModel->GetCrimeType($name));
            $crimeCatArray[] = $newCrimeCat;
        }
    }
    return $crimeCatArray;
}

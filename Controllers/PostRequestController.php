<?php

require_once '../Models/RegionsModel.php';
require_once '../Models/AreasModel.php';
require_once '../Models/CountriesModel.php';
require_once '../Models/CrimeConfig.php';
require_once '../Models/CountriesModel.php';
require_once '../Entities/Area.php';
require_once '../Entities/CrimeCatagory.php';
require_once '../Entities/Crime.php';
require_once '../Exceptions/AreaAlreadyExists.php';

$uriArray = DecodeRequestURI($_SERVER['REQUEST_URI']);

$region = $uriArray[6];
$newAreaName = $uriArray[7];
$data = $uriArray[8];
$viewType = $uriArray[9];

$crimeDataAbrivated = DecodeCrimeData($data);

try {
    $areaModel = new AreasModel();
    $regionModel = new RegionsModel();
    $countryModel = new CountriesModel();

    if($areaModel->isArea($newAreaName)){
        throw new AreaAlreadyExists("Area with name '". $newAreaName."' already exsits.");
    }
    $new = new Area();
    $new->setRegionName($region);
    $new->setName($newAreaName);
    $new->setProperName(ucfirst($newAreaName));

    $regionModel->addAreaToRegion($new);

    $crimeCats = createCrimeCategoryData($crimeDataAbrivated);
    $crimes = createCrimeData($crimeDataAbrivated);

    foreach ($crimeCats as $crime) {
        $areaModel->AddCrimeCategory($crime, $newAreaName);
    }

    foreach ($crimes as $crime) {
        $areaModel->addCrimeToArea($crime, $newAreaName);
    }
    
    $england = $countryModel->getCountryByName("ENGLAND");
    $wales = $countryModel->getCountryByName("WALES");
    $combinedTotal = $wales->getTotal() + $england->getTotal();

    $_SESSION["englandTotal"] = $england->getTotal();
    $_SESSION["combinedTotal"] = $combinedTotal;
    $_SESSION["type"] = $viewType;
    $_SESSION["area"] = $areaModel->getAreaByName($newAreaName);
    $_SESSION["region"] = $regionModel->getRegionByName($region);
    
    include "../Views/PostRequestView.php";
    
} catch (AreaAlreadyExists $ex) {
    $_SESSION["errorMessage"] = $ex->getMessage();
    $_SESSION["errorCode"] = $ex->getCode();

    include "../Views/Errors/ErrorView.php";
} catch (FieldNotFoundException $ex) {
    $_SESSION["errorMessage"] = $ex->getMessage();
    $_SESSION["errorCode"] = $ex->getCode();

    include "../Views/Errors/ErrorView.php";
} catch (Exception $ex) {
    $_SESSION["errorMessage"] = $ex->getMessage();
    $_SESSION["errorCode"] = 500;

    include "../Views/Errors/ErrorView.php";
}

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
            $newCrime->setName($crimeConfigModel->getCrimeName($name));
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
            $newCrimeCat->setName($crimeConfigModel->getCrimeName($name));
            $newCrimeCat->setCrimeType($crimeConfigModel->GetCrimeType($name));
            $crimeCatArray[] = $newCrimeCat;
        }
    }
    return $crimeCatArray;
}

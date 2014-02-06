<?php

// Standard request would be
// http://www.cems.uwe.ac.uk/~<yourusername>/atwd/crimes/6-2013/post/south_west/wessex/hom:4-vwi:15-vwoi:25/xml
// Need to create mappings for hom, vwi and vwoi. 
// Each different crime types need to be mapped accordingly, and as such then need to be placed within the correct nodes of the xml. 
// This will only ADD areas to regions. As a result, in the Areas model, we can create a standard way of doing things... I guess?
// Either way, we need a way to check the mappings/ convert them
// Could do it in this controller. This mapping isn't needed elsewhere! 
// When we create a new area, we need to create the whole thing. So crimes as well as their catagories. 
// This will all be set up in the Area stuff. I'm not a huge fan of this, as it feels like it's not fit for purpose...
// I could extract this out later. 
// Create the node. Populate it with all the crime stuff. Then bam. Done!
// Do mapping conversion here. So array ["Name of crime"] = "data"
// Then create the total stuff based off all of this information. 
// Instead we will use the URI to get the information. This a far easier way to deal with it than via the .htaccess file. 

require_once '../Models/RegionsModel.php';
require_once '../Models/AreasModel.php';
require_once '../Models/CrimeConfig.php';
require_once '../Entities/Area.php';
require_once '../Entities/CrimeCatagory.php';
require_once '../Entities/Crime.php';

$uriArray = DecodeRequestURI($_SERVER['REQUEST_URI']);

var_dump($uriArray);

$region = $uriArray[5];
$newAreaName = $uriArray[6];
$data = $uriArray[7];
$viewType = $uriArray[8];

$crimeDataAbrivated = DecodeCrimeData($data);

$crimeData = CreateMappedData($crimeDataAbrivated);

$regionModel = new RegionsModel();


$fakeArea = new Area();
$fakeArea->setRegionName($region);
$fakeArea->setName($newAreaName);

$regionModel->addAreaToRegion($fakeArea);

$areaModel = new AreasModel();


$crimeCats = createCrimeCategoryData($crimeData);
$crimes = createCrimeData($crimeData);

foreach ($crimeCats as $crime) {
    $areaModel->addCrimeCatagories($crime, $areaName);
}

foreach ($crimes as $crime) {
    $areaModel->addCrimeToArea($crime, $areaName);
}

echo "It is done";

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

    $crimeCatArray = array(); // If catagory object, then we need to store it in here. 
    // If it's not, we need to find the catagory it belongs too, then append it to that one.


    foreach ($crimeDataArray as $name => $value) {
        if ($crimeConfigModel->CheckIfCrimeCategory($name)) {
            
            $newCrimeCatObject = new CrimeCatagory();

            $newCrimeCatObject->setName($crimeConfigModel->GetCrimeName($name));
            $newCrimeCatObject->setTotal($value);
            
            $crimeCatArray[] = $newCrimeCatObject;
        }
    }

    return $crimeCatArray;
}

function createCrimeCategoryData($crimeDataArray) {
    $crimeConfigModel = new CrimeConfig();

    $crimeArray = array(); // If catagory object, then we need to store it in here. 
    // If it's not, we need to find the catagory it belongs too, then append it to that one.


    foreach ($crimeDataArray as $name => $value) {
        if (!$crimeConfigModel->CheckIfCrimeCategory($name)) {
            
            $newCrime = new Crime();

            $newCrime->setName($crimeConfigModel->GetCrimeName($name));
            $newCrime->setValue($value);
            $newCrime->setCrimeCatagory($crimeConfigModel->GetCrimeCatagory($name));
            $crimeArray[] = $newCrime;
        }
    }

    return $crimeArray;
}

<?php
//Need to access the crime config file, and get all the crimes and pass them to the session
require_once '../Models/CrimeConfig.php';
require_once '../Models/RegionsModel.php';
require_once '../Models/CountriesModel.php';
require_once '../Models/AreasModel.php';

$regionModel = new RegionsModel();
$areasModel = new AreasModel();
$coutnriesModel = new CountriesModel();
$crimeConf = new CrimeConfig();

$crimeNamesAbr = $crimeConf->getAllNamesAndAbvNames();


$regions = $regionModel->getAllRegions();

$areaNameArray = array();
$regionNames = array();
foreach($regions as $region){
   $areaNameArray = array_merge($areaNameArray,$region->getAreaNames());
   $regionNames[] = $region->getProperName();
}

$_SESSION["areaNames"] = $areaNameArray; // can i create an array the same as the crimeAbrv
$_SESSION["regionNames"] = $regionNames;
$_SESSION["crimeNamesAbv"] = $crimeNamesAbr;
include "../Views/HomeView.php"; 


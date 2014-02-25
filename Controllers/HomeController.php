<?php

/**
 * Description of HomeController
 * Controller than supplies the client interface with information
 * 
 * @author hlp2-winser
 */

require_once '../Models/CrimeConfig.php';
require_once '../Models/RegionsModel.php';
require_once '../Models/CountriesModel.php';
require_once '../Models/AreasModel.php';

$regionModel = new RegionsModel();
$crimeConf = new CrimeConfig();

$crimeNamesAbr = $crimeConf->getAllNamesAndAbvNames();

$regions = $regionModel->getAllRegions();

$areaNameArray = array();
$regionNames = array();

foreach($regions as $region){
   $areaNameArray = array_merge($areaNameArray,$region->getAreaNames());
   $regionNames[] = $region->getProperName();
}

$_SESSION["areaNames"] = $areaNameArray;
$_SESSION["regionNames"] = $regionNames;
$_SESSION["crimeNamesAbv"] = $crimeNamesAbr;

include "../Views/HomeView.php"; 


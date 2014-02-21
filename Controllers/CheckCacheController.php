<?php

require_once '../Models/CrimeConfig.php';
$date = $_GET["date"];

$crimeConf = new CrimeConfig();

$fileLastMod = filemtime($crimeConf->GetDataXMLName()); // need to get this from config file
$_SESSION["result"] = ($date > $fileLastMod);
$_SESSION["lastModified"] = $fileLastMod;
$_SESSION["givenTimeStamp"] = $date;
include "../Views/CheckCacheView.php";
?>

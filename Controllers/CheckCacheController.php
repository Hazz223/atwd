<?php

/**
 * Description of CheckCacheController
 * Used by the interface to check if local storage is new or not.
 * Better than constantly refreshing the data every time you reload
 * or every 5 minutes.
 * 
 * @author hlp2-winser
 */

require_once '../Models/CrimeConfig.php';
$date = $_GET["date"];

$crimeConf = new CrimeConfig();

$fileLastMod = filemtime($crimeConf->getDataXMLName());

$_SESSION["result"] = ($date > $fileLastMod);
$_SESSION["lastModified"] = $fileLastMod;
$_SESSION["givenTimeStamp"] = $date;

include "../Views/CheckCacheView.php";
?>

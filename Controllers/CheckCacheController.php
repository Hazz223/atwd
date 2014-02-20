<?php

$date = $_GET["date"];

$date = strtotime($date);
$fileLastMod = filemtime("../Data/CrimeStats.xml"); // need to get this from config file

$_SESSION["result"] = ($date > $fileLastMod);
include "../Views/CheckCacheView.php";
?>

<?php

/**
 * Description of CheckCacheView
 * Used by the user interface
 * Displays information on how new the cache is.
 *
 * @author hlp2-winser
 */

$result = $_SESSION["result"];
$lastModified = $_SESSION["lastModified"];
$givenTimeStamp = $_SESSION["givenTimeStamp"];
$dataArray = array();
$dataArray["timestamp"] = time();
$dataArray["lastModified"] = $lastModified;
$dataArray["givenTime"] = $givenTimeStamp;
$dataArray["isCurrent"] = $result;

$base = array();
$base["response"] = $dataArray;
header("Content-type: application/json");
echo json_encode($base, JSON_PRETTY_PRINT);
?>

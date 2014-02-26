<?php

/**
 * Description of CacheView
 * Displays a cache file
 *
 * @author hlp2-winser
 */

// If type is xml, data is a domdocument object
if($_SESSION["type"] === "xml"){
    $xmlData = $_SESSION["data"];
    
    header("Content-type: text/xml"); // content headers needed 
    echo $xmlData->saveXML();
}
else{
    // If type is json, then the data is a json string.
    $jsonData = $_SESSION["data"];
    
    header("Content-type: application/json");
    echo $jsonData;
}


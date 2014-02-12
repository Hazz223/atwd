<?php

if($_SESSION["type"] === "xml"){
    $xmlData = $_SESSION["data"];
    
    header("Content-type: text/xml");
    echo $xmlData->saveXML();
}
else{
    $jsonData = $_SESSION["data"];
    
    header("Content-type: application/json");
    echo $jsonData;
}


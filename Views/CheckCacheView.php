<?php
    
    $result = $_SESSION["result"];
    $dataArray = array();
    $dataArray["timestamp"] = date("YmdHi");
    $dataArray["isCurrent"] = $result;

    $base = array();
    $base["response"] = $dataArray;
    header("Content-type: application/json");
    echo json_encode($base, JSON_PRETTY_PRINT);
?>

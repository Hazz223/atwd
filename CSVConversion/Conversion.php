<?php

require_once 'CSVToXML.php';
$input = "data.csv"; // datafile
$xmlLocation = "../Data/CrimeStats.xml";

if (($handle = fopen($input, "r")) !== FALSE) {
    $dataArray = array();

    while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
        $dataArray[] = $data;
    }

    $converter = new CSVToXML($dataArray, $xmlLocation);
    $converter->CreateConfigFile();
    
    $converter->CreateEnglandNode();
    $converter->CreateWalesNode();
    $converter->CreateFurtherStatistics();
    
    $converter->DisplayXML();
    $converter->SaveData();
}
?>

<?php

/**
 * Description of Conversion
 * Used to convert the CSV to the XML
 * 
 * @author hlp2-winser
 */

require_once 'CSVToXML.php';
$input = "data.csv";
$xmlLocation = "../Data/CrimeStats.xml";

if (($handle = fopen($input, "r")) !== FALSE) {
    $dataArray = array();

    while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
        $dataArray[] = $data;
    }

    $converter = new CSVToXML($dataArray, $xmlLocation);
    $converter->CreateConfigFile("../Config/CrimeConfig.xml", "../Cache/CacheData/");
    
    $converter->CreateEnglandNode();
    $converter->CreateWalesNode();
    $converter->CreateFurtherStatistics();
    
    $converter->DisplayXML();
    $converter->SaveData();
}
?>

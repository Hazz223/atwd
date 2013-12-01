

<html>
    <head>
        <meta charset="utf-8"/>
    </head>
    <body>
        <p>
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$input ="data.csv";
$dataArray = array();

$row = 0;

if (($handle = fopen($input, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
    //echo $row."---------------------------------------</br>"; 
    $dataNumber = 0;
        foreach ($data  as $d){
            //echo "Place name: ".$d. "</br>";
            
            $dataNumber++;
        }
        
        $dataArray[] = $data; // should add each sectiont to the array?
        
        //echo "----Data amount: ". $dataNumber. "</br>";
        $row++;
       
    }
    
    $documentTitle = $dataArray[0][0];
    $country = $dataArray[2][0];
    $titles = $dataArray[3];
    
    $excludingFraudTitle = $titles[1];
    $includingFraud = $titles[2];
    $victimBased = $titles[2];
    
    echo $documentTitle;
    echo $country;
    
    
//    foreach ($section1  as $d){
//        echo $d. "</br>";
//
//        break;
//    }
//    
//    $section2 = $dataArray[2];
//    foreach ($section2  as $d){
//        echo $d. "</br>";
//
//        break;
//    }
    
    fclose($handle);
}

?>
        </p>
    </body>
    
</html>

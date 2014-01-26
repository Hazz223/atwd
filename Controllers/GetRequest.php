<?php

// If the URL is: 
// http://www.cems.uwe.ac.uk/~<yourusername>/atwd/crimes/6-2013/south_west/xml
// or:
// http://www.cems.uwe.ac.uk/~<yourusername>/atwd/crimes/6-2013/xml
// don't actually need to worry about if it is either xml or json. I can throw an error in this instead. 
// Need to use Regex to point it to this file. 
// It should look in this get request file. I should then break this down, and then return the correct information.

if (isset($_GET["region"])) {
    // If it's set, we need to search for this information within the xml. At which point we then need to check what comes back  
    echo $_GET["region"];
} else {
    require_once '../Models/Areas.php';
    require_once '../Models/Regions.php';
    $areasModel = new Areas();
    $regionModel = new Regions();
    $areas = $areasModel->GetAreas();
    $regions = $regionModel->GetRegions();
    
    $test = $regionModel->GetRegionByName("West Midlands Region");
   // echo $test->getCrimeStatByName("Total recorded crime - excluding fraud");
   
        foreach($regions as $region){
             echo $region->getCrimeStatByName("Total recorded crime - excluding fraud");
             var_dump($region); // wales  doesn't have any totals.. Fucking brill
    }


    // to start with, we will do it in here
    // start with the xml
//    
//    <response timestamp="xxxxxxxxxx">
//  <crimes year="6-2013">
//   <region id="North East" total="138982" />
//   <region id="North West" total="444423" />
//   ...
//   ...
//   <national id="British Transport Police" total="51968" />
//   <national id="Action Fraud" total="150389" />
//   <england total="3344716" />
//   <wales total="173614" />
// </crimes>
//</response>

    
//    header("Content-type: text/xml"); 
//    $responseXML = new DOMDocument();
//
//
//    $base = $responseXML->createElement("reponse");
//    $base->setAttribute("timestamp", date("YmdHi"));
//    
//    
//    $crime = $responseXML->createElement("crime");
//    $crime->setAttribute("year", "6-2013");
//    
//    
//    foreach($regions as $region){
//        $regionNode = $responseXML->createElement("region");
//        $regionNode->setAttribute("id", $region->getName());
//        $regionNode->setAttribute("total", $region->getCrimeStatByName("Total recorded crime - excluding fraud")); // this doesn't work yet... woo
//        $crime->appendChild($regionNode);
//    }
//    
//    $base->appendChild($crime);
//    
//    
//    $responseXML->appendChild($base); 
//    echo $responseXML->saveXML();
    
    
}
?>



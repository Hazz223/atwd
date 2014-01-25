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
    require_once '../Models/CrimeStatsWrapper.php';
    $crimeXML = new CrimeStatsWrapper();

    $test = $crimeXML->getTotalsForAllRegions();

    //var_dump($test);
    
    foreach ($test as $area) {
        echo $area->getName()."</br>";
        //var_dump($area->getName());
        $data = $area->getStats();
        //var_dump($data);
        foreach($data as $key => $value){
            echo $key." ".$value."</br>";
        }
    }








//    foreach($test as $t){
//        echo $test->getName();
//    }
//    foreach ($test as $data) {
//        $regions = $data->getElementsByTagName("Region");
//        echo "Area: ".$data->getAttribute("Name")."</br>";
//        
//        foreach ($regions as $region) {
//            //echo "Region: ".$region->getAttribute("Name")."</br>";
//            $counties = $region->getElementsByTagName("County");
//            
//
//
//            // this gets all county data information
//            foreach ($counties as $county) {
//                //echo "County: ".$county->getAttribute("Name")."</br>";
//                $crimes = $county->getElementsByTagName("Crime");
//                foreach ($crimes as $crime) {
//                    //echo "Crime: ".$crime->getAttribute("Type")."</br>";
//                    //echo $crime->textContent." </br>";
//                }
//            }
//        }
//        // Access the totals for each area
//
//        
//        //$totals->getAttribute("Name");
//        
////        foreach ($totals as $total) { // only one of tehse
////            echo"test";
////            echo $total->getAttribute("Name")."</br>";
////            $areaTotals = $total->getElementsByTagName("AreaTotal");
////            var_dump($total);
////            foreach($areaTotals as $aT){
////                var_dump($total);
////                echo "Crime Total: " . $aT->getAttribute("Type") . " Data: ";
////                echo $total->textContent . " </br>";
////            }
//
//
//       // }
//    }
}
?>



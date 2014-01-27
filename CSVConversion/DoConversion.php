<?php

$input = "data.csv"; // datafile

if (($handle = fopen($input, "r")) !== FALSE) {
    $dataArray = array();

    $headerArray = array("");
    while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
        $dataArray[] = $data;
    }


    $crimeHeadersArray = array("Total recorded crime - including fraud", "Total recorded crime - excluding fraud", "Victim-based crime", "Other crimes against society");
    // Cycle through this array as we get to certain bits.
    // cycle through the top array, until we hit certain titles.

    $catagoryArray = array("Violence against the person", "Sexual offences", "Robbery", "Theft offences", "Criminal damage and arson", "Drug offences", "Possession of weapons offences"
        , "Public order offences", "Miscellaneous crimes against society", "Fraud"); // will cause a switch in the above

    $titlesArray = RemoveEmptyArraySlots($dataArray[4]);

    // Go through it row by row? And create the correct xml based on that?

    $doc = new DOMDocument();

    $rootNode = $doc->createElement("CrimeStats");

    $englandRow = $dataArray[63]; // this gets england nicely 
    //var_dump($englandRow); // all the row data

    $englandNode = $doc->createElement("Country");
    $englandNode->setAttribute("name", "ENGLAND"); // all regions now need to be connected to this.

    $walesNode = $doc->createElement("Country");
    $walesNode->setAttribute("name", "WALES"); // all regions now need to be connected to this.
    // need to do the same for each row.

    $dataArray = RemoveEmptyArraySlots($dataArray);


    $rowCount = 0;
    $areaArray = array(); // need to seperate the correct regions out etc.
    
    foreach ($dataArray as $row) {
        $row = RemoveEmptyArraySlots($row);
        if ($rowCount > 5 && $rowCount < 75) {
            if (isset($row[0])) {
                if ($rowCount < 65) {
                    if ($dataArray[$rowCount +1][0] === "") { // Currently not skipping it!
                        $regionNode = $doc->createElement("Region");
                        $regionNode->setAttribute("name", $row[0]);

                        foreach ($areaArray as $area) {
                            $regionNode->appendChild($area);
                        }

                        $englandNode->appendChild($regionNode); 
                        $areaArray = array();
                    } else {
                        $areaNode = $doc->createElement("area");
                        $areaNode->setAttribute("name", $row[0]);
                        $areaArray[] = $areaNode;
                        
                        $columnCount = 0;
                        foreach($row as $column){
                            
                        }
                        
                        foreach($crimeHeadersArray as $catagory){
                            
//                            if(){
//                                
//                            }
                            $crimeCatagory = $doc->createElement("CrimeCatagory");
                            $crimeCatagory->setAttribute("name", $catagory);
                            $areaNode->appendChild($crimeCatagory);
                        }

                        
                    }
                }

                if ($rowCount > 65 && $rowCount < 70) {
                    //echo "Wales: ".$row[0]."</br>";
                }
            }
        }

        $rowCount++;
    }

    $rootNode->appendChild($englandNode);
    $rootNode->appendChild($walesNode);
    $doc->appendChild($rootNode);

    header("Content-type: text/xml");
    echo $doc->saveXML();
}

function RemoveEmptyArraySlots($array) {
    $newArray = array();

    foreach ($array as $data) {
        if ($data != "") {
            $newArray[] = $data;
        }
    }

    return $newArray;
}


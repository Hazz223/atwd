<?php

$input = "data.csv"; // datafile

if (($handle = fopen($input, "r")) !== FALSE) {
    $dataArray = array();

    $headerArray = array("");
    while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
        $dataArray[] = $data;
    }


    $crimeHeadersArray = array("Total recorded crime - including fraud", "Total recorded crime - excluding fraud", "Victim-based crime", "Other crimes against society");

    $catagoryArray = array("Violence against the person", "Sexual offences", "Robbery", "Theft offences", "Criminal damage and arson", "Drug offences", "Possession of weapons offences"
        , "Public order offences", "Miscellaneous crimes against society", "Fraud"); // These are catagories. 

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
                    if ($dataArray[$rowCount + 1][0] === "") {
                        if ($row[0] != "ENGLAND") {
                            $regionNode = $doc->createElement("Region");
                            $regionNode->setAttribute("name", $row[0]);

                            foreach ($areaArray as $area) {
                                $regionNode->appendChild($area);
                            }

                            $englandNode->appendChild($regionNode);
                            $areaArray = array();
                        }
                    } else {
                        $areaNode = $doc->createElement("area");
                        $areaNode->setAttribute("name", $row[0]);
                        $areaArray[] = $areaNode;

                        $crimeTypeTotal = $doc->createElement("CrimeType"); // for the two totals
                        $crimeTypeTotal->setAttribute("name", "Totals");

                        $totalWithCrime = $doc->createElement("CrimeCatagory");
                        $totalWithCrime->setAttribute("name", $crimeHeadersArray[0]);
                        $totalWithCrime->setAttribute("total", $row[1]);

                        $totalWithoutCrime = $doc->createElement("CrimeCatagory");
                        $totalWithoutCrime->setAttribute("name", $crimeHeadersArray[1]);
                        $totalWithoutCrime->setAttribute("total", $row[2]);

                        $crimeTypeTotal->appendChild($totalWithCrime);
                        $crimeTypeTotal->appendChild($totalWithoutCrime);

                        $areaNode->appendChild($crimeTypeTotal);
                        // Above catagories done. 
                        // I could just create each catagory as it is?

                        $crimeTypeVictim = $doc->createElement("CrimeType"); // for the two totals
                        $crimeTypeVictim->setAttribute("name", $crimeHeadersArray[2]);

                        // now need to loop through each of these ones to get the infomration required
                        $victimArray = ExtractItemsFromArrayBetweenBounds(3, 18, $row);
                        $titleCount = 0;
                        $catagoryNode = null;
                        foreach ($victimArray as $data) {
                            // Works fine for victim stuff
                            if (TitleInArray($titlesArray[$titleCount], $catagoryArray)) { // basically checks if it a new catagory
                                $newCatagoryNode = $doc->createElement("CrimeCatagory");
                                $newCatagoryNode->setAttribute("name", $titlesArray[$titleCount]);
                                $newCatagoryNode->setAttribute("total", $data);
                                $catagoryNode = $newCatagoryNode;
                            } else {
                                if ($catagoryNode != null) {
                                    $crime = $doc->createElement("Crime"); // for the two totals
                                    $crime->setAttribute("name", $titlesArray[$titleCount]);
                                    $text = $doc->createTextNode($data);
                                    $crime->appendChild($text);

                                    $catagoryNode->appendChild($crime);
                                }
                            }
                            $crimeTypeVictim->appendChild($catagoryNode);
                            $titleCount++;
                        }


                        $crimeTypeFraud = $doc->createElement("CrimeType"); // for the two totals
                        $crimeTypeFraud->setAttribute("name", $crimeHeadersArray[3]);

                        $fraudArray = ExtractItemsFromArrayBetweenBounds(19, 24, $row);
                        $catagoryNode = null;
                        foreach ($fraudArray as $data) {
                            // Works fine for victim stuff
                            if (TitleInArray($titlesArray[$titleCount], $catagoryArray)) { // basically checks if it a new catagory
                                $newCatagoryNode = $doc->createElement("CrimeCatagory");
                                $newCatagoryNode->setAttribute("name", $titlesArray[$titleCount]);
                                $newCatagoryNode->setAttribute("total", $data);
                                $catagoryNode = $newCatagoryNode;
                            } else {
                                if ($catagoryNode != null) {
                                    $crime = $doc->createElement("Crime"); // for the two totals
                                    $crime->setAttribute("name", $titlesArray[$titleCount]);
                                    $text = $doc->createTextNode($data);
                                    $crime->appendChild($text);

                                    $catagoryNode->appendChild($crime);
                                }
                            }
                            $crimeTypeFraud->appendChild($catagoryNode);
                            $titleCount++;
                        }



                        $areaNode->appendChild($crimeTypeVictim);
                        $areaNode->appendChild($crimeTypeFraud);
                    }
                }

                if ($rowCount > 65 && $rowCount < 70) { // WALES SECTION
                    if ($dataArray[$rowCount + 1][0] === "") {
                        $regionNode = $doc->createElement("Region");
                        $regionNode->setAttribute("name", $row[0]);

                        foreach ($areaArray as $area) {
                            $walesNode->appendChild($area);
                        }
                        $areaArray = array();
                    } else {
                        $areaNode = $doc->createElement("Region");
                        $areaNode->setAttribute("name", $row[0]);
                        $areaArray[] = $areaNode;

                        $crimeTypeTotal = $doc->createElement("CrimeType"); // for the two totals
                        $crimeTypeTotal->setAttribute("name", "Totals");

                        $totalWithCrime = $doc->createElement("CrimeCatagory");
                        $totalWithCrime->setAttribute("name", $crimeHeadersArray[0]);
                        $totalWithCrime->setAttribute("total", $row[1]);

                        $totalWithoutCrime = $doc->createElement("CrimeCatagory");
                        $totalWithoutCrime->setAttribute("name", $crimeHeadersArray[1]);
                        $totalWithoutCrime->setAttribute("total", $row[2]);

                        $crimeTypeTotal->appendChild($totalWithCrime);
                        $crimeTypeTotal->appendChild($totalWithoutCrime);

                        $areaNode->appendChild($crimeTypeTotal);
                        // Above catagories done. 
                        // I could just create each catagory as it is?

                        $crimeTypeVictim = $doc->createElement("CrimeType"); // for the two totals
                        $crimeTypeVictim->setAttribute("name", $crimeHeadersArray[2]);

                        $victimArray = ExtractItemsFromArrayBetweenBounds(3, 18, $row);
                        $titleCount = 0;
                        $catagoryNode = null;
                        foreach ($victimArray as $data) {

                            if (TitleInArray($titlesArray[$titleCount], $catagoryArray)) { // basically checks if it a new catagory
                                $newCatagoryNode = $doc->createElement("CrimeCatagory");
                                $newCatagoryNode->setAttribute("name", $titlesArray[$titleCount]);
                                $newCatagoryNode->setAttribute("total", $data);
                                $catagoryNode = $newCatagoryNode;
                            } else {
                                if ($catagoryNode != null) {
                                    $crime = $doc->createElement("Crime"); // for the two totals
                                    $crime->setAttribute("name", $titlesArray[$titleCount]);
                                    $text = $doc->createTextNode($data);
                                    $crime->appendChild($text);

                                    $catagoryNode->appendChild($crime);
                                }
                            }
                            $crimeTypeVictim->appendChild($catagoryNode);
                            $titleCount++;
                        }
                        
                        $crimeTypeFraud = $doc->createElement("CrimeType"); // for the two totals
                        $crimeTypeFraud->setAttribute("name", $crimeHeadersArray[3]);
                        
                        $fraudArray = ExtractItemsFromArrayBetweenBounds(19, 24, $row);
                        $catagoryNode = null;
                        foreach ($fraudArray as $data) {
                            // Works fine for victim stuff
                            if (TitleInArray($titlesArray[$titleCount], $catagoryArray)) { // basically checks if it a new catagory
                                $newCatagoryNode = $doc->createElement("CrimeCatagory");
                                $newCatagoryNode->setAttribute("name", $titlesArray[$titleCount]);
                                $newCatagoryNode->setAttribute("total", $data);
                                $catagoryNode = $newCatagoryNode;
                            } else {
                                if ($catagoryNode != null) {
                                    $crime = $doc->createElement("Crime"); // for the two totals
                                    $crime->setAttribute("name", $titlesArray[$titleCount]);
                                    $text = $doc->createTextNode($data);
                                    $crime->appendChild($text);

                                    $catagoryNode->appendChild($crime);
                                }
                            }
                            $crimeTypeFraud->appendChild($catagoryNode);
                            $titleCount++;
                        }

                        $areaNode->appendChild($crimeTypeVictim);
                        $areaNode->appendChild($crimeTypeFraud);
                    }
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

function ExtractItemsFromArrayBetweenBounds($lowerBound, $upperBound, $array) {
    $arrayPosition = 0;
    $newArray = array();
    foreach ($array as $data) {
        if ($arrayPosition >= $lowerBound && $arrayPosition <= $upperBound) {
            $newArray[] = $data;
        }
        $arrayPosition++;
    }

    return $newArray;
}

function TitleInArray($needle, $heystack) {
    $cleanedNeedle = str_replace(" ", "", $needle);
    foreach ($heystack as $data) {
        $cleanedData = str_replace(" ", "", $data);
        if ($cleanedNeedle === $cleanedData) {
            return true;
        }
    }
    return false;
}

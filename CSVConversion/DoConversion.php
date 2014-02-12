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


    // need to pass it the tital array, the crime headers, and the catagory array.
    // from here we should be able to fashion the config xml, which will allow us
    // Also create the abrivated ones.
    CreateConfigXML($titlesArray, $catagoryArray, $crimeHeadersArray);

    $doc = new DOMDocument();

    $rootNode = $doc->createElement("CrimeStats");

    $englandRow = $dataArray[63]; // this gets england nicely 

    $englandNode = $doc->createElement("Country");
    $englandNode->setAttribute("name", "ENGLAND");
    $englandNode->setAttribute("proper_name", "England");

    $walesNode = $doc->createElement("Country");
    $walesNode->setAttribute("name", "WALES");
    $walesNode->setAttribute("proper_name", "Wales");

    $britishTrasportNode = $doc->createElement("FurtherStatistics");
    $britishTrasportNode->setAttribute("name", "british_transport_police");
    $britishTrasportNode->setAttribute("proper_name", "British Transport Police");
    
    $actionFraudNode = $doc->createElement("FurtherStatistics");
    $actionFraudNode->setAttribute("name", "action_fraud");
    $actionFraudNode->setAttribute("proper_name", "Action Fraud");

    $dataArray = RemoveEmptyArraySlots($dataArray); // does this even work?


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
                            $nonRegionName = str_replace(" Region", "", $row[0]);
                            $withUnderscores = str_replace(" ", "_", $nonRegionName);
                            $regionNode->setAttribute("name",  strtolower($withUnderscores));
                            $regionNode->setAttribute("proper_name", $nonRegionName);

                            foreach ($areaArray as $area) {
                                $regionNode->appendChild($area);
                            }

                            $englandNode->appendChild($regionNode);
                            $areaArray = array();
                        }
                    } else {
                        $areaNode = $doc->createElement("Area");
                        $withUnderscores = str_replace(" ", "_", $row[0]);
                        $areaNode->setAttribute("name",  strtolower($withUnderscores));
                        $areaNode->setAttribute("proper_name", $row[0]);
                        $areaArray[] = $areaNode;

                        $crimeTypeTotal = $doc->createElement("CrimeType"); // for the two totals
                        $crimeTypeTotal->setAttribute("name", "Totals");

                        $totalWithCrime = $doc->createElement("CrimeCatagory");
                        $totalWithCrime->setAttribute("name", $crimeHeadersArray[0]);
                        $totalWithCrime->setAttribute("type", "Totals");
                        $totalWithCrime->setAttribute("total", str_replace(",", "", $row[1]));

                        $totalWithoutCrime = $doc->createElement("CrimeCatagory");
                        $totalWithoutCrime->setAttribute("name", $crimeHeadersArray[1]);
                        $totalWithoutCrime->setAttribute("type", "Totals");
                        $totalWithoutCrime->setAttribute("total", str_replace(",", "", $row[2]));

                        $areaNode->appendChild($totalWithCrime);
                        $areaNode->appendChild($totalWithoutCrime);

                        // now need to loop through each of these ones to get the infomration required
                        $victimArray = ExtractItemsFromArrayBetweenBounds(3, 18, $row);
                        $titleCount = 0;
                        $catagoryNode = null;
                        foreach ($victimArray as $data) {
                            // Works fine for victim stuff
                            if (TitleInArray($titlesArray[$titleCount], $catagoryArray)) { // basically checks if it a new catagory
                                $newCatagoryNode = $doc->createElement("CrimeCatagory");
                                $newCatagoryNode->setAttribute("name", $titlesArray[$titleCount]);
                                $newCatagoryNode->setAttribute("Type", $crimeHeadersArray[2]);
                                $newCatagoryNode->setAttribute("total", str_replace(",", "", $data));
                                $catagoryNode = $newCatagoryNode;
                            } else {
                                if ($catagoryNode != null) {
                                    $crime = $doc->createElement("Crime"); // for the two totals
                                    $crime->setAttribute("name", $titlesArray[$titleCount]);
                                    $text = $doc->createTextNode(str_replace(",", "", $data));
                                    $crime->appendChild($text);

                                    $catagoryNode->appendChild($crime);
                                }
                            }
                            $areaNode->appendChild($catagoryNode);
                            $titleCount++;
                        }

                        $fraudArray = ExtractItemsFromArrayBetweenBounds(19, 24, $row);
                        $catagoryNode = null;
                        foreach ($fraudArray as $data) {
                            // Works fine for victim stuff
                            if (TitleInArray($titlesArray[$titleCount], $catagoryArray)) { // basically checks if it a new catagory
                                $newCatagoryNode = $doc->createElement("CrimeCatagory");
                                $newCatagoryNode->setAttribute("name", $titlesArray[$titleCount]);
                                $newCatagoryNode->setAttribute("Type", $crimeHeadersArray[3]);
                                $newCatagoryNode->setAttribute("total", str_replace(",", "", $data));

                                $catagoryNode = $newCatagoryNode;
                            } else {
                                if ($catagoryNode != null) {
                                    $crime = $doc->createElement("Crime"); // for the two totals
                                    $crime->setAttribute("name", $titlesArray[$titleCount]);
                                    $text = $doc->createTextNode(str_replace(",", "", $data));
                                    $crime->appendChild($text);

                                    $catagoryNode->appendChild($crime);
                                }
                            }
                            $areaNode->appendChild($catagoryNode);
                            $titleCount++;
                        }
                    }
                }

                if ($rowCount > 65 && $rowCount < 70) { // WALES SECTION
                    if ($dataArray[$rowCount + 1][0] === "") {

                        $walesRegionNode = $doc->createElement("Region");
                        $walesRegionNode->setAttribute("name", "wales");
                        $walesRegionNode->setAttribute("proper_name", "Wales");
                        foreach ($areaArray as $area) {
                            $walesRegionNode->appendChild($area);
                        }

                        $walesNode->appendChild($walesRegionNode);

                        $areaArray = array();
                    } else {
                        $areaNode = $doc->createElement("Area");
                        $withUnderscores = str_replace(" ", "_", $row[0]);
                        $areaNode->setAttribute("name",  strtolower($withUnderscores));
                        $areaNode->setAttribute("proper_name", $row[0]);
                        $areaArray[] = $areaNode;
                     
                        $crimeTypeTotal = $doc->createElement("CrimeType");
                        $crimeTypeTotal->setAttribute("name", "Totals");

                        $totalWithCrime = $doc->createElement("CrimeCatagory");
                        $totalWithCrime->setAttribute("name", $crimeHeadersArray[0]);
                        $totalWithCrime->setAttribute("type", "Totals");
                        $totalWithCrime->setAttribute("total", str_replace(",", "", $row[1]));

                        $totalWithoutCrime = $doc->createElement("CrimeCatagory");
                        $totalWithoutCrime->setAttribute("name", $crimeHeadersArray[1]);
                        $totalWithoutCrime->setAttribute("type", "Totals");
                        $totalWithoutCrime->setAttribute("total", str_replace(",", "", $row[2]));

                        $areaNode->appendChild($totalWithCrime);
                        $areaNode->appendChild($totalWithoutCrime);

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
                                $newCatagoryNode->setAttribute("Type", $crimeHeadersArray[2]);
                                $newCatagoryNode->setAttribute("total", str_replace(",", "", $data));
                                $catagoryNode = $newCatagoryNode;
                            } else {
                                if ($catagoryNode != null) {
                                    $crime = $doc->createElement("Crime"); // for the two totals
                                    $crime->setAttribute("name", $titlesArray[$titleCount]);
                                    $text = $doc->createTextNode(str_replace(",", "", $data));
                                    $crime->appendChild($text);

                                    $catagoryNode->appendChild($crime);
                                }
                            }
                            $areaNode->appendChild($catagoryNode);
                            $titleCount++;
                        }

                        $crimeTypeFraud = $doc->createElement("CrimeType"); // for the two totals
                        $crimeTypeFraud->setAttribute("name", $crimeHeadersArray[3]);

                        $fraudArray = ExtractItemsFromArrayBetweenBounds(19, 24, $row);
                        $catagoryNode = null;
                        foreach ($fraudArray as $data) {
                            if (TitleInArray($titlesArray[$titleCount], $catagoryArray)) { // basically checks if it a new catagory
                                $newCatagoryNode = $doc->createElement("CrimeCatagory");
                                $newCatagoryNode->setAttribute("name", $titlesArray[$titleCount]);
                                $newCatagoryNode->setAttribute("Type", $crimeHeadersArray[3]);
                                $newCatagoryNode->setAttribute("total", str_replace(",", "", $data));
                                $catagoryNode = $newCatagoryNode;
                            } else {
                                if ($catagoryNode != null) {
                                    $crime = $doc->createElement("Crime"); // for the two totals
                                    $crime->setAttribute("name", $titlesArray[$titleCount]);
                                    $text = $doc->createTextNode(str_replace(",", "", $data));
                                    $crime->appendChild($text);

                                    $catagoryNode->appendChild($crime);
                                }
                            }
                            $areaNode->appendChild($catagoryNode);
                            $titleCount++;
                        }
                    }
                }

                if ($rowCount == 71) { // if Transport Police
                    $britishTrasportNode = CreateFurtherStatisticsNode(
                            $britishTrasportNode, $crimeHeadersArray, $titlesArray, $catagoryArray, $doc, $row);
                }

                if ($rowCount == 73) { // Action fraud
                    $actionFraudNode = CreateFurtherStatisticsNode(
                            $actionFraudNode, $crimeHeadersArray, $titlesArray, $catagoryArray, $doc, $row);
                }
            }
        }
        $rowCount++;
    }

    $rootNode->appendChild($englandNode);
    $rootNode->appendChild($walesNode);
    $rootNode->appendChild($britishTrasportNode);
    $rootNode->appendChild($actionFraudNode);

    $doc->appendChild($rootNode);

    $doc->save("../Data/CrimeStats.xml");

    echo "Completed Conversion";
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

function PopulateCrimeData($array, $node, $doc, $titleCount, $titlesArray, $catagoryArray, $type) {
    $catagoryNode = null;
    foreach ($array as $data) {
        if (TitleInArray($titlesArray[$titleCount], $catagoryArray)) {
            $newCatagoryNode = $doc->createElement("CrimeCatagory");
            $newCatagoryNode->setAttribute("name", $titlesArray[$titleCount]);
            $newCatagoryNode->setAttribute("total", str_replace(",", "", $data));
            $newCatagoryNode->setAttribute("type", $type);
            $catagoryNode = $newCatagoryNode;
        } else {
            if ($catagoryNode != null) {
                $crime = $doc->createElement("Crime");
                $crime->setAttribute("name", $titlesArray[$titleCount]);
                $text = $doc->createTextNode(str_replace(",", "", $data));
                $crime->appendChild($text);

                $catagoryNode->appendChild($crime);
            }
        }


        $node->appendChild($catagoryNode);
        $titleCount++;
    }

    return $node;
}

function CreateFurtherStatisticsNode($furtherStatsNode, $crimeHeadersArray, $titlesArray, $catagoryArray, $doc, $row) {
    $totalWithCrime = $doc->createElement("CrimeCatagory");
    $totalWithCrime->setAttribute("name", $crimeHeadersArray[0]);
    $totalWithCrime->setAttribute("type", "Totals");
    $totalWithCrime->setAttribute("total", str_replace(",", "", $row[1]));

    $totalWithoutCrime = $doc->createElement("CrimeCatagory");
    $totalWithoutCrime->setAttribute("name", $crimeHeadersArray[1]);
    $totalWithoutCrime->setAttribute("type", "Totals");
    $totalWithoutCrime->setAttribute("total", str_replace(",", "", $row[2]));

    $furtherStatsNode->appendChild($totalWithCrime);
    $furtherStatsNode->appendChild($totalWithoutCrime);

    $victimArray = ExtractItemsFromArrayBetweenBounds(3, 18, $row);
    $furtherStatsNode = PopulateCrimeData($victimArray, $furtherStatsNode, $doc, 0, $titlesArray, $catagoryArray, $crimeHeadersArray[2]);

    // Frad etc
    $fraudArray = ExtractItemsFromArrayBetweenBounds(19, 24, $row);  // I have no idea why this doesn't work...
    $furtherStatsNode = PopulateCrimeData($fraudArray, $furtherStatsNode, $doc, 16, $titlesArray, $catagoryArray, $crimeHeadersArray[3]);

    return $furtherStatsNode;
}

function CreateConfigXML($titlesArray, $catagoryArray, $crimeHeadersArray) {
    $doc = new DOMDocument();

    $rootNode = $doc->createElement("Config");

    $abrivNode = $doc->createElement("CrimeAbriviations");
    $catagory = "";
    $crimeType = $crimeHeadersArray[2];
    foreach ($titlesArray as $title) {
        $nameNode = $doc->createElement("Crime");
        $nameNode->setAttribute("name", $title);

        if ($title === "Drug offences") {
            $crimeType = $crimeHeadersArray[3];
        }

        $nameNode->setAttribute("abrivated", getAbriviatedName($title));

        if (TitleInArray($title, $catagoryArray)) {
            $catagory = $title;
            $nameNode->setAttribute("crimecatagory", $title);
            $nameNode->setAttribute("type", $crimeType);
            $nameNode->setAttribute("iscrimecatagory", "true");
        } else {
            $nameNode->setAttribute("crimecatagory", $catagory);
            $nameNode->setAttribute("type", $crimeType);
            $nameNode->setAttribute("iscrimecatagory", "false");
        }

        $abrivNode->appendChild($nameNode);
    }
    $rootNode->appendChild($abrivNode);
    $doc->appendChild($rootNode);
    $doc->save("../Config/CrimeConfig.xml");
}

function getAbriviatedName($name) {

    $acronym = "";

    $brokenName = explode(" ", $name);

    $length = count($brokenName);

    if ($length == 1) {
        $acronym = $brokenName[0][0] . $brokenName[0][1] . $brokenName[0][2];
    }

    if ($length > 1) {
        $count = 0;
        foreach ($brokenName as $word) {
            if ($count > 3) {
                break;
            }
            if (isset($word[0])) {
                // need to check for without
                if ($word === "without") {
                    $acronym .= "wo";
                } else {
                    $acronym .= $word[0];
                }

                $count++;
            }
        }
    }

    return strtolower($acronym);
}

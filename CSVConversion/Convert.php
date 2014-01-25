

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
            require_once 'Entities/Region.php';
            require_once 'Entities/County.php';

            $input = "data.csv";
            $dataArray = array();
            $regionDataArray = array();
            $row = 0;

            if (($handle = fopen($input, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                    //echo $row."---------------------------------------</br>"; 
                    $dataNumber = 0;
                    foreach ($data as $d) {
                        //echo "Place name: ".$d. "</br>";

                        $dataNumber++;
                    }

                    $dataArray[] = $data; // should add each sectiont to the array?
                    //echo "----Data amount: ". $dataNumber. "</br>";
                    $row++;
                }

                $documentTitle = $dataArray[0][0];
                $country = $dataArray[2][0]; // do i need to be real specific?

                $firstTitles = array($dataArray[3][1], $dataArray[3][2]);

                $titles = array_merge($firstTitles, $dataArray[4]);

                $excludingFraudTitle = $titles[1];
                $includingFraud = $titles[2];
                $victimBased = $titles[2];
                $headerArray = array();
                // get all the headers and remove spaces!
                foreach ($titles as $t) {
                    if (strcmp($t, "")) {
                        $headerArray[] = $t;
                    }
                }

                $regionArray = array();

                foreach ($dataArray as $row) {
                    $regionArray[] = $row[0]; // this gets every name
                }
               

                $currentRow = 0;
                $regionDictionary = array();
                $areaTitles = array();
                foreach ($regionArray as $c) { 
                    if ($currentRow > 5 && $currentRow < 76) {

                        $cData = $dataArray[$currentRow];
                        $countyData = array();
                        foreach ($cData as $cD) {
                            if (strcmp($cD, "")) {
                                $countyData[] = $cD;
                            }
                        }

                        if (strcmp($c, "")) {
                            $columnCount = 1;
                            $rowData = array();
                            foreach ($titles as $title) {
                                if (strcmp($title, "")) { // getting the data for each row and each item

                                    $rowData[$title] = $countyData[$columnCount];

                                    $columnCount ++;
                                }
                            }

                            $areaTitles[] = $c;
                            $regionDictionary[] = $rowData;
                        } else {
                            // This should now add that item as a region
                            $regionLocation = count($regionDictionary) - 1;
                            $regionTitle = $areaTitles[$regionLocation];
                            echo $regionTitle."</br>"; // so that's correct.
                            $regionData = $regionDictionary[$regionLocation];

                            $region = new Region($regionTitle, $regionData, $headerArray);

                            unset($regionDictionary[$regionLocation]);

                            $loopCount = 0;

                            foreach ($regionDictionary as $county) {
                                $county = new County($areaTitles[$loopCount], $county);
                                $region->addCounty($county);
                                $loopCount++;
                            }

                            foreach ($region->getCounties() as $county) {
                                //echo $county->getName() . "</br>"; // some how, North East Region is becoming a county...
                            }

                            $regionDictionary = array();
                            $areaTitles = array();

                            $regionDataArray[] = $region; // This now contains every region, with every county, with all the data!
                        }
                    }

                    $currentRow ++;
                }

                fclose($handle);


                // Now that we have all the data store in objects, i now need to convert this to an XML file, with a schema. 
                // Yay!
                // http://stackoverflow.com/questions/2038535/php-create-new-xml-file-and-write-data-to-it
                echo "data: ";
                $xml = new DOMDocument();
                // Base
                $base = $xml->createElement("CrimeStats");

                $regionNodeArray = array();
                $beenEngland = false;

                foreach ($regionDataArray as $r) {

                    //echo var_dump($r->getCounties()) . "</br>";
                    $hasEngland = ($r->getName() != "ENGLAND");

                    if ($hasEngland && !$beenEngland) { // if england hasn't occured yet. Once it has, then we need to cycle round again.
                        // region contains each county.
                       

                        $regionCountyStats = $r->getCounties(); // gets it's inner counties data
                        // foreach in the counties list, we need to create a county node, then have the list of crimes within it.
                        $regionNode = $xml->createElement("Region");
                        $regionNode->setAttribute("Name", $r->getName());

                        $titleCount = 0;
                        foreach ($regionCountyStats as $countyInfo) { // all county information for this one region
                            $county = $xml->createElement("County");

                            $county->setAttribute("Name", $countyInfo->getName());

                            foreach ($countyInfo->getStats() as $crimeData) {
                                $crime = $xml->createElement("Crime");
                                $crime->setAttribute("Type", $headerArray[$titleCount]);
                                $textData = $xml->createTextNode($crimeData);
                                $crime->appendChild($textData);
                                $county->appendChild($crime);
                                $titleCount ++;
                            }
                            $regionNode->appendChild($county);
                            $titleCount = 0;
                        }

                        $titleCount = 0;
                        
                        $regionStatData = $r->getStats(); // gets it's own stats.
                        $totalNode = $xml->createElement("RegionTotals");

                        foreach ($regionStatData as $rData) {
                            $crime = $xml->createElement("Crime");
                            $crime->setAttribute("Type", $headerArray[$titleCount]);
                            $textData = $xml->createTextNode($rData);
                            $crime->appendChild($textData);
                            $totalNode->appendChild($crime);
                            $titleCount ++;
                        }
                        
                        $regionNode->appendChild($totalNode);

                        $titleCount = 0;

                        $regionNodeArray[] = $regionNode;
                    } else {
                        if ($beenEngland == false) {
                            $area = $xml->createElement("Area");
                            //$area->appendChild($regionNode);
                            $area->setAttribute("Name", $r->getName());

                            foreach ($regionNodeArray as $node) {
                                $area->appendChild($node);
                            }

                            $totals = $xml->createElement("Totals");

                            $regionTotalData = $r->getStats();
                            $titleCount = 0;
                            foreach ($regionTotalData as $rd) {
                                $areaTotals = $xml->createElement("AreaTotal");
                                $areaTotals->setAttribute("Name", $headerArray[$titleCount]);
                                $textData = $xml->createTextNode($rd);
                                $areaTotals->appendChild($textData);

                                $totals->appendChild($areaTotals);

                                $titleCount++;
                            }
                            $area->appendChild($totals);

                            $base->appendChild($area);

                            $regionNodeArray = array();

                            $beenEngland = true;
                        } else {
                            // Basicaly wales and the rest, as these are regions. 
                            //echo $r->getName();

                            $regionStatData = $r->getStats(); // get stats for region

                            $regionCountyStats = $r->getCounties();
                            $regionNode = $xml->createElement("Region");
                            $regionNode->setAttribute("Name", $r->getName());

                            $titleCount = 0;
                            foreach ($regionCountyStats as $countyInfo) { // all county information for this one region
                                $county = $xml->createElement("County");

                                $county->setAttribute("Name", $countyInfo->getName());

                                foreach ($countyInfo->getStats() as $crimeData) {
                                    $crime = $xml->createElement("Crime");
                                    $crime->setAttribute("Type", $headerArray[$titleCount]);
                                    $textData = $xml->createTextNode($crimeData);
                                    $crime->appendChild($textData);
                                    $county->appendChild($crime);
                                    $titleCount ++;
                                }
                                $regionNode->appendChild($county);
                                $titleCount = 0;
                                $regionNodeArray[] = $regionNode;
                            }

                            $titleCount = 0;

                            $area = $xml->createElement("Area");
                            //$area->appendChild($regionNode);
                            $area->setAttribute("Name", $r->getName());
                            foreach ($regionNodeArray as $node) {
                                $area->appendChild($node);
                            }

                            $regionTotalData = $r->getStats();
                            $titleCount = 0;

                            $totals = $xml->createElement("Totals");

                            foreach ($regionTotalData as $rd) {
                                $areaTotals = $xml->createElement("AreaTotal");
                                $areaTotals->setAttribute("Name", $headerArray[$titleCount]);
                                $textData = $xml->createTextNode($rd);
                                $areaTotals->appendChild($textData);

                                $totals->appendChild($areaTotals);

                                $titleCount++;
                            }
                            $titleCount = 0;
                            $area->appendChild($totals);
                            $base->appendChild($area);
                            $regionNodeArray = array();
                        }
                    }
                }

                $xml->appendChild($base);

                $xml->save("../Data/CrimeStats.xml");
            }
            ?>
        </p>
    </body>

</html>

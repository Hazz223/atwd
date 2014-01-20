

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
            require_once 'Region.php';
            require_once 'County.php';

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

                echo $documentTitle . "</br>";
                echo $country . "</br>";

                $titleArray = array();

                foreach ($titles as $t) {

                    if (strcmp($t, "")) {
                        $titleArray[] = $t;
                    }
                }

                $regionArray = array();

                foreach ($dataArray as $row) {
                    $regionArray[] = $row[0];
                }

                $currentRow = 0;
                $regionDictionary = array();
                $areaTitles = array();
                foreach ($regionArray as $c) {

                    if ($currentRow > 5 && $currentRow < 76) {

                        $cData = $dataArray[$currentRow];
                        $countyData = array();
                        foreach ($cData as $cD) {
                            //echo $cD;
                            if (strcmp($cD, "")) {
                                $countyData[] = $cD;
                            }
                        }

                        if (strcmp($c, "")) {
                            $columnCount = 1;
                            $rowData = array();
                            foreach ($titles as $title) {
                                if (strcmp($title, "")) {

                                    $rowData[$title] = $countyData[$columnCount];

                                    $columnCount ++;
                                }
                            }
                            
                            $areaTitles[] = $c;
                            $regionDictionary[] = $rowData;
                        } else {
                            
                            $regionLocation = count($regionDictionary) - 1;
                            $regionTitle = $areaTitles[$regionLocation];
                            $regionData = $regionDictionary[$regionLocation];
                            
                            $test = new ArrayObject($regionData);
                            
                            $region = new Region($regionTitle, $test);

                            unset($regionDictionary[4]);

                            echo $region->getName() . "</br>";
                            $loopCount = 0;

                            foreach ($regionDictionary as $county) {

                                $county = new County($areaTitles[$loopCount], $county);
                                $region->addCounty($county);
                                $loopCount++;
                            }


                            foreach ($region->getCounties() as $county) {
                                //echo $county->getName() . "</br>";
                            }
                           // echo "----end of area ---- </br>";

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
                
                //Area
                $area = $xml->createElement("Area");
                $area->setAttribute("name", "test");
                //Area totals
                $areaTotals = $xml->createElement("AreaTotals");
                // Need a foreach look for crime
                foreach($regionDataArray as $r){
                    
                    $regionStatData = $r->getStats();
                    foreach($regionStatData as $s){
                        var_dump ($s);
                        echo $s;
                    }
//                    $crime = $xml->createElement("Crime");
//                    $crime->setAttribute("Type", $r->getName());
//                    $areaTotals->appendChild($crime);
                }
                
                
                $area->appendChild($areaTotals);
                
                $base->appendChild($area);
                $xml->appendChild($base); 
                
                //$xml->save("xmltest.xml");
            }
            ?>
        </p>
    </body>

</html>

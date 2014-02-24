<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CSVToXML
 *
 * @author hlp2-winser
 */
require_once 'ConfigFileCreator.php';

class CSVToXML {

    private $crimeHeadersArray, $catagoryArray, $titlesArray, $dataArray;
    private $doc, $englandNode, $walesNode, $rootNode;
    private $xmlLocation;

// Constructor - accepts the data array. Creates the doc component. 

    function __construct($data, $location) {
        $this->dataArray = $data;
        $this->crimeHeadersArray = array(
            "Total recorded crime - including fraud",
            "Total recorded crime - excluding fraud",
            "Victim-based crime",
            "Other crimes against society");

        $this->catagoryArray = array(
            "Violence against the person",
            "Sexual offences", "Robbery",
            "Theft offences",
            "Criminal damage and arson",
            "Drug offences",
            "Possession of weapons offences",
            "Public order offences",
            "Miscellaneous crimes against society",
            "Fraud"); // These are catagories. 

        $this->titlesArray = $this->RemoveEmptyArraySlots($this->dataArray[4]);
        $this->dataArray = $this->RemoveEmptyArraySlots($this->dataArray);

        $this->doc = new DOMDocument("1.0"); // need to put in the XML validation stuff
        $this->rootNode = $this->doc->createElement("CrimeStats");

        $this->xmlLocation = $location;
    }

    public function CreateConfigFile() {
        
        $confCreator = new ConfigFileCreator(
                $this->titlesArray, 
                $this->catagoryArray, 
                $this->crimeHeadersArray, 
                $this->xmlLocation, 
                "../Config/CrimeConfig.xml");

        $confCreator->CreateConfigFile(); // creates the config file.
    }

    public function CreateWalesNode() {
        $walesNode = $this->doc->createElement("Country");
        $walesNode->setAttribute("name", "WALES");
        $walesNode->setAttribute("proper_name", "Wales");

        $regionNodes = $this->createCountryCrimeData(65, 70);

        foreach ($regionNodes as $node) {
            $walesNode->appendChild($node);
        }

        $this->rootNode->appendChild($walesNode);
    }

    public function CreateEnglandNode() {
        $englandNode = $this->doc->createElement("Country");
        $englandNode->setAttribute("name", "ENGLAND");
        $englandNode->setAttribute("proper_name", "England");

        $regionNodes = $this->createCountryCrimeData(5, 65);

        foreach ($regionNodes as $node) {
            $englandNode->appendChild($node);
        }

        $this->rootNode->appendChild($englandNode);
    }

    public function CreateFurtherStatistics() {

        $btpData = $this->RemoveEmptyArraySlots($this->dataArray[71]);
        $afData = $this->RemoveEmptyArraySlots($this->dataArray[73]);

        $britishTrasportNode = $this->CreateFurtherStatisticsNode("british_transport_police", "British Transport Police", $btpData);
        $actionFraudNode = $this->CreateFurtherStatisticsNode("action_fraud", "Action Fraud", $afData);

        $this->rootNode->appendChild($britishTrasportNode);
        $this->rootNode->appendChild($actionFraudNode);
    }

    private function createCountryCrimeData($lowerBound, $upperBound) {
        $areaArray = array();
        $regionArray = array();
        $rowCount = 0;

        foreach ($this->dataArray as $row) {
            if ($rowCount > $lowerBound && $rowCount < $upperBound) {
                $row = $this->RemoveEmptyArraySlots($row);
                if (isset($row[0])) {
                    if ($this->isRegion($rowCount)) {
                        if ($row[0] != "ENGLAND") {
                            $regionArray[] = $this->CreateRegionNode($row, $areaArray);
                            $areaArray = array(); // resets the array;
                        }
                    } else {
                        $areaArray[] = $this->CreateAreaNode($row);
                    }
                }
            }

            $rowCount++;
        }

        return $regionArray;
    }

    private function isRegion($rowCount) {
        return ($this->dataArray[$rowCount + 1][0] === "");
    }

    private function CreateRegionNode($row, $areaArray) {
        $regionName = $row[0];

        if ($regionName === "WALES") { // Needed to give wales region a good proper name
            $regionName = "Wales";
        }

        $regionNode = $this->doc->createElement("Region");
        $nonRegionName = str_replace(" Region", "", $regionName);

        $withUnderscores = str_replace(" ", "_", $nonRegionName);
        $regionNode->setAttribute("name", strtolower($withUnderscores));
        $regionNode->setAttribute("proper_name", $nonRegionName);

        foreach ($areaArray as $area) {
            $regionNode->appendChild($area);
        }

        return $regionNode;
    }

    private function CreateAreaNode($row) {

        // Creates a new area node, then returns it.

        $areaNode = $this->doc->createElement("Area");
        $withUnderscores = str_replace(" ", "_", $row[0]);
        $areaNode->setAttribute("name", strtolower($withUnderscores));
        $areaNode->setAttribute("proper_name", $row[0]);


        $crimeTypeTotal = $this->doc->createElement("CrimeType"); // for the two totals
        $crimeTypeTotal->setAttribute("name", "Totals");

        $totalWithCrime = $this->doc->createElement("CrimeCatagory");
        $totalWithCrime->setAttribute("name", $this->crimeHeadersArray[0]);
        $totalWithCrime->setAttribute("type", "Totals");
        $totalWithCrime->setAttribute("total", str_replace(",", "", $row[1]));

        $totalWithoutCrime = $this->doc->createElement("CrimeCatagory");
        $totalWithoutCrime->setAttribute("name", $this->crimeHeadersArray[1]);
        $totalWithoutCrime->setAttribute("type", "Totals");
        $totalWithoutCrime->setAttribute("total", str_replace(",", "", $row[2]));

        $areaNode->appendChild($totalWithCrime);
        $areaNode->appendChild($totalWithoutCrime);

        // now need to loop through each of these ones to get the infomration required
        $victimArray = $this->ExtractItemsFromArrayBetweenBounds(3, 18, $row);
        $titleCount = 0;
        $catagoryNode = null;
        foreach ($victimArray as $data) {
            // Works fine for victim stuff
            if ($this->TitleInArray($this->titlesArray[$titleCount], $this->catagoryArray)) { // basically checks if it a new catagory
                $newCatagoryNode = $this->doc->createElement("CrimeCatagory");
                $newCatagoryNode->setAttribute("name", $this->titlesArray[$titleCount]);
                $newCatagoryNode->setAttribute("Type", $this->crimeHeadersArray[2]);
                $newCatagoryNode->setAttribute("total", str_replace(",", "", $data));
                $catagoryNode = $newCatagoryNode;
            } else {
                if ($catagoryNode != null) {
                    $crime = $this->doc->createElement("Crime"); // for the two totals
                    $crime->setAttribute("name", $this->titlesArray[$titleCount]);
                    $text = $this->doc->createTextNode(str_replace(",", "", $data));
                    $crime->appendChild($text);

                    $catagoryNode->appendChild($crime);
                }
            }
            $areaNode->appendChild($catagoryNode);
            $titleCount++;
        }

        $fraudArray = $this->ExtractItemsFromArrayBetweenBounds(19, 24, $row);
        $catagoryNode = null;
        foreach ($fraudArray as $data) {
            // Works fine for victim stuff
            if ($this->TitleInArray($this->titlesArray[$titleCount], $this->catagoryArray)) { // basically checks if it a new catagory
                $newCatagoryNode = $this->doc->createElement("CrimeCatagory");
                $newCatagoryNode->setAttribute("name", $this->titlesArray[$titleCount]);
                $newCatagoryNode->setAttribute("Type", $this->crimeHeadersArray[3]);
                $newCatagoryNode->setAttribute("total", str_replace(",", "", $data));

                $catagoryNode = $newCatagoryNode;
            } else {
                if ($catagoryNode != null) {
                    $crime = $this->doc->createElement("Crime"); // for the two totals
                    $crime->setAttribute("name", $this->titlesArray[$titleCount]);
                    $text = $this->doc->createTextNode(str_replace(",", "", $data));
                    $crime->appendChild($text);

                    $catagoryNode->appendChild($crime);
                }
            }
            $areaNode->appendChild($catagoryNode);
            $titleCount++;
        }

        return $areaNode;
    }

    public function SaveData() {

        $this->doc->appendChild($this->rootNode);
        $this->doc->save($this->xmlLocation);
    }

    public function DisplayXML() {
        $this->doc->appendChild($this->rootNode);
        header("Content-type: text/xml");
        echo $this->doc->saveXML();
    }

    private function ExtractItemsFromArrayBetweenBounds($lowerBound, $upperBound, $array) {
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

    private function TitleInArray($needle, $heystack) {
        $cleanedNeedle = str_replace(" ", "", $needle);
        foreach ($heystack as $data) {
            $cleanedData = str_replace(" ", "", $data);
            if ($cleanedNeedle === $cleanedData) {
                return true;
            }
        }
        return false;
    }

    private function RemoveEmptyArraySlots($array) {
        $newArray = array();

        foreach ($array as $data) {
            if ($data != "") {
                $newArray[] = $data;
            }
        }

        return $newArray;
    }

    private function CreateFurtherStatisticsNode($name, $properName, $row) {
        $newNode = $this->doc->createElement("FurtherStatistics");
        $newNode->setAttribute("name", $name);
        $newNode->setAttribute("proper_name", $properName);

        $totalWithCrime = $this->doc->createElement("CrimeCatagory");
        $totalWithCrime->setAttribute("name", $this->crimeHeadersArray[0]);
        $totalWithCrime->setAttribute("type", "Totals");
        $totalWithCrime->setAttribute("total", str_replace(",", "", $row[1]));

        $totalWithoutCrime = $this->doc->createElement("CrimeCatagory");
        $totalWithoutCrime->setAttribute("name", $this->crimeHeadersArray[1]);
        $totalWithoutCrime->setAttribute("type", "Totals");
        $totalWithoutCrime->setAttribute("total", str_replace(",", "", $row[2]));

        $newNode->appendChild($totalWithCrime);
        $newNode->appendChild($totalWithoutCrime);

        $victimArray = $this->ExtractItemsFromArrayBetweenBounds(3, 18, $row);

        $newNode = $this->PopulateCrimeData($victimArray, $newNode, 0, $this->crimeHeadersArray[2]);

        // Frad etc
        $fraudArray = $this->ExtractItemsFromArrayBetweenBounds(19, 24, $row); // this now isn't working...

        $newNode = $this->PopulateCrimeData($fraudArray, $newNode, 16, $this->crimeHeadersArray[3]);

        return $newNode;
    }

    private function PopulateCrimeData($array, $node, $titleCount, $type) {
        $catagoryNode = null;
        foreach ($array as $data) {
            if ($data != "..") {
                if ($this->TitleInArray($this->titlesArray[$titleCount], $this->catagoryArray)) { // titleCount is going too far...
                    $newCatagoryNode = $this->doc->createElement("CrimeCatagory");
                    $newCatagoryNode->setAttribute("name", $this->titlesArray[$titleCount]);
                    $newCatagoryNode->setAttribute("total", str_replace(",", "", $data));
                    $newCatagoryNode->setAttribute("type", $type);
                    $catagoryNode = $newCatagoryNode;
                } else {
                    if ($catagoryNode != null) {
                        $crime = $this->doc->createElement("Crime");
                        $crime->setAttribute("name", $this->titlesArray[$titleCount]);
                        $text = $this->doc->createTextNode(str_replace(",", "", $data));
                        $crime->appendChild($text);

                        $catagoryNode->appendChild($crime);
                    }
                }

                $node->appendChild($catagoryNode);
            }
            $titleCount++;
        }

        return $node;
    }

}

?>

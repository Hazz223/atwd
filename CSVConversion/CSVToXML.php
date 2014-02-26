<?php

/**
 * Description of CSVToXML
 * Object to allow you to convert the CSV data to XML
 * 
 * @author hlp2-winser
 */
require_once 'ConfigFileCreator.php';

class CSVToXML {

    private $crimeHeadersArray, $catagoryArray, $titlesArray, $dataArray;
    private $doc, $rootNode;
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
            "Sexual offences",
            "Robbery",
            "Theft offences",
            "Criminal damage and arson",
            "Drug offences",
            "Possession of weapons offences",
            "Public order offences",
            "Miscellaneous crimes against society",
            "Fraud"); // These are catagories. 

        $this->titlesArray = $this->removeEmptyArraySlots($this->dataArray[4]);
        $this->dataArray = $this->removeEmptyArraySlots($this->dataArray);

        $this->doc = new DOMDocument("1.0"); // need to put in the XML validation stuff
        $this->doc->formatOutput = true;

        //http://stackoverflow.com/questions/9082032/generate-xml-namespace-with-php-dom
        
        $this->rootNode = $this->doc->appendChild(
                $this->doc->createElementNS(
                        $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Data/', 'cd:CrimeStats'));

        $this->rootNode->setAttributeNS(
                'http://www.w3.org/2001/XMLSchema-instance', 'xsi:schemaLocation', 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Data/ ./CrimeStats.xsd');

        $this->xmlLocation = $location;
    }

    public function CreateConfigFile($xmlLocation, $cacheLocation) {

        $confCreator = new ConfigFileCreator(
                $this->titlesArray, $this->catagoryArray, $this->crimeHeadersArray, $this->xmlLocation, $xmlLocation, $cacheLocation);

        $confCreator->CreateConfigFile();
    }

    public function CreateWalesNode() {  // Although the same as England, it's nice to seperate them out.    
        $walesNode = $this->_createCountryNode("WALES", "Wales", 65, 70);

        $this->rootNode->appendChild($walesNode);
    }

    public function CreateEnglandNode() {
        $englandNode = $this->_createCountryNode("ENGLAND", "England", 5, 65);

        $this->rootNode->appendChild($englandNode);
    }

    public function CreateFurtherStatistics() {

        $btpData = $this->removeEmptyArraySlots($this->dataArray[71]);
        $afData = $this->removeEmptyArraySlots($this->dataArray[73]);

        $britishTrasportNode = $this->_createFurtherStatisticsNode("british_transport_police", "British Transport Police", $btpData);
        $actionFraudNode = $this->_createFurtherStatisticsNode("action_fraud", "Action Fraud", $afData);

        $this->rootNode->appendChild($britishTrasportNode);
        $this->rootNode->appendChild($actionFraudNode);
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

    private function _createCountryNode($name, $properName, $lowerBound, $upperBound) {
        $countryNode = $this->doc->createElementNS(
                $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Data/', 'cd:Country');
        $countryNode->setAttribute("name", $name);
        $countryNode->setAttribute("proper_name", $properName);

        $regionNodes = $this->_createCountryCrimeData($lowerBound, $upperBound);

        foreach ($regionNodes as $node) {
            $countryNode->appendChild($node);
        }

        return $countryNode;
    }

    // getsRegion and area data. Returns an aray of region Nodes
    private function _createCountryCrimeData($lowerBound, $upperBound) {
        $areaArray = array();
        $regionArray = array();
        $rowCount = 0;

        foreach ($this->dataArray as $row) {
            if ($rowCount > $lowerBound && $rowCount < $upperBound) {
                $row = $this->removeEmptyArraySlots($row);
                if (isset($row[0])) {
                    if ($this->_isRegion($rowCount)) {
                        if ($row[0] != "ENGLAND") {
                            $regionArray[] = $this->_createRegionNode($row, $areaArray);
                            $areaArray = array(); // resets the array;
                        }
                    } else {
                        $areaArray[] = $this->_createAreaNode($row);
                    }
                }
            }

            $rowCount++;
        }

        return $regionArray;
    }

    private function _isRegion($rowCount) {
        return ($this->dataArray[$rowCount + 1][0] === "");
    }

    private function _createRegionNode($row, $areaArray) {
        $regionName = $row[0];

        if ($regionName === "WALES") { // Needed to give wales region a differnet name, else it uts WALES
            $regionName = "Wales";
        }

        $regionNode = $this->doc->createElementNS(
                $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Data/', 'cd:Region');
        $nonRegionName = str_replace(" Region", "", $regionName); // requirements show them without the term region. 

        $withUnderscores = str_replace(" ", "_", $nonRegionName);
        $regionNode->setAttribute("name", strtolower($withUnderscores));
        $regionNode->setAttribute("proper_name", $nonRegionName);

        foreach ($areaArray as $area) {
            $regionNode->appendChild($area);
        }

        return $regionNode;
    }

    // Creates a Area node - includes totals node, and all crime data stored
    private function _createAreaNode($row) {

        // Creates a new area node, then returns it.

        $areaNode = $this->doc->createElementNS(
                $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Data/', 'cd:Area');
        $withUnderscores = str_replace(" ", "_", $row[0]);
        $areaNode->setAttribute("name", strtolower($withUnderscores));
        $areaNode->setAttribute("proper_name", $row[0]);


        $crimeTypeTotal = $this->doc->createElementNS(
                $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Data/', 'cd:CrimeType');
        ; // for the two totals
        $crimeTypeTotal->setAttribute("name", "Totals");

        $totalWithCrime = $this->doc->createElementNS(
                $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Data/', 'cd:CrimeCatagory');
        $totalWithCrime->setAttribute("name", $this->crimeHeadersArray[0]);
        $totalWithCrime->setAttribute("type", "Totals");
        $totalWithCrime->setAttribute("total", str_replace(",", "", $row[1]));

        $totalWithoutCrime = $this->doc->createElementNS(
                $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Data/', 'cd:CrimeCatagory');
        $totalWithoutCrime->setAttribute("name", $this->crimeHeadersArray[1]);
        $totalWithoutCrime->setAttribute("type", "Totals");
        $totalWithoutCrime->setAttribute("total", str_replace(",", "", $row[2]));

        $areaNode->appendChild($totalWithCrime);
        $areaNode->appendChild($totalWithoutCrime);

        $victimArray = $this->_extractItemsFromArrayBetweenBounds(3, 18, $row);

        $areaNode = $this->_populateCountryCrimeData($areaNode, $victimArray, 0);

        $fraudArray = $this->_extractItemsFromArrayBetweenBounds(19, 24, $row);

        $areaNode = $this->_populateCountryCrimeData($areaNode, $fraudArray, 16);

        return $areaNode;
    }

    // Loop through the data, looking for catagories and crimes.  If catagory, we create it
    // if crime, we append it to the current catagory. 
    private function _populateCountryCrimeData($areaNode, $dataArray, $titleCount) {
        foreach ($dataArray as $data) {
            $dataName = $this->_removeExtraWhiteSpace($this->titlesArray[$titleCount]);

            if ($this->_titleInArray($dataName, $this->catagoryArray)) {// Checks for catagory
                $newCatagoryNode = $this->doc->createElementNS(
                        $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Data/', 'cd:CrimeCatagory');
                $newCatagoryNode->setAttribute("name", $dataName);
                $newCatagoryNode->setAttribute("Type", $this->crimeHeadersArray[3]);
                $newCatagoryNode->setAttribute("total", str_replace(",", "", $data));

                $catNode = $newCatagoryNode;
            } else {
                // not a catagory, so adds it to the current catagory, catNode 
                if ($catNode != null) {
                    $crime = $this->doc->createElementNS(
                            $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Data/', 'cd:Crime');
                    $crime->setAttribute("name", $dataName);
                    $text = $this->doc->createTextNode(str_replace(",", "", $data));
                    $crime->appendChild($text);

                    $catNode->appendChild($crime);
                }
            }
            $areaNode->appendChild($catNode);
            $titleCount++;
        }

        return $areaNode;
    }

    // Takes in array, and creates a new array based on the upper and lower bounds of the current array
    // useful for only getting select information from a large array
    private function _extractItemsFromArrayBetweenBounds($lowerBound, $upperBound, $array) {
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

    // had to write my own one, due to php's one not working as i expect it to. 
    private function _titleInArray($needle, $heystack) {
        $cleanedNeedle = str_replace(" ", "", $needle);
        foreach ($heystack as $data) {
            $cleanedData = str_replace(" ", "", $data);
            if ($cleanedNeedle === $cleanedData) {
                return true;
            }
        }
        return false;
    }

    private function removeEmptyArraySlots($array) {
        $newArray = array();

        foreach ($array as $data) {
            if ($data != "") {
                $newArray[] = $data;
            }
        }

        return $newArray;
    }

    private function _createFurtherStatisticsNode($name, $properName, $row) {
        $newNode = $this->doc->createElementNS(
                $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Data/', 'cd:FurtherStatistics');
        ;

        $newNode->setAttribute("name", $name);
        $newNode->setAttribute("proper_name", $properName);

        $totalWithCrime = $this->doc->createElementNS(
                $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Data/', 'cd:CrimeCatagory');
        ;

        $totalWithCrime->setAttribute("name", $this->crimeHeadersArray[0]);
        $totalWithCrime->setAttribute("type", "Totals");
        $totalWithCrime->setAttribute("total", str_replace(",", "", $row[1]));

        $totalWithoutCrime = $this->doc->createElementNS(
                $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Data/', 'cd:CrimeCatagory');
        $totalWithoutCrime->setAttribute("name", $this->crimeHeadersArray[1]);
        $totalWithoutCrime->setAttribute("type", "Totals");
        $totalWithoutCrime->setAttribute("total", str_replace(",", "", $row[2]));

        $newNode->appendChild($totalWithCrime);
        $newNode->appendChild($totalWithoutCrime);

        $victimArray = $this->_extractItemsFromArrayBetweenBounds(3, 18, $row);

        $newNode = $this->_populateNationalCrimeData($victimArray, $newNode, 0, $this->crimeHeadersArray[2]);

        $fraudArray = $this->_extractItemsFromArrayBetweenBounds(19, 24, $row); // this now isn't working...

        $newNode = $this->_populateNationalCrimeData($fraudArray, $newNode, 16, $this->crimeHeadersArray[3]);

        return $newNode;
    }

    private function _populateNationalCrimeData($array, $node, $titleCount, $type) {
        $catagoryNode = null;
        foreach ($array as $data) {
            if ($data != "..") {
                if ($this->_titleInArray($this->titlesArray[$titleCount], $this->catagoryArray)) { // titleCount is going too far...
                    $newCatagoryNode = $this->doc->createElementNS(
                            $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Data/', 'cd:CrimeCatagory');
                    $newCatagoryNode->setAttribute("name", $this->titlesArray[$titleCount]);
                    $newCatagoryNode->setAttribute("total", str_replace(",", "", $data));
                    $newCatagoryNode->setAttribute("type", $type);
                    $catagoryNode = $newCatagoryNode;
                } else {
                    if ($catagoryNode != null) {
                        $crime = $this->doc->createElementNS(
                                $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Data/', 
                                'cd:Crime');
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

    private function _removeExtraWhiteSpace($data) {
        //http://stackoverflow.com/questions/1703320/remove-excess-whitespace-from-within-a-string
        $cleanedData = preg_replace('/\s+/', ' ', $data);

        return $cleanedData;
    }

}

?>

<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConfigFileCreator
 *
 * @author hlp2-winser
 */
class ConfigFileCreator {

    private $titlesArray, $catagoryArray, $crimeHeadersArray, $xmlLocation, $configLocation;

    function __construct($titlesArray, $catagoryArray, $crimeHeadersArray, $xmlLocation, $configLocation) {
        $this->titlesArray = $titlesArray;
        $this->catagoryArray = $catagoryArray;
        $this->crimeHeadersArray = $crimeHeadersArray;
        $this->xmlLocation = $xmlLocation;
        $this->configLocation = $configLocation;
    }

    function CreateConfigFile() {
        $doc = new DOMDocument();

        $rootNode = $doc->createElement("Config");

        $abrivNode = $doc->createElement("CrimeAbriviations");
        $catagory = "";
        $crimeType = $this->crimeHeadersArray[2];
        foreach ($this->titlesArray as $title) {
            $nameNode = $doc->createElement("Crime");
            $nameNode->setAttribute("name", $title);

            if ($title === "Drug offences") {
                $crimeType = $this->crimeHeadersArray[3];
            }

            $nameNode->setAttribute("abrivated", $this->_getAbbrivatedName($title));

            if (TitleInArray($title, $this->catagoryArray)) {
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

        $crimeDataNode = $doc->createElement("crime_data");
        $testNode = $doc->createElement("stored_xml_location");

        $text = $doc->createTextNode($this->xmlLocation);
        $testNode->appendChild($text);
        $crimeDataNode->appendChild($testNode);

        $rootNode->appendChild($abrivNode);
        $rootNode->appendChild($crimeDataNode);
        $doc->appendChild($rootNode);
        $doc->save($this->configLocation);
    }

    private function _getAbbrivatedName($name) {


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

}

?>

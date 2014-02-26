<?php

/**
 * Description of ConfigFileCreator
 * Used to create the Config file 
 * Config knows where the XML is, as well as what the crime names are and
 * their abreviations
 * 
 * @author hlp2-winser
 */
class ConfigFileCreator {

    private $titlesArray, $catagoryArray, $crimeHeadersArray, $xmlLocation, $configLocation;
    private $cacheLocation, $doc;

    function __construct($titlesArray, $catagoryArray, $crimeHeadersArray, $xmlLocation, $configLocation, $cacheLocation) {
        $this->titlesArray = $titlesArray;
        $this->catagoryArray = $catagoryArray;
        $this->crimeHeadersArray = $crimeHeadersArray;
        $this->xmlLocation = $xmlLocation;
        $this->configLocation = $configLocation;
        $this->cacheLocation = $cacheLocation;

        $this->doc = new DOMDocument("1.0");
    }

    function CreateConfigFile() {


        $rootNode = $this->doc->createElement("Config");

        $abrivNode = $this->doc->createElement("CrimeAbriviations");
        $catagory = "";
        $crimeType = $this->crimeHeadersArray[2];
        foreach ($this->titlesArray as $title) {
            
            $cleanedTitle = $this->_removeExtraWhiteSpace($title);
            
            $nameNode = $this->doc->createElement("Crime");
            $nameNode->setAttribute("name", $cleanedTitle);

            if ($cleanedTitle === "Drug offences") {
                $crimeType = $this->crimeHeadersArray[3];
            }

            $nameNode->setAttribute("abrivated", $this->_getAbbrivatedName($cleanedTitle));

            if ($this->_titleInArray($cleanedTitle, $this->catagoryArray)) {
                $catagory = $cleanedTitle;
                $nameNode->setAttribute("crimecatagory", $cleanedTitle);
                $nameNode->setAttribute("type", $crimeType);
                $nameNode->setAttribute("iscrimecatagory", "true");
            } else {
                $nameNode->setAttribute("crimecatagory", $catagory);
                $nameNode->setAttribute("type", $crimeType);
                $nameNode->setAttribute("iscrimecatagory", "false");
            }

            $abrivNode->appendChild($nameNode);
        }

        // Create the location of the both the cache folder and the data xml file.
        $crimeDataNode = $this->_createCrimeXMLNode();
        $cacheDataNode = $this->_createCacheNode();

        $rootNode->appendChild($abrivNode);
        $rootNode->appendChild($crimeDataNode);
        $rootNode->appendChild($cacheDataNode);
        $this->doc->appendChild($rootNode);
        $this->doc->save($this->configLocation);
    }

    private function _createCrimeXMLNode() {
        $crimeDataNode = $this->doc->createElement("crime_data");
        $textNode = $this->doc->createElement("stored_xml_location");

        $text = $this->doc->createTextNode($this->xmlLocation);
        $textNode->appendChild($text);
        $crimeDataNode->appendChild($textNode);
        
        return $crimeDataNode;
    }

    private function _createCacheNode() {
        $cacheDataNode = $this->doc->createElement("cache_data_location");
        $cacheDataText = $this->doc->createElement("stored_cache_location");

        $cacheText = $this->doc->createTextNode($this->cacheLocation);
        $cacheDataText->appendChild($cacheText);
        $cacheDataNode->appendChild($cacheDataText);

        return $cacheDataNode;
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
    
    private function _removeExtraWhiteSpace($data) {
        //http://stackoverflow.com/questions/1703320/remove-excess-whitespace-from-within-a-string
        $cleanedData = preg_replace( '/\s+/', ' ', $data );
        
        return $cleanedData;
    }

}

?>

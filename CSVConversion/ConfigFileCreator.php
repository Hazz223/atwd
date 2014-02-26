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
    private $cacheLocation, $doc, $rootNode;

    function __construct($titlesArray, $catagoryArray, $crimeHeadersArray, $xmlLocation, $configLocation, $cacheLocation) {
        $this->titlesArray = $titlesArray;
        $this->catagoryArray = $catagoryArray;
        $this->crimeHeadersArray = $crimeHeadersArray;
        $this->xmlLocation = $xmlLocation;
        $this->configLocation = $configLocation;
        $this->cacheLocation = $cacheLocation;

        $this->doc = new DOMDocument("1.0");
        $this->doc->formatOutput = true;

        //http://stackoverflow.com/questions/9082032/generate-xml-namespace-with-php-dom

        $this->rootNode = $this->doc->appendChild(
                $this->doc->createElementNS(
                        $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Config/', 'cd:Config'));

        $this->rootNode->setAttributeNS(
                'http://www.w3.org/2001/XMLSchema-instance', 'xsi:schemaLocation', 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Config/ ./CrimeConfig.xsd');
    }

    function CreateConfigFile() {

        $abrivNode = $this->doc->createElementNS(
                $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Config/', 'cd:CrimeAbriviations');
        $catagory = "";
        $crimeType = $this->crimeHeadersArray[2];
        foreach ($this->titlesArray as $title) {

            $cleanedTitle = $this->_removeExtraWhiteSpace($title);

            $nameNode = $this->doc->createElementNS(
                    $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Config/', 'cd:Crime');
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

        $this->rootNode->appendChild($abrivNode);
        $this->rootNode->appendChild($crimeDataNode);
        $this->rootNode->appendChild($cacheDataNode);
        $this->doc->appendChild($this->rootNode);
        $this->doc->save($this->configLocation);        
       
    }

    private function _createCrimeXMLNode() {
        $crimeDataNode = $this->doc->createElementNS(
                $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Config/', 'cd:crime_data');
        $textNode = $this->doc->createElementNS(
                $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Config/', 'cd:stored_xml_location');

        $text = $this->doc->createTextNode($this->xmlLocation);
        $textNode->appendChild($text);
        $crimeDataNode->appendChild($textNode);

        return $crimeDataNode;
    }

    private function _createCacheNode() {
        $cacheDataNode = $this->doc->createElementNS(
                $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Config/', 'cd:cache_data_location');


        $cacheDataText = $this->doc->createElementNS(
                $cd = 'http://www.cems.uwe.ac.uk/~hlp2-winser/atwd/Config/', 'cd:stored_cache_location');
        

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
        $cleanedData = preg_replace('/\s+/', ' ', $data);

        return $cleanedData;
    }

}

?>

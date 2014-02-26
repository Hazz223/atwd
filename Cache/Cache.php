<?php

/**
 * Description of Cache
 * Object to operate the cache files on the server for responses.
 * Caches stotred in CacheData Folder
 * 
 * 
 * @author hlp2-winser
 */

require_once '../Models/CrimeConfig.php';

class Cache {

    private $xmlLocation, $cacheLocation;

    function __construct() {
        // create the config object, and then get the data we need
        $crimeConfig = new CrimeConfig();
        $this->xmlLocation = $crimeConfig->getDataXMLName();
        $this->cacheLocation = $crimeConfig->getCacheLocation();
    }

    public function hasCacheFile($name, $type) {
        if (is_file($this->cacheLocation . $name . "." . $type)) {
            if ($this->_hasUpdate($name, $type)) {
                // http://stackoverflow.com/questions/4594180/deleting-all-files-from-a-folder-using-php
                $files = glob($this->cacheLocation . '*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                return false;
            } else {

                return true;
            }
        }

        return false;
    }

    public function getCacheFile($name, $type) {
        if ($type === "xml") {
            $xml = new DOMDocument();
            $xml->load($this->cacheLocation . $name . "." . $type);
            return $xml;
        }

        if ($type === "json") {
            return file_get_contents($this->cacheLocation . $name . "." . $type);
        }
    }

    public function createCacheFile($name, $data, $type) {

        if ($type === "xml") {
            $data->save($this->cacheLocation . $name . "." . $type);
        }

        if ($type === "json") {
            file_put_contents($this->cacheLocation . $name . "." . $type, $data);
        }
    }

    // checks last time main data was modified compared to the Cache location
    private function _hasUpdate($name, $type) {
        $cache = filemtime($this->cacheLocation . $name . "." . $type);
        $mainData = filemtime($this->xmlLocation);

        return $cache < $mainData;
    }

}

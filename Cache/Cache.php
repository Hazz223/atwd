<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cache
 *
 * @author Harry
 */
class Cache {

    public function hasCacheFile($name, $type) {

        if (is_file("../Cache/CacheData/" . $name . "." . $type)) {
            if ($this->hasUpdate($name, $type)) {
                // http://stackoverflow.com/questions/4594180/deleting-all-files-from-a-folder-using-php
                $files = glob('../Cache/CacheData/*');
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
            $xml->load("../Cache/CacheData/" . $name . "." . $type);
            return $xml;
        }

        if ($type === "json") {
            return file_get_contents("../Cache/CacheData/" . $name . "." . $type);
        }

        return false;
    }

    private function hasUpdate($name, $type) {
        $cache = filemtime("../Cache/CacheData/" . $name . "." . $type);
        $mainData = filemtime("../Data/CrimeStats.xml"); // this needs to be put into the config!

        return $cache < $mainData;
    }

    public function createCacheFile($name, $data, $type) {

        if ($type === "xml") {
            $data->save("../Cache/CacheData/" . $name . "." . $type);
        }

        if ($type === "json") {
            file_put_contents("../Cache/CacheData/" . $name . "." . $type, $data);
        }
    }

}

<?php

// We can cache this by looking at the request, and then store it in a text file. 
// check age of text file against the age of the XML file. If younger, serve it, if not, then delete and refresh.

require_once '../Models/RegionsModel.php';
require_once '../Models/CountriesModel.php';
require_once '../Models/FurtherStatisticsModel.php';
require_once '../Models/AreasModel.php';
require_once '../Exceptions/FieldNotFoundException.php';
require_once '../Cache/Cache.php';

$cache = new Cache();
$countryModel = new CountriesModel();
$regionModel = new RegionsModel();
$fStatsModel = new FurtherStatisticsModel();
$areasModel = new AreasModel();

$responseXML = new DOMDocument();
$base = $responseXML->createElement("reponse");

$base->setAttribute("timestamp", date("YmdHi"));
$crime = $responseXML->createElement("crimes");
$crime->setAttribute("year", "6-2013");
$type = $_GET["type"];
if (isset($_GET["region"])) {
    $givenRegionName = $_GET["region"];

    if ($fStatsModel->isFurtherStat($givenRegionName)) {
        if ($cache->hasCacheFile($givenRegionName . "-cache", $type)) {
            $data = $cache->getCacheFile($givenRegionName . "-cache", $type);
            $_SESSION["data"] = $data;
            $_SESSION["type"] = $type;

            include "../Views/CacheView.php";
        } else {
            try {
                $obj = $fStatsModel->getFurtherStatisticsByName($givenRegionName);

                $_SESSION["fStat"] = $obj;
                $_SESSION["type"] = $type;

                include '../Views/GetRequests/GetFurtherStatRequestView.php';
            } catch (FieldNotFoundException $ex) {
                $_SESSION["errorMessage"] = $ex->getMessage();
                $_SESSION["errorCode"] = $ex->getCode();

                include "../Views/Errors/ErrorView.php";
            } catch (Exception $ex) {
                $_SESSION["errorMessage"] = $ex->getMessage();
                $_SESSION["errorCode"] = 500;

                include "../Views/Errors/ErrorView.php";
            }
        }
    } else {
        if ($cache->hasCacheFile($givenRegionName . "-cache", $type)) {
            $data = $cache->getCacheFile($givenRegionName . "-cache", $type);
            $_SESSION["data"] = $data;
            $_SESSION["type"] = $type;

            include "../Views/CacheView.php";
        } else {
            try {
                $region = $regionModel->getRegionByName($givenRegionName);
                $_SESSION["region"] = $region;

                $areaArray = array();
                foreach ($region->getAreaNames() as $areaName) {
                    $areaArray[] = $areasModel->getAreaByName($areaName);
                }

                $_SESSION["areas"] = $areaArray;
                $_SESSION["type"] = $type;

                include '../Views/GetRequests/GetRegionRequestView.php';
            } catch (FieldNotFoundException $ex) {
                $_SESSION["errorMessage"] = $ex->getMessage();
                $_SESSION["errorCode"] = $ex->getCode();
                include "../Views/Errors/ErrorView.php";
            } catch (Exception $ex) {
                $_SESSION["errorMessage"] = $ex->getMessage();
                $_SESSION["errorCode"] = 500;

                include "../Views/Errors/ErrorView.php";
            }
        }
    }
} else {
    if ($cache->hasCacheFile("all-get", $type)) {
        $data = $cache->getCacheFile("all-get", $type);
        $_SESSION["data"] = $data;
        $_SESSION["type"] = $type;
        include "../Views/CacheView.php";
    } else {
        try {
            $regions = $regionModel->getAllRegions();
            $countries = $countryModel->getAllCountries();
            $fStats = $fStatsModel->getAllFurtherStatistics();
            
            $_SESSION["regions"] = $regions;
            $_SESSION["countries"] = $countries;
            $_SESSION["fStats"] = $fStats;
            $_SESSION["type"] = $type;

            include '../Views/GetRequests/FullGetRequestView.php';
        } catch (FieldNotFoundException $ex) {
            $_SESSION["errorMessage"] = $ex->getMessage();
            $_SESSION["errorCode"] = $ex->getCode();

            include "../Views/Errors/ErrorView.php";
        } catch (Exception $ex) {
            $_SESSION["errorMessage"] = $ex->getMessage();
            $_SESSION["errorCode"] = 500;

            include "../Views/Errors/ErrorView.php";
        }
    }
}

    
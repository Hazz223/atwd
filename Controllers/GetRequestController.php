<?php

/**
 * Description of GetRequestController
 * The controller that gets the 'full' get request,
 * or by region/national statistics
 * 
 * @author hlp2-winser
 */

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

$responseXML = new DOMDocument("1.0");
$base = $responseXML->createElement("reponse");

$base->setAttribute("timestamp", date("YmdHi"));
$crime = $responseXML->createElement("crimes");
$crime->setAttribute("year", "6-2013");

$type = $_GET["type"];

if (isset($_GET["region"])) { // checks if it's a full get request, or just region
    // either a region or a National Stat
    $givenRegionName = $_GET["region"];

    if ($fStatsModel->isFurtherStat($givenRegionName)) {
        if ($cache->hasCacheFile($givenRegionName . "-cache", $type)) { // check for the cache
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
        // is a region
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
    // full get request
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

    
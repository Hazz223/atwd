<?php

// This only works with areas
// This doesn't work with wales.

require_once '../Models/AreasModel.php';
require_once '../Models/FurtherStatisticsModel.php';

$areaName = $_GET["area"];
$data = $_GET["data"];
$type = $_GET["type"];

$areasModel = new AreasModel();
$furtherStatsModel = new FurtherStatisticsModel();

try {
    if ($areasModel->isArea($areaName)) {
        $old = $areasModel->getAreaByName($areaName);
        $areasModel->UpdateAreaTotal($old->getName(), $data);
        $new = $areasModel->getAreaByName($areaName);

        $_SESSION["old"] = $old;
        $_SESSION["new"] = $new;
        $_SESSION["type"] = $type;
    } else {
        
        if ($furtherStatsModel->isFurtherStat($areaName)) {
            $oldFurtherStat = $furtherStatsModel->getFurtherStatisticsByName($areaName);
            $furtherStatsModel->updateTotal($areaName, $data);
            $newFurtherStat = $furtherStatsModel->getFurtherStatisticsByName($areaName);
            
            $_SESSION["type"] = $type;
            $_SESSION["old"] = $oldFurtherStat;
            $_SESSION["new"] = $newFurtherStat;
        }
        else{
            throw new FieldNotFoundException("[".$areaName."] is not a National Statistic or an Area");
        }
    }

    include "../Views/PutRequestView.php";
} catch (FieldNotFoundException $ex) {
    $_SESSION["errorMessage"] = $ex->getMessage();
    $_SESSION["errorCode"] = $ex->getCode();

    include "../Views/Errors/ErrorView.php";
} catch (Exception $ex) {
    $_SESSION["errorMessage"] = $ex->getMessage();
    $_SESSION["errorCode"] = 500;

    include "../Views/Errors/ErrorView.php";
}

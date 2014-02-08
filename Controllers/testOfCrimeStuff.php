<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../Models/AreasModel.php';

$areaModel = new AreasModel();

$area = $areaModel->getAreaByName("Northumbria");

$data = $area->getCrimeData();
var_dump($area);
//foreach($data as $d){
//    $crimeList = $d->getCrimeList();
//    if(isset($crimeList)){
//        var_dump($crimeList);
//    }
//    else{
//        echo"No Crime List Available</br>";
//    }
//}
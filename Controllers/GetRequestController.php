<?php



if(isset($_GET["region"])){
    $region = $_GET["region"]; // this needs to be changed to the safer version

    echo $region."</br>";
}else{
    require_once '../Models/RegionsModel.php';
    
    $regionModel = new RegionsModel();
    
    $regions = $regionModel->getAllRegions(); // gets all regions correctly.
    
    foreach($regions as $region){
       var_dump($region); 
       
    }
}




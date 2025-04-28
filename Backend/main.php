<?php

    include("./api_utils.php");
    include("./database.php");

    $connection = connect_database("localhost", "root", "", "aqi");
    
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");

    if(!isset($_GET["lat"]) || !isset($_GET["lon"])){
        echo '{error: lat and lon are not defined}';
        exit;
    }

    $lat = $_GET["lat"];
    $lon = $_GET["lon"];

    $lat = intval($lat);
    $lon = intval($lon);
    

 


    $allData = get_air_quality_data($connection, $lat, $lon);
    $currentData = count($allData) > 0 ? $allData[0] : null;

    $refreshTime = 1;

    $needsRefresh = !$currentData || (
        isset($currentData["timestamp"]) && time()-$currentData["timestamp"]>$refreshTime);

        if($needsRefresh){
            $apiData = fetch_current_data($lat, $lon);
            $insertedData = insert_air_quality_data($connection, $lat, $lon, $apiData);
            if(isset($_GET["history"])){
                $allData = get_air_quality_data($connection, $lat, $lon);
                echo json_encode($allData);
                exit;
            }
            echo json_encode($insertedData);
            exit;
        }
    
    if(isset($_GET["history"])){
        echo json_encode($allData);
        exit;
    }


    if($currentData){
        echo json_encode($currentData);
        exit;
    }

    $apiData = fetch_current_data($lat, $lon);
    $insertedData = insert_air_quality_data($connection, $lat, $lon, $apiData);

    echo json_encode($insertedData);



?>

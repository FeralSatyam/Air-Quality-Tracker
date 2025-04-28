<?php 

    function connect_database($server, $username, $password, $db) {
        try{

            //connect to database server

            $connection = new mysqli($server, $username, $password);
            if($connection -> connect_error){
                echo "CONNECTION ERROR";
                exit;
            }

            $connection->query("CREATE DATABASE IF NOT EXISTS ".$db.";");

            $connection->query("USE ".$db.";");

            $connection->query("CREATE TABLE IF NOT EXISTS air_quality (
                id INT AUTO_INCREMENT PRIMARY KEY,
                lat INT,
                lon INT,
                timestamp INT,
                pm2 FLOAT,
                pm10 FLOAT
            )");

            return $connection;

        } catch(Throwable $th){
            exit;
        }
    }

    function insert_air_quality_data($connection, $lat, $lon, $apiData){
    $lat = $apiData["coord"]["lat"];
    $lon = $apiData["coord"]["lon"];
    $pm2 = $apiData["list"][0]["components"]["pm2_5"];
    $pm10 = $apiData["list"][0]["components"]["pm10"];
    $timestamp = $apiData["list"][0]["dt"];


        $result = $connection->query("
        INSERT INTO `air_quality` (lat, lon, pm2, pm10, timestamp) VALUES(
            ".$lat.", 
            ".$lon.",
            ".$pm2.",
            ".$pm10.",
            ".$timestamp."
            )
        ");

        if($result){
            $last_id = $connection->insert_id;
            $lastResult = $connection->query("SELECT * FROM air_quality WHERE id=".$last_id.";");
            if($lastResult){
                $data = $lastResult->fetch_all(MYSQLI_ASSOC);
                return $data[0];
            } else{
                return null;
            }
        }
        else{
            return null;
        }


    }

    function get_air_quality_data($connection, $lat, $lon){
        try{

            $query = "SELECT * FROM `air_quality` WHERE lat = ".$lat." AND lon = ".$lon." ORDER BY timestamp DESC;";

            $result = $connection->query($query);
            if($result){
                $data = $result->fetch_all(MYSQLI_ASSOC);
                return $data;
            } else{
                return null;
            }

        }catch(Throwable $th){
            return null;
        }
    }

?>
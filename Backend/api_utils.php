<?php 
    function fetch_current_data($lat, $lon){
        try {
            $url = "http://api.openweathermap.org/data/2.5/air_pollution?lat=".$lat."&lon=".$lon."&appid=a886c522ad3cbe2a04e77605628011b0";
            $responseString = file_get_contents($url);
            $data = json_decode($responseString, true);
            return $data;
        } catch(\Throwable $th){
            return null;
        }
    }
?>
<?php

//initialize vars
   if(!(empty($_POST['editTrip']))) {
        $tripdescrip = $startdate = $enddate = $location = $activity = $diff = $result =$message = $descripErr = $locErr = "";
        $success = "not set"; $dateErr = $actErr= "";

        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        else {
            // update database with new trip
            if($_SERVER["REQUEST_METHOD"] == "POST"){
                
                 $query = "UPDATE Trips SET ";

                $tripdescrip = filter_input(INPUT_POST,'tripdescrip',FILTER_SANITIZE_STRING);
                
                if(!empty($tripdescrip)) {
                    if (!preg_match("/^[a-zA-Z ]{8,64}$/",$tripdescrip)){
                        $descripErr = "fail";
                        echo("<p>Please enter between 8 and 64 valid characters.</p>");
                    } else {
                        $query = $query."tripDescrip = '$tripdescrip', ";
                    }
                }
                
                $startdate = $_POST['startdate'];
                
                if(!empty($startdate)) {
                    $query = $query."startDate = '$startdate', ";
                }
                
                
                $enddate = $_POST['enddate'];
                
                if(!empty($enddate)) {
                    $query = $query."endDate = '$enddate', ";
                }
                
                $location = filter_input(INPUT_POST,'location',FILTER_SANITIZE_STRING);
                
                if(!empty($location)) {
                    if (!preg_match("/^[a-zA-Z0-9, ]{8,64}$/",$location)){
                        $locErr = "Please enter between 8 and 64 valid characters.";
                    } else {
                        $query = $query."location = '$location', ";
                    }
                }
                
                $activity = filter_input(INPUT_POST, 'activity', FILTER_SANITIZE_NUMBER_INT);
                
                if(!(empty($activity))) {
                    $query = $query."activity = $activity, ";
                }
                
                if(!(empty($_POST['diff']))) {
                    $query = $query."difficulty = {$_POST['diff']}, ";
                }
                
                $tripID = $_POST['tripID'];
                if(!empty($tripID)) {
                    $query = $query."tripID = $tripID WHERE tripID = $tripID";
                }
                
                if(strtotime($enddate) < strtotime($startdate)){
                    echo("<p>Please choose an end date after the start date.</p>");
                    $query = "UPDATE Trips SET tripID = $tripID WHERE tripID = $tripID";
                } 
                
                
                if($locErr == "" && $descripErr =="" && $dateErr =="" && $actErr ==""){
                    if($result = $conn->query($query)){
                        
                        echo("<p>Your update worked!</p>");   
                    }
                    else{
                        echo("<p>Your update failed! Remember you must update all the fields correctly or not at all");
                    }
                }
                else {
                    $success = 'false';
                    $message = "Prepare failed or input incorrect.";
                }
            }
            else {
                $success = 'false';
            }
        }
 }
?>
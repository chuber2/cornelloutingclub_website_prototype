<?php

    if(!(empty($_POST['editActivity']))) {
        
        //true if either no image was uploaded or the image is valid
        $imgCheck = true;
        
        //Get the actID from the select tag of the Activities.php page submitted. Be sure to filter the input
        $actIDInput = (int)(filter_input(INPUT_POST, 'editedActivity', FILTER_SANITIZE_NUMBER_INT)); 
        
        //write query to perform update
        $activitiesEditQuery = "UPDATE activities SET ";
        
        //
        $actNameInput = filter_input(INPUT_POST, 'actNameEdit', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if(!(empty($actNameInput))) {
            $activitiesEditQuery = $activitiesEditQuery."actName = '$actNameInput', ";
        }
    
        $actBlurbInput = filter_input(INPUT_POST, 'actBlurbEdit', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if(!(empty($actBlurbInput))) {
            $activitiesEditQuery = $activitiesEditQuery."actBlurb = '$actBlurbInput', ";
        }
    
        if(is_uploaded_file($_FILES['actImgEdit']['tmp_name'])) {
            $newFile = $_FILES['actImgEdit'];
            if($newFile['error'] != 0) {
                echo("<p>There was an error uploading your image. Try again!</p>");
                $file_name = null;
            } else {
                $originalName = str_replace(' ', '_', $newFile['name']);

                //Gets the position of the dot in the string so it can be parsed to properly use the SQL Query
                $dot_pos = strrpos($originalName, '.');
                $file_name = substr($originalName, 0, $dot_pos);
                $file_type = substr($originalName, $dot_pos+1);
                $tempName = $newFile['tmp_name'];
                
                //checks that the image is valid
                $imgCheck = getimagesize($tempName);
                
                if($imgCheck) {
                    //positions file at right point in the directory for SQL query
                    $file_insert = 'img/activities/'.$file_name.'.'.$file_type;
                    $activitiesEditQuery = $activitiesEditQuery."actImg = '$file_insert', ";

                    move_uploaded_file($tempName, "img/activities/$originalName");
                } 

            }
        }

        //setup mysqli connection
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        
        //begin prepared statement
        $stmt = $mysqli->stmt_init();
        
        $activitiesEditQuery = $activitiesEditQuery."actID = ? WHERE actID = ?";
        
        if($imgCheck) {
            
            //execute query
            if ($stmt->prepare($activitiesEditQuery)) {     
                $stmt->bind_param('ss', $actIDInput, $actIDInput);  
                $stmt->execute(); 
                echo("<p>Your update worked! This message will soon be removed.</p>");
                header("Refresh:0");
            } else {
                echo("<p class='error'>Your update failed. Make sure you only select an activity from those listed.</p>");
            }
            
        } else{
            echo("<p>You did not upload a valid image!</p>");
        }
        
        //close mysqli connection
        $mysqli->close();

    }
?>
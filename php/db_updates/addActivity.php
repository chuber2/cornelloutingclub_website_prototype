<?php 

//Helps to implement the addition of an EBoard member functionality

if(!(empty($_POST['addActivity']))) {
    
    //true if either no image was uploaded or the image is valid
    $imgCheck = true;
    
    //access all the variables from the input form
    $actNameInput = filter_input(INPUT_POST, 'actName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    $actBlurbInput = filter_input(INPUT_POST, 'actBlurb', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    if(!(empty($_FILES['actImg']))) {
        $newFile = $_FILES['actImg'];
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
                move_uploaded_file($tempName, "img/activities/$originalName");
            }
        }
    } else {
        $file_name = null;
    }
    
    if(empty($actNameInput) || empty($actBlurbInput) || empty($file_name)) {
        echo("<p>Sorry, you need to enter a value for all fields. Please try again!</p>");
    } else {
        
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        
        //Performing check for duplicate Executive Board members to be added
        $dupCheck = false; 
        $dupQuery = "SELECT actName FROM activities";
        $stmt = $mysqli->stmt_init();
        if($stmt->prepare($dupQuery)) {
            $stmt->execute();
            $stmt->bind_result($actName);
            while ($stmt->fetch())
                
                //trim, htmlspecialchars, and strtoupper all used to make sure that the input will match the DB
                if(htmlspecialchars(trim(strtoupper($actName))) == htmlspecialchars(trim(strtoupper($actNameInput)))) {
                    $dupCheck = true;
                }
            
        }
        
        //uses dupCheck to say what code should execute
        
        if($dupCheck) {
            echo("<p class='error'>Sorry, this activity was already added. Try again!</p>");
        } else {
            
            //checks that the image is valid
            if($imgCheck) {
                //concatenates file_name and file_type to insert the right file into the database with its proper location
                $file_insert = 'img/activities/'.$file_name.'.'.$file_type;

                $query = "INSERT INTO Activities (actName, actBlurb, actImg) VALUES (?, ?, ?)";

                $stmt = $mysqli->stmt_init();
                if($stmt->prepare($query)) {
                    $stmt->bind_param('sss', $actNameInput, $actBlurbInput, $file_insert);
                    if($stmt->execute()) {
                        echo("<p>Your addition was made! This message will soon disappear.</p>");
                        header("Refresh:0");
                    } else {
                        echo("<p class='error'>Your addition failed. Please try again.</p>");
                    }
                }
            } 
            
            else {
                echo("<p>Sorry! You did not add a valid image!</p>");
            }
        }
        
        $mysqli->close();
    }
    
}
<?php 
    if(!(empty($_POST['editEboardMember']))) {
        
        //true if either no image was uploaded or the image is valid
        $imgCheck = true;
        
        //Get the positionID from the select tag of the editEboard.php page submitted. Be sure to filter the input
        $positionID = filter_input(INPUT_POST, 'editEboardMemberOption', FILTER_SANITIZE_NUMBER_INT); 
        //echo $positionID;
        
        /*Use prepared statements to write an update statement in conjunction with MySQL prepared statements to update the code. The query should look something like UPDATE EBoard SET positionName = (user inputted position), bio = (user inputted bio), posImg = (user inputted image) WHERE userID = (user inputted ID from select tag)
        *Note that a user should be able to update however many fields are selected; not all fields need to be selected
        */
        
        //write query to perform update
        $execBoardEditQuery = "UPDATE EBoard SET ";
        
        //
        $positionNameInput = filter_input(INPUT_POST, 'position', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if(!(empty($positionNameInput))) {
            $execBoardEditQuery = $execBoardEditQuery."positionName = '$positionNameInput', ";
        }
    
        $bio = filter_input(INPUT_POST, 'biography', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if(!(empty($bio))) {
            $execBoardEditQuery = $execBoardEditQuery."bio = '$bio', ";
        }
    
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        if(!(empty($email))) {
            $execBoardEditQuery = $execBoardEditQuery."email = '$email', ";
        }
    
        if(is_uploaded_file($_FILES['profilePhoto']['tmp_name'])) {
            $newFile = $_FILES['profilePhoto'];
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
                    $file_insert = 'img/eboard/'.$file_name.'.'.$file_type;
                    $execBoardEditQuery = $execBoardEditQuery."posImg = '$file_insert', ";

                    move_uploaded_file($tempName, "img/eboard/$originalName");
                } 
                
            }
        } 

        //checks to ensure there's a valid image
        if($imgCheck) {
            //setup mysqli connection
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

            //begin prepared statement
            $stmt = $mysqli->stmt_init();
            
            //print("execBoardQuery".$execBoardEditQuery."positionID=$positionID WHERE positionID = $positionID");
            $execBoardEditQuery = $execBoardEditQuery."positionID = $positionID WHERE positionID = ?";
            
            //execute query
            if ($stmt->prepare($execBoardEditQuery)) {     
                $stmt->bind_param('i', $positionID);  
                $stmt->execute(); 
                echo("<p>Your update worked!</p>");
            } else {
                echo("<p class='error'>Your update failed. Make sure you only select an EBoard member from those listed.</p>");
            }
            
            //close mysqli connection
            $mysqli->close();
        } else {
            echo("<p>You did not upload a valid image!</p>");
        }
        
    }
?>
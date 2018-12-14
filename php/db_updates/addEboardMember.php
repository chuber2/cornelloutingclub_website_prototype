<?php 

//Helps to implement the addition of an EBoard member functionality

if(!(empty($_POST['addEboardMember']))) {
    
    //true if either no image was uploaded or the image is valid
    $imgCheck = true;
    
    $positionNameInput = filter_input(INPUT_POST, 'position', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    $bio = filter_input(INPUT_POST, 'biography', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    $newFile = null;
    
    if(!(empty($_FILES['profilePhoto']))) {
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
                move_uploaded_file($tempName, "img/eboard/$originalName");
            }
            
        }
    } else {
        $file_name = null;
    }
    
    if(empty($positionNameInput) || empty($bio) || empty($email) || empty($newFile)) {
        echo("<p>Sorry, you need to enter a value for all fields. Please try again!</p>");
    } else {
        
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        
        //Querying the database to perform operations to ensure that a duplicate EBoard member is not created
        
        //need to find user ID of the member whose email is being promoted
        $userIDQuery = "SELECT userID FROM Users WHERE email = ?";
        $stmt = $mysqli->stmt_init();
        if($stmt->prepare($userIDQuery)) {
            $stmt->bind_param('s', $email);
            if($stmt->execute()) {
                $stmt->bind_result($userID);
                while ($stmt->fetch())
                    $userIdentification = $userID;
            } else {
                echo("<p class='error'>Sorry, the email was entered incorrectly. Try again!</p>");
            }
        }
        
        //Performing check for duplicate Executive Board members to be added
        $dupCheck = false; 
        $dupQuery = "SELECT Users.userID, positionName FROM Users INNER JOIN EBoard ON Users.userID = EBoard.userID WHERE email = ?";
        $stmt = $mysqli->stmt_init();
        if($stmt->prepare($dupQuery)) {
            $stmt->bind_param('s', $email);
            if($stmt->execute()){
                $stmt->bind_result($userID, $positionName);
                while ($stmt->fetch()) {

                    //trim, htmlspecialchars, and strtoupper all used to make sure that the input will match the DB
                    if($userIdentification == $userID && htmlspecialchars(trim(strtoupper($positionNameInput))) == htmlspecialchars(trim(strtoupper($positionName)))) {
                        $dupCheck = true;
                    }
                }
            } else {
                echo("<p class='error'>Sorry, the email was entered incorrectly. Try again!</p>");
            }
            
        }
        
        //uses dupCheck to say what code should execute
        
        if($dupCheck) {
            echo("<p class='error'>Sorry, an EBoard member has already been entered with these credentials. To avoid duplicates, this feature is not allowed. Try again!</p>");
        } else {
            
            if($imgCheck) {
                //concatenates file_name and file_type to insert the right file into the database with its proper location
                $file_insert = 'img/eboard/'.$file_name.'.'.$file_type;
                
                if( isset($userIdentification) )  {
                    $query = "INSERT INTO EBoard (positionName, bio, posImg, userID) VALUES (?, ?, ?, $userIdentification)";

                    $stmt = $mysqli->stmt_init();
                    if($stmt->prepare($query)) {
                        $stmt->bind_param('sss', $positionNameInput, $bio, $file_insert);
                        if($stmt->execute()){
                            echo("<p>Your addition was made!</p>");
                        } else {
                            echo("<p class='error'>Your addition failed. Please try again.</p>");
                        }
                    }
                } else{
                    echo("<p class='error'>Please enter the email of a user who is already in the system.</p>");
                }
            }
            
            else {
                echo("<p>You did not upload a valid image!</p>");
            }
            
        }
        
        $mysqli->close();
    }
    

}
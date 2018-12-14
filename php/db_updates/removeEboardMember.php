<?php
    if(!(empty($_POST['removeEboardMember']))) {
        
        //get positionID from select statement
        $positionID = (int)(filter_input(INPUT_POST, 'removedEboardMember', FILTER_SANITIZE_NUMBER_INT)); 
        
        
        //setup mysqli connection
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        
        //begin prepared statement
        $stmt = $mysqli->stmt_init();
        
        //write query to perform deletion
        $execBoardRemoveQuery = "DELETE FROM EBoard WHERE positionID = ?";
        $execTest = "DELETE FROM EBoard WHERE positionID = $positionID";
        print("EXECTEST IS ".$execTest);
        
        //execute query
        if ($stmt->prepare($execBoardRemoveQuery)) {     
            $stmt->bind_param('i', $positionID);  
            $stmt->execute(); 
            echo("<p>Your deletion worked!</p>");
        } else {
            echo("<p class='error'>Your deletion failed. Make sure you only select an EBoard member from those listed</p>");
        }
        
        //close mysqli connection
        $mysqli->close();
    }
?>
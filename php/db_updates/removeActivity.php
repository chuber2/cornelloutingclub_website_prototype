<?php

if(!(empty($_POST['removeCurrentActivity']))) {
    
    //get actID from select statement
    $actIDInput = (int)(filter_input(INPUT_POST, 'removedActivity', FILTER_SANITIZE_NUMBER_INT)); 


    //setup mysqli connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    //begin prepared statement
    $stmt = $mysqli->stmt_init();

    //write query to perform deletion
    $activityRemoveQuery = "DELETE FROM activities WHERE actID = ?";

    //execute query
    if ($stmt->prepare($activityRemoveQuery)) {     
        $stmt->bind_param('s', $actIDInput);  
        if($stmt->execute()) {
            echo("<p>Your deletion worked! This message will soon disappear.</p>");
            header("Refresh:0");
        } else {
            echo("<p class='error'>Your deletion failed. Make sure you only select an activity from those listed</p>");
        }
    } else {
        echo("<p class='error'>Your deletion failed. Make sure you only select an activity from those listed</p>");
    }

    //close mysqli connection
    $mysqli->close();
}

?>
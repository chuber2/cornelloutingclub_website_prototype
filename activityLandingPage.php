<!DOCTYPE html>
<html>
    <?php 
        session_start(); 
        require_once("php/config.php"); 
        $actID = 0;
        //gets the activity ID from the url        
        $actID = FILTER_INPUT(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if($actID == 0){
            header("Refresh:0; url=activities.php");
            exit();
        }
    ?>
        <head>
            <meta charset="utf-8">
            <title>Cornell Outing Club</title>
            <!--<link type="text/css" rel="stylesheet" href="css/style.css">-->
            <link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <link type="text/css" rel="stylesheet" href="css/style.css">
            <link type="text/css" rel="stylesheet" href="css/navbar.css">
            <link type="text/css" rel="stylesheet" href="css/activitiesLandingPage.css">
            <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
            <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
            <meta name="viewport" content="width=device-width">
            <!-- <meta name="viewport" content="width=device-height, initial-scale=1.0"> -->

        </head>
    <?php include "php/nav.php"; ?>
    
    <div class="container-fluid">
        <div class="row" id="fullRow">
            <div class= "member_col">
    <?php
        
        //gets the activity ID from the url        
        $actID = FILTER_INPUT(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                
        if(empty($actID)) {
            header("Refresh:0; url=activities.php");
            exit();
        }
                      
        //sets up mysqli connection
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME,PORT);
          
        $activityQuery = "SELECT * FROM activities WHERE actID = $actID";
        
        //queries database to get activities
        if($result = $mysqli->query($activityQuery)) {

            while($row = $result->fetch_assoc()) {
                echo("
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='activityName'>
                        <h1 class='activityCaption'>{$row['actName']}</h1>
                    </div>
                    <img class='activityImage' src='{$row['actImg']}' alt='{$row['actName']}'/>
                    <p class='activityBlurb'>{$row['actBlurb']}</p>
                </div>
            ");
            }
        } else {
            echo("<p class='error>You did not select a valid activity. Try again.</p>");
        }
                
        //Run while loop to get each $row of a record from the returned SQL result
                
        //Output the actName, actBlurb, and actImg onto the page
                
    ?>
                
            </div>
        </div>
    </div>
</html>
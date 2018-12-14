<!DOCTYPE html>
<html>
    <?php session_start(); 
        require_once("php/config.php") ?>
        <head>
            <meta charset="utf-8">
            <title>Cornell Outing Club</title>
            <!--<link type="text/css" rel="stylesheet" href="css/style.css">-->
            <link type="text/css" rel="stylesheet" href="css/activities.css">
            <link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <link type="text/css" rel="stylesheet" href="css/style.css">
            <link type="text/css" rel="stylesheet" href="css/navbar.css">
            <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
            <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
            <meta name="viewport" content="width=device-width">
            <!-- <meta name="viewport" content="width=device-height, initial-scale=1.0"> -->

        </head>
    <?php include "php/nav.php"; ?>
    <div class="container-fluid">
        <div class="row" id="fullRow">
                   <div class='activityName'>
                       <h1>Activities</h1>
                    </div>
                
                    <p>Click an image to learn more about the activity!</p>


                    <?php

                    //brings in the config to set up mysqli connection
                    require_once("php/config.php");
                    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, PORT);

                    //queries database to get activities
                    $activityQuery = "SELECT * FROM activities";
                    $result = $mysqli->query($activityQuery);

                    while($row = $result->fetch_assoc()) {
                        echo("
                        
                        <div class='col-md-12 col-sm-12 col-xs-12'>
                            <h2>{$row['actName']}</h2>
                            <div class='imageWrapper'>
                                <a href='activityLandingPage.php?id=".$row['actID']."'>
                                    <img class='activityImage' src='{$row['actImg']}' alt='{$row['actName']}'/>
                                </a>
                            </div>
                        </div>
                    ");
                    }


                    ?>

        </div>
	</div>
    <?php include "php/footer.php";?>
</html>
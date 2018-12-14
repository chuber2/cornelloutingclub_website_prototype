<!DOCTYPE html>
<html>
    <?php include "php/head.php"; ?>
    <?php include "php/nav.php"; ?>
    
    <div class="container-fluid">
        <div class="row" id="fullRow">
            <div class= "member_col">
    
    <?php
        
        //check if the user is an admin
        if(isset($_SESSION["admin"])) {
            
            echo('<h1> Admin Tools- Activities </h1> ');
        
            //Admins will be able to add new activities to craft new landing pages for activities
            echo('<div class="member col-md-12 col-sm-12 col-xs-12 wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">');

                echo("<h2>Add an Activity</h2>");

                include 'php/db_updates/addActivity.php';

                echo("

                    <form action='editActivities.php' method='post' enctype='multipart/form-data' id='addActivities'>
                        <table  class='editingForm'>
                            <tr>
                                <th>Field</th>
                                <th>Value to Input</th>
                            </tr>
                            <tr>

                                <td>Activity Name</td>
                                <td>
                                    <input type='text' name='actName' class='textInput'>
                                </td>
                            </tr>
                            <tr>
                                <td>Activity Description</td>
                                <td>
                                    <textarea name='actBlurb' class='textInput' form='addActivities'></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>Banner Image</td>
                                <td>
                                    <input type='file' name='actImg'>
                                </td>
                            </tr>
                            <tr>
                                <td><input class='button' type='submit' name='addActivity' value='Add New Activity'></td>
                                <td></td>
                            </tr>
                        </table>
                    </form>");

                echo("</div>");

            //Admins will be able to delete existing activities 
            echo '<div class="member col-md-12 col-sm-12 col-xs-12 wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">';


                echo("<h2>Remove an Activity</h2>");
                include 'php/db_updates/removeActivity.php';

                echo("
                <form action = 'editActivities.php' method='post'>
                    <table  class='editingForm'>
                            <tr>
                                <th>Field</th>
                                <th>Activity to Remove</th>
                            </tr>
                            <tr>

                                <td>Activity Name</td>
                                <td>
                                    <select name='removedActivity'>");
                                        require_once('php/config.php');
                                        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, PORT);

                                        $activityQuery = 'SELECT * FROM activities';
                                        $activityResults = $mysqli->query($activityQuery);
                                        if($activityResults) {
                                            while($row = $activityResults->fetch_assoc()) {
                                                echo("<option value='{$row['actID']}'>{$row['actName']}");
                                            }
                                        }

                                    echo("</select>
                                </td>
                            </tr>
                            <tr>
                                <td><input class='button' type='submit' name='removeCurrentActivity' value='Remove Activity'></td>
                                <td></td>
                            </tr>
                    </table>
                </form>");

                echo("<div>");


                 echo '<div class="member col-md-12 col-sm-12 col-xs-12 wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">';


                echo("<h2>Edit an Activity's Information</h2>");
                include 'php/db_updates/editActivity.php';

                echo("
                <form action = 'editActivities.php' method='post' id='editActivities' enctype='multipart/form-data'>
                    <table  class='editingForm'>
                            <tr>
                                <th>Field</th>
                                <th>Information to Update</th>
                            </tr>
                            <tr>

                                <td>Select Activity</td>
                                <td>
                                    <select name='editedActivity'>");
                                       require_once('php/config.php');
                                        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, PORT);

                                        $activityQuery = 'SELECT * FROM activities';
                                        $activityResults = $mysqli->query($activityQuery);
                                        if($activityResults) {
                                            while($row = $activityResults->fetch_assoc()) {
                                                echo("<option value='{$row['actID']}'>{$row['actName']}</option>");
                                            }
                                        } 

                                    echo("</select>
                                </td>
                            </tr>
                            <tr>

                                <td>Activity Name</td>
                                <td>
                                    <input type='text' name='actNameEdit' class='textInput'>
                                </td>
                            </tr>
                            <tr>
                                <td>New Description</td>
                                <td>
                                    <textarea name='actBlurbEdit' class='textInput' form='editActivities'></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>New Banner Image</td>
                                <td>
                                    <input type='file' name='actImgEdit'>
                                </td>
                            </tr>
                            <tr>
                                <td><input class='button' type='submit' name='editActivity' value='Edit Activity Record'></td>
                                <td></td>
                            </tr>
                    </table>
                </form>");

                echo("<div>");
        } 
           
        //if the user is not an admin, tell them they must be an admin to access content
        else {
            echo("<p class='error'>You must be logged in as an admin to access this page. Please login as an admin to continue!</p>");
        }
    ?>
            </div>
        </div>
    </div>

    <?php include "php/footer.php";?>
</html>
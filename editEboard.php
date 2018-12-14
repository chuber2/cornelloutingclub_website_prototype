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
        
            //Admins will be able to add new members to the Executive Board page. 
            echo('<div class="member col-md-12 col-sm-12 col-xs-12 wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">');
                
                echo('<h1> Admin Tools- EBoard </h1> ');
            
                echo("<h2>Add a Member to the Executive Board</h2>
                <em>Note that the executive board member must have been already created as a normal member.</em>
                <em>All fields must be filled out and an image must be uploaded.</em>");

                include 'php/db_updates/addEboardMember.php';

                echo("

                    <form action='editEboard.php' method='post' id='addEboard' enctype='multipart/form-data'>
                        <table  class='editingForm'>
                            <tr>
                                <th>Field</th>
                                <th>Value to Input</th>
                            </tr>
                            <tr>

                                <td>Position</td>
                                <td>
                                    <input type='text' name='position' class='textInput'>
                                </td>
                            </tr>
                            <tr>
                                <td>Biography</td>
                                <td>
                                    <textarea name='biography' class='textInput' form='addEboard'></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>Email of New Executive Board Member</td>
                                <td>
                                    <input type='text' name='email' class='textInput'>
                                </td>
                            </tr>
                            <tr>
                                <td>Profile Image</td>
                                <td>
                                    <input type='file' name='profilePhoto'>
                                </td>
                            </tr>
                            <tr>
                                <td><input class='button' type='submit' name='addEboardMember' value='Add EBoard Member'></td>
                                <td></td>
                            </tr>
                        </table>
                    </form>");

                echo("</div>");

            //Admins will be able to delete new members of the Executive Board page.
            echo '<div class="member col-md-12 col-sm-12 col-xs-12 wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">';


                echo("<h2>Remove a Member from the Executive Board</h2>");
                include 'php/db_updates/removeEboardMember.php';

                echo("
                <form action = 'editEboard.php' method='post'>
                    <table  class='editingForm'>
                            <tr>
                                <th>Field</th>
                                <th>Member to Remove</th>
                            </tr>
                            <tr>

                                <td>Executive Board Member</td>
                                <td>
                                    <select name='removedEboardMember'>");
                                        require_once('php/config.php');
                                        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, PORT);

                                        $execBoardQuery = 'SELECT * FROM Users INNER JOIN EBoard ON Users.userID = EBoard.userID';
                                        $execBoardResults = $mysqli->query($execBoardQuery);
                                        if($execBoardResults) {
                                            while($row = $execBoardResults->fetch_assoc()) {
                                                echo("<option value='{$row['positionID']}'>{$row['name']}, {$row['positionName']}");
                                            }
                                        } else {
                                            echo("<p class='error'>Executive Board members query failed to load");
                                        }

                                    echo("</select>
                                </td>
                            </tr>
                            <tr>
                                <td><input class='button' type='submit' name='removeEboardMember' value='Remove EBoard Member'></td>
                                <td></td>
                            </tr>
                    </table>
                </form>");

                echo("</div>");


                 echo '<div class="member col-md-12 col-sm-12 col-xs-12 wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">';


                echo("<h2>Edit an Executive Board Member's Record</h2>");
                include 'php/db_updates/editEboardMember.php';

                echo("
                <form action = 'editEboard.php' method='post' id='editEboard' enctype='multipart/form-data'>
                    <table  class='editingForm'>
                            <tr>
                                <th>Field</th>
                                <th>Information to Update</th>
                            </tr>
                            <tr>

                                <td>Select Executive Board Member</td>
                                <td>
                                    <select name='editEboardMemberOption'>");
                                        require_once('php/config.php');
                                        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                                        
                                        $execBoardQuery = 'SELECT * FROM Users INNER JOIN EBoard ON Users.userID = EBoard.userID';
                                        $execBoardResults = $mysqli->query($execBoardQuery);
                                        if($execBoardResults) {
                                            while($row = $execBoardResults->fetch_assoc()) {
                                                echo("<option value='{$row['positionID']}'>{$row['name']}, {$row['positionName']}");
                                            }
                                        } else {
                                            echo("<p class='error'>Executive Board members query failed to load");
                                        }

                                    echo("</select>
                                </td>
                            </tr>
                            <tr>

                                <td>Position</td>
                                <td>
                                    <input type='text' name='position' class='textInput'>
                                </td>
                            </tr>
                            <tr>
                                <td>Biography</td>
                                <td>
                                    <textarea name='biography' class='textInput' form='editEboard'></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>Profile Image</td>
                                <td>
                                    <input type='file' name='profilePhoto'>
                                </td>
                            </tr>
                            <tr>
                                <td><input class='button' type='submit' name='editEboardMember' value='Edit EBoard Record'></td>
                                <td></td>
                            </tr>
                    </table>
                </form>");

                echo("</div>");
        }
                
        //if the user is not logged in as admin, say they must log in
        else {
            echo("<p class='error'>You must be logged in to access this page.</p>");
        }
    ?>
            </div>
        </div>
    </div>

    <?php include "php/footer.php";?>
</html>
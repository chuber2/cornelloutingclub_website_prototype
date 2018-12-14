<!DOCTYPE html>
<html>
    <?php
        session_start();
        require_once("php/config.php"); 
        $tripID = 0;
        $userID = 0;

        //sets up mysqli connection
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, PORT);
        
        $isLeader = false;
    
        //gets the trip ID from the url        
        $tripID = FILTER_INPUT(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if(isset($_POST["editTrip"])){
            header("Refresh:0; url=specifictrip.php?id=".$tripID);
        }
    
        $email = "";
        $joined = false;
        $left = false;
        $deleted = false;
    
        if(isset($_SESSION["user"]) ) {
            $email = $_SESSION["user"]; 
        }
        //get the user email stored in admin, if admin.
        if(isset($_SESSION["admin"])) {
            $email = $_SESSION["admin"];
        }
            
        if($tripID == 0 || $email == ""){
            header("Refresh:0; url=trips.php");
            die();
        }
    
        //select user id
        $userQuerya = "SELECT * FROM Users WHERE email ='".$email."'";
    
        if($resulta = $mysqli->query($userQuerya)){
            $rowa = $resulta->fetch_assoc();
            $userID = $rowa['userID'];
        }
    
        if(isset($_POST['join'])){
            //update triprecord table
            $userQuerya = "SELECT * FROM Users WHERE email ='".$email."'";
            
            //sets up mysqli connection
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            
            if($resulta = $mysqli->query($userQuerya)){
                $rowa = $resulta->fetch_assoc();

                $queryTR = "INSERT INTO tripRecord VALUES (".$rowa['userID'].",1,".$tripID.",FALSE)";

                if($resulthere = $mysqli->query($queryTR)){
                    $joined=true;
                }
                else{
                    echo "<p>DATABASE ERROR `</p>";
                }
            }
            else{
                echo "<p>DATABASE ERROR 2</p>";
            }
        }
    
        if(isset($_POST['delete'])||isset($_POST['leave'])){
            //get userID
            $userQuerya = "SELECT * FROM Users WHERE email ='".$email."'";
            
            
            
            //sets up mysqli connection
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, PORT);
            
            
            //get userID
            if($resulta = $mysqli->query($userQuerya)){
                
                $rowa = $resulta->fetch_assoc();
                
                $TRupdate = "SELECT G.gearID FROM Gear G, tripRecord TR
                WHERE TR.tripID = ".$tripID." AND TR.userID = ".$rowa['userID']." AND TR.gearID = G.gearID";
                
                $gearsuccess = false;
                
                //select all gearIDs
                if($TR = $mysqli->query($TRupdate)){
                    
                    
                    //while another gearID, update gear table to available
                    while($rowTR = $TR->fetch_assoc()){
                        $gearUpdate = "UPDATE Gear SET gearStatus=0 WHERE gearID = ".$rowTR['gearID'];

                        //update gear table
                        if($gearU = $mysqli->query($gearUpdate)){ 
                            $gearsuccess = true;
                        }
                        else{
                            echo "<p>DATABASE ERROR A</p>";
                        }
                        
                    }
                }
                else{
                    echo "<p>DATABASE ERROR B</p>";
                }
                
                if($gearsuccess && isset($_POST['delete'])){
                    $queryTR = "DELETE FROM tripRecord WHERE tripID =".$tripID;
                    $queryDel = "DELETE FROM Trips WHERE tripID =".$tripID;
                    
                    // delete from tripRecord the user on the trip
                    if($resulthere = $mysqli->query($queryTR) && $resultnum2 = $mysqli->query($queryDel)){
                        $deleted=true;
                        
                        if($deleted){
                            header("Refresh:0; url=trips.php");
                            die();
                        }
                    }
                    else{
                        echo "<p>DATABASE ERROR 2</p>";
                    }
                }
                
                
                // if updated gear table
                if($gearsuccess && !isset($_POST['delete'])){
                    $queryTR = "DELETE FROM tripRecord WHERE userID = ".$rowa['userID']." AND tripID =".$tripID;
                    
                    
                    // delete from tripRecord the user on the trip
                    if($resulthere = $mysqli->query($queryTR)){
                        $left=true;
                    }
                    else{
                        echo "<p>DATABASE ERROR 2</p>";
                    }
                }
                
            }
            else{
                echo "<p>SUPER DUPER DATABASE ERROR </p>";
            }
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
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <?php                
                        //sets up mysqli connection
                        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, PORT);
                        $ontrip = false;
                        $descrip = $start = $end = $loc = $activity = $difficulty ="";

                        $tripQuery = "SELECT * FROM Trips WHERE tripID = $tripID";
                        $tripRecord = "SELECT * FROM tripRecord WHERE tripID = $tripID";
                        $result = $mysqli->query($tripQuery);
                        $record = $mysqli->query($tripRecord);
                        $userlist = array();

                        //queries database to get activities
                        if($result && $record) {
                            
                            
                            $row = $result->fetch_assoc();

                            $actQuery = "SELECT actName FROM activities WHERE actID =".$row['activity'];
                            $result2 = $mysqli->query($actQuery);
                            
                            if($row2 = $result2->fetch_assoc()){
                                $diffname = "";
                                
                                $descrip = $row['tripDescrip'];
                                $start = $row['startDate'];
                                $end = $row['endDate'];
                                $loc = $row['location'];
                                $activity = $row['activity'];
                                $difficulty = $row['difficulty'];
                                
                                if($row['difficulty']==0){$diffname = "Novice";}
                                if($row['difficulty']==1){$diffname = "Intermediate";}
                                if($row['difficulty']==2){$diffname = "Advanced";}  

                                echo("
                                    <h1>".$row['tripDescrip']."</h1>
                                    <h1>".$row['startDate']."</h1>
                                    <h1>".$row['endDate']."</h1>
                                    <h1>".$row['location']."</h1>
                                    <h1>".$row2['actName']."</h1>
                                    <h1>".$diffname."</h1>
                                    
                                ");
                                                        
                                echo(
                                    "<div class='col-xs-12 col-md-8'>
                                        <h1>COC Members on This Trip</h1>
                                        <table class='table table-hover upcomingTable'>
                                            <tr>
                                                <th>Member Name</th>
                                                <th>Email</th>
                                                <th>Medical Cert</th>
                                                <th>Experience</th>
                                                <th>Leader</th>
                                            </tr>"
                                );

                                while($rowrecord = $record->fetch_assoc()){
                                    $userQuery = "SELECT * FROM Users WHERE userID =".$rowrecord['userID'];
                                    if($user = $mysqli->query($userQuery)){
                                        $userrow = $user->fetch_assoc();
                                        $emailrow = $userrow['email'];
                                        
                                        if(!in_array($userrow['userID'], $userlist)){
                                            if($emailrow == $email){
                                                $ontrip = true;
                                            }

                                            $cert = "None";
                                            $mem = "New";

                                            if($userrow['medCert']==0){$cert = "None";}
                                            if($userrow['medCert']==1){$cert = "CPR/FA";}
                                            if($userrow['medCert']==2){$cert = "WFA";} 
                                            if($userrow['medCert']==3){$cert = "WFR";} 

                                            if($userrow['memLevel']==0){$mem = "New";}
                                            if($userrow['memLevel']==1){$mem = "Attended Trips";}
                                            if($userrow['memLevel']==2){$mem = "Led Trips";} 
                                            
                                            $lead = "Participant";
                                            
                                            if($rowrecord['isLeader']){$lead="Leader";}
                                            
                                            if($rowrecord['isLeader'] && $userID==$userrow['userID']){$isLeader = true;};
                                            
                                            echo("
                                                <tr>
                                                    <td>".$userrow['name']."</td>
                                                    <td>".$userrow['email']."</td>
                                                    <td>".$cert."</td>
                                                    <td>".$mem."</td>
                                                    <td>".$lead."<td>
                                                </tr>
                                            ");
                                        }
                                        array_push($userlist, $userrow['userID']);
                                    }
                                }
                                echo("</table></div>") ;
                            }
                            if(!$ontrip){
                                echo "<form action='' method='post'><input class='button' type='submit' name='join' value='Join Trip'></form>";
                            }
                            if($ontrip && !$isLeader){
                                echo "<form action='' method='post'><input class='button' type='submit' name='leave' value='Leave Trip'></form>";
                            }
                            if($ontrip && $isLeader){
                                echo "<form action='' method='post'><input class='red' type='submit' name='delete' value='Delete Trip'></form><p>Warning: Irreversible if clicked.</p>";
                            }
                            
                        } else {
                            echo("<p class='error'>You did not select a valid trip. Try again.</p>");
                        }
                    ?>
                </div>
                
                <div class="member col-md-12 col-sm-12 col-xs-12 wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">
                
                <?php include "php/db_updates/editTrip.php";
                    
                    if($isLeader) {
                        
                        echo("
                        
                        <h2>Edit This Trip!</h2>
                    
                        <p>Note you can edit one or more fields.</p>

                        <form action='specifictrip.php?id=$tripID' method='post'>
                                <table  class='editingForm'>
                                    <tr>
                                        <th>Field</th>
                                        <th>Value to Input</th>
                                    </tr>
                                    <tr>
                                        <td>Trip Description</td>
                                        <td><input type='text' name='tripdescrip' value='".$descrip."' placeholder='8-64 Characters'>
                                    </tr>
                                    <tr>

                                        <td>Start Date</td>
                                        <td><input type='date' name='startdate' value='".$start."' placeholder='YYYY-MM-DD'></td>
                                    </tr>
                                    <tr>
                                        <td>End Date</td>
                                        <td><input type='date' name='enddate' value='".$end."' placeholder='YYYY-MM-DD'></td>
                                    </tr>
                                    <tr>

                                       <td>Location</td>
                                        <td><input type='text' name='location' value='".$loc."' placeholder='8-64 Characters'></td>
                                    </tr>

                                    <tr>
                                        <td>Activity</td>
                                        <td>
                                            <select name='activity'>
                                            <option value='0'>--</option>");


                                                $query = 'SELECT * FROM activities';
                                                if($result = $conn->query($query)){
                                                    while($row = $result->fetch_assoc()){
                                                        $output = "<option value='".$row['actID']."' ";

                                                        if ($row['actID'] == $activity){
                                                            $output = $output . "selected = 'selected' ";
                                                        }
                                                        
                                                        else {
                                                            $output = $output . " ";
                                                        }

                                                        $output = $output .">".$row['actName']."</option>";

                                                        echo $output;
                                                    }
                                                }            
                                    
                                    $checked0 = $checked1 = $checked2 = "";
                        
                                    if($difficulty == 0){ $checked0 = "checked='checked'";}
                                    if($difficulty == 1){ $checked1 = "checked='checked'";}
                                    if($difficulty == 2){ $checked2 = "checked='checked'";}
                        
                                    echo("        
                                            </select></td>
                                        </tr>

                                        <tr>
                                            <td>Difficulty</td>
                                            <td>
                                                <input type='radio' name='diff' ".$checked0." value='0'>Novice
                                                <input type='radio' name='diff' ".$checked1." value='1'>Intermediate
                                                <input type='radio' name='diff' ".$checked2." value='2'>Advanced
                                                <input type='hidden' name='tripID' value='$tripID'>
                                            </td>
                                        </tr>

                                    <tr>
                                        <td><input class='button' type='submit' name='editTrip' value='Edit Trip Record'></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </form>");
                    }
                    ?>
              </div>
            
            </div>
        </div>
    </div>
</html>
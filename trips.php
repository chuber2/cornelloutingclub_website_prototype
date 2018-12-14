<!DOCTYPE html>
<html>
    <?php include "php/head.php"; ?>
    <?php include "php/nav.php"; ?>
    <?php
        if(!isset($_SESSION["admin"]) && !isset($_SESSION["user"])){
            header("Refresh:0; url=index.php");
            die();
        }
        //initialize vars
        $tripdescrip = $startdate = $enddate = $location = $activity = $diff = $result =$message = $descripErr = $locErr = "";
        $success = "not set"; $dateErr = $actErr= "";

        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, PORT); 
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        else {
            // update database with new trip
            if($_SERVER["REQUEST_METHOD"] == "POST"){

                $tripdescrip = filter_input(INPUT_POST,'tripdescrip',FILTER_SANITIZE_STRING);
                
                if (!preg_match("/^[a-zA-Z ]{8,64}$/",$tripdescrip)){
                    $descripErr = "Please enter between 8 and 64 valid characters.";
                }
                
                $startdate = $_POST['startdate'];
                $enddate = $_POST['enddate'];
                
                if(strtotime($enddate) < strtotime($startdate)){
                    $dateErr = "Please choose an end date after the start date.";
                } 
                
                $location = filter_input(INPUT_POST,'location',FILTER_SANITIZE_STRING);
                
                if (!preg_match("/^[a-zA-Z0-9, ]{8,64}$/",$location)){
                    $locErr = "Please enter between 8 and 64 valid characters.";
                }
                
                $activity = $_POST['activity'];
                
                if($activity == 0){
                    $actErr = "Please select an activity.";
                }
                
                $diff = $_POST['diff'];

                $stmt = $conn->stmt_init();
                $stmt2 = $conn->stmt_init();
                $query = "INSERT INTO Trips VALUES (NULL,CURRENT_TIME,?,?,?,?,?,?)";
                $queryb = "INSERT INTO tripRecord VALUES (?,1,?,TRUE)";
                $userEmail = isset($_SESSION["user"]) ? $_SESSION["user"] : $_SESSION['admin'];
                $userquery = "SELECT userID FROM Users WHERE email='".$userEmail."'";
                $userresult = $conn->query($userquery);
                
                
                if($locErr == "" && $descripErr =="" && $dateErr =="" && $actErr ==""){
                    if($stmt->prepare($query) && $stmt2->prepare($queryb)){
                        $stmt->bind_param('ssssii',$tripdescrip, $startdate, $enddate, $location, $activity, $diff);
                        
                        // update trips table
                        if($stmt->execute()){
                            $result = $stmt->get_result();
                            
                            $tripID = $conn->insert_id;
                            $userrow = $userresult->fetch_assoc();
                            
                            $stmt2->bind_param('ii', $userrow['userID'],$tripID);
                                
                            // update the tripRecord table   
                            if($stmt2->execute()){
                                $success = 'true';
                                $tripdescrip = $startdate = $enddate = $location = $activity = $diff ="";
                            }
                            else {
                                $success = 'false';
                            }
                            
                        }
                        else{
                            $success = 'false';
                        }
                    }
                    else {
                        $success = 'false';
                        $message = "Prepare failed or input incorrect.";
                    }
                }
                else {
                    $success = 'false';
                }
            }
        }

    ?>
    
    <div class="container-fluid">
        <h1>Trips</h1>
        <div class="row" id="fullRow">
		    <div class="col-sm-5">
                <h1>Add Trip</h1>
                <form name="myform" action="trips.php" method="post">
                    <!-- trip description, startdate, enddate, location, activity, difficulty-->
                    <p>Trip Description</p>
                    <input type="text" name="tripdescrip" placeholder="8-64 Characters" required value="<?php echo $tripdescrip?>"><br>
                    <span class="error"> <?php echo $descripErr;?></span>

                    <p>Start Date</p>
                    <input type="date" name="startdate" placeholder="YYYY-MM-DD" required value="<?php echo $startdate?>"><br>

                    <p>End Date</p>
                    <input type="date" name="enddate" placeholder="YYYY-MM-DD" required value="<?php echo $enddate?>"><br>
                    <span class="error"> <?php echo $dateErr;?></span>
                    
                    <p>Location</p>
                    <input type="text" name="location" placeholder="8-64 Characters"required value="<?php echo $location?>"><br>
                    <span class="error"> <?php echo $locErr;?></span>

                    <!-- Drop Down With All Activities-->
                    <p>Activity</p>
                    <select name='activity' required>
                        <option value="0">--</option>
                        <?php 

                            $query = "SELECT * FROM activities";
                            if($result = $conn->query($query)){
                                while($row = $result->fetch_assoc()){
                                    $output = "<option value='".$row['actID']."' ";

                                    if(isset($_POST['activity'])){ 
                                        if ($row['actID'] == $activity){
                                            $output = $output . "selected = 'selected' ";
                                        }
                                    }
                                    else {
                                        $output = $output . " ";
                                    }

                                    $output = $output .">".$row['actName']."</option>";
                                    
                                    echo $output;
                                }
                            }
                        ?>
                    </select><br>
                    <span class="error"> <?php echo $actErr;?></span>
                    
                    <p>Difficulty</p>
                    <p><input type="radio" name="diff" value="0" <?php if(isset($_POST['diff']) && $diff == 0){echo "checked='checked'";} ?> required> Novice</p> 
                    <p><input type="radio" name="diff" value="1" <?php if(isset($_POST['diff']) && $diff == 1){echo "checked='checked'";} ?> required> Intermediate</p>
                    <p><input type="radio" name="diff" value="2" <?php if(isset($_POST['diff']) && $diff == 2){echo "checked='checked'";} ?> required> Advanced</p>

                    <input type="submit" value="Submit">
                </form>
		    </div>
            <div class="col-xs-12 col-md-6">
                <?php include "php/upcoming.php"; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <?php 
                    if($success == 'true'){
                        echo"<h1>Trip successfully added to database.</h1>";
                    }
                    if($success == 'false' && $locErr=='' && $descripErr=='' && $dateErr=='' && $actErr==""){
                        echo"<h1>SQL ERROR:".$conn->error."</h1>";
                        echo"<h1>".$message."</h1>";
                    }
                ?>
            </div>
        </div>
        
	</div>
    <?php include "php/footer.php";?>
</html>
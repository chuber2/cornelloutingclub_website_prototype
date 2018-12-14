<!DOCTYPE html>

<html lang=en>
    


<!--Need to sanitize the $userID GET VARIABLE-->
    <?php include "php/head.php";
        include "php/nav.php"; 
        if(!isset($_SESSION["user"]) && !isset($_SESSION["admin"])){
            header("Refresh:0; url=index.php");
            die();
        }
        ?>
    <div class="container-fluid">
        <div class="row" id="fullRow">
		    <div class="content col">
    	       <h1>My Profile</h1>
                <?php
                //get the user email stored in the $_SESSION variable "user"
                if( isset($_SESSION["user"]) ) {
                    $email = $_SESSION["user"]; 
                }
                //get the user email stored in admin, if admin.
                if( isset($_SESSION["admin"]) ) {
                    $email = $_SESSION["admin"];
                }
                
                //connect to the database.
                $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, PORT);
                
                if(isset($_POST['Submit'])){
                    $pass = "";
                    $pass = trim(filter_input(INPUT_POST,'pass',FILTER_SANITIZE_STRING));
                    $validPass = true;
                    $validPass = $validPass && preg_match("/[0-9]+/",$pass); //testing for requirements
                    $validPass = $validPass && preg_match("/[A-Z]+/",$pass);
                    $validPass = $validPass && preg_match("/[a-z]+/",$pass);
                    $validPass = strlen($validPass >= 8);
                    if($validPass){                        
                        $newHash = password_hash($pass,PASSWORD_DEFAULT);

                        $queryNew = "UPDATE Users SET hashedPass ='".$newHash."' WHERE email = '".$email."'";
                        if($change = $conn->query($queryNew)){
                            if($conn->affected_rows === 0){
                                print('<p class="error">Updating Database failed.</p>');
                            } else {
                                print('<p class="message">Successfully changed password.</p>');
                            }
                        }
                        else{
                            print('<p class="error">Updating Database failed in changing password.</p>');
                        }
                    }
                    else{
                        print('<p class="error">Invalid password characters.</p>');
                    }
                }
                
                //set up an SQL query that queries the USER table, getting relevant fields to display.
                //use the SESSION variable in a WHERE clause.
                $query = "SELECT * FROM Users WHERE Users.email =   '$email'" ;
                
                //debugging
                //echo $email;
                //echo $query;
                
                //if there are any results...
                if($result = $conn->query($query)){
                    //since there is only one email per user, this loop only runs once.
                    while($row = $result->fetch_assoc()){
                        
                        //convert the fields that are integer codes into their appropriate strings
                        $medCert = 0;
                        $memLevel = 'Undefined';
                        
                        //set the medical certification level
                        if($row[ 'medCert' ]==0){$medCert = "None";}
                        if($row[ 'medCert' ]==1){$medCert = "CPR/FA";}
                        if($row[ 'medCert' ]==2){$medCert = "WFA";}
                        if($row[ 'medCert' ]==3){$medCert = "WFR";}
                        
                        //set the membership level
                        if($row [ 'memLevel' ]==0){$memLevel = "New";}
                        if($row [ 'memLevel' ]==1){$memLevel = "Attended Trips";}
                        if($row [ 'memLevel' ]==2){$memLevel = "Led Trips";}

                        //these are the rest of the fields we're using
                        $userID = $row['userID'];
                        $user_name = $row[ 'name' ];
                        $isPaid = $row[ 'paid' ];
                        $isActive = $row[ 'isActive' ];
                        $isAdmin = $row[ 'isAdmin' ];
                        //email is obtained from session variable
                        
                        //translate the paid variable
                        if ( $isPaid ) { $isPaid = "Yes"; } 
                        else { $isPaid = "No"; }
                        
                        //translate the isActive variable.
                        if ( $isActive ) { $isActive = "Yes"; } 
                        else { $isActive = "No"; }
                        
                        /*BUILD THE ACTUAL HTML OF THE PAGE*/
                    echo    
                    "<div class = 'profile_banner'>
                            <h2> $user_name </h2>
                     </div>
                    <div class = 'profile_details'>
                        <h3> Profile Details </h3>
                        <p>Paid Dues? : $isPaid </p>
                        <p>Active: $isActive</p>
                        <p> Medical Certification Level: $medCert</p>
                        <p>Membership Level: $memLevel </p>
                        <p> Registered Email: $email </p>      
                    </div>";
                    }?>
                    <div class = 'profile_banner'>
                        <h2>Change Password</h2>
                    </div>
                    <div class = 'profile_details'>
                        <form action='' method='post'>
                            <h3>Enter New Password: <input type="password" name="pass" placeholder="Password"/></h3>
                            <input type="submit" name="Submit" value="Change Password"/>
                        </form>
                        <p>Password must have: </p><br>
                        <ul id="advisory">
                            <li> At least 8 characters </li>
                            <li> At least one lowercase letter </li>
                            <li> At least one uppercase letter </li>
                            <li> At least one number </li>
                        </ul>
                    </div>
                    <?php    
                        //sets up query to see which trips user is on
                        $userTripQuery = "SELECT DISTINCT Trips.tripID, isLeader, tripDescrip, startDate, endDate, location, actName, difficulty FROM tripRecord INNER JOIN Trips on tripRecord.tripID = Trips.tripID INNER JOIN activities ON Trips.activity = activities.actID WHERE userID = $userID";
                        
                        //executes the query on the database 
                        if($result = $conn->query($userTripQuery)) {
                            if($result->num_rows == 0) {
                                echo("<p>You are currently signed up for no trips.</p>");
                            } else {
                                echo("
                                <table>
                                    <tr>
                                        <th>Trip Description</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Location</th>
                                        <th>Activity</th>
                                        <th>Difficulty Level</th>
                                        <th>Remove Me From Trip</th>
                                        <th>Edit Trip (Leaders Only)</th>
                                    </tr>
                                >");
                                while($row = $result->fetch_assoc()) {
                                    
                                    //beginning of table to represent the different trips a user is on
                                    
                                    echo(
                                        "<tr>
                                            <td>{$row['tripDescrip']}</td>
                                            <td>{$row['startDate']}</td>
                                            <td>{$row['endDate']}</td>
                                            <td>{$row['location']}</td>
                                            <td>{$row['actName']}</td>
                                            <td>{$row['difficulty']}</td>
                                            <td><a href='specifictrip.php?id={$row['tripID']}'>View record</a></td>
                                        ");
                                    if($row['isLeader'] == 1) {
                                        echo("
                                            <td><a href='specifictrip.php?id={$row['tripID']}'>Edit Trip</a></td>");
                                    }
                                    
                                    echo(
                                    "</tr>"
                                    
                                    
                                    );
                                    
                                    
                                }
                                
                                echo("</table>");
                            }
                        }
                }
                else{
                    echo "Something with your query went wrong";
                }
                
                
                ?>
		    </div>
		</div>
	</div>
    <?php include "php/footer.php";?>
</html>
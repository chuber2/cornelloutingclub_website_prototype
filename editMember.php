<!DOCTYPE html>
<html>
    <?php include "php/head.php"; 
        if(!isset($_SESSION["admin"])){ 
            header("Refresh:0; url=index.php");
            die();
        }

        if(isset($_POST['back'])){
            header("Refresh:0; url=editMember.php");
            die();
        }
    ?>
    <?php include "php/nav.php"; 
    
    
    ?>
    <body>
        <div class="container-fluid">
            <div class="row" id="fullRow">
                <div class= "member_col">
                    <?php
                        require_once("php/config.php");
                        $mysqli =  new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, PORT);
                        $counter =0;

                        $userID = 0; //originally gearID
                        $choice = false;
                        $success = false;
                        $delsuccess = false;
                        $addsuccess = false;
                        $paid = false; //originally status
                        $medCert = 0; //condition
                        
                        //additional--need to add these in the queries below.
                        $memLevel = 0;
                        $isActive = false;
                        $isAdmin = false;

                        //TODO: initialize variables

                        $userID = FILTER_INPUT(INPUT_GET, 'id', FILTER_VALIDATE_INT);

                        if($userID != 0 && $userID !=1){
                            $choice = true;
                        }
                        
                        //process delete form post data
                        if(isset($_POST['delete'])){
                            $delete = "DELETE FROM Users WHERE userID=".$userID;

                            if($result = $mysqli->query($delete)){
                                $delsuccess = true;
                            }
                        }

                        //process edit form post data
                        if(isset($_POST['editMember']) && !isset($_POST['delete'])){
                            $paid = $_POST['paid'];
                            $medCert = $_POST['medCert'];
                            
                            $email = FILTER_INPUT(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                            
                            $memLevel = $_POST['memLevel'];
                            $isActive = $_POST['isActive'];
                            $isAdmin = $_POST['isAdmin'];
                            
                            //debugging
                            echo "userID is ". $userID;
                            echo "paid is ". $paid;
                            echo "email is ". $email;

                            $query = "UPDATE Users SET paid=".$paid.", medCert=".$medCert.", email='".$email."', memLevel=".$memLevel .", isActive=".$isActive.", isAdmin=".$isAdmin." WHERE userID=".$userID;
                            echo $query;

                            if($result = $mysqli->query($query)){
                                $success = true;
                            }
                            else{
                                echo "Something is wrong; the query didn't work";
                            }


                        }
                    ?>
                    <?php
                        //$namesearch
                        $ns = $nsErr = $sqlErr ="";
                        if (isset($_POST['ns'])) {
                            $ns = filter_input(INPUT_POST,'ns',FILTER_SANITIZE_STRING);
                            if($ns!="") {
                                if (!preg_match("/^[a-zA-Z ]{1,40}$/",$ns)) {
                                    $nsErr = "Please enter between 1 and 40 valid characters."; 
                                }
                            }
                            if ($ns!=""&&$nsErr=="") {
                                echo "<h2>Results of Search for \"$ns\"</h2>";
                                echo "<div class = 'break'></div>";

                                $sql="SELECT * FROM `Users` WHERE name LIKE '%$ns%'";

                                $counter = 0;

                                if($result = $conn->query($sql)){

                                    while($row = $result->fetch_assoc()) {
                                        echo("<table class='editingForm'>
                                        <tr>
                                            <th>Field</th>
                                            <th>Value</th>
                                        </tr>
                                    ");
                                        $medCert = "None";
                                        $paid = "No";

                                        //set the medical certification level
                                        if($row[ 'medCert' ]==0){$medCert = "None";}
                                        if($row[ 'medCert' ]==1){$medCert = "CPR/FA";}
                                        if($row[ 'medCert' ]==2){$medCert = "WFA";}
                                        if($row[ 'medCert' ]==3){$medCert = "WFR";}
                                        
                                        //translate paid status
                                        if($row['paid'] == 0){$paid = "No";}
                                        if($row['paid'] == 1){$paid = "Yes";}
                                        
                                        //translate membership level
                                        if($row [ 'memLevel' ]==0){$memLevel = "New";}
                                        if($row [ 'memLevel' ]==1){$memLevel = "Attended Trips";}
                                        if($row [ 'memLevel' ]==2){$memLevel = "Led Trips";}
                                        
                                        //translate active
                                        if($row['isActive'] == 0){$isActive = "No";}
                                        if($row['isActive'] == 1){$isActive = "Yes";}
                                        
                                        //translate admin
                                        if($row['isAdmin'] == 0){$isAdmin = "No";}
                                        if($row['isAdmin'] == 1){$isAdmin = "Yes";}
                                                       

                                        echo ("
                                            <tr>
                                                <td>User ID</td>
                                                <td>".$row['userID']."</td>
                                            </tr>
                                            <tr>
                                                <td>Name</td>
                                                <td>".$row['name']."</td>
                                            </tr>
                                            <tr>
                                                <td>Paid?</td>
                                                <td>".$paid."</td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td>".$row['email']."</td>
                                            </tr>
                                            <tr>
                                                <td>Medically Certified?</td>
                                                <td>".$medCert."</td>
                                            </tr>
                                            <tr>
                                                <td>Member Level</td>
                                                <td>".$memLevel."</td>
                                            </tr>
                                            <tr>
                                                <td>Active?</td>
                                                <td>".$isActive."</td>
                                            </tr>
                                            <tr>
                                                <td>Admin?</td>
                                                <td>".$isAdmin."</td>
                                            </tr>
                                            <tr>
                                                <td><form action='editMember.php?id=".$row['userID']."' method='post'><input class='button' type='submit' name='edit' value='Edit'></form></td>
                                                <td><form action='editMember.php?id=".$row['userID']."' method='post'><input class='button' type='submit' name='delete' value='Delete'></form></td>
                                            </tr>"
                                        );

                                        $counter ++;
                                    }
                                    echo "</table>";
                                }
                                else {
                                    $counter ++;
                                    echo "<p>SQL ERROR: ".$conn->error."</p>";
                                }

                                if($counter==0){
                                    echo "<p>No results.</p>";
                                }

                                $conn->close();
                            }
                        }
                        ?>
                        <?php
                    if($choice && !$delsuccess){
                        $mysqli =  new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                        $queryid = "SELECT * FROM Users WHERE userID=".$userID;

                        if($resultid = $mysqli->query($queryid)){
                            $row = $resultid->fetch_assoc();

                            $medCert = $row['medCert'];
                            $name = $row['name'];
                            $paid = $row['paid'];
                            $email = $row['email'];
                            
                            $memLevel = $row['memLevel'];
                            $isActive = $row['isActive'];
                            $isAdmin = $row['isAdmin'];
                        }

                    ?>

                    <h2>Edit<?php echo " '".$name."', Users ID: ".$userID;?></h2>
                    <?php if($success){echo"<p>Successfully Updated.</p>";}?>
                    <form action = 'editMember.php<?php echo "?id=".$userID?>' method='post' enctype='multipart/form-data'>
                        <table  class='editingForm'>
                            <tr>
                                <th>Field</th>
                                <th>Information to Update</th>
                            </tr>
                            <tr>
                                <td>Update Paid Status</td>
                                <td>
                                    <select name='paid'>
                                        <option value='0' <?php if($paid==0){echo"selected='selected'";}?>>Not Paid</option>
                                        <option value='1' <?php if($paid==1){echo"selected='selected'";}?>>Paid</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Update medCert</td>
                                <td>
                                    <select name='medCert'>
                                        <option value='0'<?php if($medCert==0){echo"selected='selected'";}?>>None</option>
                                        <option value='1'<?php if($medCert==1){echo"selected='selected'";}?>>CPR/FA</option>
                                        <option value='2'<?php if($medCert==2){echo"selected='selected'";}?>>WFA</option>
                                        <option value='3'<?php if($medCert==3){echo"selected='selected'";}?>>WFR</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Update Email</td>
                                <td>
                                    <input type='email' name='email' value='<?php echo $email;?>'>
                                </td>
                            </tr>
                            <tr>
                            </tr>
                            <!--Update Membership Level-->
                            <tr>
                                <td>Update Membership Level</td>
                                <td>
                                    <select name='memLevel'>
                                        <option value='0'<?php if($memLevel==0){echo"selected='selected'";}?>>New</option>
                                        <option value='1'<?php if($memLevel==1){echo"selected='selected'";}?>>Attended Trips</option>
                                        <option value='2'<?php if($memLevel==2){echo"selected='selected'";}?>>Led Trips</option>
                                    </select>
                                </td>
                            </tr>
                            <!--Update Active Status-->
                            <tr>
                                <td>Update Active Status</td>
                                <td>
                                    <select name='isActive'>
                                        <option value='0'<?php if($isActive==0){echo"selected='selected'";}?>>No</option>
                                        <option value='1'<?php if($isActive==1){echo"selected='selected'";}?>>Yes</option>
                                    </select>
                                </td>
                            </tr>
                            <!--Update Admin Status-->
                            <tr>
                                <td>Update Admin Status</td>
                                <td>
                                    <select name='isAdmin'>
                                        <option value='0'<?php if($isAdmin==0){echo"selected='selected'";}?>>No</option>
                                        <option value='1'<?php if($isAdmin==1){echo"selected='selected'";}?>>Yes</option>
                                    </select>
                                </td>
                            </tr>
                            <td><input class='button' type='submit' name='editMember' value='Edit User'></td>
                            <td></td>
                            
                        </table>
                        <?php 
                            echo "<br><form action = 'editMember.php' method='post'><input class='returnButton' type='submit' name='back' value='Back to Main'></form>";
                        ?>
                    </form>


                    <?php }?>

                    <?php if($delsuccess){echo "<h2>User Successfully Deleted.</h2>";}
                        if(!$choice && !isset($_POST['ns'])){
                    ?>
                    <br>
                    <h2>Search for User to Edit</h2>                

                    <form id="mysearch" action="editMember.php" method="post">
                            <p>Search: <input type="text" name="ns" value="<?php if($nsErr!=""||$sqlErr!=""){echo $ns;}?>"><br>
                            <span class="error"><?php echo $nsErr; ?></span></p>

                            <input type="submit" name="Submit"/>
                    </form>
                    <?php }
                    if(isset($_POST['ns'])){ 
                        echo"<br><form action = 'editMember.php' method='post'><input class='returnButton' type='submit' name='back' value='Back to Main'></form>";
                     }
                    ?>
                </div>
            </div>
        </div>
    </body>

    <?php include "php/footer.php";?>
</html>
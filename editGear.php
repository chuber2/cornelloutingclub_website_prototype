<!DOCTYPE html>
<html>
    <?php include "php/head.php"; 
        if(!isset($_SESSION["admin"])){ 
            header("Refresh:0; url=index.php");
            die();
        }

        if(isset($_POST['back'])){
            header("Refresh:0; url=editGear.php");
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

                        $gearID = 0;
                        $choice = false;
                        $success = false;
                        $delsuccess = false;
                        $addsuccess = false;
                        $addstat = 0;
                        $addcond = 0;

                        $type = $typeErr =$addlocale = $lErr = $sqlErr ="";

                        $gearID = FILTER_INPUT(INPUT_GET, 'id', FILTER_VALIDATE_INT);

                        if($gearID != 0 && $gearID !=1){
                            $choice = true;
                        }

                        if(isset($_POST['delete'])){
                            $delete = "DELETE FROM Gear WHERE gearID=".$gearID;

                            if($result = $mysqli->query($delete)){
                                $delsuccess = true;
                            }
                        }

                        if(!$choice && isset($_POST['addGear'])){
                            $type = filter_input(INPUT_POST,'type',FILTER_SANITIZE_STRING);
                            if($type!="") {
                                if (!preg_match("/^[a-zA-Z ]{1,40}$/",$type)) {
                                    $typeErr = "Please enter between 1 and 40 valid characters."; 
                                }
                            }
                            $addlocale = filter_input(INPUT_POST,'addlocale',FILTER_SANITIZE_STRING);
                            if($addlocale!="") {
                                if (!preg_match("/^[a-zA-Z ]{1,40}$/",$addlocale)) {
                                    $lErr = "Please enter between 1 and 40 valid characters."; 
                                }
                            }

                            $addstat = $_POST['addstatus'];
                            $addcond = $_POST['addcond'];

                            if($type!="" && $addlocale!="" && $lErr == "" && $typeErr == ""){
                                $add = "INSERT INTO Gear (gearType, gearCondition, gearLocale, gearStatus) VALUES ('".$type."', ".$addcond.", '".$addlocale."', ".$addstat.")";

                                if($result = $mysqli->query($add)){
                                    $addsuccess = true;
                                    $type = $addlocale = "";
                                    $addcond = $addstat  = 0;
                                }
                            }

                        }


                        if(isset($_POST['editGear']) && !isset($_POST['delete'])){
                            $status = $_POST['status'];
                            $condition = $_POST['cond'];
                            $locale = FILTER_INPUT(INPUT_POST, 'locale', FILTER_SANITIZE_STRING);

                            $query = "UPDATE Gear SET gearCondition=".$condition.", gearStatus=".$status.", gearLocale='".$locale."' WHERE gearID=".$gearID;

                            if($result = $mysqli->query($query)){
                                $success = true;
                            }


                        }

                    if(!$choice && !isset($_POST['ns'])){
                    ?>            

                    <!--Only Admins can add new gear.-->
                    <h2>Add Gear</h2>
                    <?php if($addsuccess){echo"<p>Successfully Added.</p>";}?>
                    <form action = 'editGear.php' method='post' enctype='multipart/form-data'>
                        <table  class='editingForm'>
                            <tr>
                                <th>Field</th>
                                <th>Information to Update</th>
                            </tr>
                            <tr>
                                <td>Gear Type</td>
                                <td>
                                    <input type='text' name='type' value='<?php echo $type;?>'>
                                    <span><?php echo $typeErr; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td>Gear Status</td>
                                <td>
                                    <select name='addstatus'>
                                        <option value='0' <?php if($addstat==0){echo"selected='selected'";}?>>Available</option>
                                        <option value='1' <?php if($addstat==1){echo"selected='selected'";}?>>Reserved</option>
                                        <option value='2' <?php if($addstat==2){echo"selected='selected'";}?>>Checked Out</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Condition</td>
                                <td>
                                    <select name='addcond'>
                                        <option value='0'<?php if($addcond==0){echo"selected='selected'";}?>>Good</option>
                                        <option value='1'<?php if($addcond==1){echo"selected='selected'";}?>>Fair</option>
                                        <option value='2'<?php if($addcond==2){echo"selected='selected'";}?>>Poor</option>
                                        <option value='3'<?php if($addcond==3){echo"selected='selected'";}?>>Broken</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Storage Location</td>
                                <td>
                                    <input type='text' name='addlocale' value='<?php echo $addlocale;?>'>
                                    <span><?php echo $lErr; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td><input class='button' type='submit' name='addGear' value='Add Gear'></td>
                                <td></td>
                            </tr>
                        </table>
                    </form>



                    <?php
                    }
                    ?>

                    <?php
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

                                $sql="SELECT * FROM `Gear` WHERE gearType LIKE '%$ns%'";

                                $counter = 0;

                                if($result = $conn->query($sql)){
                                    echo("
                                    <table class='table upcomingTable'>
                                        <tr>
                                            <th>Gear ID</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Storage Location</th>
                                            <th>Condition</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    ");


                                    while($row = $result->fetch_assoc()) {
                                        if($row['gearID']!=1){
                                            $cond = "Fair";
                                            $stat = "Available";

                                            if($row['gearCondition'] == 0){$cond = "Good";}
                                            if($row['gearCondition'] == 1){$cond = "Fair";}
                                            if($row['gearCondition'] == 2){$cond = "Poor";}
                                            if($row['gearCondition'] == 3){$cond = "Broken";}

                                            if($row['gearStatus'] == 0){$stat = "Available";}
                                            if($row['gearStatus'] == 1){$stat = "Reserved";}
                                            if($row['gearStatus'] == 2){$stat = "Checked Out";}                

                                            echo ("
                                                <tr>
                                                    <td>".$row['gearID']."</td>
                                                    <td>".$row['gearType']."</td>
                                                    <td>".$stat."</td>
                                                    <td>".$row['gearLocale']."</td>
                                                    <td>".$cond."</td>
                                                    <td><form action='editGear.php?id=".$row['gearID']."' method='post'><input class='button' type='submit' name='edit' value='Edit'></form></td>
                                                    <td><form action='editGear.php?id=".$row['gearID']."' method='post'><input class='button' type='submit' name='delete' value='Delete'></form></td>
                                                </tr>"
                                            );

                                            $counter ++;
                                        }
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
                        $mysqli =  new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, PORT);
                        $queryid = "SELECT * FROM Gear WHERE gearID=".$gearID;

                        if($resultid = $mysqli->query($queryid)){
                            $row = $resultid->fetch_assoc();

                            $cond = $row['gearCondition'];
                            $gearType = $row['gearType'];
                            $stat = $row['gearStatus'];
                            $locale = $row['gearLocale'];
                        }

                    ?>

                    <h2>Edit<?php echo " '".$gearType."', Gear ID: ".$gearID;?></h2>
                    <?php if($success){echo"<p>Successfully Updated.</p>";}?>
                    <form action = 'editGear.php<?php echo "?id=".$gearID?>' method='post' enctype='multipart/form-data'>
                        <table  class='editingForm'>
                            <tr>
                                <th>Field</th>
                                <th>Information to Update</th>
                            </tr>
                            <tr>
                                <td>Update Gear Status</td>
                                <td>
                                    <select name='status'>
                                        <option value='0' <?php if($stat==0){echo"selected='selected'";}?>>Available</option>
                                        <option value='1' <?php if($stat==1){echo"selected='selected'";}?>>Reserved</option>
                                        <option value='2' <?php if($stat==2){echo"selected='selected'";}?>>Checked Out</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Update Condition</td>
                                <td>
                                    <select name='cond'>
                                        <option value='0'<?php if($cond==0){echo"selected='selected'";}?>>Good</option>
                                        <option value='1'<?php if($cond==1){echo"selected='selected'";}?>>Fair</option>
                                        <option value='2'<?php if($cond==2){echo"selected='selected'";}?>>Poor</option>
                                        <option value='3'<?php if($cond==3){echo"selected='selected'";}?>>Broken</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Update Storage Location</td>
                                <td>
                                    <input type='text' name='locale' value='<?php echo $locale;?>'>
                                </td>
                            </tr>
                            <tr>
                                <td><input class='button' type='submit' name='editGear' value='Edit Gear'></td>
                                <td></td>
                            </tr>
                        </table>
                        <?php 
                            echo "<br><form action = 'editGear.php' method='post'><input class='button' type='submit' name='back' value='Back to Main'></form>";
                        ?>
                    </form>


                    <?php }?>

                    <?php if($delsuccess){echo "<h2>Gear Successfully Deleted.</h2>";}
                        if(!$choice && !isset($_POST['ns'])){
                    ?>
                    <br>
                    <h2>Search for Gear to Edit</h2>                

                    <form id="mysearch" action="editGear.php" method="post">
                            <p>Search: <input type="text" name="ns" value="<?php if($nsErr!=""||$sqlErr!=""){echo $ns;}?>"><br>
                            <span class="error"><?php echo $nsErr; ?></span></p>

                            <input type="submit" name="Submit"/>
                    </form>
                    <?php }
                    if(isset($_POST['ns'])){ 
                        echo"<br><form action = 'editGear.php' method='post'><input class='button' type='submit' name='back' value='Back to Main'></form>";
                     }
                    ?>
                </div>
            </div>
        </div>
    </body>
    <?php include "php/footer.php";?>
</html>
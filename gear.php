<!DOCTYPE html>
<html>
    <?php include "php/head.php"; ?>
    <?php include "php/nav.php"; 
        require_once("php/config.php");
        $ns = $nsErr = $sqlErr ="";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $ns = filter_input(INPUT_POST,'ns',FILTER_SANITIZE_STRING);
            if($ns!="") {
                if (!preg_match("/^[a-zA-Z ]{1,40}$/",$ns)) {
                    $nsErr = "Please enter between 1 and 40 valid characters."; 
                }
            }
            
        }
    ?>
    <div class="container-fluid">
        <div class="row" id="fullRow">
		    <div class="content col">
    	       <h1>Gear</h1>
                <!--Users can access this form.-->
                <h2>Search for Type of Gear</h2>
                    <form id="mysearch" action="gear.php" method="post">
                        <p>Search: <input type="text" name="ns" value="<?php if($nsErr!=""||$sqlErr!=""){echo $ns;}?>"><br>
                        <span class="error"><?php echo $nsErr; ?></span></p>

                        <input type="submit" name="Submit"/>
                    </form> 
                
                <?php
                    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, PORT);
                    
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
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
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Storage Location</th>
                                    <th>Condition</th>
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
                                            <td>".$row['gearType']."</td>
                                            <td>".$stat."</td>
                                            <td>".$row['gearLocale']."</td>
                                            <td>".$cond."</td>
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
                ?>
                
		    </div>
		</div>
	</div>
    <?php include "php/footer.php";?>
</html>
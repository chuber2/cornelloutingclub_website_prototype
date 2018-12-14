<!DOCTYPE html>
<html>
    <?php include "php/head.php"; ?>
    <?php include "php/nav.php"; ?>
    <div class="container-fluid">
        
        <div class="row" id="fullRow">
            <!-- <h1> Send us feedback or contact us <a href="contact.php">here!</a></h1> -->
        <?php

            $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, PORT);

            //Query the Eboard Table.
            //Extract postitionID, positionName, posImg, bio, and userID (all the fields)
            $query = "SELECT * FROM EBoard";

            //if there are any results...
            if($result = $conn->query($query)){
                // $colCounter = 0;
                while($row = $result->fetch_assoc()){
                    // $colCounter++;
                    //we need to get the name of the current user from the users table.

                    //userID can be NULL
                    //if it's not null...
                    if(!empty($row['userID'])){
                        $userID = $row['userID'];
                        $query_user = "SELECT name FROM Users WHERE userID = $userID";
                        $result_user = $conn->query($query_user);

                        //this loop only runs once.
                        while($row_user = $result_user->fetch_assoc()){
                            //set the member name to the associated user.
                            $memberName = $row_user['name'];

                         }
                    }
                    //userID is null
                    else{
                        $memberName = "Not Set";
                    }
                    // if($colCounter === 3){
                    //     echo("</div>");
                    //     echo("<div class='row'>");
                    //     $colCounter = 0;
                    // }
                    $positionImg = $row['posImg'];
                    if(!is_file($positionImg)){
                        $positionImg = 'img/eboard/cocRoundLogo.png';
                    }

                    //build the Eboard member cell, for each member.
                    
                        $counter = 0; 
   
                        echo("<div class='member col-md-4 col-xs-12 wow fadeInUp animated' style='visibility: visible; animation-name: fadeInUp;'>");
                            echo  "<h4 class='memberName'>$memberName</h4>";
                            echo "<img class='eboardImg' alt = '".$row['positionName']."' src= $positionImg><br>";

                            echo "<span class='memberPosition'><em>".$row['positionName']."</em></span>";
                            // echo("<div class='memberBioContainer'>");
                            echo("<p><b>Biography:</b>".$row['bio']."</p>");
                        echo ('</div>');

                }
            }
        ?>
        </div>
    </div>

    <?php include "php/footer.php";?>
</html>




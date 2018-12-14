<body>    
    
<div id="menucol">
    <div class="desktop_navbar">
        <div class="container-fluid">
            <div class="navdiv">
                <div class="col-md-2 col-sm-2 col-xs-12">
                    <a href="index.php">
                        <img class="logo" alt="COC" src="img/coclogo.png"/>
                    </a>
                </div>
                <div class="col-md-10 col-sm-10 col-xs-12">
                    <div class="navpages">
                        <ul>
                            <li>
                                <a href='https://www.facebook.com/CornellOutingClub/'>
                                    <i class="fa fa-facebook-square fa-2x"></i>
                                </a>
                            
                            </li>
                            <li>
                            <a href='https://www.instagram.com/cornell_outing_club/'>
                                <i class="fa fa-instagram fa-2x"></i>
                            </a>
                            </li>
                            <?php
                                if(isset($_SESSION["user"]) || isset($_SESSION["admin"])){
                                    echo('<li><form id="logOutForm" method="post">
                                    <input type="submit" name="logoutSubmit" id="logoutButton" value="Logout"/>
                                    </form></li>');
                                    if(isset($_POST["logoutSubmit"])){
                                        $_SESSION = array();
                                        header("Refresh:0; url=index.php");
                                    }
                                }
                                if(isset($_SESSION["user"]) || isset($_SESSION["admin"])){
                                    echo('<li><a href="profile.php">My Profile</a></li>
                                            <li><a href="trips.php">Trips</a></li>
                                            <li><a href="gear.php">Gear</a></li>');
                                } else {
                                    echo('<li><a href="login.php">Login/Join</a></li>');
                                }
                                //if the user is an admin, display the admin tools dropdown
                                if(isset($_SESSION["admin"])){
                                ?>
                                    <li class=drop_down>
                                        <a href="javascript:;" class= "drop_button">Admin Tools</a>
                                        <div class="drop_content">
                                            <a href='editEboard.php'>Edit E-Board</a>
                                            <a href='editActivities.php'>Edit Activities</a>
                                            <a href='editGear.php'>Edit Gear</a>
                                            <a href='editMember.php'>Edit Member</a>
                                        </div>
                                    </li>
                            <?php        
                                }
                            ?>
                            <li><a href="contact.php">Contact Us</a></li>
                            <li><a href="eboard.php">Our Team</a></li>
                            <li class= drop_down>
                                <a href="activities.php" class= "drop_button">Activities</a>
                                <div class="drop_content">
                                <?php
                                    /*Run a SELECT statement from MySQL to find the activities that are available. Store the results
                                    */
                                    
                                    //local host testing
                                    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                                    
                                  /*  $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);*/
                                    $query = "SELECT actName, actID FROM activities";
                                    if($result = $conn->query($query)){
                                        while($row = $result ->fetch_assoc()){
                                            $actName = $row['actName'];
                                            $actID = $row['actID'];
                                            //fill in the dropdown content with links to each activity.
                                            echo "<a href='activityLandingPage.php?id=$actID'>$actName</a> ";
                                        }
                                    }
                                    else{
                                        echo "Your query was not successful.";
                                    }
                                ?> 
                                </div>
                            </li>
                               
                            <li><a href="index.php">Home</a></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="mobile_navbar">
        <a href="#" class="openButton" id="opener">&#9776;</a>
        <img class="logo" src="img/coclogo.png">
        <div id="slide-panel" class="invisibleMenu">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="activities.php">Activities</a></li>


                <li><a href="eboard.php">Our Team</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php 
                    if(isset($_SESSION["user"]) || isset($_SESSION["admin"])){
                        echo('<li><a href="profile.php">My Profile</a></li>
                                <li><a href="trips.php">Trips</a></li>
                                <li><a href="gear.php">Gear</a></li>');
                    } else {
                        echo('<li><a href="login.php">Login/Join</a></li>');
                    }
                    if(isset($_SESSION["admin"])){
                        echo('<li><a href="admin.php">Admin Tools</a></li>');
                    }
                    //if any user or admin is logged in, display the logout button.        
                    if(isset($_SESSION["user"]) || isset($_SESSION["admin"])){
                        echo('<form id="logOutForm" method="post">
                        <input type="submit" name="logoutSubmit" id="logoutButton" value="Logout"/>
                        </form>');
                        if(isset($_POST["logoutSubmit"])){
                            $_SESSION = array();
                            header("Refresh:0; url=index.php");
                        }
                    }
                ?>
            </ul>
        </div>
    </div>
</div>

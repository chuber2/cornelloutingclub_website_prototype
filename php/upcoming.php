<h1>Upcoming Trips</h1>
<table class="table table-hover upcomingTable">
    <tr>
        <th>View or Join Trip</th>
        <th>Trip Description</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Location</th>
        <th>Activity</th>
        <th>Difficulty</th>
    </tr>
    <?php
        $query = "SELECT tripID, tripdescrip, startdate, enddate, location, activity, difficulty FROM Trips";
        if($result = $conn->query($query)){
            while($row = $result->fetch_assoc()){
                // upcoming trips today doesn't work yet
                if(strtotime($row['startdate']) >= mktime(0, 0, 0, date("m")  , date("d"), date("Y"))){
                    echo "<tr>";
                    
                    echo "<td class='button clickable'><a class= 'clickable' href='specifictrip.php?id=".$row['tripID']."'>View Trip</a></td>";
                    
                    echo ("
                        <td>".$row['tripdescrip']."</td>
                        <td>".$row['startdate']."</td>
                        <td>".$row['enddate']."</td>
                        <td>".$row['location']."</td>");

                    $actQuery = "SELECT actName FROM activities WHERE actID =".$row['activity'];

                    $result2 = $conn->query($actQuery) or die($conn->error);
                    $row2 = $result2->fetch_assoc();

                    echo "<td>".$row2['actName']."</td>";

                    $diffname = "";

                    if($row['difficulty']==0){$diffname = "Novice";}
                    if($row['difficulty']==1){$diffname = "Intermediate";}
                    if($row['difficulty']==2){$diffname = "Advanced";}

                    echo "<td>".$diffname."</td>
                    </tr>";
                }
            }
        }
        else{
            echo "<h1>DATABASE ERROR</h1>";
        }
    ?>
</table>
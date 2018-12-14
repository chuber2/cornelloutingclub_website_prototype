<!DOCTYPE html>
<html>
    <?php include "php/head.php"; 
        include "php/nav.php"; 
        if(!isset($_SESSION["admin"])){
            header("Refresh:0; url=index.php");
            die();
        }
    ?>
    <div class="container-fluid">
        <div class="row" id="fullRow">
		    <div class="content col">
    	 		<h1>Admin Tools</h1>
                <p><a href="editEboard.php">Edit the Executive Board page!</a></p>
                <p><a href="editActivities.php">Edit the Activities Available to Members!</a></p>
                <p><a href="editGear.php">Edit the Gear Available to Members!</a></p>
                <p><a href="editMember.php">Edit a Member's Profile! (Update Dues, Active Status, Certification)</a></p>
   			</div>
		</div>
	</div>
	<?php include "php/footer.php";?>
</html>
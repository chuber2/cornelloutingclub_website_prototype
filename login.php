<!DOCTYPE html>
<html>
    <?php include "php/head.php"; ?>
    <?php include "php/nav.php"; ?>
    <div class="container-fluid">
        <div class="row" id="fullRow">
		    <div class="loginContent col-xs-12 col-sm-6">
    	 		<h1>Login</h1>
    	 		<form id ="loginForm" method="post">
                    <input type="text" id="loginEmail" name="loginEmail" placeholder="Email"/>
                    <input type="password" id="loginPass" name="loginPass" placeholder="Password"/>
                    <input type="submit" name="loginSubmit" id="login_button" value="Login"/>
                </form>
                <?php
                	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, PORT);
                	if(isset($_SESSION["user"]) || isset($_SESSION["admin"])){
                		print('<p class="message">You have been logged in.</p>');
                	}
                	if(isset($_POST["loginSubmit"])){
                		if($_POST["loginPass"] != '' && $_POST["loginEmail"] != ''){
                			$validEmail = filter_input(INPUT_POST,"loginEmail",FILTER_VALIDATE_EMAIL);
                			if($validEmail){
                				$loginEmail = trim(filter_input(INPUT_POST,"loginEmail",FILTER_SANITIZE_EMAIL));
                				$loginEmailCaps = strtoupper($loginEmail);
                				$loginPass = trim(filter_input(INPUT_POST,"loginPass",FILTER_SANITIZE_STRING));
                				$stmt = $mysqli->stmt_init();
								$accQuery = "SELECT hashedPass, isAdmin FROM Users U WHERE UPPER(U.email) = ?";
								$goodAcc = true;
								if($stmt->prepare($accQuery)){
									$stmt->bind_param('s',$loginEmailCaps);
						        	$stmt->execute();
                                    $stmt->store_result();
                                    // print($stmt->num_rows);
						        	$stmt->bind_result($hashedPass, $isAdmin);
                                    if(($stmt->num_rows) < 1){
                                        $goodAcc = false;
                                       print('<p class="error">Incorrect email or password.</p>');
                                    }
                                    while($stmt->fetch()){
                                        $dbHash = $hashedPass;
                                        if(password_verify($loginPass,trim($dbHash))){
                                            if($isAdmin){
                                                $_SESSION['admin'] = $loginEmail;
                                            } else {
                                                $_SESSION['user'] = $loginEmail;
                                            }
                                        } else {
                                            $goodAcc = false;
                                        }
                                        if(!$goodAcc){
                                            print('<p class="error">Incorrect email or password.</p>');
                                        } else {
                                            header("Refresh:0; url=index.php");
                                        }
                                    }
						    	}

                			} else {
                				print('<p class="error">Please input a valid email.</p>');
                			}
                		} else { //both not filled
                			print('<p class="error">Please fill both fields.</p>');
                		}
                	} 
                ?>
		    </div>
		    <div class="loginContent col-xs-12 col-sm-6">
		    	<h1>Create an Account</h1>
		    	<form id ="joinForm" method="post">
		    		<input type="text" id="joinName" name="joinName" placeholder="Full Name"/>
                    <input type="text" id="joinEmail" name="joinEmail" placeholder="Email Address"/>
                    <input type="password" id="joinPass" name="joinPass" placeholder="Password"/>
                    <input type="submit" name="joinSubmit" id="join_button" value="Join!"/>
                </form>
                Password must have: <br>
                <ul id="advisory">
                	<li> At least 8 characters </li>
                	<li> At least one lowercase letter </li>
                	<li> At least one uppercase letter </li>
                	<li> At least one number </li>
                </ul>
                <?php
					if(isset($_POST['joinSubmit'])){
						if($_POST["joinEmail"] != '' && $_POST["joinPass"] != '' && $_POST["joinName"] != ''){ //if both fields are filled in
							$newEmail = filter_input(INPUT_POST,"joinEmail",FILTER_VALIDATE_EMAIL);
							$goodEmail = true;
							if($newEmail){
								$newEmailCaps = strtoupper($newEmail);
								$stmt = $mysqli->stmt_init();
								$existingEmailQuery = "SELECT * FROM Users U WHERE UPPER(U.email) = ?";
								if($stmt->prepare($existingEmailQuery)){
									$stmt->bind_param('s',$newEmailCaps);
						        	$stmt->execute();
						        	$emailResult = $stmt->get_result();
						        	if($emailResult->num_rows > 0){
										print('<p class="error">Email already exists.</p>');
										$goodEmail = false;
									} 
						    	}
							}
							if($newEmail && $goodEmail){
								$newName = trim(filter_input(INPUT_POST,"joinName",FILTER_SANITIZE_STRING));
								if($newName ===''){
									print('<p class="error">Please enter a valid name.</p>');
								} else {
									$newPass = trim(filter_input(INPUT_POST,"joinPass",FILTER_SANITIZE_STRING));
									$validPass = true;
									$validPass = $validPass && preg_match("/[0-9]+/",$newPass); //testing for requirements
									$validPass = $validPass && preg_match("/[A-Z]+/",$newPass);
									$validPass = $validPass && preg_match("/[a-z]+/",$newPass);
									$validPass = strlen($validPass >= 8);
									if($validPass){
										$newHash = password_hash($newPass,PASSWORD_DEFAULT);
										$stmt = $mysqli->stmt_init();
										$newAccQuery = "INSERT INTO Users VALUES (NULL,?,0,CURRENT_DATE(),0,0,0,0,?,?)";

										if($stmt->prepare($newAccQuery)){
						                    $stmt->bind_param('sss',$newName,$newEmail,$newHash);
						                    $stmt->execute();
						                } 
						                if($mysqli->affected_rows === 0){
						                	print('<p class="error">Adding to Database failed.</p>');
						                } else {
						                	print('<p class="message">Successfully created account!</p>');
						                }
									} else {
										print('<p class="error">Please follow the password specifications above!</p>');
									}
								}
							} else {
								print('<p class="error">Please enter a valid email.</p>');
							}
							
						} else { //if one, or both, are missing
							print('<p class="error">Please fill in all fields!</p>');
						}
					}
				?>
		    </div>
		</div>
    </div>
    <?php include "php/footer.php";?>
</html>
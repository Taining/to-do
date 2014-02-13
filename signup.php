<?php
<<<<<<< HEAD
	session_save_path("sess");
	session_start();
	$page = "signup";
=======
    session_save_path("sess");
	session_start();

>>>>>>> da4aff5269288e03b6b6ef75bbdc70691ecad6f7
	require 'config.inc';
	require 'header.php';

	$fname = $lname = $email = $reemail = $password = $sex = "";
	$EMPTY = "";
	$validated = true;
	$errMessage = array($EMPTY,$EMPTY,$EMPTY,$EMPTY,$EMPTY);
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		//validate form input
		if ($_POST['fname'] == $EMPTY || $_POST['lname'] == $EMPTY) {
			$errMessage[0] = "Please enter your name.";
			$validated = false;
		}
		if ($_POST['email'] == $EMPTY) {
			$errMessage[1] = "Please enter your email.";
			$validated = false;
		} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$errMessage[1] = "Please enter a valid email.";
			$validated = false;
		} else {
			$email = $_POST['email'];
		}
		if ($_POST['re-email'] == $EMPTY || $_POST['re-email'] != $_POST['email']) {
			$errMessage[2] = "Your emails do not match.";
			$_POST['re-email'] = $EMPTY;
			$validated = false;
		} else {
			$reemail = $_POST['re-email'];
		}
		if ($_POST['password'] == $EMPTY) {
			$errMessage[3] = "Please enter your password.";
			$validated = false;
		}
		if (!(isset($_POST['sex'])) || $_POST['sex'] == $EMPTY) {
			$errMessage[4] = "Please select your gender.";
		} else {
			$sex = $_POST['sex'];
		}

		$fname = $_POST['fname'];
		$lname = $_POST['lname'];

		if ($validated) {
			$dbconn = pg_connect("host=localhost port=5432 dbname=$db_name user=$db_user password=$db_password");
            if(!$dbconn){
                $errMessage = "Connect to server failed";
                exit;
            } 
			$insert_user_query = "INSERT INTO appuser (email, fname, lname, password, sex) VALUES($1, $2, $3, $4, $5);";
			$result = pg_prepare($dbconn, "insert_user", $insert_user_query);
			$result = pg_execute($dbconn, "insert_user", array($email, $fname, $lname, md5($password)), $sex);

			preventFormResubmission();
			header("Location: login.php");
		}
	}


	function preventFormResubmission(){
		unset($_POST);
	}
?>
	
	<form method = "POST">
        <table class="form" id="signup">
        	<tr>
        		<td colspan="3"><h2>Sign Up</h1></td>
        	</tr>
            <tr>
                <td><input type="text" name="fname" value="<?php echo $fname; ?>" size="25" placeholder="First Name"/></td>
                <td><input type="text" name="lname" value="<?php echo $lname; ?>" size="25" placeholder="Last Name"/></td>
            	<td class="error"><?php echo $errMessage[0]; ?></td>
            </tr>
            <tr>
                <td colspan="2"><input type="text" name="email" value="<?php echo $email; ?>" placeholder="Your Email" size="56" /></td>
                <td class="error"><?php echo $errMessage[1]; ?></td>
            </tr>
            <tr>
                <td colspan="2"><input type="text" name="re-email" value="<?php echo $reemail; ?>" placeholder="Re-enter Email" size = "56" /></td>
                <td class="error"><?php echo $errMessage[2]; ?></td>
            </tr>
            <tr>
                <td colspan="2"><input type="password" name="password" placeholder="New Password" size = "56" /></td>
                <td class="error"><?php echo $errMessage[3]; ?></td>
            </tr>
            <tr id="sex">
            	<td colspan="2">
         			<label><input type="radio" name="sex" value="1" />Female &nbsp;&nbsp;</label>
         			<label><input type="radio" name="sex" value="2" />Male </label>
            	</td>
            	<td class="error"><?php echo $errMessage[4]; ?></td>
            </tr>
            
            <tr>
                <td></td>
                <td><input type="submit" class="submit" value="Sign Up" /></td>
            </tr>
        </table>
    </form>

<?php	require 'footer.php'; ?>
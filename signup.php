<?php
	require 'config.inc';
	session_start();
	require 'header.php';

	$fname = $lname = $email = $reemail = $password = "";
	$EMPTY = "";
	$validated = true;
	$errMessage = array($EMPTY,$EMPTY,$EMPTY,$EMPTY);
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

		$fname = $_POST['fname'];
		$lname = $_POST['lname'];

		if ($validated) {
			$insert_user_query = "INSERT INTO appuser (username, password, email) VALUES($1, $2, $3);";

		}
	}
?>
	
	<form method = "POST">
        <table class="login" id="signup">
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
            <tr>
                <td></td>
                <td><input type="submit" class="submit" value="Sign Up" /></td>
            </tr>
        </table>
    </form>

<?php	require 'footer.php'; ?>
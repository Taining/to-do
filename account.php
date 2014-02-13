<?php
	session_save_path("sess");
	session_start();

	$page = "acount";
	require 'config.inc';
	require 'header.php';

	if (!isset($_SESSION['user'])) {
		header("Location: login.php");
		exit;
	}

	$dbconn = pg_connect("host=localhost port=5432 dbname=$db_name user=$db_user password=$db_password");
	if(!$dbconn){
		$errMessage = "Connect to server failed";
		exit;
	}
	$result = pg_query($dbconn, "SELECT * FROM appuser WHERE uid = $_SESSION[user]");
	$row = pg_fetch_array($result);
	
	$fname = $row['fname'];
	$lname = $row['lname'];
	$email = $row['email'];
	$sex = $row['sex'];
	$password = $row['password'];

	$EMPTY = "";
	$inforMessage = $EMPTY;
	$pwdMessage = $EMPTY;
	if (isset($_POST['info'])) {
		if ($_POST['email'] != $EMPTY && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
			$inforMessage = "Please enter a valid email.";
		} else {
			if ($_POST['email'] != $EMPTY) {
				$email = $_POST['email'];
			}
			if ($_POST['fname'] != $EMPTY) {
				$fname = $_POST['fname'];
			}
			if ($_POST['lname'] != $EMPTY) {
				$lname = $_POST['lname'];
			}
			if ($sex != $_POST['sex']) {
				$sex = $_POST['sex'];
			}

			$update_user_query = "UPDATE appuser SET (fname, lname, email, sex) = ($1, $2, $3, $4) WHERE uid = $5;";
			$result = pg_prepare($dbconn, "update_user", $update_user_query);
			$result = pg_execute($dbconn, "update_user", array($fname, $lname, $email, $sex, $_SESSION['user']));

			$inforMessage = "Your information has been updated.";

			//prevent resubmission
			unset($_POST);
		}
	} else if (isset($_POST['pwd'])) {
		
	} else {
		$inforMessage = $EMPTY;
	}
?>

<div class="content">
	<form method="POST" class="form account-form">
		<fieldset>
			<legend>Account Information</legend>
			<table>
				<tr>
					<td><label>First Name: <input type="text" name="fname" size="14" placeholder="<?php echo $fname; ?>"></label></td>
					<td><label>Last Name: <input type="text" name="lname" size="14" placeholder="<?php echo $lname; ?>"></label></td>
				</tr>
				<tr>
					<td colspan="2"><label>Email: <input type="text" name="email" size="54" placeholder="<?php echo $email; ?>"></label></td>
				</tr>
				<tr>
					<td colspan="2">
         			<label><input type="radio" name="sex" value="1" <?php if($sex==1) echo "checked";?> />Female &nbsp;&nbsp;</label>
         			<label><input type="radio" name="sex" value="2" <?php if($sex==2) echo "checked";?> />Male </label>
            	</td>
				</tr>
				<tr>
					<td></td>
                	<td><input type="submit" class="submit" name="info" value="Update Info" /></td>
				</tr>
				<tr>	
					<td colspan="2" class="error"><?php echo $inforMessage; ?></td>
				</tr>		
			</table>
		</fieldset>
	</form>

	<form method="POST" class="form account-form" >
		<fieldset>
			<legend>Change Password</legend>
			<table>
				<tr>
					<td>Old Password: </td>
					<td><input type="text" name="old-password" size="40"></td>
				</tr>
				<tr>
					<td>New Password: </td>
					<td><input type="text" name="new-password" size="40"></td>
				</tr>
				<tr>
					<td>Confirm Password: </td>
					<td><input type="text" name="re-password" size="40"></td>
				</tr>
				<tr>
					<td></td>
                	<td><input type="submit" class="submit" name="pwd" value="Update Password" /></td>
				</tr>	
			</table>	
		</fieldset>
	</form>
</div>

<?php require 'footer.php' ?>
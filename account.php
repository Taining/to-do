<?php
	session_save_path("sess");
	session_start();

	$page = "acount";
	require 'config.inc';
	require 'header.php';

	//only authenticated users can access this page
	if (!isset($_SESSION['user'])) {
		header("Location: login.php");
		exit;
	}

	$dbconn = pg_connect("host=localhost port=5432 dbname=$db_name user=$db_user password=$db_password");
	if(!$dbconn){
		echo "Aw, Snap!";
		exit;
	}
	$result = pg_query($dbconn, "SELECT * FROM appuser WHERE uid = $_SESSION[user]");
	$row = pg_fetch_array($result);
	
	$fname = $row['fname'];
	$lname = $row['lname'];
	$email = $row['email'];
	$sex = $row['sex'];
	$birthday = explode("-", $row['birthday']);
	$year = intval($birthday[0]);
	$month = intval($birthday[1]);
	$day = intval($birthday[2]);
	$signupdate = $row['signupdate'];
	$news = $row['news'];
	$password = $row['password'];

	$EMPTY = "";
	$inforMessage = $EMPTY;
	$pwdMessage = $EMPTY;
	if (isset($_POST['info'])) {
		if($_POST['fname'] == $EMPTY || $_POST['lname'] == $EMPTY){
			$inforMessage = "Please enter your name";
		} else if ($_POST['email'] == $EMPTY || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
			$inforMessage = "Please enter a valid email.";
		}
		if ($sex != $_POST['sex']) {
			$sex = $_POST['sex'];
		}
		if (isset($_POST['news']) && $_POST['news'] == 1) {
			$news = 'true';
		} else $news = 'false';

		//update parameters
		$birthday = $_POST['year']."-".$_POST['month']."-".$_POST['day'];
		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$email = $_POST['email'];

		//error message is empty, then update user info
		if ($inforMessage == $EMPTY) {
			//update user info
			$update_user_query = "UPDATE appuser SET (email, fname, lname, birthday, news, sex) = ($1, $2, $3, $4, $5, $6) WHERE uid = $7;";
			$result = pg_prepare($dbconn, "update_user", $update_user_query);
			$result = pg_execute($dbconn, "update_user", array($email, $fname, $lname, $birthday, $news, $sex, $_SESSION['user']));

			if ($result) {
				$inforMessage = "Your information has been updated.";
			}
			//prevent resubmission
			unset($_POST);
		}

	} else if (isset($_POST['pwd'])) {
		if (md5($_POST['old-password']) != $password) {
			$pwdMessage = "Please enter correct old password.";
		} elseif ($_POST['new-password'] == $EMPTY) {
			$pwdMessage = "Please enter a new password.";
		} elseif ($_POST['new-password'] != $_POST['re-password']) {
			$pwdMessage = "New passwords do not match.";
		} else {
			$update_pwd_query = "UPDATE appuser SET (password) = ($1) WHERE uid = $2;";
			$result = pg_prepare($dbconn, "update_pwd", $update_pwd_query);
			$result = pg_execute($dbconn, "update_pwd", array(md5($_POST['new-password']), $_SESSION['user']));

			if ($result) {
				$pwdMessage = "Your password has been updated.";
			}
		}
	} else {
		$inforMessage = $EMPTY;
		$pwdMessage = $EMPTY;
	}
?>

<div class="container">
	<h2>My Account</h2>
	<form method="POST" class="form account-form">
		<fieldset>
			<legend>Account Information</legend>
			<table>
				<div class="error" <?php if($inforMessage=="") echo "hidden"; ?> ><?php echo $inforMessage; ?></div>
				<tr>
					<td><label>First Name: <input type="text" name="fname" size="14" value="<?php echo $fname; ?>"></label></td>
					<td><label>Last Name: <input type="text" name="lname" size="14" value="<?php echo $lname; ?>"></label></td>
				</tr>
				<tr>
					<td colspan="2"><label>Email: <input type="text" name="email" size="54" value="<?php echo $email; ?>"></label></td>
				</tr>
				<tr>
					<td colspan="2">
						Birthday:  
						<select name="month">
							<?php 
							for ($i=1; $i < 13; $i++) { 
								if ($month == $i) {
									echo "<option value=$i selected=1>".date("M", mktime(0,0,0,$i,1,2014))."</option>";
								} else echo "<option value=$i>".date("M", mktime(0,0,0,$i,1,2014))."</option>";
							}
							?>
						</select>
						<select name="day">
							<?php 
							for ($i=1; $i < 32; $i++) { 
								if ($day == $i) {
									echo "<option value=$i selected=1>$i</option>";
								} else echo "<option value=$i>$i</option>";
							}
							?>
						</select>
						<select name="year">
							<?php 
							for ($i=2014; $i > 1904; $i--) { 
								if ($year == $i) {
									echo "<option value=$i selected=1>$i</option>";
								} else echo "<option value=$i>$i</option>";
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
         			<label><input type="radio" name="sex" value="1" <?php if($sex==1) echo "checked";?> />Female &nbsp;&nbsp;</label>
         			<label><input type="radio" name="sex" value="2" <?php if($sex==2) echo "checked";?> />Male </label>
            	</td>
				</tr>
				<tr>
					<td colspan="2">
						<label><input type="checkbox" name="news" value="1" <?php if ($news) echo "checked"; ?>/>I'd like to recieve news from To-do Manager.</label>
					</td>
				</tr>
				<tr>
					<td></td>
                	<td><input type="submit" class="submit" name="info" value="Update Info" /></td>
				</tr>		
			</table>
		</fieldset>
	</form>

	<form method="POST" class="form account-form" >
		<fieldset>
			<legend>Change Password</legend>
			<table>
				<div class="error" <?php if($pwdMessage=="") echo "hidden"; ?> ><?php echo $pwdMessage; ?></div>
				<tr>
					<td>Old Password: </td>
					<td><input type="password" name="old-password" size="40"></td>
				</tr>
				<tr>
					<td>New Password: </td>
					<td><input type="password" name="new-password" size="40"></td>
				</tr>
				<tr>
					<td>Confirm Password: </td>
					<td><input type="password" name="re-password" size="40"></td>
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
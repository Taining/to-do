<?php
	session_save_path("sess");
	session_start();
	$page = "signup";

	require 'config.inc';
	require 'header.php';

	$fname = $lname = $email = $reemail = $password = $sex = $news = $year = $month = $day = "";
	$EMPTY = "";
	$validated = true;
	$errMessage = $EMPTY;
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		//validate form input
		if ($_POST['fname'] == $EMPTY || $_POST['lname'] == $EMPTY) {
			$errMessage = "Please enter your name.";
		} else if ($_POST['email'] == $EMPTY) {
			$errMessage = "Please enter your email.";
		} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$errMessage = "Please enter a valid email.";
		} else if ($_POST['re-email'] == $EMPTY || $_POST['re-email'] != $_POST['email']) {
			$errMessage = "Your emails do not match.";
			$_POST['re-email'] = $EMPTY;
		} else if ($_POST['password'] == $EMPTY) {
			$errMessage = "Please enter your password.";
		} else if (!checkdate($_POST['month'], $_POST['day'], $_POST['year'])) {
			$errMessage = "Please enter a valid date.";
		} else if (!isset($_POST['sex'])) {
			$errMessage = "Please select your gender.";
		} else if (!isset($_POST['policy'])) {
			$errMessage = "Please agree to our Terms.";
		}

		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$email = $_POST['email'];
		$reemail = $_POST['re-email'];
		$year = $_POST['year'];
		$month = $_POST['month'];
		$day = $_POST['day'];

		if (isset($_POST['news']) && $_POST['news']==1) {
			$news = 'true';
		} else $news = 'false';
        if (isset($_POST['sex'])) {
            $sex = $_POST['sex'];
        }
        if (isset($_POST['policy'])) {
            $policy = $_POST['policy'];
        }

		if ($errMessage == $EMPTY) {
			$dbconn = pg_connect("host=localhost port=5432 dbname=$db_name user=$db_user password=$db_password");
            if(!$dbconn){
                $errMessage = "Connect to server failed";
                exit;
            } 
			$insert_user_query = "INSERT INTO appuser (email, fname, lname, password, birthday, signupdate, news, sex, done) VALUES($1, $2, $3, $4, $5, $6, $7, $8, 0);";
			$result = pg_prepare($dbconn, "insert_user", $insert_user_query);
			$result = pg_execute($dbconn, "insert_user", array($email, $fname, $lname, md5($password), "$year-$month-$day", date("Y-m-d"), $news, $sex));

			preventFormResubmission();
			header("Location: login.php");
		}
	}


	function preventFormResubmission(){
		unset($_POST);
	}
?>

<div class="container">
	<form method = "POST">
        <table class="form" id="signup">
        	<tr>
        		<td colspan="3"><h2>Sign Up</h1></td>
        	</tr>
        	<tr>
        		<td colspan="3">
        			<div class="error" <?php if($errMessage=="") echo "hidden"; ?> ><?php echo $errMessage; ?></div>
        		</td>
        	</tr>
            <tr>
                <td><input type="text" name="fname" value="<?php echo $fname; ?>" size="25" placeholder="First Name"/></td>
                <td><input type="text" name="lname" value="<?php echo $lname; ?>" size="25" placeholder="Last Name"/></td>
            </tr>
            <tr>
                <td colspan="2"><input type="text" name="email" value="<?php echo $email; ?>" placeholder="Your Email" size="56" /></td>
            </tr>
            <tr>
                <td colspan="2"><input type="text" name="re-email" value="<?php echo $reemail; ?>" placeholder="Re-enter Email" size = "56" /></td>
            </tr>
            <tr>
                <td colspan="2"><input type="password" name="password" placeholder="New Password" size = "56" /></td>
            </tr>
            <tr>
            	<td colspan="2">
            		<span>
            			Birthday: 
            			<select name="month">
            				<option value="0" selected="1">Month</option>
                            <option value="1">Jan</option>
            				<option value="2">Feb</option>
            				<option value="3">Mar</option>
            				<option value="4">Apr</option>
            				<option value="5">May</option>
            				<option value="6">Jun</option>
            				<option value="7">Jul</option>
            				<option value="8">Aug</option>
            				<option value="9">Sep</option>
            				<option value="10">Oct</option>
            				<option value="11">Nov</option>
            				<option value="12">Dec</option>
            			</select>
            			<select name="day">
            				<option value="0" selected="1">Day</option>
                            <?php
                                for ($i=1; $i < 32; $i++) {
                                    echo "<option value=$i>$i</option>";
                                }
                            ?>
            			</select>
            			<select name="year">
            				<option value="0" selected="1">Year</option>
            				<?php
                                for ($i=2014; $i > 1904; $i--) { 
                                    echo "<option value=$i>$i</option>";
                                }
                            ?>
            			</select>
            		</span>
            	</td>
            </tr>
            <tr id="sex">
            	<td colspan="2">
         			<label><input type="radio" name="sex" value="1" <?php if($sex==1) echo "CHECKED"; ?>/>Female &nbsp;&nbsp;</label>
         			<label><input type="radio" name="sex" value="2" <?php if($sex==2) echo "CHECKED"; ?>/>Male </label>
            	</td>
            </tr>
            <tr>
            	<td colspan="2">
            		<label><input type="checkbox" name="news" value="1" <?php if($news=='true') echo "CHECKED"; ?>/>I'd like to recieve news from To-do Manager.</label>
            	</td>
            </tr>
            <tr>
            	<td colspan="2">
            		<label><input type="checkbox" name="policy" value="1"/>I've read and agree to the Terms and Conditions.</label>
            	</td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" class="submit" value="Sign Up" /></td>
            </tr>
        </table>
    </form>
</div>

<?php	require 'footer.php'; ?>
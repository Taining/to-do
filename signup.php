<?php
	session_save_path("sess");
	session_start();
	$page = "signup";

	require 'config.inc';
	require 'header.php';

	$fname = $lname = $email = $reemail = $password = $sex = $news = $year = $month = $day = $policy = "";
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
		} else if ($_POST['password'] == $EMPTY) {
			$errMessage = "Please enter your password.";
		} else if ($_POST['re-password'] == $EMPTY || $_POST['re-password'] != $_POST['password']) {
            $errMessage = "Your passwords do not match.";
            $_POST['re-password'] = $EMPTY;
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
		$year = $_POST['year'];
		$month = $_POST['month'];
		$day = $_POST['day'];
        $password = $_POST['password'];

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
                <td colspan="2"><input type="password" name="password" placeholder="New Password" size = "56" /></td>
            </tr>
            <tr>
                <td colspan="2"><input type="password" name="re-password" placeholder="Re-enter Password" size = "56" /></td>
            </tr>
            <tr>
            	<td colspan="2">
            		<span>
            			Birthday: 
            			<select name="month">
                            <?php 
                            if ($month == $EMPTY) {
                                    echo "<option value='0' selected='1'>Month</option>";
                            }
                            for ($i=1; $i < 13; $i++) {
                                if ($month == $i) {
                                    echo "<option value=$i selected=1>".date("M", mktime(0,0,0,$i,1,2014))."</option>";
                                } else echo "<option value=$i>".date("M", mktime(0,0,0,$i,1,2014))."</option>";
                            }
                            ?>
            			</select>
            			<select name="day">
                            <?php
                                if ($day == $EMPTY) {
                                    echo "<option value='0' selected='1'>Day</option>";
                                }
                                for ($i=1; $i < 32; $i++) {
                                    if ($day == $i) {
                                        echo "<option value=$i selected=1>$i</option>";
                                    } else echo "<option value=$i>$i</option>";
                                }
                            ?>
            			</select>
            			<select name="year">
            				<?php
                                if ($year == $EMPTY) {
                                    echo "<option value='0' selected='1'>Year</option>";
                                }
                                for ($i=2014; $i > 1904; $i--) { 
                                    if ($year == $i) {
                                        echo "<option value=$i selected=1>$i</option>";
                                    } else echo "<option value=$i>$i</option>";
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
            		<label><input type="checkbox" name="policy" value="1" <?php if($policy!=$EMPTY) echo "CHECKED"; ?>/>I've read and agree to the Terms and Conditions.</label>
            	</td>
            </tr>
            <tr>
                <td></td>
                <td>
                	<input type="submit" class="submit" value="Sign Up" />
                	<button class="submit"><a href='login.php'><font color="black">Go back</font></a></button>
                </td>
            </tr>
        </table>
    </form>
</div>

<?php	require 'footer.php'; ?>
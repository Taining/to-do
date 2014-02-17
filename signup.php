<?php
	session_save_path("sess");
	session_start();
	$page = "signup";

	require 'config.inc';
	require 'header.php';

	$fname = $lname = $email = $reemail = $password = $sex = $news = "";
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
		} else if (!isset($_POST['month']) || !isset($_POST['day']) || !isset($_POST['year']) || !checkdate($_POST['month'], $_POST['day'], $_POST['year'])) {
			$errMessage = "Please enter a valid date.";
		} else if (!(isset($_POST['sex'])) || $_POST['sex'] == $EMPTY) {
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

		if ($errMessage == $EMPTY) {
			$sex = $_POST['sex'];

			$dbconn = pg_connect("host=localhost port=5432 dbname=$db_name user=$db_user password=$db_password");
            if(!$dbconn){
                $errMessage = "Connect to server failed";
                exit;
            } 
			$insert_user_query = "INSERT INTO appuser (email, fname, lname, password, birthday, signupdate, news, sex) VALUES($1, $2, $3, $4, $5, $6, $7, $8);";
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
	
	<form method = "POST">
        <table class="form" id="signup">
        	<tr>
        		<td colspan="3"><h2>Sign Up</h1></td>
        	</tr>
        	<tr>
        		<td>
        			<div class="error"><?php echo $errMessage; ?></div>
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
            				<option value="1">1</option>
            				<option value="2">2</option>
            				<option value="3">3</option>
            				<option value="4">4</option>
            				<option value="5">5</option>
            				<option value="6">6</option>
            				<option value="7">7</option>
            				<option value="8">8</option>
            				<option value="9">9</option>
            				<option value="10">10</option>
            				<option value="11">11</option>
            				<option value="12">12</option>
            				<option value="13">13</option>
            				<option value="14">14</option>
            				<option value="15">15</option>
            				<option value="16">16</option>
            				<option value="17">17</option>
            				<option value="18">18</option>
            				<option value="19">19</option>
            				<option value="20">20</option>
            				<option value="21">21</option>
            				<option value="22">22</option>
            				<option value="23">23</option>
            				<option value="24">24</option>
            				<option value="25">25</option>
            				<option value="26">26</option>
            				<option value="27">27</option>
            				<option value="28">28</option>
            				<option value="29">29</option>
            				<option value="30">30</option>
            				<option value="31">31</option>
            			</select>
            			<select name="year">
            				<option value="0" selected="1">Year</option>
            				<option value="2014">2014</option>
            				<option value="2013">2013</option>
            				<option value="2012">2012</option>
            				<option value="2011">2011</option>
            				<option value="2010">2010</option>
            				<option value="2009">2009</option>
            				<option value="2008">2008</option>
            				<option value="2007">2007</option>
            				<option value="2006">2006</option>
            				<option value="2005">2005</option>
            				<option value="2004">2004</option>
            				<option value="2003">2003</option>
            				<option value="2002">2002</option>
            				<option value="2001">2001</option>
            				<option value="2000">2000</option>
            				<option value="1999">1999</option>
            				<option value="1998">1998</option>
            				<option value="1997">1997</option>
            				<option value="1996">1996</option>
            				<option value="1995">1995</option>
            				<option value="1994">1994</option>
            				<option value="1993">1993</option>
            				<option value="1992">1992</option>
            				<option value="1991">1991</option>
            				<option value="1990">1990</option>
            				<option value="1989">1989</option>
            				<option value="1988">1988</option>
            				<option value="1987">1987</option>
            				<option value="1986">1986</option>
            				<option value="1985">1985</option>
            				<option value="1984">1984</option>
            				<option value="1983">1983</option>
            				<option value="1982">1982</option>
            				<option value="1981">1981</option>
            				<option value="1980">1980</option>
            				<option value="1979">1979</option>
            				<option value="1978">1978</option>
            				<option value="1977">1977</option>
            				<option value="1976">1976</option>
            				<option value="1975">1975</option>
            				<option value="1974">1974</option>
            				<option value="1973">1973</option>
            				<option value="1972">1972</option>
            				<option value="1971">1971</option>
            				<option value="1970">1970</option>
            			</select>
            		</span>
            	</td>
            </tr>
            <tr id="sex">
            	<td colspan="2">
         			<label><input type="radio" name="sex" value="1" />Female &nbsp;&nbsp;</label>
         			<label><input type="radio" name="sex" value="2" />Male </label>
            	</td>
            </tr>
            <tr>
            	<td colspan="2">
            		<label><input type="checkbox" name="news" value="1"/>I'd like to recieve news from To-do Manager.</label>
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

<?php	require 'footer.php'; ?>
<?php
    session_save_path("sess");
	session_start();
    
    $page = "login";

    require 'config.inc';
    require 'header.php';

    $errMessage = "";
    $email = "";
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $dbconn = pg_connect("host=localhost port=5432 dbname=$db_name user=$db_user password=$db_password");
        if(!$dbconn){
            echo "Aw, Snap!";
            exit;
        } 

        $email = $_POST['email'];
        $password = md5($_POST['password']);

        $select_user_query = ("SELECT * FROM appuser WHERE email = $1 AND password = $2;");
        $result = pg_prepare($dbconn, "select_user", $select_user_query);
        $result = pg_execute($dbconn, "select_user", array($email, $password));

        if(pg_num_rows($result)){
            $row = pg_fetch_array($result);
            $_SESSION['user'] = $row['uid'];
            $_SESSION['fname'] = $row['fname'];
            header("Location: home.php");
        } else {
            $errMessage = "Invalid user or password.";
        }
    }
    
?>  
<div class="container">
    <form method = "POST">
        <table class="form">
            <tr>
                <td colspan="2"><div class="error" <?php if($errMessage=="") echo "hidden"; ?> ><?php echo($errMessage); ?></div></td>
            </tr>
            <tr>
                <td>Email: </td>
                <td><input type="text" name="email" value="<?php echo $email; ?>"size="25" /></td>
            </tr>
            <tr>
                <td>Password: </td>
                <td><input type="password" name="password" size = "25" /></label></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" class="submit" value="Log In" /> or <a href="signup.php">Sign up</a></td>
            </tr>
        </table>
    </form>
</div>

<?php require 'footer.php'; ?>

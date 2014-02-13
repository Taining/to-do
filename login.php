<?php
    session_save_path("sess");
	session_start();
    
    $page = "login";
    require 'config.inc';
    require 'header.php';
    
    $errMessage = "";
    $email = "";
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(isset($_POST['email']) && isset($_POST['password'])){
            $dbconn = pg_connect("host=localhost port=5432 dbname=$db_name user=$db_user password=$db_password");
            if(!$dbconn){
                $errMessage = "Connect to server failed";
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
                header("Location: home.php");
            } else {
                $errMessage = "Invalid user or password.";
            }
        }
    }

?>  
    <form method = "POST">
        <table class="login">
            <tr>
                <td colspan="2" class="error"><?php echo($errMessage); ?></td>
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

<?php require 'footer.php'; ?>

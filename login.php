<?php
    require 'config.inc';
    require 'header.php';

    session_start();
    $errMessage = "";
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(isset($_POST['user']) && isset($_POST['password'])){
            $dbconn = pg_connect("host=localhost port=5432 dbname=$db_name user=$db_user password=$db_password");
            if(!$dbconn){
                $errMessage = "Connect to server failed";
                exit;
            } 
            
            $result = pg_query($dbconn, "SELECT * FROM appuser WHERE username = '$_POST[user]' AND password = '$_POST[password]'");
            if(pg_num_rows($result)){
                $_SESSION['authenticated'] = true;
                header("Location: home.php");
            } else {
                $errMessage = "Invalid user or password.";
            }
        } else if(!isset($_SESSION['authenticated'])){
            $_SESSION['authenticated'] = false;
        }
    }

?>  
    <form method = "POST">
        <table class="login">
            <tr>
                <td colspan="2" class="error"><?php echo($errMessage); ?></td>
            </tr>
            <tr>
                <td>User Name: </td>
                <td><input type="text" name="user" size="25" /></td>
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

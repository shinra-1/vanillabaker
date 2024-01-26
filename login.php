<?php
session_start();
//check if may nakalog in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if($id === 1){
        header("location: dashboard.php");
    }else{
        header("location: index.php");
    }
    exit;
}

require_once "config.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if pw is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    //check kung tama credentials
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id, username, password FROM login_user WHERE username = ?";
        
        if($statement = mysqli_prepare($conn, $sql)){
            // bind variables sa prepared statement as parameters
            mysqli_stmt_bind_param($statement, "s", $param_username);
            
            // set parameter
            $param_username = $username;
            
            // execute
            if(mysqli_stmt_execute($statement)){
                mysqli_stmt_store_result($statement);
                
                // check if username exists, verify password kung oo
                if(mysqli_stmt_num_rows($statement) == 1){                    
                    mysqli_stmt_bind_result($statement, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($statement)){
                        if(password_verify($password, $hashed_password)){

                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            
                            if($id === 1){
                                header("location: dashboard.php");
                            }else{
                                header("location: index.php");
                            }
                        } else{
                            // pw is not valid, error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // username doesn't exist, error message
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // close
            mysqli_stmt_close($statement);
        }
    }
    
    // close
    mysqli_close($conn);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="wrapper">
        <h2 class="heading">Login</h2>

        <!-- <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">'.$login_err.'</div>';
        }        
        ?> -->

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label class="head2">Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label class="head2">Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p class="footer">Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </form>
    </div>
</body>
</html>
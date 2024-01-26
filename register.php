<?php
require_once "config.php";
 
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$age = $_POST['age'];
    $cnumber = $_POST['cNumber'];
    $email = $_POST['email'];

    // check if tama username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    }else if(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    }else{
        $sql = "SELECT id FROM login_user WHERE username = ?";
        
        if($statement = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($statement, "s", $param_username);
            
            // set parameter
            $param_username = trim($_POST["username"]);
            
            // execute
            if(mysqli_stmt_execute($statement)){
                mysqli_stmt_store_result($statement);
                
                if(mysqli_stmt_num_rows($statement) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // close
            mysqli_stmt_close($statement);
        }
    }
    
    // check if tama pw
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    }else if(strlen(trim($_POST["password"])) < 8){
        $password_err = "Password must have atleast 8 characters.";
    }else{
        $password = trim($_POST["password"]);
    }
    
    // check if parehas sa pw
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    }else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // check input errors bago ipasok sa db
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        if (empty($fname) ||empty($lname) || empty($age) ||  empty($cNumber) ||empty($email)){
            echo '<script>alert("Please fill all information.")</script>';
            // echo '<script>location.href = "register.php";</script>';
        
        $sql = "INSERT INTO login_user (username, password, category) VALUES (?, ?, 'customer')";
         
        if($statement = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($statement, "ss", $param_username, $param_password);
            
            // set parameter
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // creates a password hash
            
            // execute
            if(mysqli_stmt_execute($statement)){
                $insert = "INSERT INTO users(`firstname`, `lastname`, `age`, `username`, `password`, `contact_number`, `email`) VALUES ('$fname','$lname','$age','$param_username','$param_password','$cnumber','$email')";
		        $upload = mysqli_query($conn, $insert);
                echo "<script>alert('Successfull Registered!')</script>";
                header("location: login.php");
            }else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // close
            mysqli_stmt_close($statement);
        }
    }}
    
    // close
    mysqli_close($conn);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="wrapper">
        <h2 class="heading">Sign Up</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-col">
                <div class="form-divider">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="fname" class="form-control" value="" required>
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Age</label>
                        <input type="number" name="age" class="form-control" value="" required>
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="" required>
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div> 
                </div> 
                <div class="form-divider2">  
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="lname" class="form-control" value="" required>
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="number" name="cNumber" class="form-control" value="" required>
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                    </div>
                </div>
            </div>
            <div class="form-col2">
                <div class="form-group2">
                    <label for="termscon"><input type="checkbox" id="termscon" name="termscon" required> I have read and agree to the <a href="terms-and-conditions.php" target="_blank">Terms and Conditions</a>.</label>
                </div>  
                <div class="form-group2">
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <input type="reset" class="btn btn-secondary ml-2" value="Reset">
                </div>
                <p class="redirect">Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </form>
    </div>    
</body>
</html>
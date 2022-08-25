<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to index page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location:login.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);

            
           }

    // Validate credentials for admin
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ? ";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = "$username";                            
                            // check if user is admin or user
                            $query = "SELECT * FROM users WHERE username='$username' AND status='admin'";
                            $results = mysqli_query($link, $query);
                                $logged_in_user = mysqli_fetch_assoc($results);
                                if ($logged_in_user['status'] == 'admin') {
                    
                                    $_SESSION['user'] = $logged_in_user;
                                    $_SESSION['success']  = "You are now logged in";
                                    header('location: index.php');		  
                                }else{
                                    $_SESSION['user'] = $logged_in_user;
                                    $_SESSION['success']  = "You are now logged in";
                    
                                    header('location: welcome.php');
                                }

                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
               //Close connection
            mysqli_close($link);
        }
    }
    





















}






?>
 
<!DOCTYPE html>
<html lang="en">
<head>

<title></title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>


<div class="container-fluid">
		<div class="row">
			<div class="col-lg-6 col-md-6 form-container">
				<div class="col-lg-8 col-md-12 col-sm-9 col-xs-12 form-box text-center">
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
           
            <div class="form-input">
                <span><i class="fa fa-envelope"></i></span>
                <input type="text" name="username" placeholder="Email Address" <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    


           
            <div class="form-input">
                <span><i class="fa fa-lock"></i></span>
                <input type="password" name="password" placeholder="Password" <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>

            <div class="row mb-3">
            <div class="col-6 text-right">
			<a href="./forget-password.php" class="forget-link">Forget password</a>
			</div>
            </div>
            <div class="text-left mb-3 col-6">
            <input type="submit" class="btn btn-primary" value="Login">
			</div>

            <div class="text-white">Don't have an account?
				<a href="./register.php" class="register-link">Register here</a>
			</div>

        </form>
    </div>
    </div>
			</div>

			<div class="col-lg-6 col-md-6 d-none d-md-block image-container"></div>
		</div>
        </div>
</body>
</html>





<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$loginId = $password = $confirm_password = $rollno = "";
$loginId_err = $password_err = $confirm_password_err = $rollno_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    if(empty(trim($_POST["loginId"]))){
        $loginId_err = "Please enter a login Id.";
    } else {
        $sql = "SELECT loginId FROM authentication WHERE loginId = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_loginId);
            
            // Set parameters
            $param_loginId = trim($_POST["loginId"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $loginId_err = "This login Id is already taken.";
                } else{
                    $loginId = trim($_POST["loginId"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    // Validate rollno
    if(empty(trim($_POST["rollno"]))){
        $rollno_err = "Please enter a roll number.";
    } else{
        // Prepare a select statement
        $sql = "SELECT rollno FROM reader WHERE rollno = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_rollno);
            
            // Set parameters
            $param_rollno = trim($_POST["rollno"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1){
                    $rollno = trim($_POST["rollno"]);
                } else{
                    $rollno_err = "This roll number does not exist.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    $password = $_POST["password"];
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 8 || strlen(trim($_POST["password"])) > 26 || !preg_match("#[0-9]+#",$password)
    || !preg_match("#[A-Z]+#",$password) || !preg_match("#[a-z]+#",$password)){
        $password_err = "Password must have 8 to 26 characters and include lowercase, uppercase and number.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($loginId_err) && empty($rollno_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO authentication VALUES (?, ?)";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_loginId, $param_password);
            // Set parameters
            $param_loginId = $loginId;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            // Attempt to execute the prepared statement
            $check = mysqli_stmt_execute($stmt);
            
            if(!$check){
                echo "Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }

        $sql = "INSERT INTO login VALUES (?, ?)";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_loginId, $param_rollno);
            // Set parameters
            $param_loginId = $loginId;
            $param_rollno = $rollno;
            // Attempt to execute the prepared statement
            $check = mysqli_stmt_execute($stmt);
            if($check){
                // Redirect to login page
                header("location: login.php");
            }
            else{
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif;}
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Mahatma Gandhi Library portal</h1>
        <h2>Sign Up</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($loginId_err)) ? 'has-error' : ''; ?>">
                <label>Login Id</label>
                <input type="text" name="loginId" class="form-control" value="<?php echo $loginId; ?>">
                <span class="help-block"><?php echo $loginId_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($rollno_err)) ? 'has-error' : ''; ?>">
                <label>rollno</label>
                <input type="text" name="rollno" class="form-control" value="<?php echo $rollno; ?>">
                <span class="help-block"><?php echo $rollno_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>
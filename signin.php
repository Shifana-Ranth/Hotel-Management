<?php
    include("databasee.php");
    $showalert=false;
    $showerror=false;
    $exist=false;
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $username=trim($_POST["username"]);
        $password=($_POST["password"]);
        $cpassword=($_POST["cpassword"]);
        $admincode=($_POST["admincode"]);
        $hash=password_hash($password,PASSWORD_DEFAULT);

        if(empty($username)){
            $showerror="please enter your username";
        }

        else if(empty($password)){
            $showerror="please enter yourssss password";
        }
        else if(($password!=$cpassword))
        {
            $showerror="Password didnt match";
        }
        elseif (!preg_match("/^(?=.*[a-zA-Z])[a-zA-Z0-9]+$/", $username)) {
            $showerror= "Username must contain at least one letter and only letters/numbers.";
        }
        else if (!preg_match("/[a-zA-Z]/", $password) || !preg_match("/\d/", $password) || !preg_match("/[!@#$%^&*()_+]/", $password)) {
            $showerror = "Password should contain at least one letter, one number, and one special character.";
        }
        else if(strlen($username)<5 || strlen($password)>30){
            $showerror="username should be in the range of 5 to 30";
        }
        else if (strlen($password) < 8)
        {
            $showerror="password should be atleast 8 char long";
        }
        else{
            $existsql="SELECT * FROM users WHERE uname='$username'";
            $result=mysqli_query($conn,$existsql);
            $nrows=mysqli_num_rows($result);
            if($nrows>0)
            {
                $showerror="Username taken";
                $exist=true;
            }
            else {
                try{
                    $roless='';
                    if($admincode=="adminvoyage")
                    {
                        $roless='Admin';
                    }
                    else{
                        $roless='User';
                    }
                    $sql="INSERT INTO users (uname,upassword,roles) VALUES ('$username','$hash','$roless')";
                    mysqli_query($conn,$sql);
                    $userid = mysqli_insert_id($conn);
                    $showalert=true;
                    session_start();
                    $_SESSION["username"]=$username;
                    $_SESSION["loggedin"]=true;
                    $_SESSION["roles"]=$roless;
                    $_SESSION["userid"] = $userid;
                    echo '<script>alert("Successfully Registered"); window.location.href="prof.php";</script>';
                }
                catch(mysqli_sql_exception)
                {
                    $showerror="Unable to store data";
                } 
            }
        }
    }
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8">
<head>
    <title>VoyageVista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="stylesignup.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
            *{
                font-family: 'Times New Roman', Times, serif;
            }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <?php 
            if($showerror){
                echo '
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="color:black;font-weight:700;padding-left:70px;">
                <strong style="background-color:transparent;"><i style="color:red;background-color:transparent;" class="fa-solid fa-circle-exclamation"></i></strong> '.$showerror.'
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                ';
            }
        ?>
        <div class="container">
            <div class="login-box">
                <form action="signin.php" method="post">
                    <?php  echo "<h2 style='background-color:transparent;color:white;'>Welcome to Register...</h2>"; ?>
                    <label for="username"><i class="fa-solid fa-user" style="background-color:transparent;color:white;"></i>  Username <span style="background-color:transparent;color:red;">*</span>  </label>
                    <div class="box">
                    <input type="text" name="username" placeholder="Enter your username" required>
                    </div>
                    <label for="password"><i class="fa-solid fa-lock" style="background-color:transparent;color:white;"></i>  Password <span style="background-color:transparent;color:red;">*</span></label>
                    <div class="box">
                    <input type="password" name="password" id="password" placeholder="Enter your Password" style="width:90%;"required ><i style="text-align:rightwidth:10%;" id="eye" class="fa-solid fa-eye-slash"></i>
                    </div>
                    <label for="cpassword"><i class="fa-solid fa-key" style="background-color:transparent;color:white;"></i>  Confirm Password <span style="background-color:transparent;color:red;">*</span></label>
                    <div class="box">
                    <input type="password" name="cpassword" id="cpassword" placeholder="Enter the same password" required>
                    </div>
                    <label for="admincode"><i class="fa-solid fa-user-secret" style="background-color:transparent;color:white;"></i>  Admin Code </label>
                    <div class="box">
                    <input type="text" name="admincode" id="admincode" placeholder="Enter the Admin Code">
                    </div>
                    <button type="submit" name="register" class="signin-btn" value="register" >SIGN UP</button>
                    <p class="create-account">Already a user? <a href="login.php">login</a></p>
                </form>
            </div>
            <div class="content">
                <h1>FIND YOUR PERFECT STAY.</h1>
                <p>Where Your Dream Destinations <br> Become Reality.</p>
            </div>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script>
        const password = document.getElementById("password");
        const eye = document.getElementById("eye");

        eye.addEventListener("click", function () {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            
            eye.className= type === "text" ? "fa-solid fa-eye" : "fa-solid fa-eye-slash";
        }); 
    </script>
</body>
</html>
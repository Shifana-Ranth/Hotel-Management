<?php
    session_start();
    $inactive = 1200;  
    if(isset($_SESSION['username']))
    {
        session_destroy();
        header("location:index.php");
        exit;
    }
    include("databasee.php");
    $login=false;
    $showerror=false;
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login']))
    {
        $username=trim($_POST["username"]);
        $password=trim($_POST["password"]);

        if(empty($username)){
            $showerror="please enter your username";
        }
        else if(empty($password)){
            $showerror="please enter yourssss password";
        }

        $sql="SELECT * FROM users WHERE uname='$username'";
        $res=mysqli_query($conn,$sql);
        $num=mysqli_num_rows($res);
        if($res && $num==1)
        {
            $row=mysqli_fetch_assoc($res);
            $hashed_password=$row["upassword"];
            if(password_verify($password,$hashed_password))
            {
                $login=true;
                $_SESSION["username"]=$username;
                $_SESSION["loggedin"]=true;
                $_SESSION["roles"]=$row["roles"];
                $_SESSION["userid"]=$row["uid"];
                $_SESSION["firstname"]=$row["firstname"];
                $_SESSION["lastname"]=$row["lastname"];
                $_SESSION["last_activity"]=time();
                if($_SESSION["firstname"]===''){
                    header("Location:prof.php");
                    exit();
                }
                if($_SESSION["roles"]==='Admin'){
                    header("Location:adminhome.php");
                    exit();
                }
                else if($_SESSION["roles"]==='manager'){
                    header("Location:managerhome.php");
                    exit();
                    
                }
                else{
                    header("Location: index.php");
                    exit();
                }
            }
            else{
                $showerror="Invalid credentials";
            }
        }
        else{
            $showerror="Invalid credentials";
        }
    }
    $error=false;
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset']))
    {
        $uname=trim($_POST["uname"]);
        $newPassword=($_POST["newPassword"]);
        $CPassword=($_POST["CPassword"]);
        $existsql="SELECT * FROM users WHERE uname='$uname'";
        $result=mysqli_query($conn,$existsql);
        $row=mysqli_fetch_assoc($result);
        $nrows=mysqli_num_rows($result);
        if($row && $nrows==1)
        {
            $username=$row['uname'];
            if(($newPassword!=$CPassword))
            {
                $showerror="Password should match with Confirm Password To reset";
            }
            else if (strlen($newPassword) < 8)
            {
                $showerror="password should be atleast 8 char long";
            }
            else if (!preg_match("/[a-zA-Z]/", $newPassword) || !preg_match("/\d/",  $newPassword) || !preg_match("/[!@#$%^&*()_+]/",  $newPassword)) {
                $showerror = "Password should contain at least one letter, one number, and one special character.";
            }
            else{
                $hash=password_hash($newPassword,PASSWORD_DEFAULT);
                try{
                    $sql = "UPDATE users SET upassword = '$hash' WHERE uname = '$username'";
                    mysqli_query($conn,$sql);
                    echo '<script>
                    alert("Password has been RESET Successfully for ' . $username . '");
                    </script>';
                }
                catch(mysqli_sql_exception){
                    $showerror="Unable to Reset password";
                }
            }
        }
        else{
            $showerror="Sorry!..Invalid Username";
        }
    }
    if(isset($_SESSION['username'])){
        $_SESSION["last_activity"]=time();
    }
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VoyageVista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="stylelog.css">
    <style>
        *{
            font-family: 'Times New Roman', Times, serif;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 110%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fff;
            /* margin: 10% auto; */
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            max-width: 100%;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .close {
            background-color:transparent;
            color: #fff;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: white;
            font-size:32px;
        }
        #forgotForm input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            /* margin-top: 8px; */
            margin-bottom: 20px;
            /* border: 1px solid #ccc; */
            border-radius: 5px;
            background-color:white;
            color:black;
        }
        button[type="submitt"] {
            background-color: #008CBA;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        #forgotBtn {
            background-color:transparent;
            color: white;
            cursor: pointer;
            text-align:right;
            width: 100%;
            text-decoration:underline;
        }
        button[type="submitt"]:hover {
            background-color: #005f73;
        }
        .box{
            background:white;
            height:52px;
            border-radius: 5px;
            border:1px solid black;
        }
        form input {
            padding: 10px;
            margin-top: 5px;
            border: none;
            outline:none;
            border-radius: 5px;
            color:black;;
        }
        .box:hover{
            border:2px solid black;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php
        if($showerror){
            echo '
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="position:relative;top:70px;color:black;font-weight:900;padding-left:70px;">
            <strong style="background-color:transparent;"><i style="color:red;background-color:transparent;" class="fa-solid fa-circle-exclamation"></i></strong> '.$showerror.'
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            ';
        }
    ?>
    <main style="height:90vh;">
        <div class="container">
            <div class="content">
                <h1>EXPLORE <br> HORIZONS</h1>
                <p>Where Your Dream Destinations <br> Become Reality.</p>
                <p>Embark on a journey where every corner of the world is within your reach.</p>
            </div>
            <div class="login-box">
                <form action="login.php" method="post">
                    <?php echo "<h2 style='background-color:transparent;color:white;'>Welcome Back...<i class='fa-regular fa-face-smile'  style='background-color:transparent;color:white;'></i></h2>"; ?>
                    <label for="username"> <i class="fa-solid fa-user" style="background-color:transparent;color:white;"></i>  Username <span style="background-color:transparent;color:red;">*</span></label>
                    <div class="box">
                    <input type="text" name="username" id="username" placeholder="Enter your username" required>
                    </div>
                    <label for="password"><i class="fa-solid fa-key" style="background-color:transparent;color:white;"></i>   Password  <span style="background-color:transparent;color:red;">*</span></label>
                    <div class="box">
                    <input type="password" name="password" id="password" placeholder="********" style="width:90%;"  required><i style="text-align:rightwidth:10%;" id="eye" class="fa-solid fa-eye-slash"></i>
                    </div>
                    <!-- Forgot Password Button -->
                    <a  id="forgotBtn">Forgot Password?</a>

                    <button type="submit" name="login" class="signin-btn" value="login" >LOGIN</button>
                    <p class="create-account">Are you new? <a href="signin.php">Create an Account</a></p>
                </form>
                <div id="forgotModal" class="modal">
                    <div class="modal-content" style="background: #444444;">
                        <div style="display:flex;justify-content:space-between;background-color:#444444;color:white;">
                            <h2  style="background-color:transparent;color:white;">Reset Password</h2>
                            <span class="close">&times;</span>
                        </div>
                        <form id="forgotForm" method="post" style="background: #444444;">
                        <label for="uname" style="font-size:14px;color:white;"> <i class="fa-solid fa-user" style="background-color:transparent;color:white;"></i> Username <span style="background-color:transparent;color:red;">*</span></label>
                        <input type="text" id="uname" name="uname" placeholder="Enter your username" required>

                        <label for="newPassword" style="font-size:14px;color:white;"> <i class="fa-solid fa-key" style="background-color:transparent;color:white;"></i> New Password <span style="background-color:transparent;color:red;">*</span></label>
                        <input type="password" id="newPassword" name="newPassword" placeholder="********"required>
                        
                        <label for="CPassword" style="font-size:14px;color:white;"> <i class="fa-solid fa-key" style="background-color:transparent;color:white;"></i> Confirm Password <span style="background-color:transparent;color:red;">*</span></label>
                        <input type="password" id="CPassword" name="CPassword" placeholder="********"required>
                        
                        <button id="rstbtn" type="submitt" name="reset" value="reset"  >Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script>
        const modal = document.getElementById("forgotModal");
        const btn = document.getElementById("forgotBtn");
        const span = document.getElementsByClassName("close")[0];
        const rstbtn =  document.getElementById("rstbtn");

        btn.onclick = function () {
            modal.style.display = "block";
        }

        rstbtn.addEventListener("click",function () {
            console.log("hehe");
            modal.style.display = "block";
        });

        span.onclick = function () {
            modal.style.display = "none";
        }
    </script>
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

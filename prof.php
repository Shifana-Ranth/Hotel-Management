
<?php
    session_start();
    include("databasee.php");
    $inactive=1200;
    if (isset($_SESSION["last_activity"])) {
        $session_life = time() - $_SESSION["last_activity"];
        
        if ($session_life > $inactive) {
            session_unset();
            session_destroy();
            header("Location: index.php");
        }
    }
    $showalert=false;
    $showerror=false;
    $exist=false;
    if(!isset($_SESSION["username"]) && !isset($_SESSION["userid"])) {
        header("Location: login.php");
        exit();
    }
    $username = $_SESSION["username"]; 
    $userid = $_SESSION["userid"]; 
    if($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['loggedin'] ===true)
    {
        $firstname=trim($_POST["firstname"]);
        $lastname=trim($_POST["lastname"]);
        $address=trim($_POST["address"]);
        $city=trim($_POST["city"]);
        $state=trim($_POST["state"]);
        $pincode=trim($_POST["pincode"]);
        $phno=trim($_POST["phno"]);
        $email=trim($_POST["email"]);

        if(empty($firstname)){
            $showerror="please enter your firstname";
        }
        if(empty($lastname)){
            $showerror="please enter your lastname";
        }
        elseif (!preg_match("/^[a-zA-Z\s ]+$/", $firstname)) {
            $showerror="Firstname must contain only alphabets (A-Z, a-z) without numbers or special characters.";
        }
        elseif (!preg_match("/^[a-zA-Z\s ]+$/", $lastname)) {
            $showerror="Lastname must contain only alphabets (A-Z, a-z) without numbers or special characters.";
        }
        elseif (!preg_match("/^[a-zA-Z\s ]+$/", $city)) {
            $showerror="City must contain only alphabets (A-Z, a-z) without numbers or special characters.";
        }
        elseif (!preg_match("/^[a-zA-Z\s ]+$/", $state)) {
            $showerror="State must contain only alphabets (A-Z, a-z) without numbers or special characters.";
        }
        elseif (!preg_match("/^[a-zA-Z0-9\s,.-\/]+$/", $address)) {
            $showerror = "Address must contain only alphabets, spaces, numbers, comma, dot and hyphen.";
        }
        else if(strlen($pincode) !==6)
        {
            $showerror="Pin code should be 6 digits";
        }
        else if(strlen($phno) !==10)
        {
            $showerror="Ph no should be 10 digits";
        }
        else{
            $checkphsql="SELECT * FROM users WHERE phno='$phno'"; 
            $checkemailsql="SELECT * FROM users WHERE email='$email'"; 
            $re=mysqli_query($conn,$checkphsql);
            $remail=mysqli_query($conn,$checkemailsql);
            $num=mysqli_num_rows($remail);
            $nrows=mysqli_num_rows($re);
            if($nrows>0)
            {
                $showerror="Already registerd phone no";
                $exist=true;
            }
            else if($num>0)
            {
                $showerror="Already registerd email";
                $exist=true;
            }
            else {
                try{
                    $sql = "UPDATE users SET 
                            firstname='$firstname', 
                            lastname='$lastname', 
                            addresss='$address', 
                            city='$city', 
                            states='$state', 
                            pincode='$pincode', 
                            phno='$phno', 
                            email='$email'
                            WHERE uid='$userid'"; 
                    mysqli_query($conn,$sql);
                    $showalert=true;
                    $_SESSION["firstname"]=$firstname;
                    $_SESSION["lastname"]=$lastname;
                    if($_SESSION["roles"]==='Admin'){
                        header("location:adminhome.php");
                        exit();
                    }
                    else if($_SESSION["roles"]==='manager'){
                        header("location:managerhome.php");
                        exit();
                    }
                    else{
                        header("Location:index.php");
                        exit();
                    }
                }
                catch(mysqli_sql_exception $e)
                {
                    $showerror = "Cannot store data: " . $e->getMessage();
                } 
            }
        }
    }
    $_SESSION["last_activity"] = time();
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VoyageVista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styleprof.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
        *{
            font-family: 'Times New Roman', Times, serif;
        }
    </style>
</head>
<body>
    <?php include 'header.php';?>
    <?php 
    if($showerror){
        echo '
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="position:relative;top:70px;color:black;font-weight:700;padding-left:70px;">
        <strong style="background-color:transparent;"><i style="color:red;background-color:transparent;" class="fa-solid fa-circle-exclamation"></i></strong> '.$showerror.'
        <button  type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span style="background-color:transparent;" aria-hidden="true">&times;</span>
        </button>
        </div>
        ';
    }
    ?>
    <main>
    <center>
    <div class="contty">
        
        <form action="prof.php" method="post">
        <?php
                    if($showalert==true)
                    {
                        echo "<h2 style='color:green;background-color:white;'>You are Succesfully filled</h2>".$firstname;
                    }
                    else{
                        echo "<h2 style='background-color:powderblue;'>Kindly,Fill Up Your Details</h2>";
                    }
        ?>
            <div class="row">
                <div class="column">
                    <label>First Name <span style="background-color:transparent;color:red;">*</span></label>
                    <input type="text" name="firstname" required>
                </div>
                <div class="column">
                    <label>Last Name <span style="background-color:transparent;color:red;">*</span></label>
                    <input type="text" name="lastname" required>
                </div>
            </div>
            

            <div class="row">
                <div class="column">
                    <label>Address <span style="background-color:transparent;color:red;">*</span></label>
                    <input type="text" name="address" required>
                </div>
            </div>

            <div class="row">
                <div class="column">
                    <label>City <span style="background-color:transparent;color:red;">*</span></label>
                    <input type="text" name="city" required>
                </div>
                <div class="column">
                    <label>State <span style="background-color:transparent;color:red;">*</span></label>
                    <input type="text" name="state"required>
                </div>
                <div class="column">
                    <label>Pin Code <span style="background-color:transparent;color:red;">*</span></label>
                    <input type="number" name="pincode" required>
                </div>
            </div>

            <div class="row">
                <div class="column">
                    <label>Phone <span style="background-color:transparent;color:red;">*</span></label>
                    <input type="number" name="phno"required>
                </div>
                <div class="column">
                    <label>Email Address <span style="background-color:transparent;color:red;">*</span></label>
                    <input type="email" name="email" required>
                </div>
            </div>

            <button type="submit">Submit</button>
        </form>
    </div>
    </center>
    </main>
    <?php include 'footer.php';?>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
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
    $userid=isset($_SESSION['userid']) ? $_SESSION["userid"]:''; 
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['loggedin']) ===true)
    {
        $name=trim($_POST["name"]);
        $phno=trim($_POST["phno"]);
        $email=trim($_POST["email"]);
        $msg=trim($_POST["msg"]);

        if(empty($name)){
            $showerror="please enter your firstname";
        }
        elseif (!preg_match("/^[a-zA-Z]+$/", $name)) {
            $showerror="Firstname must contain only alphabets (A-Z, a-z) without numbers or special characters.";
        }
        else if(strlen($phno) !==10)
        {
            $showerror="Ph no should be 10 digits";
        }
        else{
                try{
                    $sql="INSERT INTO review (uidd,namee,email,phno,msg) VALUES ('$userid','$name','$email','$phno','$msg')";
                    mysqli_query($conn,$sql);
                    $showalert=true;
                }
                catch(mysqli_sql_exception $e)
                {
                    $showerror = "Cannot store data: " . $e->getMessage();
                } 
            }
    }
    else if($_SERVER["REQUEST_METHOD"] == "POST"){
        $showerror="Please Register/Login to send Message";
    }
    mysqli_close($conn);
    $_SESSION["last_activity"] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VoyageVista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="st.css">
    <link rel="stylesheet" href="stylebot.css" >
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
        if($showalert)
        {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert" style="color:black;font-weight:700;padding-left:70px;">
                    <strong style="background-color:transparent;">Thanks For Your Feedback...<i style="font-size:1.5rem;color:green ;background-color:transparent;"  class="fa-regular fa-thumbs-up"></i></strong>
                    <button style="left:85%;" type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            ';
        }
        if($showerror){
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert" style="color:black;font-weight:700;padding-left:70px;">
                    <strong style="background-color:transparent;"><i style="color:red;background-color:transparent;" class="fa-solid fa-circle-exclamation"></i></strong> '.$showerror.'
                    <button style="left:85%;" type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            ';
        }
    ?>
        <div class="abt"><h1>Contact Us</h1></div>
        <div class="info">
            <div class="info1" style="border-right:1px solid black;">
                <i class="fa-solid fa-phone" style="color: #246A73;"></i>
                <b style="font-size: 1.2rem;">Phone</b>
                <p>(+91) 9597422515</p>
                <p>(+91) 9597422515</p>
            </div>
            <div class="info1" style="border-right:1px solid black;">
                <i class="fa-solid fa-location-dot" style="color: #246A73;"></i>
                <b style="font-size: 1.2rem;">Address</b>
                <p>No:345,North Street,Delhi</p>
                <p>Branch:431,Southern Agra road.</p>
            </div>
            <div class="info1">
                <i class="fa-regular fa-envelope" style="color: #246A73;"></i>
                <b style="font-size: 1.2rem;">Email</b>
                <p>voyagevista@gmail.com</p>
                <p>travelplan@gmail.com</p>
            </div>
        </div>
        <div class="formdiv">
        <div class="contdiv"> picturee</div>
        <div class="contact-form">
            <h2 style="color: #246A73;">Contact Us For Any Queries</h2>
            <form action="contactus.php" method="post">
                <div class="input-group">
                    <label>Full Name<span style="background-color:transparent;color:red;">*</span></label>
                    <input type="text" name="name" placeholder="Full Name" required>
                </div>
                <div class="input-group">
                    <label>Contact No<span style="background-color:transparent;color:red;">*</span></label>
                    <input type="number" name="phno" placeholder="Phone" required>
                </div>
                <div class="input-group">
                <label>Email<span style="background-color:transparent;color:red;">*</span></label>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <label>Message<span style="background-color:transparent;color:red;">*</span></label>
                <textarea placeholder="Message" name="msg" required></textarea>
                <button class="sbtn" type="submit" style="background-color: #246A73;">Send Message</button>
            </form>
        </div>
        </div>
    </section>
    </main>
    <div class="chat">
    <a class="chatbot-button" onclick="toggleChatbot()" style="color:white;font-size:15px;">Chat with us</a>
    <div class="chatbot-popup" id="chatbotBox">
        <div class="chatbot-header">
            VoyageVista Chat
            <span style="background-color:transparent;" class="chatbot-close" onclick="toggleChatbot()">&times;</span>
        </div>
        <iframe src="https://shifana2604-voyagevista-bot.hf.space"></iframe>
    </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script>
        function toggleChatbot() {
            var box = document.getElementById("chatbotBox");
            box.style.display = box.style.display === "block" ? "none" : "block";
        }
    </script>
    <script
        type="module"
        src="https://gradio.s3-us-west-2.amazonaws.com/5.0.1/gradio.js"
    ></script>
</body>
</html>
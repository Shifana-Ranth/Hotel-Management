<?php
    session_start();
    if(isset($_SESSION['loggedin']) ===false){
        header("Location: index.php");
        exit();
    }
    $inactive=1200;
    if (isset($_SESSION["last_activity"])) {
        $session_life = time() - $_SESSION["last_activity"];
        
        if ($session_life > $inactive) {
            session_unset();
            session_destroy();
            header("Location: index.php");
        }
    }
    $_SESSION["last_activity"] = time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success Payment</title>
    <link rel="stylesheet" href="stylemsg.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        *{
            font-family: 'Times New Roman', Times, serif;
        }
        .checkmark-wrapper {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color:  #4caf50;
            background-color:  blue;
            /* border: 4px solid green; */
            display: flex;
            justify-content: center;
            align-items: center;
            animation: pop 0.4s ease-out forwards;
        }

        .checkmark {
            transform: rotate(-180deg) scale(0);
            animation: draw 0.3s ease-out 0.4s forwards;
        }

        @keyframes pop {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes draw {
            to {
                transform: rotate(0deg) scale(1);
            }
        }
    </style>
</head>
<body>
    <audio id="audioPlayer" preload="auto">
        <!-- <source src="audio_file.mp3" type="audio/mpeg"> -->
        <source src="aud.mpeg" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <?php include 'header.php';?>
    <main style="margin-top:10rem;" >
        <div class="container text-center">
            <div class="success-icon">
                <center>
                <div class="checkmark-wrapper">
                <i class="checkmark fa-solid fa-check" style="background-color:transparent;color:white;font-size:3rem;"></i>
                </center> 
                <br>
            </div>
            <h1 class="thank-you-message">Payment Successful!</h1>
            <p class="thank-you-submessage">Thank you for your booking. Your reservation is confirmed.</p>
            <a href="bill.php" class="btn btn-primary mt-4" style="background-color:darkblue;">View Bill</a>
        </div>
    </main>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const audio = document.getElementById('audioPlayer');
        audio.play();
        setTimeout(() => {
            audio.pause();
        }, 5000); 
    });
</script>
</html>
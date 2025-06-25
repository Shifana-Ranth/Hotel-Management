<?php
    session_start();
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
    <title>VoyageVista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="stylefaq.css">
</head>
<body>
    <?php include 'header.php';?>
    <main>
    <div class="abt"><h1 style="color: white;">Frequently Asked Questions</h1></div>
        <div class="formdiv">
            <div class="contdiv" style="border:3px solid #246A73 ;"></div>
            <div class="b">
            <div class="ques" style="background-color: #246A73;">
                <details>
                    <summary style="background-color: #246A73;">what is voyagevista?</summary>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Saepe, eum</p>
                </details>
            </div>
            <div class="ques" style="background-color: #246A73;">
                <details>
                    <summary style="background-color: #246A73;">what is voyagevista?</summary>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Saepe, eum</p>
                </details>
            </div>
            <div class="ques" style="background-color: #246A73;">
                <details>
                    <summary style="background-color: #246A73;">what is voyagevista?</summary>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Saepe, eum</p>
                </details>
            </div>
            <div class="ques" style="background-color: #246A73;">
                <details>
                    <summary style="background-color: #246A73;">what is voyagevista?</summary>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Saepe, eum</p>
                </details>
            </div>
            <div class="ques" style="background-color: #246A73;">
                <details>
                    <summary style="background-color: #246A73;">what is voyagevista?</summary>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Saepe, eum</p>
                </details>
            </div>
            <div class="ques" style="background-color: #246A73;">
                <details>
                    <summary style="background-color: #246A73;">what is voyagevista?</summary>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Saepe, eum</p>
                </details>
            </div>
            </div>
        </div>
    </main>
    <?php include 'footer.php';?>
</body>
</html>
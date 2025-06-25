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
    <link rel="stylesheet" href="styleabout.css">
    <link rel="stylesheet" href="stylebot.css" >
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <div class="abt"><h1 style="color:black;">About Us</h1></div>
        <div class="aboutcontent">
            <div class="aboutimg1">
            </div>
            <div class="aboutinfo" style="padding:1rem;flex-direction: column;line-height:40px;font-size:18px;text-align:center;">
                <h1 style="color:#246A73;margin:1rem;">Wanna Know About Us...?</h1>
                Our hotel booking system makes it easy to find and reserve accommodations worldwide. Whether you traveling for business or leisure, we offer a wide range of hotels, from budget stays to luxury resorts. With a user-friendly interface, you can search and book hotels in just a few clicks. We ensure secure payments and provide the best price guarantees for your stay. Flexible cancellation policies allow you to modify or cancel bookings hassle-free. Our 24/7 customer support is always ready to assist you. Enjoy exclusive deals and discounts for an affordable travel experience. Detailed hotel descriptions, guest reviews, and high-quality images help you make informed decisions. Book with confidence and enjoy a seamless travel experience. Let us help you find the perfect stay for your next trip!
            </div>
        </div>
        <div class="team">
            <img src="teamm.png" style="width:100%;">
            <div class="teammem">
                <div class="nam"><b>Shifana Ranth</b></div>
                <div class="nam"><b>Pavithra</b></div>
                <div class="nam"><b>Santhoshini</b></div>
                <div class="nam"><b>Udhaya Dharani</b></div>
            </div>
        </div>
        <div class="explorediv">
            <div class="exp">
                <div class="dare">
                    <i class="fa-solid fa-earth-americas" style="display:block;"></i>
                    <h3>Dare to Explore with Travel</h3>
                </div>
            </div>
            <div class="census">
                <div class="cenbox">
                    <h2 class="number"><p>210k  <i class="fa-solid fa-face-smile"></i> </p></h2>
                    <p>Happy Travelers</p>
                </div>
                <div class="cenbox">
                    <h2 class="number"><p>120k  <i class="fa-solid fa-compass"></i></p></h2>
                    <p>Destination</p>
                </div>
                <div class="cenbox">
                    <h2 class="number"><p>154k  <i class="fa-solid fa-briefcase"></i></p></h2>
                    <p>Tour</p>
                </div>
                <div class="cenbox">
                    <h2 class="number"><p>200k <i class="fa-solid fa-thumbs-up"></i></p></h2>
                    <p>Satisfaction</p>
                </div>
            </div>
        </div>
    </main>
    <div class="chat">
    <button class="chatbot-button" onclick="toggleChatbot()">Chat with us</button>
    <div class="chatbot-popup" id="chatbotBox">
        <div class="chatbot-header">
            VoyageVista Chat
            <span style="background-color:transparent;" class="chatbot-close" onclick="toggleChatbot()">&times;</span>
        </div>
        <iframe src="https://shifana2604-voyagevista-bot.hf.space"></iframe>
    </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
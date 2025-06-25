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
    <link rel="stylesheet" href="styleindex.css">
    <link rel="stylesheet" href="stylebot.css" >
    <style>
        .text{
            display:flex;
            flex-direction:row;
            justify-content:center; 
            align-items:center;
        }
    #quote {
      font-size: 24px;
      font-family: 'Arial', sans-serif;
      margin: 10px;
      padding: 10px;
      border: 2px solid #ddd;
      border-radius: 8px;
      text-align: center;
      background: linear-gradient(135deg, #246A73, #3ca6a6);
      width:85%;
    }
    .btnn{
      padding: 15px 20px;
      font-size: 16px;
      cursor: pointer;
      border: none;
      background-color: #246A73;
      background: linear-gradient(135deg, #246A73, #F29F05);
      color: white;
      width:15%;
      border-radius: 5px;
    }
    .btnn:hover{
        background-color: #246A73;
        opacity:0.9;
    }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <h1 id="greeting" style="padding-left:40px;"></h1>
    <?php  //if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { echo "<h1 id='greet'>Welcome..".($_SESSION['firstname'] ? $_SESSION['firstname'] : $_SESSION['username'])." ".$_SESSION['userid']." ".$_SESSION['roles']." "."</h1>"; }?>
        <div class="mainimg">
            <div class="main1">
                <p id="typewriter"></p>
                <pre>Explore.Experience.Enjoy.</pre>
            </div>
            <div class="main2"></div>
        </div>
        <div class="text">
            <div id="quote">Relax, unwind, and recharge with us</div>
            <button class="btnn" onclick="generateQuote()">Our Values</button>
        </div>
        <br>
        <div class="search">
            <a href="explore.php"><button type="submit" class="se">Explore Our Hotels</button></a>
        </div>
        <div class="popcontent">
            <b style="font-size: 1.5rem;">Popular Destination</b>
            <p>Have a Hassle Free travel with us</p>
            <a>All Destinations -></a>
        </div>
        <div class="dest">
            <a href="explore.php?states=Tamil Nadu" style="text-decoration:none;"><div class="dest1 de" style="background-image: url('tamilnadu.jpeg');">Tamil Nadu</div></a>
            <a href="explore.php?states=Delhi" style="text-decoration:none;"><div class="dest3 de" style="background-image: url('del.jpeg');">Delhi</div></a>
            <a href="explore.php?states=Maharashtra" style="text-decoration:none;"><div class="dest2 de" style="background-image: url('maharashtra.jpeg');">Maharashtra</div></a>
            <a href="explore.php?states=West Bengal" style="text-decoration:none;"><div class="dest4 de" style="background-image: url('westbengal.jpeg');">West Bengal</div></a>
        </div>
        <hr>
        <div class="info">
            <div class="info1">
                <i class="fa-solid fa-plane"></i>
                <b style="font-size: 1.2rem;">Luxury Tours and Travels</b>
                <p>Book with Confidence</p>
                <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit.Lorem ipsum dolor sit, amet consectetur adipisicing elit.</p>
            </div>
            <div class="info1">
                <i class="fa-solid fa-ship"></i>
                <b style="font-size: 1.2rem;">Fantastic Visits</b>
                <p>Stress Free Experience</p>
                <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit.Lorem ipsum dolor sit, amet consectetur adipisicing elit.</p>
            </div>
        </div>
        <hr>
        <div class="client">
            <b style="font-size: 1.2rem;">What our clients say about us</b>
            <i class="fa-solid fa-user-tie"></i>
            <pre>Kendall Jenner</pre>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Et, delectus.Lorem ipsum dolor sit amet consectetur adipisicing elit. Et, delectus.</p>
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
    <script>
        function typeWriter(elementId, text, speed) {
            let i = 0;
            const element = document.getElementById(elementId);
            function write() {
                if (i < text.length) {
                element.innerHTML += text.charAt(i);
                i++;
                setTimeout(write, speed);
                }
            }   
            write();
        }
        typeWriter("typewriter", "Live Your Dream Destionations.", 200);
        const quotes = [
        "Escape to a world of comfort and luxury",
        "Book your dream stay with us",
        "Experience the art of hospitality",
        "Where every stay is a memorable one",
        "Book now and make unforgettable memories",
        "Your home away from home",
        "Relax, unwind, and recharge with us",
        "Discover the perfect blend of comfort and style",
        "Where luxury meets warmth",
        "Your perfect getaway awaits"
        ];

        function generateQuote() {
            document.getElementById("quote").innerText ="";
            const randomIndex = Math.floor(Math.random() * quotes.length); 
            typeWriter("quote",quotes[randomIndex], 100); 
        }
    </script>
    <?php
    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {  
        $name = isset($_SESSION['firstname']) ? $_SESSION['firstname'] : $_SESSION['username'];
    echo '
    <script>
        function showGreeting() {
            const now = new Date();
            const hour = now.getHours();
            let greeting = "";

            if (hour < 12) {
            greeting = "Good Morning!";
            } else if (hour < 17) {
            greeting = "Good Afternoon!";
            } else if (hour < 20) {
            greeting = "Good Evening!";
            } else {
            greeting = "Good Night!";
            }
            document.getElementById("greeting").innerText = greeting + " ' . $name . '";
        }
        
        window.onload = showGreeting;
    </script>';
    }
    ?>
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
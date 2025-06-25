<?php
    session_start();
    $inactive = 1200;  
    include("databasee.php");
    if (isset($_SESSION["last_activity"])) {
        $session_life = time() - $_SESSION["last_activity"];
        
        if ($session_life > $inactive) {
            session_unset();
            session_destroy();
            header("Location: index.php");
        }
    }
    if($_SESSION["loggedin"]==false || $_SESSION["roles"]!=='Admin'){
        header("Location: index.php");
        exit();
    }
    $hotel_count_query = "SELECT COUNT(*) FROM hotels";
    $booking_count_query = "SELECT COUNT(*) FROM bookings";
    $user_count_query = "SELECT COUNT(*) FROM users";
    $review_count_query = "SELECT COUNT(*) FROM review";
    $hotel_count_result = mysqli_query($conn, $hotel_count_query);
    $booking_count_result = mysqli_query($conn, $booking_count_query);
    $user_count_result = mysqli_query($conn, $user_count_query);
    $review_count_result = mysqli_query($conn, $review_count_query);
    $hotel_count = mysqli_fetch_assoc($hotel_count_result)['COUNT(*)'];
    $booking_count = mysqli_fetch_assoc($booking_count_result)['COUNT(*)'];
    $user_count = mysqli_fetch_assoc($user_count_result)['COUNT(*)'];
    $review_count = mysqli_fetch_assoc($review_count_result)['COUNT(*)'];
    $_SESSION["last_activity"] = time();
    mysqli_close($conn);
?>
<!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VoyageVista</title>
    <style>
        *{
            font-family: 'Times New Roman', Times, serif;
        }
        .sidebara {
            width: 250px;
            background: #2c3e50;
            height: 100vh;
            position: fixed;
            color: white;
            padding-top: 20px;
        }
        .sidebara a {
            display: block;
            color: white;
            padding: 15px;
            text-decoration: none;
        }
        .sidebara a:hover {
            background: #1a252f;
        }
        .card {
            background: white;
            padding: 20px;
            margin: 10px 0;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, #485563, #29323c);
            color:white;
        }
        .se{
            /* background-color: #29323c; */
            background-color:brown;
            background-color:  #4B5D67;
            width:15%;
            color: white;
            padding: 11px;
            margin-left: 120px;
            border-radius: 8px;
            cursor: pointer;
            border: none;
            font-size:1.5rem;
        }
        .se:hover {
            background-color:darkblue;
        }
        .c{
            background-color:transparent;
            color:white;
        }
    </style>
</head>
<body>
    <?php include 'headeradmin.php'; ?>
    <main  style="margin:5rem;">
    <h1 id="greeting" style="padding-left:10px;"></h1>
    <?php //echo src="WhatsApp Image 2025-03-31 at 8.43.49 PM.jpeg" "<h1>Welcome Admin:".($_SESSION['firstname'] ? $_SESSION['firstname'] : $_SESSION['username'])."</h1>"; ?></h1>
    <img src="adm.png" style="width:100%;height:500px;border-radius:20px;box-shadow:box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);">
    </div>
    <br>
    <br>
    <div class="manage">
        <a href="manageuser.php"><button type="submit" class="se">Manage Users</button></a>
        <a href="managehotel.php"><button type="submit" class="se">Manage Hotels</button></a>
        <a href="managebooking.php"><button type="submit" class="se">Manage Bookings</button></a>
        <a href="managereview.php"><button type="submit" class="se">Manage Review</button></a>
    </div>
    <br>
    <br>
    <div class="content">
    <h1>Report:</h1>
    <div class="card">Total Users: <strong class="c" id="userCount"><?php echo $user_count; ?></strong></div>
    <div class="card">Total Hotels: <strong class="c" id="hotelCount"><?php echo $hotel_count; ?></strong></div>
    <div class="card">Total Bookings: <strong class="c" id="bookingCount"><?php echo $booking_count; ?></strong></div>
    <div class="card">Total Reviews: <strong class="c" id="reviewCount"><?php echo $review_count; ?></strong></div>
    </div>
    </main>
    <?php include 'footer.php'; ?>
    <script>
function animateCount(id, endValue, duration = 1000) {
    let start = 0;
    const increment = endValue / (duration / 10);
    const element = document.getElementById(id);

    const counter = setInterval(() => {
        start += increment;
        if (start >= endValue) {
            element.textContent = endValue;
            clearInterval(counter);
        } else {
            element.textContent = Math.floor(start);
        }
    }, 10);
}

window.onload = function () {
    animateCount("userCount", parseInt(document.getElementById("userCount").textContent));
    animateCount("hotelCount", parseInt(document.getElementById("hotelCount").textContent));
    animateCount("bookingCount", parseInt(document.getElementById("bookingCount").textContent));
    animateCount("reviewCount", parseInt(document.getElementById("reviewCount").textContent));
};
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
</body>
</html>
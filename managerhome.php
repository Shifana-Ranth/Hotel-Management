<?php
    session_start();
    include("databasee.php");
    if (!isset($_SESSION['userid'])) {
        header("Location: login.php");
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
    if($_SESSION["loggedin"]==false || $_SESSION["roles"]!=='manager'){
        header("Location: index.php");
        exit();
    }
    
    $username = $_SESSION['username'];
    $hotel_id = null;
    $query = "SELECT hotel_id FROM users WHERE uname = '$username'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $hotel_id = $row['hotel_id'];
        $_SESSION['hotel_id']=$hotel_id ;
    }
    $hotel_name = '';
    $hotel_image = '';

    if ($hotel_id) {
        $query = "SELECT hotel_name, himage_url FROM hotels WHERE hotel_id = $hotel_id";
        $result = mysqli_query($conn, $query);
        if ($row = mysqli_fetch_assoc($result)) {
            $hotel_name = $row['hotel_name'];
            $hotel_image = $row['himage_url']; 
        }
    }
    
    $total_bookings = 0;
    $query = "SELECT COUNT(*) as total FROM bookings WHERE hotel_id = $hotel_id";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $total_bookings = $row['total'];
    }

    $total_rooms = 0;
    $query = "SELECT COUNT(*) as total FROM rooms WHERE hotel_id = $hotel_id";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $total_rooms = $row['total'];
    }

    $today = date("Y-m-d");
    $booked_rooms_today = 0;
    $query = "
        SELECT COUNT(*) AS booked
        FROM bookings b
        WHERE b.hotel_id = $hotel_id
        AND b.checkin <= '$today'
        AND b.checkout >= '$today'
    ";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $booked_rooms_today = $row['booked'] ?? 0;
    }

    $available_rooms = $total_rooms - $booked_rooms_today;
    if ($available_rooms < 0) $available_rooms = 0;

    $total_revenue = 0;
    $query = "
        SELECT SUM(amount) as revenue 
        FROM bookings 
        WHERE hotel_id = $hotel_id
    ";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $total_revenue = $row['revenue'] ?? 0;
    }
    $_SESSION["last_activity"]=time();
    mysqli_close($conn);
?>

<!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - VoyageVista</title>
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
            background-color: #29323c;
            width:15%;
            height:10%;
            color: white;
            padding: 11px;
            margin-left: 120px;
            border-radius: 8px;
            cursor: pointer;
            border: none;
            font-size:1.5rem;
        }
        .se:hover {
            background-color: darkblue;
        }
        .c{
            background-color:transparent;
            color:white;
        }
    </style>
</head>
<body>
    <?php include 'headermanager.php'; ?>
    <main  style="margin:5rem;">
    <h1 id="greeting"></h1>
    <br>
    <?php //echo "<h1>Welcome Manager:".($_SESSION['firstname'] ? $_SESSION['firstname'] : $_SESSION['username'])."</h1>"; ?></h1>
    <h2><?php echo '<h1 style="color:purple;">'.$hotel_name.'</h1>'; ?></h2>
    <img src="./hphotos/<?php echo $hotel_image; ?>" style="width:100%;height:500px;border-radius: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);object-fit: cover;">
    </div>
    <br>
    <br>
    <div class="manage">
        <a href="viewhotel.php"><button type="submit" class="se">View Hotel</button></a>
        <a href="viewuser.php"><button type="submit" class="se">View Users</button></a>
        <a href="viewbookingman.php"><button type="submit" class="se">View Bookings</button></a>
        <a href="viewroom.php"><button type="submit" class="se">View Rooms</button></a>
    </div>
    <br>
    <br>
    <div class="content">
    <h1>Report:</h1>
    <div class="card">Total Bookings: <strong class="c" id="userCount"><?php echo $total_bookings; ?></strong></div>
    <div class="card">Total Rooms: <strong class="c" id="hotelCount"><?php echo $total_rooms;  ?></strong></div>
    <div class="card">Available Rooms:  <strong class="c" id="bookingCount"><?php echo $available_rooms; ?></strong></div>
    <div class="card">Total Revenue: <strong class="c" id="reviewCount"><?php echo $total_revenue; ?></strong></div>
    </div>
    </main>
    <?php include 'footer.php'; ?>
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
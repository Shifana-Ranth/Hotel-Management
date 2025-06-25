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
    $hotel_id = $_SESSION['hotel_id'];
    if (isset($_GET['id'])) {
      $user_id = $_GET['id'];
    }

    $query = "SELECT b.*, h.hotel_name, s.stname, d.dt_name 
              FROM bookings b
              JOIN hotels h ON b.hotel_id = h.hotel_id
              JOIN states s ON h.st_id = s.st_id
              JOIN district d ON h.dt_id = d.dt_id
              WHERE b.uidd = '$user_id' AND b.hotel_id = '$hotel_id'
              ORDER BY b.booked_at DESC";

    $result = mysqli_query($conn, $query);

    if (!$result || mysqli_num_rows($result) == 0) {
        echo "<div style='text-align:center; font-size:18px; margin-top:50px;'>No bookings found for this user in your hotel.</div>";
        exit();
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View User Booking - Manager</title>
  <style>
    *{
        font-family: 'Times New Roman', Times, serif;
    }
    body{
        margin-top:4rem;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f6f8;
    }
    .view-booking-container {
      max-width: 800px;
      margin: 50px auto;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      padding: 30px 40px;
    }

    .booking-header {
      text-align: center;
      font-size: 26px;
      color: #2d3436;
      font-weight: bold;
      margin-bottom: 25px;
    }

    .booking-info {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 30px;
    }

    .info-item {
      background-color: #f9f9f9;
      padding: 15px 20px;
      border-radius: 8px;
      border-left: 4px solid #4b0082;
    }

    .info-label {
      font-weight: bold;
      color: #555;
      background-color:transparent;
      margin-bottom: 5px;
      font-size: 14px;
    }

    .info-value {
        background-color:transparent;
        font-size: 15px;
        color: #333;
    }

    .back-button {
      display: block;
      text-align: center;
      margin-top: 20px;
    }

    .back-button a {
      text-decoration: none;
      background-color: #246A73;
      color: white;
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: bold;
    }

    .back-button a:hover {
      background-color: #1e5660;
    }
    .vu-btn {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 1rem;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
        }

        .vu-btn:hover {
            background-color: #1a252f;
        }
  </style>
</head>
<body>
<?php include 'headermanager.php'; ?>
<div class="view-booking-container">
<a href="viewuser.php" class="vu-btn">← Back to Dashboard</a>
    <?php while($booking = mysqli_fetch_assoc($result)): ?>
    <?php echo '<div class="booking-header">Booking Details</div>';?>
    <?php $roomDisplay = $booking['no_of_rooms'] . "x " . $booking['roomtype']; ?>
    <div class="booking-info">
        <div class="info-item">
            <div class="info-label">Booking Id:</div>
            <div class="info-value"><?php echo $booking['booking_id']; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">User Name</div>
            <div class="info-value"><?php echo $booking['namee']; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Hotel Name</div>
            <div class="info-value"><?php echo $booking['hotel_name']; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">State</div>
            <div class="info-value"><?php echo $booking['stname']; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">District</div>
            <div class="info-value"><?php echo $booking['dt_name']; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Phone No:</div>
            <div class="info-value"><?php echo $booking['phno']; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Check-in</div>
            <div class="info-value"><?php echo $booking['checkin']; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Check-out</div>
            <div class="info-value"><?php echo $booking['checkout']; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Amount Paid</div>
            <div class="info-value">₹ <?php echo $booking['amount']; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Room Type(s)</div>
            <div class="info-value"><?php echo $roomDisplay; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Booking Date:</div>
            <div class="info-value"><?php echo $booking['booked_at']; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Booking Status</div>
            <div class="info-value"><?php echo $booking['status']; ?></div>
        </div>
    </div>
    <hr style="margin:30px 0;">
<?php endwhile; ?>    
</div>
<?php include 'footer.php'; ?>
</body>
</html>
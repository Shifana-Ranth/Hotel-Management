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
    if($_SESSION["loggedin"]==false || $_SESSION["roles"]!=='Admin'){
      header("Location: index.php");
      exit();
    }

    $book_id = $_GET['id'] ;

    if (!$book_id) {
        echo "Invalid request.";
        exit();
    }
    $query = "SELECT b.*, h.hotel_name, 
                 s.stname, d.dt_name
          FROM bookings b
          JOIN hotels h ON b.hotel_id = h.hotel_id
          JOIN states s ON h.st_id = s.st_id
          JOIN district d ON h.dt_id = d.dt_id
          WHERE b.booking_id = '$book_id'";
    $result = mysqli_query($conn, $query);

    if (!$result || mysqli_num_rows($result) == 0) {
        echo "Booking not found.";
        exit();
    }
    $booking = mysqli_fetch_assoc($result);
    $roomDisplay = $booking['no_of_rooms'] . "x " . $booking['roomtype'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Booking Details - Admin Panel</title>
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
      border-left: 4px solid #246A73;
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
  </style>
</head>
<body>
<?php include 'headeradmin.php'; ?>
<div class="view-booking-container">
    <div class="booking-header">Booking Details</div>

    <div class="booking-info">
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
        <div class="info-label">Phone No:</div>
        <div class="info-value"><?php echo $booking['phno']; ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Booking Date:</div>
        <div class="info-value"><?php echo $booking['booked_at']; ?></div>
      </div>
    </div>

    <div class="back-button">
      <a href="managebooking.php">Back to Manage Bookings</a>
    </div>
  </div>
  <?php include 'footer.php'; ?>
</body>
</html>
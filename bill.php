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
    if (!isset($_SESSION['booking_id'])) {
        echo "Booking ID not set!";
        exit();
    }

    $booking_id = $_SESSION['booking_id'];
    $sql = "SELECT b.*, u.uname AS user_name, h.hotel_name, h.address
            FROM bookings b
            JOIN users u ON b.uidd = u.uid
            JOIN hotels h ON b.hotel_id = h.hotel_id
            WHERE b.booking_id = '$booking_id'";
    $result = mysqli_query($conn, $sql);

    if (!$result || mysqli_num_rows($result) == 0) {
        echo "Booking not found!";
        exit();
    }

    $row = mysqli_fetch_assoc($result);
    $sql = "SELECT * FROM bills WHERE booking_id = '$booking_id'";
    $b = mysqli_query($conn, $sql);

    if (!$b || mysqli_num_rows($b) == 0) {
        echo "Booking not found!";
        exit();
    }

    $bill=mysqli_fetch_assoc($b);
    $room_price     =$bill['room_price'];
    $gst            = $bill['gst'];
    $service_charge = $bill['service_fee'];
    $tourism_fee    = 200;
    $total_amount   = $bill['total_amount'];
    $_SESSION["last_activity"] = time();
    mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Hotel Booking Invoice</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 20px;
    }
    .invoice-box {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 30px;
      border: 1px solid #eee;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
      border-radius: 10px;
    }
    .header {
        display:flex;
        justify-content:center;
        margin-bottom: 10px;
    }
    .header h2 {
      margin: 0;
      color: #2c3e50;
      line-height:50px;
    }
    .details, .charges, .footerr {
      width: 100%;
      margin-bottom: 20px;
    }
    .details td, .charges td {
      padding: 8px 0;
    }
    .details td.label {
      font-weight: bold;
      color: #555;
      width: 200px;
    }
    .charges td {
      border-bottom: 1px solid #eee;
    }
    .total-row {
      font-weight: bold;
      color: #2c3e50;
    }
    .footer {
      text-align: center;
      font-size: 14px;
      color: #888;
    }
    .download-btn {
      display: block;
      width: fit-content;
      margin: 20px auto 0;
      padding: 10px 25px;
      background-color: #2ecc71;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      transition: 0.3s;
    }
    .download-btn:hover {
      background-color: #27ae60;
    }
    #loader {
      border: 6px solid #f3f3f3;
      border-top: 6px solid #3498db;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin:auto;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
  <div class="invoice-box">
    <div class="header">
        <img src="logo.jpeg" style="height:50px;widht:50px;border-radius:50%;">
      <h2>VoyageVista</h2>
    </div>
    <h2 style="margin: 0;
      color: #2c3e50;
      line-height:50px;text-align:center;">Hotel Booking Invoice</h2>
      <br>
    <table class="details">
      <tr>
        <td class="label">Invoice No:</td>
        <td>#VV<?= $booking_id ?></td>
        <td class="label">Date:</td>
        <td><?= date("F j, Y") ?></td>
      </tr>
      <tr>
        <td class="label">Booked By:</td>
        <td><?= $row['user_name'] ?></td>
        <td class="label">Name:</td>
        <td colspan="3"><?= $row['namee']; ?></td>
      </tr>
        <td class="label">Check-in:</td>
        <td><?= $row['checkin'] ?></td>
        <td class="label">Check-out:</td>
        <td><?= $row['checkout'] ?></td>
      </tr>
      <tr>
        <td class="label">Hotel Name:</td>
        <td><?= $row['hotel_name'] ?></td>
        <td class="label">Room ID(s):</td>
        <td colspan="3"><?= $row['rid'] ?></td>
      </tr>
      <tr>
        <td class="label">Hotel Address:</td>
        <td colspan="3"><?= $row['address'] ?></td>
      </tr>
    </table>

    <table class="charges">
      <tr>
        <td>Room Charges</td>
        <td style="text-align: right;">₹<?= $room_price ?></td>
      </tr>
      <tr>
        <td>Service Fee</td>
        <td style="text-align: right;">₹<?= $service_charge ?></td>
      </tr>
      <tr>
        <td>Tourism Fee</td>
        <td style="text-align: right;">₹<?= $tourism_fee ?></td>
      </tr>
      <tr>
        <td>GST (12%)</td>
        <td style="text-align: right;">₹<?= $gst ?></td>
      </tr>
      <tr class="total-row">
        <td>Total Amount</td>
        <td style="text-align: right;">₹<?= $total_amount ?></td>
      </tr>
    </table>

    <div class="footerr">
    <p style="text-align:center;">Thank you for choosing us!&hearts;</p>
    </div>
    <div id="loader" style="display:none;"></div>
    <a id="down" href="generate_pdf.php?booking_id=<?= $booking_id ?>" class="download-btn">Download PDF</a>
    <a href="index.php" class="download-btn" style="background-color:darkblue;">Go to Home</a>
  </div>
  <script>
    const b=document.getElementById("down");
    console.log(b);
    b.addEventListener('click',() =>{
      document.getElementById('loader').style.display = 'block';

      setTimeout(() => {
        document.getElementById('loader').style.display = 'none';
      }, 500);
      
    });
  </script>
</body>
</html>
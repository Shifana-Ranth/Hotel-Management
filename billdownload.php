<?php
include("databasee.php");

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

$q="SELECT * FROM bills WHERE booking_id = '$booking_id'";
$b = mysqli_query($conn, $q);
$bill=mysqli_fetch_assoc($b);
$room_price     =$bill['room_price'];
$gst            = $bill['gst'];
$service_charge = $bill['service_fee'];
$tourism_fee    = 200;
$total_amount   = $bill['total_amount'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Hotel Booking Invoice</title>
  <style>
    body {
        font-family: 'DejaVu Sans', sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
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
        /* background-color:pink; */
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        margin-bottom: 10px;
    }
    .header h2 {
      margin: 0;
      color: #2c3e50;
      line-height:50px;
    }
    .details, .charges, .footer {
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
      transition: background 0.3s;
    }
    .download-btn:hover {
      background-color: #27ae60;
    }
    .invoice-container {
        width: 100%;
        max-width: 700px;
        margin: 0 auto;
        padding: 0px;
        font-size: 14px;
    }
  </style>
</head>
<body>

<div class="invoice-container">
  <div class="invoice-box">
    <div class="header">
        <?php
            $logo = base64_encode(file_get_contents('logo.jpeg'));
            $logo_src = 'data:image/jpeg;base64,' . $logo;
        ?>
        <img src="<?= $logo_src ?>" style="text-align:center;height:50px;width:50px;border-radius:50%;">
        <h5 style="margin:-5px;">VoyageVista</h5>
        <h2 style="margin: 0;
      color: #2c3e50;
      line-height:50px;text-align:center;">Hotel Booking Invoice</h2>
    </div>
      <br>
      <br>
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
      <tr>
    </table>
    <br>
    <br>
    <br>
    <br>
    <table class="charges">
      <tr>
        <td>Room Charges</td>
        <td style="text-align: right;">₹<?= $room_price ?></td>
      </tr>
      <tr>
        <td>Service Fee</td>
        <td style="text-align: right;">&#8377;<?= $service_charge ?></td>
      </tr>
      <tr>
        <td>Tourism Fee</td>
        <td style="text-align: right;">&#8377;<?= $tourism_fee ?></td>
      </tr>
      <tr>
        <td>GST (12%)</td>
        <td style="text-align: right;">&#8377;<?= $gst ?></td>
      </tr>
      <tr class="total-row">
        <td>Total Amount</td>
        <td style="text-align: right;">&#8377;<?= $total_amount ?></td>
      </tr>
    </table>
    <br>
    <br>
    <br>
    <br>
    <br>
    <div class="footer">
    <p style="text-align:center;">Thank you for choosing us!&hearts;</p>
    </div>
  </div>
</div>
</body>
</html>
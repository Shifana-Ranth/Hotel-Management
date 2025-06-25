<?php
  session_start();
  include("databasee.php");
  if (!isset($_SESSION['userid'])) {
      header("Location: login.php");
      exit();
  }
  $inactive=300;
  if (isset($_SESSION["last_activity"])) {
      $session_life = time() - $_SESSION["last_activity"];
      
      if ($session_life > $inactive) {
          session_unset();
          session_destroy();
          header("Location: index.php");
      }
  }
  $hotel_id =$_SESSION['hotel_id'];
  $room_id = $_GET['id'];

  $sql = "SELECT r.*, h.hotel_name 
          FROM rooms r 
          JOIN hotels h ON r.hotel_id = h.hotel_id 
          WHERE r.rid = $room_id";

  $result = mysqli_query($conn, $sql);
  $room = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Room Details - Manager</title>
  <style>
    *{
            font-family: 'Times New Roman', Times, serif;
        }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa;
      margin: 0;
      padding: 0;
    }

    .room-detail-wrapper {
      max-width: 800px;
      margin: 50px auto;
      background-color: white;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
    }

    .room-detail-wrapper h1 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 30px;
    }

    .room-info {
      display: flex;
      flex-direction: column;
      gap: 20px;
      font-size: 1.1rem;
    }

    .room-info .info-item {
      display: flex;
      justify-content: space-between;
      padding: 12px 20px;
      background-color: #f2f2f2;
      border-radius: 8px;
    }

    .info-item span.label {
      font-weight: bold;
      color: #333;
      background-color:transparent;
    }

    .info-item span.value {
      color: #555;
      background-color:transparent;
    }

    .back-button {
      margin-top: 30px;
      text-align: center;
    }

    .back-button a {
      background-color: #246A73;
      color: white;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 5px;
    }

    .back-button a:hover {
      background-color: darkblue;
    }
  </style>
</head>
<body>
<?php include 'headermanager.php';?>
<div class="room-detail-wrapper">
    <h1>Room Details</h1>

    <?php if ($room) { ?>
    <div class="room-info">
      <div class="info-item">
        <span class="label">Room Image:</span>
        <img src="<?php echo $room['rimage']; ?>" alt="Room Image" style="height:400px;width:500px;">
      </div>
      <div class="info-item">
        <span class="label">Hotel Name:</span>
        <span class="value"><?php echo $room['hotel_name']; ?></span>
      </div>
      <div class="info-item">
        <span class="label">Room ID:</span>
        <span class="value"><?php echo $room['rid']; ?></span>
      </div>
      <div class="info-item">
        <span class="label">Room Type:</span>
        <span class="value"><?php echo $room['r_type']; ?></span>
      </div>
      <div class="info-item">
        <span class="label">Quantity:</span>
        <span class="value"><?php echo $room['quantity']; ?></span>
      </div>
      <div class="info-item">
        <span class="label">Rating:</span>
        <span class="value"><?php echo $room['rating']; ?></span>
      </div>
      <div class="info-item">
        <span class="label">Price per Night:</span>
        <span class="value">₹<?php echo $room['price_per_night']; ?></span>
      </div>
      <div class="info-item">
        <span class="label">Availability:</span>
        <span class="value"><?php echo $room['availability']; ?></span>
      </div>
      <div class="info-item">
        <span class="label">Bed-Type:</span>
        <span class="value"><?php echo $room['bed_type']; ?></span>
      </div>
    </div>
    <?php } else { ?>
      <p style="text-align:center;">Room not found.</p>
    <?php } ?>

    <div class="back-button">
      <a href="viewroom.php">Back to Room List</a>
      <a href="modifyroom.php?room_id=<?php echo $room['rid']; ?>" style="background-color:red;">Modify</a>
    </div>
  </div>
  <?php include 'footer.php';?>
</body>
</html>
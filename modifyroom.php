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
  $hotel_id =$_SESSION['hotel_id'];
  $room_id = $_GET['room_id'];

  $sql = "SELECT * FROM rooms WHERE rid = '$room_id'";

  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Modify Room - Manager</title>
  <style>
    .modify-room-wrapper {
      max-width: 700px;
      margin: 50px auto;
      background-color: #ffffff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .modify-room-wrapper h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 30px;
    }

    .modify-room-form .modify-room-group {
      margin-bottom: 20px;
    }

    .modify-room-group label {
      display: block;
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
    }

    .modify-room-group input,
    .modify-room-group select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 1rem;
      background-color: #fdfdfd;
    }

    .modify-room-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 30px;
    }

    .modify-room-buttons button {
      padding: 10px 20px;
      font-size: 1rem;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .modify-room-submit {
      background-color: #246A73;
      color: #fff;
    }

    .modify-room-cancel {
      background-color: #ccc;
      color: #333;
    }

    .modify-room-submit:hover {
      background-color: #1b4f59;
    }

    .modify-room-cancel:hover {
      background-color: #999;
    }
  </style>
</head>
<body>
<?php include 'headermanager.php' ?>
<div class="modify-room-wrapper">
  <h2>Modify Room Details</h2>
  <form class="modify-room-form" method="POST" action="updateroom.php" enctype="multipart/form-data">
    <input type="hidden" name="room_id" value="<?php echo $row['rid']; ?>">

    <div class="modify-room-group">
      <label>Room Id</label>
      <input type="text" name="rid" value="<?php echo $row['rid']; ?>" readonly>
    </div>

    <div class="modify-room-group"> 
    <label>Room Type</label> 
    <select name="r_type">
        <option value="Deluxe" <?php if ($row['r_type'] == 'Deluxe') echo 'selected'; ?>>Deluxe</option>
        <option value="Standard" <?php if ($row['r_type'] == 'Standard') echo 'selected'; ?>>Standard</option>
    </select>
</div>

    <div class="modify-room-group">
      <label>Price per Night (₹)</label>
      <input type="number" name="price_per_night" value="<?php echo $row['price_per_night']; ?>">
    </div>

    <div class="modify-room-group">
      <label>Quantity</label>
      <input type="number" name="quantity" min="1" value="<?php echo $row['quantity']; ?>">
    </div>

    <div class="modify-room-group">
      <label>Availability</label>
      <select name="availability">
        <option value="Available" <?php if ($row['availability'] == 'Available') echo 'selected'; ?>>Available</option>
        <option value="Not Available" <?php if ($row['availability'] == 'Not Available') echo 'selected'; ?>>Not Available</option>
      </select>
    </div>

    <div class="modify-room-group"> 
        <label>Bed Type</label> 
        <select name="bed_type">
            <option value="King" <?php if ($row['bed_type'] == 'King') echo 'selected'; ?>>King</option>
            <option value="Queen" <?php if ($row['bed_type'] == 'Queen') echo 'selected'; ?>>Queen</option>
        </select>
    </div>
    
    <div class="modify-room-group">
      <label>Rating</label>
      <input type="number" step="0.1" name="rating" min="1" max="5" value="<?php echo $row['rating']; ?>">
    </div>

    <div class="modify-room-group">
      <label>Room Image</label>
      <input type="file" name="room_image">
      <?php if (!empty($row['image'])): ?>
        <p>Current image: <br><img src="roomphotos/<?php echo $row['image']; ?>" width="200"></p>
      <?php endif; ?>
    </div>

    <div class="modify-room-buttons">
      <button type="submit" class="modify-room-submit">Save Changes</button>
      <button type="button" onclick="window.location.href='viewroom.php'" class="modify-room-cancel">Cancel</button>
    </div>
  </form>
</div>
<?php include 'footer.php';?>
</body>
</html>
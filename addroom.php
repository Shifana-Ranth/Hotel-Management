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
  $query="SELECT * FROM hotels WHERE hotel_id=$hotel_id ";
  $result = mysqli_query($conn, $query);
  $row=mysqli_fetch_assoc($result);
  $hotelName=$row['hotel_name'];
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $r_type = $_POST['r_type'];
      $price = $_POST['price_per_night'];
      $availability = $_POST['availability'];
      $bed_type = $_POST['bed_type'];
      $rating = $_POST['rating'];
      $image_url = ""; 

        if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
            $target_dir = "roomphotos/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);  
            }
            $image_name = basename($_FILES["room_image"]["name"]);
            $target_file = $target_dir .time() . "_" . $image_name;

            if (move_uploaded_file($_FILES["room_image"]["tmp_name"], $target_file)) {
                $image_url = $target_file; 
            } else {
                $showerror = "Failed to upload hotel image.";
            }
        }
      $sql = "INSERT INTO rooms (hotel_id, r_type, price_per_night, availability, bed_type,rimage , rating) 
      VALUES ('$hotel_id', '$r_type', '$price', '$availability', '$bed_type','$image_url', '$rating')";

      $result = mysqli_query($conn, $sql);

      if ($result) {
        echo "<script>alert('Room added successfully!');window.location.href='viewroom.php'</script>";
      } else {
        echo "Error: " . mysqli_error($conn);
      }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Room - Manager</title>
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

<div class="modify-room-wrapper">
  <h2>Add Room Details</h2>
  <form class="modify-room-form" method="POST" action="addroom.php" enctype="multipart/form-data">
  <h1 style="text-align:center;"><?php echo $hotelName; ?></h1>
    <div class="modify-room-group"> 
    <label>Room Type<span style="background-color:transparent;color:red;">*</span></label> 
    <select name="r_type" required>
        <option value="" >Room Type</option>
        <option value="Deluxe" >Deluxe</option>
        <option value="Standard" >Standard</option>
    </select>
    </div>

    <div class="modify-room-group">
      <label>Price per Night (₹)<span style="background-color:transparent;color:red;">*</span></label>
      <input type="number" name="price_per_night" required>
    </div>

    <div class="modify-room-group">
      <label>Quantity<span style="background-color:transparent;color:red;">*</span></label>
      <input type="number" name="qunatity" min="1" required>
    </div>

    <div class="modify-room-group">
      <label>Availability<span style="background-color:transparent;color:red;">*</span></label>
      <select name="availability" required>
        <option value="" >Availability</option>
        <option value="Available" >Available</option>
        <option value="Not Available" >Not Available</option>
      </select>
    </div>

    <div class="modify-room-group"> 
        <label>Bed Type<span style="background-color:transparent;color:red;">*</span></label> 
        <select name="bed_type" required>
            <option value="" >Bed Type</option>
            <option value="King" >King</option>
            <option value="Queen" >Queen</option>
        </select>
    </div>
    
    <div class="modify-room-group">
      <label>Rating<span style="background-color:transparent;color:red;">*</span></label>
      <input type="number" step="0.1" name="rating" min="1" max="5" required>
    </div>

    <div class="modify-room-group">
      <label>Room Image<span style="background-color:transparent;color:red;">*</span></label>
      <input type="file" name="room_image" required>
    </div>

    <div class="modify-room-buttons">
      <button type="submit" class="modify-room-submit">Add Room</button>
      <button type="button" onclick="window.location.href='viewroom.php'" class="modify-room-cancel">Cancel</button>
    </div>
  </form>
</div>
<?php include 'footer.php';?>
</body>
</html>
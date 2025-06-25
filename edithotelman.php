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
    if (!$hotel_id) {
        echo "Invalid request.";
        exit();
    }
    $success = "";
    $showerror = false;

    $sql = "SELECT * FROM hotels WHERE hotel_id = $hotel_id";
    $result = mysqli_query($conn, $sql);
    $hotel = mysqli_fetch_assoc($result);
    if (!$hotel) {
        $showerror= "Hotel not found.";
        exit();
    }
    $exist_image=$hotel['himage_url'];
    $sql_state = "SELECT stname FROM states WHERE st_id = " . $hotel['st_id'];
    $result_state = mysqli_query($conn, $sql_state);
    $state_row = mysqli_fetch_assoc($result_state);
    $state_name = $state_row['stname'];

    $sql_district = "SELECT dt_name FROM district WHERE dt_id = " . $hotel['dt_id'];
    $result_district = mysqli_query($conn, $sql_district);
    $district_row = mysqli_fetch_assoc($result_district);
    $district_name = $district_row['dt_name'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $hotel_name = $_POST['hotel_name'];
        $address = $_POST['hotel_address'];
        $description = $_POST['hotel_descrip'];
        $amenities = $_POST['hotel_amenities'];
        $email = $_POST['hotel_email'];
        $phone = $_POST['hotel_phno'];
        $price = $_POST['hotel_price'];
        $rating = $_POST['hotel_rating'];
        $availability = $_POST['availability'];
        $state_name = trim($_POST['hotel_state']);
        $district_name = trim($_POST['hotel_district']);

        $state_name = ucwords(strtolower($state_name));  
        $district_name = ucwords(strtolower($district_name));
        $image_url = ""; 

        if (isset($_FILES['hotel_image']) && $_FILES['hotel_image']['error'] == 0) {
            $target_dir = "hphotos/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);  
            }
            $image_name = basename($_FILES["hotel_image"]["name"]);
            $target_file = $target_dir .time() . "_" . $image_name;

            if (move_uploaded_file($_FILES["hotel_image"]["tmp_name"], $target_file)) {
                $target_file =time() . "_" . $image_name;
                $image_url = $target_file; 
            } else {
                $showerror = "Failed to upload hotel image.";
            }
        }
        else if($image_url == ""){
            $image_url = $exist_image;
        }
        if(strlen($phone) !==10)
        {
            $showerror="Ph no should be 10 digits";
        }
        elseif (!is_numeric($_POST['hotel_rating']) || $_POST['hotel_rating'] < 1 || $_POST['hotel_rating'] > 5) {
            $showerror = "Rating must be a number between 1 and 5.";
        } 
        else {
            $state_query = "SELECT st_id FROM states WHERE stname = '$state_name'";
            $state_result = mysqli_query($conn, $state_query);
        
            if (mysqli_num_rows($state_result) > 0) {
                $state = mysqli_fetch_assoc($state_result);
                $st_id = $state['st_id'];
            } else {
                $insert_state = "INSERT INTO states (stname) VALUES ('$state_name')";
                mysqli_query($conn, $insert_state);
                $st_id = mysqli_insert_id($conn);
            }
        
            $district_query = "SELECT dt_id FROM district WHERE dt_name = '$district_name' AND st_id = '$st_id'";
            $district_result = mysqli_query($conn, $district_query);
        
            if (mysqli_num_rows($district_result) > 0) {
                $district = mysqli_fetch_assoc($district_result);
                $dt_id = $district['dt_id'];
            } else {
                $insert_district = "INSERT INTO district (dt_name, st_id) VALUES ('$district_name', '$st_id')";
                mysqli_query($conn, $insert_district);
                $dt_id = mysqli_insert_id($conn);
            }
        
            $update_hotel = "UPDATE hotels SET
                                hotel_name = '$hotel_name',
                                st_id = '$st_id',
                                dt_id = '$dt_id',
                                address = '$address',
                                description = '$description',
                                amenities = '$amenities',
                                hemail = '$email',
                                hcontact_number = '$phone',
                                hprice = '$price',
                                rating = '$rating',
                                himage_url= '$image_url' ,
                                availability = '$availability'
                            WHERE hotel_id = '$hotel_id'";
        
            if (mysqli_query($conn, $update_hotel)) {
                echo "<script>alert('Hotel updated successfully'); window.location.href = 'viewhotel.php';</script>";
            } else {
                $showerror= "Error updating hotel: " . mysqli_error($conn);
            }
        }
        
    }
    mysqli_close($conn);
    $_SESSION["last_activity"] = time();
?>

<!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hotel-Manager</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
        *{
            font-family: 'Times New Roman', Times, serif;
        }
        body{
            margin-top:4rem;
        }
        .edit-hotel-container {
            width: 50%;
            margin: 3rem auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .edit-hotel-title {
            text-align: center;
            font-size: 22px;
            font-weight:900;
            color: #333;
            margin-bottom: 20px;
        }
        .edit-hotel-form label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        .edit-hotel-form input, .edit-hotel-form select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .edit-hotel-form button {
            margin-top: 15px;
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 4px;
            background-color: #246A73;
            color: white;
            cursor: pointer;
        }
        .edit-hotel-form button:hover {
            background-color: #1a4f52;
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
    <?php 
        if($showerror){
                echo '
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="color:black;font-weight:700;padding-left:70px;">
                <strong style="background-color:transparent;"><i style="color:red;background-color:transparent;" class="fa-solid fa-circle-exclamation"></i></strong> '.$showerror.'
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                ';
        }
    ?>
    <div class="edit-hotel-container">
        <h2 class="edit-hotel-title">Edit Hotel Details</h2>
        <form class="edit-hotel-form" method="POST" action="edithotelman.php?" >

            <label for="hotel_name">Hotel Name</label>
            <input type="text" id="hotel_name" name="hotel_name" required value="<?= $hotel['hotel_name'] ?>">

            <label for="hotel_state">State</label>
            <input type="text" id="hotel_state" name="hotel_state" required value="<?= $state_name ?>">

            <label for="hotel_district">District</label>
            <input type="text" id="hotel_district" name="hotel_district" required value="<?= $district_name ?>">

            <label for="hotel_address">Address</label>
            <input type="text" id="hotel_address" name="hotel_address" required value="<?= $hotel['address'] ?>">

            <label for="hotel_descrip">Description</label>
            <input type="text" id="hotel_descrip" name="hotel_descrip" required value="<?= $hotel['description'] ?>">

            <label for="hotel_amenities">Amenities</label>
            <input type="text" id="hotel_amenities" name="hotel_amenities" required value="<?= $hotel['amenities'] ?>">

            <label for="hotel_email">Email</label>
            <input type="email" id="hotel_email" name="hotel_email" required value="<?= $hotel['hemail'] ?>">

            <label for="hotel_phno">Phone No</label>
            <input type="number" id="hotel_phno" name="hotel_phno" required value="<?= $hotel['hcontact_number'] ?>">

            <label for="hotel_price">Price</label>
            <input type="number" id="hotel_price" name="hotel_price" required value="<?= $hotel['hprice'] ?>">

            <label for="hotel_rating">Rating</label>
            <input type="text" id="hotel_rating" name="hotel_rating" required value="<?= $hotel['rating'] ?>">

            <label for="availability">Availability</label>
            <select id="availability" name="availability" required>
                <option value="Available" <?= ($hotel['availability'] === 'Available') ? 'selected' : '' ?>>Available</option>
                <option value="Not Available" <?= ($hotel['availability'] === 'Not Available') ? 'selected' : '' ?>>Not Available</option>
            </select>

            <label for="hotel_image">Hotel Image</label>
            <input type="file" id="hotel_image" name="hotel_image">

            <button type="submit">Update Hotel</button>
            <a href="viewhotel.php" class="vu-btn">← Back to hotel</a>
        </form>
    </div>
    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
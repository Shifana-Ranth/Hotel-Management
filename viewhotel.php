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
    $query = "
        SELECT h.hotel_name, h.address, h.himage_url, h.hemail, h.hcontact_number, h.description, 
            s.stname, d.dt_name
        FROM hotels h
        JOIN states s ON h.st_id = s.st_id
        JOIN district d ON h.dt_id = d.dt_id
        WHERE h.hotel_id = $hotel_id
    ";

    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $hotel_name = $row['hotel_name'];
        $hotel_address = $row['address'];
        $hotel_image = $row['himage_url'];
        $email = $row['hemail'];
        $phone = $row['hcontact_number'];
        $description = $row['description'];
        $state = $row['stname'];
        $district = $row['dt_name'];
    } else {
        echo "Hotel not found!";
        exit();
    }
    $_SESSION["last_activity"]=time();
    mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Hotel - Manager</title>
    <style>
        .view-hotel-container {
            max-width: 1000px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .view-hotel-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }

        .view-hotel-img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }

        .view-hotel-details {
            margin-top: 25px;
        }

        .view-hotel-info-row {
            font-size: 1.2rem;
            margin: 12px 0;
            color: #444;
        }

        .view-hotel-edit-btn {
            background-color: #246A73;
            color: white;
            border: none;
            padding: 12px 26px;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 30px;
        }

        .view-hotel-edit-btn:hover {
            background-color: #1d5b65;
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
<div class="view-hotel-container">
    <h1 class="view-hotel-title"><?php echo htmlspecialchars($hotel_name); ?></h1>
    <img src="./hphotos/<?php echo htmlspecialchars($hotel_image); ?>" alt="Hotel Image" class="view-hotel-img">

    <div class="view-hotel-details">
        <div class="view-hotel-info-row"><strong>Name:</strong> <?php echo htmlspecialchars($hotel_name); ?></div>
        <div class="view-hotel-info-row"><strong>Address:</strong> <?php echo htmlspecialchars($hotel_address); ?></div>
        <div class="view-hotel-info-row"><strong>Location:</strong> <?php echo htmlspecialchars($district); ?>, <?php echo htmlspecialchars($state); ?></div>
        <div class="view-hotel-info-row"><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></div>
        <div class="view-hotel-info-row"><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></div>
        <div class="view-hotel-info-row"><strong>Description:</strong> <?php echo htmlspecialchars($description); ?></div>
    </div>

    <a href="managerhome.php" class="vu-btn">← Back to Dashboard</a>
    <a href="edithotelman.php"><button class="view-hotel-edit-btn">Edit Hotel</button></a>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
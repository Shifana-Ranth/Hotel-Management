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
    $userid = $_SESSION['userid']; 
    $sql = "SELECT * FROM users WHERE uid = '$userid' ";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $user = mysqli_fetch_assoc($result);
    } else {
        echo "Error fetching profile details!";
        exit();
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
        $hotelName = $_POST['hotelname'] ?? ''; 
        $dateFilter = $_POST['date_filter'] ?? ''; 
        $query = "SELECT b.*, h.hotel_name, s.stname, d.dt_name,h.himage_url 
        FROM bookings b 
        JOIN hotels h ON b.hotel_id = h.hotel_id 
        JOIN states s ON h.st_id = s.st_id 
        JOIN district d ON h.dt_id = d.dt_id 
        WHERE b.uidd= '$userid'";
        if ($hotelName) {
            $query .= " AND h.hotel_name LIKE '%$hotelName%'";
        }
        if ($dateFilter) {
            $currentDate = date('Y-m-d');
            switch ($dateFilter) {
                case 'last_6_months':
                    $query .= " AND b.checkin >= DATE_SUB('$currentDate', INTERVAL 6 MONTH)";
                    break;
                case 'last_1_year':
                    $query .= " AND b.checkin >= DATE_SUB('$currentDate', INTERVAL 1 YEAR)";
                    break;
                case 'last_3_months':
                    $query .= " AND b.checkin >= DATE_SUB('$currentDate', INTERVAL 3 MONTH)";
                    break;
                case 'before_1_year':
                    $query .= " AND b.checkin <= DATE_SUB('$currentDate', INTERVAL 1 YEAR)";
                    break;
            }
        }
        $result = mysqli_query($conn, $query);
    }
    $_SESSION["last_activity"] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VoyageVista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="stylemy.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<body>
    <?php include 'headeradmin.php'; ?>
    <main style="background-color: powderblue; position: relative; top: 15px;">
        <div class="content-container" style="background-color: powderblue;display:flex;justify-content:center;">
            <div class="profile-container">
                <div class="profile-title">Your Profile</div>
                <div class="profile-info">
                    <p><span class="profile-label">First Name:</span> <?php echo $user['firstname']; ?></p>
                    <p><span class="profile-label">Last Name:</span> <?php echo $user['lastname']; ?></p>
                    <p><span class="profile-label">Email:</span> <?php echo $user['email']; ?></p>
                    <p><span class="profile-label">Phone:</span> <?php echo $user['phno']; ?></p>
                    <p><span class="profile-label">Address:</span> <?php echo $user['addresss']; ?></p>
                    <p><span class="profile-label">City:</span> <?php echo $user['city']; ?></p>
                    <p><span class="profile-label">State:</span> <?php echo $user['states']; ?></p>
                    <p><span class="profile-label">Role:</span> <?php echo $user['roles']; ?></p>
                </div>
                <a href="editprofileadmin.php" class="profile-link">Edit Profile</a>
            </div>
        </div>
    </main>
    <?php include 'footer.php';?>
</body>
</html>
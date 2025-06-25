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
        $fromDate = $_POST['from_date'] ?? '';
        $toDate = $_POST['to_date'] ?? '';
    
        $query = "SELECT b.*, h.hotel_name, s.stname, d.dt_name,h.himage_url 
                  FROM bookings b 
                  JOIN hotels h ON b.hotel_id = h.hotel_id 
                  JOIN states s ON h.st_id = s.st_id 
                  JOIN district d ON h.dt_id = d.dt_id 
                  WHERE b.uidd= '$userid'";
    
        if (!empty($hotelName)) {
            $query .= " AND h.hotel_name LIKE '%$hotelName%'";
        }
    
        if (!empty($fromDate) && !empty($toDate)) {
            $query .= " AND b.booked_at BETWEEN '$fromDate' AND '$toDate'";
        }
    
        $result = mysqli_query($conn, $query);
    }
    mysqli_close($conn);
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
    <style>
        .booking-search-form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
    align-items: center;
}

.booking-search-form input[type="text"],
.booking-search-form input[type="date"] {
    padding: 8px 10px;
    font-size: 16px;
    width: 200px;
    border-radius: 5px;
    border: 1px solid #ccc;
}
.booking-search-form input[type="text"]{
    width:38%;
}
.booking-search-form .btn {
    padding: 8px 16px;
    border-radius: 5px;
    font-size: 16px;
}

.booking-search-form .btn-primary {
    background-color: #246A73;
    color: white;
    border: none;
}

.booking-search-form .btn-secondary {
    background-color: gray;
    color: white;
    text-decoration: none;
    display: inline-block;
}
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <main style="background-color: powderblue; position: relative; top: 15px;">
        <div class="content-container">
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
                </div>
                <a href="editprofile.php" class="profile-link">Edit Profile</a>
            </div>
            <div class="booking-history">
                <h2 class="profile-title">Booking History :</h2>
                <br>
                <form action="my.php" method="post" class="booking-search-form">
                    <input type="text" name="hotelname" placeholder="Enter Hotel Name" class="form-control" />

                    <input type="date" name="from_date" class="form-control" />
                    <input type="date" name="to_date" class="form-control" />

                    <button type="submit" name="search" class="btn btn-primary">Search</button>
                    <a href="my.php" class="btn btn-secondary">Clear Filter</a>
                </form>
                <table border="1" width="100%" style="margin-top: 15px">
                    <?php 
                    if (mysqli_num_rows($result) > 0) { ?>
                        <thead>
                            <tr>
                                <th>Booked On</th>
                                <th>Hotel Name</th>
                                <th>State</th>
                                <th>District</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Receipt</th>
                            </tr>
                        </thead>
                        <tbody id="bookingTable">
                            <?php 
                            while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo $row['booked_at']; ?></td>
                                    <td>
                                        <img src="hphotos/<?php echo $row['himage_url']; ?>" style="width:300px;height:200px;">
                                        <h3><?php echo $row['hotel_name']; ?></h3>
                                    </td>
                                    <td><?php echo $row['stname']; ?></td>
                                    <td><?php echo $row['dt_name']; ?></td>
                                    <td><?php echo $row['checkin']; ?></td>
                                    <td><?php echo $row['checkout']; ?></td>
                                    <td><a href="generate_pdf.php?booking_id=<?= $row['booking_id'] ?>" class="btn btn-primary mt-4" style="background-color:green;">Download Receipt</a></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    <?php } else { ?>
                        <h1 >No Result Found</h1>
                    <?php } ?>
                </table>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
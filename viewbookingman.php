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
    $username = $_SESSION['username'];
    $hotel_id = null;
    $query = "SELECT hotel_id FROM users WHERE uname = '$username'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $hotel_id = $row['hotel_id'];
        $_SESSION['hotel_id']=$hotel_id ;
    }
    $search = "";
    $users = [];
    if ($hotel_id) {
        $sql = "SELECT bookings.roomtype ,bookings.booking_id, users.uname, bookings.booked_at, bookings.checkin, bookings.checkout, bookings.status
        FROM bookings 
        JOIN users ON bookings.uidd = users.uid 
        WHERE bookings.hotel_id = $hotel_id";

        if (!empty($_GET['uname'])) {
            $uname = $_GET['uname'];
            $sql .= " AND users.uname LIKE '%$uname%'";
        }
        if ($hotel_id) {
            $sql .= " AND bookings.hotel_id = $hotel_id";
        }
        if (!empty($_GET['booking_id'])) {
            $bookingID = $_GET['booking_id'];
            $sql .= " AND bookings.booking_id = '$bookingID'";
        }
        if (!empty($_GET['room_type'])) {
            $roomType = $_GET['room_type'];
            $sql .= " AND bookings.roomtype = '$roomType'";
        }
        if (!empty($_GET['status'])) {
            $status = $_GET['status'];
            $sql .= " AND bookings.status = '$status'";
        }
        if (!empty($_GET['fromdate']) && !empty($_GET['todate'])) { 
            $sql .= " AND bookings.booked_at BETWEEN '".$_GET['fromdate']."' AND '".$_GET['todate']."'";
        }
        $sql .= " ORDER BY bookings.booking_id DESC";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    else{
        echo "No hotel Found";
    }
    $_SESSION["last_activity"]=time();
    mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>View Bookings - Manager</title>
  <style>
    .admin-booking-container {
      background-color: #fff;
      max-width: 1200px;
      margin: 30px auto;
      padding: 25px;
      margin-top:4rem;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.08);
      /* font-family: 'Segoe UI', sans-serif; */
    }

    .admin-booking-header {
      text-align: center;
      font-size: 26px;
      font-weight:900;
      color: black;
      margin-bottom: 25px;
    }

    .admin-booking-filters {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      justify-content: space-evenly;
      margin-bottom: 20px;
    }

    .admin-booking-filters input,
    .admin-booking-filters select {
      padding: 8px 10px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 6px;
      width: 250px;
    }

    .admin-booking-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    .admin-booking-table th,
    .admin-booking-table td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: center;
      font-size: 14px;
    }

    .admin-booking-table th {
      background-color: #246A73;
      color: white;
    }

    .admin-booking-table tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    .admin-booking-actions button {
      margin: 2px;
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 13px;
    }
    .admin-btn {
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        text-align: center;
    }
    .admin-btn-modify {
        background-color: #e74c3c;
        color: white;
        margin-right:1rem;
    }
    .admin-btn-cancel {
        background-color: #f39c12;
        color: white;
        margin-right:1rem;
    }
    .admin-btn-delete {
        background-color: #e74c3c;
        color: white;
        margin-right:1rem;
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
        #backToTopBtn {
          position: fixed;
          bottom: 30px;
          right: 30px;
          display: none;
          background-color: #333;
          color: white;
          border: none;
          padding: 10px 15px;
          border-radius: 5px;
          cursor: pointer;
          z-index: 1000;
        }

        #backToTopBtn:hover {
          background-color: #555;
        }

  </style>
</head>
<body>
    <?php include 'headermanager.php'; ?>
  <div class="admin-booking-container">
  <a href="managerhome.php" class="vu-btn">← Back to Dashboard</a>
    <div class="admin-booking-header">View Bookings</div>

    <div class="admin-booking-filters">
    <form method="GET" class="admin-booking-filters">
        <div>
            <label>Name</label>
            <input type="text" name="uname" placeholder="Search by User Name" value="<?= isset($_GET['uname']) ? $_GET['uname'] : '' ?>">
        </div>
        <div>
            <label>Booking ID:</label>
            <input type="text" name="booking_id" placeholder="Search by Booking ID" value="<?= $_GET['booking_id'] ?? '' ?>">
        </div>
        <div>
            <label>Booked Between:</label>
            <input type="date" name="fromdate" value="<?= isset($_GET['fromdate']) ? $_GET['fromdate'] : '' ?>">
            <input type="date" name="todate" value="<?= isset($_GET['todate']) ? $_GET['todate'] : '' ?>">
        </div>
        <div>
            <label>Room Type:</label>
            <select name="room_type">
                <option value="">Select Type</option>
                <option value="Deluxe" <?= (isset($_GET['room_type']) && $_GET['room_type'] == 'Deluxe') ? 'selected' : '' ?>>Deluxe</option>
                <option value="Standard" <?= (isset($_GET['room_type']) && $_GET['room_type'] == 'Standard') ? 'selected' : '' ?>>Standard</option>
            </select>
        </div>
        <div>
            <label>Status:</label>
            <select name="status">
                <option value="">Select Status</option>
                <option value="Booked" <?= (isset($_GET['status']) && $_GET['status'] == 'Booked') ? 'selected' : '' ?>>Booked</option>
                <option value="Cancelled" <?= (isset($_GET['status']) && $_GET['status'] == 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                <option value="Checked In" <?= (isset($_GET['status']) && $_GET['status'] == 'Checked In') ? 'selected' : '' ?>>Checked In</option>
                <option value="Checked Out" <?= (isset($_GET['status']) && $_GET['status'] == 'Checked Out') ? 'selected' : '' ?>>Checked Out</option>
            </select>
            <button type="submit" style="padding:8px 12px; background:#246A73; text-decoration:none; border-radius:5px;">Apply All Filters</button>
        </div>
            <a href="viewbookingman.php" style="padding:8px 12px; background:#ccc; text-decoration:none; border-radius:5px;">Clear Filters</a>
    </form>
    </div>
    <table class="admin-booking-table">
      <thead>
        <tr>
          <th>Booking id</th>
          <th>User</th>
          <th>Room Type</th>
          <th>Check-in</th>
          <th>Check-out</th>
          <th>Status</th>
          <th>Booked At</th>
          <th>Actions</th>
        </tr>
      </thead>
        <tbody>
            <?php 
            if (!empty($users)) {
                foreach ($users as $row) { ?>
                    <tr>
                        <td><?=$row['booking_id']?></td>
                        <td><?= $row['uname'] ?></td>
                        <td><?= $row['roomtype'] ?></td>
                        <td><?= $row['checkin'] ?></td>
                        <td><?= $row['checkout'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td><?= $row['booked_at'] ?></td>
                        <td class='admin-booking-actions'>
                            <a href="modifybooking.php?id=<?= $row['booking_id']; ?>" class="admin-btn admin-btn-modify">Modify</a>
                            <a href="cancelbookingman.php?id=<?= $row['booking_id']; ?>" class="admin-btn admin-btn-cancel" onclick="return confirmCancel()">Cancel</a>
                        </td>
                    </tr>
                    <?php 
                }
            } else { ?>
                <tr>
                    <td colspan="6" style="text-align:center;">No results found.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <button onclick="scrollToTop()" id="backToTopBtn">↑ Top</button>
  </div>
  <?php include 'footer.php'; ?>
  <script>
    function confirmCancel() {
        return confirm("Are you sure you want to cancel this booking?");
    }
  </script>
  <script>
      window.onscroll = function () {
        const btn = document.getElementById("backToTopBtn");
        if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
          btn.style.display = "block";
        } else {
          btn.style.display = "none";
        }
      };

      function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
  </script>
</body>
</html>
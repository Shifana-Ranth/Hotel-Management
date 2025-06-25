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
    $search = "";
    $users = [];

    $states = [];
    $sqlst="SELECT * FROM states";
    $res=mysqli_query($conn,$sqlst);
    while ($row = mysqli_fetch_assoc($res)) {
        $states[] = $row['stname'];
    }
    $selectedState = isset($_GET['states']) ? $_GET['states'] : ( isset($_POST['states']) ? $_POST['states'] : '');
    $s = "SELECT st_id FROM states WHERE stname='$selectedState'";
    $stid = mysqli_query($conn, $s);
    $row2 = mysqli_fetch_assoc($stid);
    $sid = $row2['st_id'] ?? '';
    $districts = [];
    if ($sid) {
        $sqldt = "SELECT * FROM district WHERE st_id=$sid";
        $res = mysqli_query($conn, $sqldt);
        while ($row = mysqli_fetch_assoc($res)) {
            $districts[] = $row['dt_name'];
        }
    }

    $sql = "SELECT bookings.booking_id, users.uname, hotels.hotel_name, bookings.checkin, bookings.checkout, bookings.status
    FROM bookings 
    JOIN users ON bookings.uidd = users.uid 
    JOIN hotels ON bookings.hotel_id = hotels.hotel_id 
    JOIN district ON hotels.dt_id = district.dt_id 
    JOIN states ON district.st_id = states.st_id 
    WHERE 1=1";

    if (!empty($_GET['uname'])) {
        $uname = $_GET['uname'];
        $sql .= " AND users.uname LIKE '%$uname%'";
    }

    if (!empty($selectedState)) {
        $sql .= " AND states.stname = '$selectedState'";
    }

    if (!empty($_GET['district'])) {
        $district = $_GET['district'];
        $sql .= " AND district.dt_name = '$district'";
    }

    if (!empty($_GET['hotelName'])) {
        $hotelName = $_GET['hotelName'];
        $sql .= " AND hotels.hotel_name LIKE '%$hotelName%'";
    }
    
    if (!empty($_GET['fromdate']) && !empty($_GET['todate'])) { 
        $sql .= " AND bookings.booked_at BETWEEN '".$_GET['fromdate']."' AND '".$_GET['todate']."'";
    }
    $sql .= " ORDER BY bookings.booking_id DESC";
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    $_SESSION["last_activity"]=time();
    mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Bookings - Admin Panel</title>
  <style>
    .admin-booking-container {
      background-color: #fff;
      max-width: 1200px;
      margin: 30px auto;
      padding: 25px;
      margin-top:4rem;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.08);
      font-family: 'Segoe UI', sans-serif;
    }

    .admin-booking-header {
      text-align: center;
      font-size: 26px;
      font-weight:900px;
      color: #2d3436;
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
      width: 200px;
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
    .admin-btn-view {
        background-color: #27ae60;
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
    <?php include 'headeradmin.php'; ?>
  <div class="admin-booking-container">
  <a href="adminhome.php" class="vu-btn">← Back to Dashboard</a>
    <div class="admin-booking-header">Manage Bookings</div>
    <div class="admin-booking-filters">
    <form method="GET" class="admin-booking-filters">
        <div>
        <label>User Name:</label>
        <input type="text" name="uname" placeholder="Search by User Name" value="<?= isset($_GET['uname']) ? $_GET['uname'] : '' ?>">
        </div>
        <div>
        <label>Hotel Name:</label>
        <input type="text" name="hotelName" placeholder="Search by Hotel Name" value="<?= $_GET['hotelName'] ?? '' ?>">
        </div>
        <select id="states" name="states" onchange="this.form.submit()">
            <option value="">Select State</option>
            <?php foreach ($states as $state) { ?>
            <option value="<?= $state ?>" <?= ($state == $selectedState) ? 'selected' : '' ?>><?= $state ?></option>
            <?php } ?>
        </select>

        <select id="district" name="district">
            <option value="">Select District</option>
            <?php foreach ($districts as $district) { ?>
            <option value="<?= $district ?>" <?= (isset($_GET['district']) && $_GET['district'] == $district) ? 'selected' : '' ?>><?= $district ?></option>
            <?php } ?>
        </select>
        <div style="display:flex;justify-content:space-between;width:100%;padding:20px;padding-right:30px;padding-left:30px;">
        <div >
            <label>Booked Between:</label>
            <input type="date" name="fromdate" value="<?= isset($_GET['fromdate']) ? $_GET['fromdate'] : '' ?>">
            <input type="date" name="todate" value="<?= isset($_GET['todate']) ? $_GET['todate'] : '' ?>">
            <button type="submit" style="padding:8px 12px; background:#246A73; text-decoration:none; border-radius:5px;">Apply Filters</button>
        </div>
        <a href="managebooking.php" style="padding:8px 12px; background:#ccc; text-decoration:none; border-radius:5px;">Clear Filters</a>
        </div>
    </form>
    </div>
    <table class="admin-booking-table">
      <thead>
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Hotel</th>
          <th>Check-in</th>
          <th>Check-out</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
        <tbody>
            <?php 
            if (!empty($users)) {
                $serial = 1;
                foreach ($users as $row) { ?>
                    <tr>
                        <td><?= $serial ?></td>
                        <td><?= $row['uname'] ?></td>
                        <td><?= $row['hotel_name'] ?></td>
                        <td><?= $row['checkin'] ?></td>
                        <td><?= $row['checkout'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td class='admin-booking-actions'>
                            <a href="viewbooking.php?id=<?= $row['booking_id']; ?>" class="admin-btn admin-btn-view">View</a>
                            <a href="cancelbooking.php?id=<?= $row['booking_id']; ?>" class="admin-btn admin-btn-cancel" onclick="return confirmCancel()">Cancel</a>
                            <a href="deletebooking.php?id=<?= $row['booking_id']; ?>" class="admin-btn admin-btn-delete" onclick="return confirmDelete()">Delete</a>
                        </td>
                    </tr>
                    <?php 
                    $serial++;
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
    function confirmDelete(bookingId) {
        return confirm("Are you sure you want to DELETE this booking permanently?");
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
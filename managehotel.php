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

    if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty(trim($_POST["search"]))) {
        $search = mysqli_real_escape_string($conn, $_POST["search"]);
    
        $sql = "SELECT h.*, s.stname AS state_name, d.dt_name AS district_name 
                FROM hotels h 
                JOIN states s ON h.st_id = s.st_id 
                JOIN district d ON h.dt_id = d.dt_id 
                WHERE h.hotel_name LIKE '%$search%'
                   OR h.hotel_id = '$search'
                   OR s.stname LIKE '%$search%'
                   OR d.dt_name LIKE '%$search%'
                   OR h.availability LIKE '%$search%'
                ORDER BY h.hotel_id";
    }
    else {
        $sql = "SELECT h.*, s.stname AS state_name, d.dt_name AS district_name 
                FROM hotels h 
                JOIN states s ON h.st_id = s.st_id 
                JOIN district d ON h.dt_id = d.dt_id 
                ORDER BY h.hotel_id 
                LIMIT 10";
    }

    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    $_SESSION["last_activity"]=time();
    mysqli_close($conn);
?>

<!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hotels - Admin Panel</title>
    <style>
        *{
            font-family: 'Times New Roman', Times, serif;
        }
        body{
            margin-top:4rem;
        }
        .admin-container {
            width: 90%;
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .admin-title {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        .search-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .search-container input {
            padding: 8px;
            width: 70%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .admin-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
        }
        .admin-btn-add {
            background-color: #007bff;
            color: white;
            float: right;
        }
        .admin-btn-edit {
            background-color: #2E8B57;
            color: white;
            margin-right:2rem;
        }
        .admin-btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .admin-table th, .admin-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .admin-table th {
            background-color: #246A73;
            color: white;
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
    <div class="admin-container">
        <!-- <div style="display:flex;flex-direction:row;background-color:red;align-items:center;"> -->
        <a href="adminhome.php" class="vu-btn">← Back to Dashboard</a>
        <h2 class="admin-title" >Manage Hotels</h2>
        <form method="POST" class="search-container" action="managehotel.php">
            <input type="text" name="search" placeholder="Search by Hotel Name, Hotel ID, Location, or Availability">
            <button type="search" class="admin-btn" style="background-color:#246A73;color:white;">Search</button>
            <a href="addhotel.php" class="admin-btn admin-btn-add">Add Hotel</a>
            <a href="managehotel.php" style="padding:8px 16px;background:#ccc; text-decoration:none; border-radius:5px;">Clear Filters</a>
        </form>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Hotel ID</th>
                <th>Name</th>
                <th>Image</th>
                <th>Location (District, State)</th>
                <th>Availability</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)) {
                    foreach ($users as $row) { ?>
                    <tr>
                        <td><?= $row['hotel_id']; ?></td>
                        <td><?= $row['hotel_name']; ?></td>
                        <td><img src="hphotos/<?= $row['himage_url']; ?>" style="width:300px;height:200px;" alt="hotel image"></td>
                        <td><?= $row['state_name']. ', ' . $row['district_name']; ?></td>
                        <td><?= $row['availability']; ?></td>
                        <td>
                            <a href="edithotel.php?id=<?= $row['hotel_id']; ?>" class="admin-btn admin-btn-edit">Edit</a>
                            <a href="deletehotel.php?id=<?= $row['hotel_id']; ?>" class="admin-btn admin-btn-delete" onclick="return confirm('Are you sure you want to delete this hotel?');">Delete</a>
                        </td>
                    </tr>
            <?php } } else { ?>
                <tr>
                <td colspan="6" style="text-align:center;">No results found.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <button onclick="scrollToTop()" id="backToTopBtn">↑ Top</button>
    <!-- <a href="adminhome.php" class="vu-btn">← Back to Dashboard</a> -->
</div>
<?php include 'footer.php'; ?>
<script>
    function confirmDelete(hotelId) {
        if (confirm("Are you sure you want to delete this Hotel?")) {
            window.location.href = "deletehotel.php?id=" + userId;
        }
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
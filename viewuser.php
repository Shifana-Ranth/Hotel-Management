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
    $search='';

    if ($row = mysqli_fetch_assoc($result)) {
        $hotel_id = $row['hotel_id'];
        $_SESSION['hotel_id']=$hotel_id ;
    }
    if ($hotel_id) {
        $sql = "
            SELECT u.uid, u.uname, u.firstname, u.email, u.phno,
                COUNT(b.booking_id) AS total_bookings,
                MAX(b.booked_at) AS last_booking_date
            FROM users u
            INNER JOIN bookings b ON u.uid = b.uidd
            WHERE b.hotel_id = $hotel_id
            GROUP BY u.uid
        ";
        $result = mysqli_query($conn, $sql);
    }
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search'])) {
        $search = isset($_POST['search']) ? trim($_POST['search']) : '';
        $fromdate = isset($_POST['fromdate']) ? $_POST['fromdate'] : '';
        $todate = isset($_POST['todate']) ? $_POST['todate'] : '';
        $conditions = [];
        if ($hotel_id) {
            $baseQuery = "
                SELECT u.uid, u.uname, u.firstname, u.email, u.phno,
                    COUNT(b.booking_id) AS total_bookings,
                    MAX(b.booked_at) AS last_booking_date
                FROM users u
                INNER JOIN bookings b ON u.uid = b.uidd
                WHERE b.hotel_id = $hotel_id
            ";
            if (!empty($search)) {
                $safeSearch = mysqli_real_escape_string($conn, $search);
                $conditions[] = "(u.uid LIKE '%$safeSearch%' OR u.uname LIKE '%$safeSearch%')";
            }

            if (!empty($fromdate) && !empty($todate)) {
                $conditions[] = "DATE(b.booked_at) BETWEEN '$fromdate' AND '$todate'";
            }
            if (count($conditions) > 0) {
                $baseQuery .= " AND " . implode(" AND ", $conditions);
            }
            $baseQuery .= " GROUP BY u.uid ORDER BY last_booking_date DESC";

            if (count($conditions) === 0) {
                $baseQuery .= " LIMIT 10"; 
            }

            $result = mysqli_query($conn, $baseQuery);
        }
    }
    $_SESSION["last_activity"]=time();
    mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Users - Manager</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            /* background-color: #f4f4f4; */
            margin: 0;
            padding: 0;
        }
        .search-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 15px;
        background-color:transparent;
        align-items: center;
    }
    .search-container input[type="text"],
    .search-container input[type="date"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 1rem;
    }
    .search-container input[type="text"] {
        width:80%;
        margin-right: 5px;
    }
    .search-container label {
        font-weight: bold;
        margin-right: 5px;
    }
    .search-container button {
        padding: 8px 16px;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .btn-search {
        background-color: #246A73;
        color: white;
    }
    .btn-clear {
        background-color: #ccc;
        color: #333;
        border-radius: 5px;
    }
        .vu-container {
            margin: 2rem 2rem 2rem 2rem;
            /* margin:2rem; */
            margin-top:6rem;
            padding:2rem;
        }

        .vu-container h1 {
            color: #2c3e50;
            margin-bottom:2rem;
            text-align:center;
        }

        .vu-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .vu-table th, .vu-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .vu-table th {
            background-color: #246A73;
            color: white;
        }

        .vu-table tr:hover {
            background-color: #f9f9f9;
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

        .vu-small-btn {
            padding: 5px 10px;
            font-size: 0.9rem;
            /* background-color: #246A73; */
            background-color: #4b0082;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .vu-small-btn:hover {
            background-color: #1c4f57;
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
<div class="vu-container">
<a href="managerhome.php" class="vu-btn">← Back to Dashboard</a>
 <h1>Users Who Booked This Hotel</h1>
<form method="POST" class="search-container" action="viewuser.php">
    <label>Enter UserName/Id:</label>
    <input type="text" id="searchUser" name="search" placeholder="Enter User ID or Name..." value="<?php echo htmlspecialchars($search); ?>">
        <label>Booked Between:</label>
        <input type="date" name="fromdate" value="<?= isset($_POST['fromdate']) ? $_POST['fromdate'] : '' ?>">
        <input type="date" name="todate" value="<?= isset($_POST['todate']) ? $_POST['todate'] : '' ?>">
        <button type="submit" class="btn-search">Search</button>
        <a href="viewuser.php" class="btn-clear" style="text-decoration:none; display:inline-block; padding:8px 16px;">Clear Filter</a>
    </form>

    <table class="vu-table">
        <thead>
            <tr>
                <th>User Id</th>
                <th>User Name</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Total Bookings</th>
                <th>Last Booking Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['uid']); ?></td>
                    <td><?php echo htmlspecialchars($row['uname']); ?></td>
                    <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phno']); ?></td>
                    <td><?php echo $row['total_bookings']; ?></td>
                    <td><?php echo $row['last_booking_date']; ?></td>
                    <td>
                        <a href="viewuserbookingman.php?id=<?php echo $row['uid']; ?>" class="vu-small-btn"> View Bookings </a>
                    </td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="8"><h1>No results found</h1></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
    </table>
    <button onclick="scrollToTop()" id="backToTopBtn">↑ Top</button>
    
</div>

<?php include 'footer.php'; ?>
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
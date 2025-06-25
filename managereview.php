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
    
    $uid = isset($_GET['uid']) ? intval($_GET['uid']) : '';
    $reviewid = isset($_GET['reviewid']) ? intval($_GET['reviewid']) : '';
    $from = $_GET['fromdate'] ?? '';
    $to = $_GET['todate'] ?? '';

    $query = "SELECT 
                r.uidd, 
                MAX(r.namee) AS namee,
                MAX(r.email) AS email,
                MAX(r.phno) AS phno,
                COUNT(*) AS total_reviews,
                MAX(r.created_at) AS latest_review,
                MIN(r.rid) AS rid
            FROM review r
            WHERE 1";

    if (!empty($uid)) {
        $query .= " AND r.uidd = $uid";
    }
    if (!empty($reviewid)) {
        $query .= " AND r.rid = $reviewid";
    }
    if (!empty($from) && !empty($to)) {
        $query .= " AND DATE(r.created_at) BETWEEN '$from' AND '$to'";
    }

    $query .= " GROUP BY r.uidd ORDER BY latest_review DESC";

    $result = mysqli_query($conn, $query);
    $_SESSION["last_activity"]=time();
    mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Users - VoyageVista</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
        }

        .vu-container {
            /* margin: 5rem 2rem 2rem 270px; */
            margin:2rem;
            margin-top:5rem;
        }

        .vu-container h1 {
            color: #2c3e50;
            text-align:center;
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

        .vu-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            /* margin:50px; */
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

<?php include 'headeradmin.php'; ?>

<div class="vu-container">
<a href="adminhome.php" class="vu-btn">← Back to Dashboard</a>
    <h1>Reviews</h1>
    <br>
    <br>
    <form method="GET" class="admin-booking-filters">
        <div>
            <label>Review Id:</label>
            <input type="number" name="reviewid" placeholder="Search by Review Id" value="<?= $_GET['reviewid'] ?? '' ?>">
        </div>
        <div>
            <label>User Id:</label>
            <input type="number" name="uid" placeholder="Search by User Id" value="<?= isset($_GET['uid']) ? $_GET['uid'] : '' ?>">
        </div>
        <div >
            <label>Date:</label>
            <input type="date" name="fromdate" value="<?= isset($_GET['fromdate']) ? $_GET['fromdate'] : '' ?>">
            <input type="date" name="todate" value="<?= isset($_GET['todate']) ? $_GET['todate'] : '' ?>">
            <button type="submit" style="padding:8px 12px; background:#246A73; text-decoration:none; border-radius:5px;">Apply Filters</button>
        </div>
        <a href="managereview.php" style="padding:8px 12px; background:#ccc; text-decoration:none; border-radius:5px;">Clear Filters</a>
    </form>
    <br>
    <table class="vu-table">
        <thead>
            <tr>
                <th>Msg Id</th>
                <th>User Id</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Total Review</th>
                <th>Last Review Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['rid']; ?></td>
                <td><?php echo htmlspecialchars($row['uidd']); ?></td>
                <td><?php echo htmlspecialchars($row['namee']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['phno']); ?></td>
                <td><?php echo $row['total_reviews']; ?></td>
                <td><?php echo $row['latest_review']; ?></td>
                <td>
                    <a href="viewreview.php?id=<?php echo $row['uidd']; ?>" class="vu-small-btn">
                        View Reviews
                    </a>
                </td>
            </tr>
        <?php } ?>
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
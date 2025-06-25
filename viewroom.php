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

    $query = "SELECT * FROM rooms WHERE hotel_id = '$hotel_id'";

    if (!empty($_GET['room_id'])) {
        $room_id = mysqli_real_escape_string($conn, $_GET['room_id']);
        $query .= " AND rid = $room_id";
    }

    if (!empty($_GET['room_type'])) {
        $room_type = mysqli_real_escape_string($conn, $_GET['room_type']);
        $query .= " AND r_type = '$room_type'";
    }

    if (!empty($_GET['availability'])) {
        $availability = mysqli_real_escape_string($conn, $_GET['availability']);
        $query .= " AND availability = '$availability'";
    }

    $result = mysqli_query($conn, $query);
    $_SESSION["last_activity"]=time();
    mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Rooms</title>
  <style>
    .room-management-wrapper {
      font-family: 'Times New Roman', Times, serif;
      background-color: #f4f4f4;
      padding: 40px;
      margin-top:2rem;
    }

    .room-management-wrapper h1 {
      font-size: 2.5rem;
      color: #2c3e50;
      text-align:center;
      margin-bottom: 20px;
      background-color:transparent;
    }
    .room-management-wrapper .room-actions {
      margin-bottom: 20px;
      background-color:transparent;
    }

    .room-management-wrapper .room-actions input,
    .room-management-wrapper .room-actions select,
    .room-management-wrapper .room-actions button {
      padding: 10px;
      background-color:transparent;
      margin-left:40px;
      margin-right: 40px;
      font-size: 1rem;
      border-radius: 5px;
      border: 1px solid #ccc;
      background-color: white;
    }
    .room-management-wrapper .room-actions button {
      background-color: #246A73;
      color: white;
      cursor: pointer;
    }

    .room-management-wrapper .room-actions button:hover {
      background-color: darkblue;
    }

    .room-management-wrapper .room-table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .room-management-wrapper .room-table th,
    .room-management-wrapper .room-table td {
      padding: 12px 15px;
      text-align: center;
      border: 1px solid #ccc;
    }

    .room-management-wrapper .room-table th {
      background-color: #246A73;
      color: white;
    }

    .room-management-wrapper .edit-btn,
    .room-management-wrapper .delete-btn {
      padding: 8px 14px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }

    .room-management-wrapper .edit-btn {
      background-color: #3498db;
      color: white;
    }

    .room-management-wrapper .delete-btn {
      background-color: #e74c3c;
      color: white;
    }

    .room-management-wrapper .edit-btn:hover {
      background-color: #2980b9;
    }

    .room-management-wrapper .delete-btn:hover {
      background-color: #c0392b;
    }

    .room-management-wrapper .add-room {
      margin-top: 20px;
      margin-left: 10px;
      background-color:transparent;
    }

    .room-management-wrapper .add-room button {
      background-color: darkblue;
      color: white;
      padding: 11px 20px;
      font-size: 1rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }

    .room-management-wrapper .add-room button:hover {
      background-color: darkgreen;
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
    <div class="room-management-wrapper">
    <a href="managerhome.php" class="vu-btn">← Back to Dashboard</a>
        <h1>View Rooms - Manager</h1>
        <br>
        <form method="GET" action="viewroom.php" style="display: inline-block;" class="room-actions">
            <div class="room-actions">
                <input type="number" name="room_id" placeholder="Search by Room ID" value="<?php echo isset($_GET['room_id']) ? htmlspecialchars($_GET['room_id']) : ''; ?>">

                <select name="room_type">
                    <option value="">Filter by Room Type</option>
                    <option value="Deluxe" <?php if(isset($_GET['room_type']) && $_GET['room_type'] == 'Deluxe') echo 'selected'; ?>>Deluxe</option>
                    <option value="Standard" <?php if(isset($_GET['room_type']) && $_GET['room_type'] == 'Standard') echo 'selected'; ?>>Standard</option>
                </select>

                <select name="availability">
                    <option value="">Filter by Availability</option>
                    <option value="available" <?php if(isset($_GET['availability']) && $_GET['availability'] == 'available') echo 'selected'; ?>>Available</option>
                    <option value="Unavailable" <?php if(isset($_GET['availability']) && $_GET['availability'] == 'Unavailable') echo 'selected'; ?>>Unavailable</option>
                </select>

                <button type="submit">Apply All Filters</button>
                <a href="viewroom.php" style="padding:11px 12px; background:#ccc; text-decoration:none; border-radius:5px;">Clear Filters</a>
                <a href="addroom.php" class="add-room" style="margin-left:30px;padding:11px 12px; background:darkblue;color:white; text-decoration:none; border-radius:5px;">Add New Room</a>
            </div>
        </form>
        <br><br>
        <table class="room-table">
        <thead>
            <tr>
            <th>Room ID</th>
            <th>Type</th>
            <th>Rating</th>
            <th>Price</th>
            <th>Availability</th>
            <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['rid']) . "</td>";
                echo "<td>" . htmlspecialchars($row['r_type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['rating']) . "</td>";
                echo "<td>₹" . htmlspecialchars($row['price_per_night']) . "</td>";
                echo "<td>" . htmlspecialchars($row['availability']) . "</td>";
                echo "<td>
                        <a href='seeroom.php?id=" . $row['rid'] . "' class='admin-btn admin-btn-modify'>View</a>
                        <a href='deleteroom.php?id=" . $row['rid'] . "' class='admin-btn admin-btn-cancel' onclick='return confirm(\"Are you sure you want to delete this room?\")'>Delete</a>
                    </td>";
                echo "</tr>";
            }
            } else {
            echo "<tr><td colspan='6'>No rooms found for this hotel.</td></tr>";
            }
            ?>
        </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
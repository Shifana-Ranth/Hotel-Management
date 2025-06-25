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
    $role = "";
    $users = [];
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search'])) {
        $search = trim($_POST['search']);
        $role=$_POST['role'];
        if ($search === "") {
            if ($role !== "") {
                $sql = "SELECT uid, uname, email, roles FROM users WHERE roles = '$role'";
            } else {
                $sql = "SELECT uid, uname, email, roles FROM users ORDER BY uid DESC LIMIT 10";
            }
        } else if (is_numeric($search)) {
            if ($role !== "") {
                $sql = "SELECT uid, uname, email, roles FROM users WHERE uid = '$search' AND roles = '$role'";
            } else {
                $sql = "SELECT uid, uname, email, roles FROM users WHERE uid = '$search'";
            }
        } else {
            if ($role !== "") {
                $sql = "SELECT uid, uname, email, roles FROM users WHERE uname LIKE '%$search%' AND roles = '$role' ORDER BY uid DESC";
            } else {
                $sql = "SELECT uid, uname, email, roles FROM users WHERE uname LIKE '%$search%' ORDER BY uid DESC";
            }
        }
    } else {
        $sql = "SELECT uid, uname, email, roles FROM users ORDER BY uid DESC LIMIT 10";
    }

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> 
    <style>
        *{
            font-family: 'Times New Roman', Times, serif;
        }
        .admin-container {
            width: 90%;
            max-width: 1200px;
            margin: auto;
            padding: 100px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .admin-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-container {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 15px;
        }
        .search-container input {
            padding: 8px;
            margin-right:10px;
            width: 65%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-container select{
            padding: 8px;
            margin-right:10px;
            border-radius: 4px;
            border: 1px solid #ccc;
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
        .admin-table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .admin-btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            display: inline-block;
            text-align: center;
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
        .admin-btn-add {
            margin-left:1rem;
            background-color: #007bff;
            color: white;
            float: right;
        }
        .admin-btn-edit:hover {
            background-color: #218838;
        }
        .admin-btn-delete:hover {
            background-color: #c82333;
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
    <a href="adminhome.php" class="vu-btn">← Back to Dashboard</a>
        <h1 class="admin-title">Manage Users</h1>
        <form method="POST"  class="search-container" action="manageuser.php">
            <input type="text" id="searchUser" name="search" placeholder="Enter User ID or Name..." value="<?php echo htmlspecialchars($search); ?>" >
            <select name="role">
                <option value="" <?php echo ($role === "") ? "selected" : ""; ?>>Role</option>
                <option value="user" <?php echo ($role === "user") ? "selected" : ""; ?>>User</option>
                <option value="manager" <?php echo ($role === "manager") ? "selected" : ""; ?>>Manager</option>
                <option value="Admin" <?php echo ($role === "Admin") ? "selected" : ""; ?>>Admin</option>
            </select>
            <button type="search" class="admin-btn" style="background-color:#246A73;color:white;">Search</button>
            <a href='addaccount.php?' class='admin-btn admin-btn-add'>Add Account</a>
            <a href="manageuser.php" style="padding:0px 0px;background:#ccc; text-decoration:none; margin-left:10px;text-align:center;border-radius:5px;">Clear Filters</a>
        </form>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
                if (!empty($users)) {
                    foreach ($users as $row) {
                        echo "<tr>
                            <td>{$row['uid']}</td>
                            <td>{$row['uname']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['roles']}</td>
                            <td>
                                <a href='edituser.php?id={$row['uid']}' class='admin-btn admin-btn-edit'>Edit</a>
                                <a href='javascript:void(0);' onclick=\"confirmDelete('{$row['uid']}')\" class='admin-btn admin-btn-delete'>Delete</a> 
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center;'>No users found</td></tr>";
                }
            ?>
            </tbody>
        </table>
        <button onclick="scrollToTop()" id="backToTopBtn">↑ Top</button>
    </div>
    <br>
    <?php include 'footer.php';?>
    <script>
    function confirmDelete(userId) {
        if (confirm("Are you sure you want to delete this user?")) {
            window.location.href = "deleteuser.php?id=" + userId;
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
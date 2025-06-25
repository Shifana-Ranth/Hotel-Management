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
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        echo "Invalid request.";
        exit();
    }

    $userId = $_GET['id'];
    $sql = "SELECT uid, uname, email, roles FROM users WHERE uid = '$userId'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        echo "User not found.";
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $uname = trim($_POST['uname']);
        $email = trim($_POST['email']);
        $roles = trim($_POST['roles']);

        if ($uname && $email && $roles) {
            $updateSql = "UPDATE users SET uname='$uname', email='$email', roles='$roles' WHERE uid='$userId'";
            if (mysqli_query($conn, $updateSql)) {
                echo "<script>alert('User updated successfully!'); window.location.href='manageuser.php';</script>";
            } else {
                echo "<script>alert('Error updating user: " . mysqli_error($conn) . "');</script>";
            }
        } else {
            echo "<script>alert('All fields are required!');</script>";
        }
    }
    $_SESSION["last_activity"]=time();
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User -Admin Panel</title>
    <style>
        *{
            font-family: 'Times New Roman', Times, serif;
        }
        .edit-container {
            width: 50%;
            margin: auto;
            padding: 200px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
        }
        input, select {
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
        }
        button {
            margin-top: 15px;
            padding: 10px;
            background-color: #246A73;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #1d5a60;
        }
        .back-btn {
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            color: #007BFF;
        }
    </style>
</head>
<body>
    <?php include 'headeradmin.php'; ?>
    <div class="edit-container">
        <h2>Edit User</h2>
        <form method="POST">
            <label for="uname">Name:</label>
            <input type="text" id="uname" name="uname" value="<?php echo htmlspecialchars($user['uname']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            
            <label for="roles">Role:</label>
            <select id="roles" name="roles" required>
                <option>Select Role</option>
                <option value="admin" <?php if ($user['roles'] == 'admin') echo 'selected'; ?>>Admin</option>
                <option value="user" <?php if ($user['roles'] == 'user') echo 'selected'; ?>>User</option>
                <option value="manager" <?php if ($user['roles'] == 'manager') echo 'selected'; ?>>Manager</option>
            </select>

            <button type="submit" style="background-color: #246A73;">Update User</button>
        </form>
        <a href="manageuser.php" class="back-btn">← Back to User Management</a>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
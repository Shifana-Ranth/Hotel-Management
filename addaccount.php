<?php 
    session_start();
    include("databasee.php");
    if (!isset($_SESSION['userid'])) {
        header("Location: login.php");
        exit();
    }
    $inactive = 300;
    if (isset($_SESSION["last_activity"])) {
        $session_life = time() - $_SESSION["last_activity"];
        if ($session_life > $inactive) {
            session_unset();
            session_destroy();
            header("Location: index.php");
        }
    }
    $message = "";
    $showHotelField = false;
    $uname = $email = $password = $role = $hname = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        $uname = trim($_POST['uname']);
        $password = trim($_POST['password']);
        $role = $_POST['roles'];
        $hname = isset($_POST['hname']) ? trim($_POST['hname']) : "";

        if ($role == "manager") {
            $showHotelField = true;
        }
        else{
            $showHotelField = false;
        }
        $dupQuery = "SELECT * FROM users WHERE LOWER(uname) = LOWER('$uname')";
        $dupResult = mysqli_query($conn, $dupQuery);

        if (mysqli_num_rows($dupResult) > 0) {
            $message = "Username already exists. Please choose a different username.";
        }

        else if (!preg_match("/^(?=.*[a-zA-Z])[a-zA-Z0-9]+$/", $uname)) {
            $message = "Username must contain at least one letter and only letters/numbers.";
        }
        else if (!preg_match("/[a-zA-Z]/", $password) || !preg_match("/\d/", $password) || !preg_match("/[!@#$%^&*()_+]/", $password)) {
            $message = "Password should contain at least one letter, one number, and one special character.";
        }
        else if (strlen($uname) < 5 || strlen($uname) > 30) {
            $message = "Username should be in the range of 5 to 30 characters.";
        }
        else if (strlen($password) < 8) {
            $message = "Password should be at least 8 characters long.";
        }
        else if ($role == "Select Role") {
            $message = "Please select a valid role.";
        }
        else if ($role == "manager") {
            $hotelQuery = "SELECT hotel_id FROM hotels WHERE hotel_name = '$hname'";
            $hotelResult = mysqli_query($conn, $hotelQuery);
            if (mysqli_num_rows($hotelResult) > 0) {
                $hotelRow = mysqli_fetch_assoc($hotelResult);
                $hotelId = $hotelRow['hotel_id'];
                $checkManagerQuery = "SELECT * FROM users WHERE hotel_id = '$hotelId' AND roles = 'manager'";
                $assignedResult = mysqli_query($conn, $checkManagerQuery);
                if (mysqli_num_rows($assignedResult) > 0) {
                    $message = "A manager is already assigned to this hotel.";
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $insertQuery = "INSERT INTO users (uname, upassword, roles, hotel_id)
                                    VALUES ('$uname', '$hashedPassword', '$role', '$hotelId')";
                    if (mysqli_query($conn, $insertQuery)) {
                        header("Location: manageuser.php?success=1");
                        exit();
                    } else {
                        $message = "Error adding manager: " . mysqli_error($conn);
                    }
                }
            } else {
                $message = "Hotel not found. Please add the hotel first.";
            }
        } else {
            $showHotelField = false;
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertQuery = "INSERT INTO users (uname, email, upassword, roles)
                            VALUES ('$uname', '$email', '$hashedPassword', '$role')";
            if (mysqli_query($conn, $insertQuery)) {
                header("Location: manageuser.php?success=1");
                exit();
            } else {
                $message = "Error adding user: " . mysqli_error($conn);
            }
        }
    }

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <style>
        * { font-family: 'Times New Roman', Times, serif; }
        .edit-container {
            width: 50%;
            margin: auto;
            padding: 200px 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #333;font-weight:700; }
        form { display: flex; flex-direction: column; }
        label { margin-top: 10px; font-weight: bold; }
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
        button:hover { background-color: #1d5a60; }
        .back-btn {
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            color: #007BFF;
        }
        .message {
            color: red;
            font-weight: bold;
            margin-top: 15px;
            text-align: center;
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
    <?php  include 'headeradmin.php'; ?>
    <div class="edit-container">
    <?php  if ($message) echo "<div class='message'>$message</div>"; ?>
        <h2>Add Account</h2>
        <form action="addaccount.php" method="POST">
            <label for="uname">Username<span style="background-color:transparent;color:red;">*</span></label>
            <input type="text" id="uname" name="uname" value="<?= htmlspecialchars($uname) ?>" required>

            <label for="password">Password<span style="background-color:transparent;color:red;">*</span></label>
            <input type="password" id="password" name="password" required>

            <label for="roles">Role<span style="background-color:transparent;color:red;">*</span></label>
            <select name="roles" required>
                <option <?= $role == "Select Role" ? 'selected' : '' ?>>Select Role</option>
                <option value="admin" <?= $role == "admin" ? 'selected' : '' ?>>Admin</option>
                <option value="user" <?= $role == "user" ? 'selected' : '' ?>>User</option>
                <option value="manager" <?= $role == "manager" ? 'selected' : '' ?>>Manager</option>
            </select>

            <?php if ($showHotelField): ?>
                <label for="hname">Hotel Name<span style="background-color:transparent;color:red;">*</span></label>
                <input type="text" id="hname" name="hname" value="<?= htmlspecialchars($hname) ?>" required>
            <?php endif; ?>

            <button type="submit" name="submit">Add User</button>
        </form>
        <a href="manageuser.php" class="vu-btn">← Back to User Management</a>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
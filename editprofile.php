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
    $user = mysqli_fetch_assoc($result);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $state = $_POST['state'];

        $update_sql = "UPDATE users SET 
                    firstname='$firstname', lastname='$lastname', email='$email', phno='$phone', 
                    addresss='$address',states='$state' , city='$city' 
                    WHERE uid='$userid' ";

        if (mysqli_query($conn, $update_sql)) {
            header("Location: my.php");
            exit();
        } else {
            echo "Error updating profile!";
        }
    }
    $_SESSION["last_activity"] = time();
    mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VoyageVista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styleditprofile.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
        *{
            font-family: 'Times New Roman', Times, serif;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <main style="background-color:powderblue; position:relative;top:15px;">
        <div class="edit-profile-container">
            <div class="edit-profile-title">Edit Profile</div>
            <form action="editprofile.php" method="POST" class="edit-profile-form">

                <label class="edit-label">Full Name:</label>
                <input type="text" name="firstname" value="<?php echo $user['firstname']; ?>" class="edit-input" required>

                <label class="edit-label">Last Name:</label>
                <input type="text" name="lastname" value="<?php echo $user['lastname']; ?>" class="edit-input" required>

                <label class="edit-label">Email:</label>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" class="edit-input" required>

                <label class="edit-label">Phone:</label>
                <input type="text" name="phone" value="<?php echo $user['phno']; ?>" class="edit-input" required>

                <label class="edit-label">Address:</label>
                <input type="text" name="address" value="<?php echo $user['addresss']; ?>" class="edit-input" required>

                <label class="edit-label">City:</label>
                <input type="text" name="city" value="<?php echo $user['city']; ?>" class="edit-input" required>

                <label class="edit-label">State:</label>
                <input type="text" name="state" value="<?php echo $user['states']; ?>" class="edit-input" required>

                <button type="submit" class="edit-profile-button">Update Profile</button>
            </form>
            <a href="my.php" class="cancel-link">Cancel</a>
        </div>

    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
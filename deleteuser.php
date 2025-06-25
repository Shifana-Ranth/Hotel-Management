<?php
    session_start();
    include("databasee.php");

    if (!isset($_SESSION['userid'])) {
        header("Location: login.php");
        exit();
    }
    if($_SESSION["loggedin"]==false || $_SESSION["roles"]!=='Admin'){
        header("Location: index.php");
        exit();
    }
    if (isset($_GET['id'])) {
        $userId = $_GET['id'];
        if ($userId == $_SESSION['userid']) {
            echo "<script>alert('You cannot delete your own account!'); window.location.href='manageuser.php';</script>";
            exit();
        }
        $sql = "DELETE FROM users WHERE uid = '$userId'";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('User deleted successfully!'); window.location.href='manageuser.php';</script>";
        } else {
            echo "<script>alert('Error deleting user: " . mysqli_error($conn) . "'); window.location.href='manageuser.php';</script>";
        }
    }

    mysqli_close($conn);
?>
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
        $hotelId = $_GET['id'];
        
        $sql = "DELETE FROM hotels WHERE hotel_id = '$hotelId'";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Hotel deleted successfully!'); window.location.href='managehotel.php';</script>";
        } else {
            echo "<script>alert('Error deleting hotel: " . mysqli_error($conn) . "'); window.location.href='managehotel.php';</script>";
        }
    }

    mysqli_close($conn);
?>
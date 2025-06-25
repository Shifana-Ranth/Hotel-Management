<?php
    session_start();
    include("databasee.php");

    if (!isset($_SESSION['userid'])) {
        header("Location: login.php");
        exit();
    }

    if (!isset($_GET['id'])) {
        echo "<script>alert('No room ID provided.'); window.location.href='viewroom.php';</script>";
        exit();
    }
    if($_SESSION["loggedin"]==false || $_SESSION["roles"]!=='manager'){
        header("Location: index.php");
        exit();
    }
    $room_id = $_GET['id'];
    $hotel_id = $_SESSION['hotel_id'];

    // Step 1: Get all booking IDs for this hotel
    $booking_ids_query = "SELECT booking_id FROM bookings  WHERE hotel_id = '$hotel_id'";
    $booking_ids_result = mysqli_query($conn, $booking_ids_query);

    $has_booking = false;

    if ($booking_ids_result && mysqli_num_rows($booking_ids_result) > 0) {
        while ($row = mysqli_fetch_assoc($booking_ids_result)) {
            $booking_id = $row['booking_id'];
            
            // Step 2: Check if this room is booked under this booking ID
            $check_room_query = "SELECT * FROM bookings WHERE booking_id = '$booking_id' AND rid = '$room_id'";
            $check_room_result = mysqli_query($conn, $check_room_query);
            
            if ($check_room_result && mysqli_num_rows($check_room_result) > 0) {
                $has_booking = true;
                break;
            }
        }
    }

    if ($has_booking) {
        echo "<script>alert('This room is already booked and cannot be deleted.'); window.location.href='viewroom.php';</script>";
    } else {
        // Safe to delete
        $delete_query = "DELETE FROM rooms WHERE rid = '$room_id' AND hotel_id = '$hotel_id'";
        if (mysqli_query($conn, $delete_query)) {
            echo "<script>alert('Room deleted successfully.'); window.location.href='viewroom.php';</script>";
        } else {
            echo "<script>alert('Error deleting room.'); window.location.href='viewroom.php';</script>";
        }
    }

    mysqli_close($conn);
?>
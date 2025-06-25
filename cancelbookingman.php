<?php
    session_start();
    include("databasee.php");

    if (!isset($_SESSION['userid'])) {
        header("Location: login.php");
        exit();
    }
    $book_id = $_GET['id'] ;

    if (!$book_id) {
        echo "Invalid request.";
        exit();
    }
    if (isset($_GET['id'])) {
        $bookingId = $_GET['id'];
        $sql = "UPDATE bookings SET status='Cancelled' WHERE booking_id=$book_id";
        if (mysqli_query($conn, $sql)) {
            header("Location: viewbookingman.php?message=Booking+Cancelled");
        } else {
            echo "Error cancelling booking: " . mysqli_error($conn);
        }
        mysqli_close($conn);
    }
?>
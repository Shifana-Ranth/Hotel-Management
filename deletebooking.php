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
    $book_id = $_GET['id'] ;

    if (!$book_id) {
        echo "Invalid request.";
        exit();
    }
    if ($book_id) {
        $sqlDeleteBooking = "DELETE FROM bookings WHERE booking_id = $book_id";
        if (mysqli_query($conn, $sqlDeleteBooking)) {
            header("Location: managebooking.php?msg=Booking+Deleted+Successfully");
        } else {
            echo "Error deleting booking: " . mysqli_error($conn);
        }
    }
    mysqli_close($conn);
?>
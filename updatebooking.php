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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $booking_id = $_POST['booking_id'];
        $status = $_POST['status'];
        $checkin = $_POST['checkin_date'];
        $checkout = $_POST['checkout_date'];

        $checkinDate = DateTime::createFromFormat('Y-m-d', $checkin);
        $checkoutDate = DateTime::createFromFormat('Y-m-d', $checkout);
        $todayDate = new DateTime();

        if ($checkinDate > $checkoutDate) { 
            $_SESSION['showerror'] = "Check-in date cannot be later than check-out date."; 
            header("Location: " . $_SERVER['HTTP_REFERER']); 
            exit(); 
        } else if($checkinDate <= $todayDate) { 
            $_SESSION['showerror'] = "Check-in date must be after today's date."; 
            header("Location: " . $_SERVER['HTTP_REFERER']); 
            exit(); 
        }

        $bookingQuery = "SELECT * FROM bookings WHERE booking_id = '$booking_id'";
        $bookingResult = mysqli_query($conn, $bookingQuery);
    
        if (mysqli_num_rows($bookingResult) > 0) {
            $bookingRow = mysqli_fetch_assoc($bookingResult);
            $hotel_id = $bookingRow['hotel_id'];
            $room_id = $bookingRow['rid'];
            $old_checkin = $bookingRow['checkin'];
            $old_checkout = $bookingRow['checkout'];
            $paid_amount = $bookingRow['amount'];
            $no_of_rooms = $bookingRow['no_of_rooms'];
            $balance_amount=$bookingRow['balance'];
            $payment = $bookingRow['payment'];
    
            // Get total quantity of rooms for that room_id and hotel_id
            $quantityQuery = "SELECT price_per_night, quantity FROM rooms WHERE rid = '$room_id' AND hotel_id = '$hotel_id'";
            $quantityResult = mysqli_query($conn, $quantityQuery);
            $quantityRow = mysqli_fetch_assoc($quantityResult);
            $room_price = $quantityRow['price_per_night'];
            $totalQuantity = $quantityRow['quantity'];
            $new_total=0;
            $bookedCountQuery = "SELECT SUM(no_of_rooms) as total_booked
                FROM bookings 
                WHERE rid = '$room_id' 
                AND hotel_id = '$hotel_id'
                AND booking_id != '$booking_id'
                AND (
                    ('$checkin' < checkout AND '$checkout' > checkin)
                )";
            $bookedCountResult = mysqli_query($conn, $bookedCountQuery);
            $bookedCountRow = mysqli_fetch_assoc($bookedCountResult);
            $alreadyBooked = $bookedCountRow['total_booked'] ?? 0;
    
            $availableRooms = $totalQuantity - $alreadyBooked;
            if ($availableRooms <= 0) {
                $_SESSION['showerror'] = "No Rooms are available during the selected dates.";
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
            else if ($availableRooms < $no_of_rooms) {
                $_SESSION['showerror'] = "Only $availableRooms room(s) available during the selected dates.";
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
    
            $old_days = (new DateTime($old_checkin))->diff(new DateTime($old_checkout))->days + 1;
            $new_days = (new DateTime($checkin))->diff(new DateTime($checkout))->days + 1;
    
            if($new_days > $old_days){
                $new_total = $room_price * $new_days * $no_of_rooms;
                $balance_amount += ($new_total - $paid_amount);
                $paid_amount=$new_total;
                $payment = "Pending";
    
            }
            if ($balance_amount <= 0) {
                $payment = "Paid";
                $balance_amount=0;
            }
            $updateQuery = "UPDATE bookings 
                            SET checkin = '$checkin', 
                                checkout = '$checkout', 
                                no_of_rooms = '$no_of_rooms',
                                amount = '$paid_amount',
                                balance = '$balance_amount',
                                payment = '$payment'
                            WHERE booking_id = '$booking_id'";
    
            if (mysqli_query($conn, $updateQuery)) {
                echo "<script>alert('Booking updated successfully.'); window.location.href='viewbookingman.php';</script>";
            } else {
                echo "<script>alert('Update failed.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Update failed.'); window.history.back();</script>";
        }
    }
?>
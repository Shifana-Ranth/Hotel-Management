<?php
    session_start();
    include("databasee.php");
    $inactive=1200;
    if (isset($_SESSION["last_activity"])) {
        $session_life = time() - $_SESSION["last_activity"];
        if ($session_life > $inactive) {
            session_unset();
            session_destroy();
            header("Location: index.php");
            exit();
        }
    }
    $userid=isset($_SESSION['userid']) ? $_SESSION["userid"]:''; 
    $rid = isset($_GET['rid']) ? $_GET['rid'] : '';
    $hid = isset($_GET['hid']) ? $_GET['hid'] : '';
    $rid = isset($_GET['rid']) ? $_GET['rid'] : '';
    $checkin = isset($_GET['checkin']) ? $_GET['checkin'] : '';
    $checkout= isset($_GET['checkout']) ? $_GET['checkout'] : '';
    $hotelName = isset($_GET['hotelName']) ? $_GET['hotelName'] : '';
    $showalert=false;
    $showerror=false;
    if (isset($_GET['rid']) && isset($_GET['hid'])) {
        $room_id = $_GET['rid'];
        $hotel_id = $_GET['hid'];
        $q="SELECT * FROM ROOMS WHERE hotel_id='$hid' AND rid='$rid'";
        $re = mysqli_query($conn,$q);
        $room=mysqli_fetch_assoc($re);
        $room_type = $room['r_type'];
    }
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['loggedin']) ===true && (isset($_POST["check_availability"])===true || isset($_POST["book_now"])===true))
    {
        $name=trim($_POST["name"]);
        $phno=trim($_POST["phno"]);
        $checkin=trim($_POST["checkin"]);
        $checkout=trim($_POST["checkout"]);
        $noofroom=trim($_POST["noofroom"]);
        $hid = isset($_POST['hid']) ? $_POST['hid'] : '';
        $rid = isset($_POST['rid']) ? $_POST['rid'] : '';
        $checkin_date = new DateTime($checkin);
        $checkout_date = new DateTime($checkout);
        $interval = $checkin_date->diff($checkout_date);
        $noofnights = $interval->days;
        $checkinDate = DateTime::createFromFormat('Y-m-d', $checkin);
        $checkoutDate = DateTime::createFromFormat('Y-m-d', $checkout);
        $todayDate = new DateTime();
        $q="SELECT * FROM ROOMS WHERE hotel_id='$hid' AND rid='$rid'";
        $re = mysqli_query($conn,$q);
        $room=mysqli_fetch_assoc($re);
        if (!$room) {
            echo "Room not found.";
            exit;
        }
        if($room){
            $room_type = $room['r_type'];
            $total_quantity = $room['quantity'];
        }
        if(empty($name)){
            $showerror="please enter your firstname";
        }
        else if (!preg_match("/^[a-zA-Z ]+$/", $name)) { 
            $showerror="Yourname must contain only alphabets and spaces without numbers or special characters.";
        }
        else if(strlen($phno) !== 10)
        {
            $showerror="Ph no should be 10 digits";
        }
        else if (!preg_match("/^[6-9][0-9]{9}$/", $phno)) {
            $showerror = "Your phone number is not valid";
        }
        else if ($checkinDate > $checkoutDate) {
            $showerror = "Check-in date cannot be later than check-out date.";
        } 
        else if($checkinDate <= $todayDate) {
            $showerror = "Check-in date must be after today's date.";
        } 
        else{
                try{
                    $booking_sql = "
                        SELECT SUM(no_of_rooms) as total_booked
                        FROM bookings
                        WHERE rid = $rid AND status != 'cancelled'
                        AND (
                            ('$checkin' < checkout AND '$checkout' > checkin)
                        )
                    ";

                    $booking_result = mysqli_query($conn, $booking_sql);
                    $booked_row = mysqli_fetch_assoc($booking_result);
                    $total_booked = $booked_row['total_booked'] ?? 0;
                    $available_rooms = $total_quantity - $total_booked;
                    if($available_rooms <= 0){
                        $showerror= "Sorry...No Rooms available on this date";
                    }
                    if ($available_rooms < $noofroom) {
                        $showerror= "Only $available_rooms rooms are available. Please adjust your selection.";
                    }
                    else if ($available_rooms >= $noofroom) {
                        $base_price = 0;
                        $base_price += $room['price_per_night'];
                        $base_price *= ($noofroom * $noofnights);
                        $_SESSION['hid'] = $_POST['hid'];
                        $_SESSION['rid']=$_POST['rid'];
                        $_SESSION['name'] = $_POST['name'];
                        $_SESSION['phno'] = $_POST['phno'];
                        $_SESSION['checkin'] = $_POST['checkin'];
                        $_SESSION['checkout'] = $_POST['checkout'];
                        $_SESSION['noofroom'] = $_POST['noofroom'];
                        $_SESSION['room_type'] = $_POST['room_type'];
                        $_SESSION['room_price'] = $base_price;
                        header("location:payment.php");
                    }
                   else {
                        $showerror="Sorry!! Room already booked..Try some other rooms";
                    }
                }
                catch(mysqli_sql_exception $e)
                {
                    $showerror = "Cannot store data: " . $e->getMessage();
                } 
            }
    }
    else if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['loggedin']) ===false){
        $showerror="Please Register/Login to Book Your Rooms";
    }
    $_SESSION["last_activity"] = time();
    mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation</title>
    <link rel="stylesheet" href="stylereservat.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<?php include 'header.php'; ?>
    <?php 
        if($showerror){
            echo '
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="position:relative;top:70px;color:black;font-weight:700;padding-left:70px;">
    <strong style="background-color:transparent;"><i style="color:red;background-color:transparent;" class="fa-solid fa-circle-exclamation"></i></strong> '.$showerror.'
    <button style="background-color:transparent;position:relative;left:80%;" type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true" style="background-color:transparent;padding:8px;font-size:2rem;">&times;</span>
    </button>
    </div>
    ';
        }
    ?>
<main>
    <section class="reservation">
        <div class="container">
            <h2 class="section-title" style="margin-top:5rem;">MAKE A RESERVATION</h2>
            <br>
            <h3 class="section-subtitle">Book Your Stay and Enjoy Exceptional Comfort</h3>
            <br>
            <div class="reservation-content">
                <form class="reservation-form" action="reserv.php" method="post">
                    <input type="hidden" name="hid" value="<?php echo htmlspecialchars($hid); ?>">
                    <input type="hidden" name="rid" value="<?php echo htmlspecialchars($rid); ?>">
                    <input type="hidden" name="room_type" value="<?php echo htmlspecialchars($room_type); ?>">
                    <div class="form-group">
                        <label>Your Name <span style="background-color:transparent;color:red;">*</span></label>
                        <input type="text" name="name" placeholder="Ex. John Doe" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number <span style="background-color:transparent;color:red;">*</span></label>
                        <input type="number" name="phno" placeholder="Enter Phone Number" value="<?php echo isset($_POST['phno']) ? $_POST['phno'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Check-in Date <span style="background-color:transparent;color:red;">*</span></label>
                        <input type="date" name="checkin" required value="<?php echo $checkin; ?>" >
                    </div>
                    <div class="form-group">
                        <label>Check-out Date <span style="background-color:transparent;color:red;">*</span></label>
                        <input type="date" name="checkout" required value="<?php echo $checkout; ?>" >
                    </div>
                    <div class="form-group">
                        <label>Room Type <span style="background-color:transparent;color:red;">*</span></label>
                        <select name="room_type" disabled>
                        <option value="">Select</option>
                        <option value="Deluxe" <?php echo ($room_type == 'Deluxe') ? 'selected' : ''; ?>>Deluxe</option>
<option value="Standard" <?php echo ($room_type == 'Standard') ? 'selected' : ''; ?>>Standard</option>
                        </select>
                </div>
                <div class="form-group">
                    <label>Number of Rooms <span style="background-color:transparent;color:red;">*</span></label>
                    <select name="noofroom" required>
                        <option value="">Select</option>
                        <option value="1" <?php echo (isset($_POST['noofroom']) && $_POST['noofroom'] == '1') ? 'selected' : ''; ?>>1</option>
                        <option value="2" <?php echo (isset($_POST['noofroom']) && $_POST['noofroom'] == '2') ? 'selected' : ''; ?>>2</option>
                    </select>
                </div>
                <button type="submit" name="check_availability" class="btn" style="background-color:#246A73;color:white;">Check Availability</button>
                </form>
                <div class="reservation-image">
                    <img src="reservat.jpeg" alt="Hotel Staff" style="height:450px;width:500x;">
                    <div class="tags">
                        <span>Breakfast Included</span>
                        <span>Swimming Pool</span>
                        <span>WiFi</span>
                        <span>Spa & Wellness</span>
                        <span>Pick Up & Drop</span>
                        <span>Fitness Hub</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="newsletter">
        <div class="container">
            <h2>OUR NEWSLETTER</h2><br>
            <h3>Unlock Exclusive Updates and Offers from Our Luxury Hotel</h3><br>
            <form class="newsletter-form">
                <input type="email" placeholder="Enter Email Address" required>
                <button type="submit" class="btn" style="background-color:maroon;color:white;">Subscribe</button>
            </form>
        </div>
    </section>
    </main>
    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
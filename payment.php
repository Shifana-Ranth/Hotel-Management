<?php
    session_start();
    include("databasee.php");
    if(isset($_SESSION['loggedin']) ===false){
        header("Location: index.php");
        exit();
    }
    $showerror=false;
    $userid=isset($_SESSION['userid']) ? $_SESSION["userid"]:''; 
    $room_price = $_SESSION['room_price']; 
    $hid= $_SESSION['hid'];
    $rid= $_SESSION['rid'];
    $name = $_SESSION['name'];
    $phno = $_SESSION['phno'];
    $checkin = $_SESSION['checkin'];
    $checkout = $_SESSION['checkout'];
    $noofroom = $_SESSION['noofroom'];
    $room_type = $_SESSION['room_type'];
    $gst = $room_price * 0.12; 
    $service_charge = $room_price * 0.05; 
    $tourism_fee = 200; 
    $total_amount = $room_price + $gst + $service_charge + $tourism_fee;
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        try
        {
            $sql="INSERT INTO bookings(uidd,rid,hotel_id,checkin,checkout,namee,phno,no_of_rooms,roomtype,amount) VALUES ('$userid','$rid' ,'$hid','$checkin','$checkout','$name','$phno','$noofroom','$room_type','$total_amount')";
            mysqli_query($conn,$sql);
            $bid=mysqli_insert_id($conn);
            $_SESSION['booking_id']=$bid;
            $check_bill_sql = "SELECT * FROM bills WHERE booking_id = '$booking_id'";
            $bill_exists = mysqli_query($conn, $check_bill_sql);

            if (mysqli_num_rows($bill_exists) == 0) {
                $insert_bill = "INSERT INTO bills (booking_id,room_price , gst, service_fee, tourism_fee, total_amount) 
                                VALUES ('$bid', $room_price ,'$gst', '$service_charge', '$tourism_fee', '$total_amount')";
                mysqli_query($conn, $insert_bill);
            }
            header("location:msg.php");
        }
        catch(mysqli_sql_exception $e)
        {
            $showerror = "Cannot store data: " . $e->getMessage();
        } 
    }
?>
<!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> 
    <style>
        *{
            font-family: 'Times New Roman', Times, serif;
        }
        .payment-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #fff;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
            font-family: Arial, sans-serif;
        }
        .payment-container h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 20px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 16px;
        }
        .total {
            font-weight: bold;
            font-size: 18px;
            margin-top: 10px;
            border-top: 2px solid #ddd;
            padding-top: 10px;
        }
        .pay-btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            text-align: center;
            background-color: #246A73;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .pay-btn:disabled {
            background: #aaa;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php 
        if($showerror){
            echo '
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="position:relative;top:70px;color:black;font-weight:700;padding-left:70px;">
            <strong style="background-color:transparent;"><i style="color:red;background-color:transparent;" class="fa-solid fa-circle-exclamation"></i></strong> '.$showerror.'
            <button style="border:1px solid black;background-color:transparent;position:relative;left:70%;" type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true" style="background-color:transparent;font-size:1.2rem;">&times;</span>
            </button>
            </div>
            ';
        }
    ?>
    <audio id="audioPlayer" preload="auto">
        <source src="audio_file.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <div class="payment-container" style="line-height:50px;margin-top:100px;">
    <h1 style="text-align:center;"><b style="color: #246A73;">Payment Summary</b></h1>
    <div class="summary-item"><span><b>Room Price:</b></span><span>&#8377; <?php echo number_format($room_price, 2); ?></span></div>
    <div class="summary-item"><span><b>GST (12%):</b></span><span>&#8377; <?php echo number_format($gst, 2); ?></span></div>
    <div class="summary-item"><span><b>Service Charge (5%):</b></span><span>&#8377; <?php echo number_format($service_charge, 2); ?></span></div>
    <div class="summary-item"><span><b>Tourism Fee:</b></span><span>&#8377; <?php echo number_format($tourism_fee, 2); ?></span></div>
    <div class="summary-item total"><span>T<b>otal Amount:</b></span><span>&#8377; <?php echo number_format($total_amount, 2); ?></span></div>

    <form action="payment.php" method="post">
        <input type="hidden" name="hid" value="<?php echo $hid; ?>">
        <input type="hidden" name="checkin" value="<?php echo $checkin; ?>">
        <input type="hidden" name="checkout" value="<?php echo $checkout; ?>">
        <input type="hidden" name="noofroom" value="<?php echo $noofroom; ?>">
        <input type="hidden" name="room_type" value="<?php echo $room_type; ?>">
        <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
        <button type="submit" class="pay-btn">Pay Now</button>
    </form>
</div>
</body>
</html>

<?php
include("databasee.php");

$hotelResultsHTML = '';
$showerror = false;

$selectedState = $_POST['states'] ?? '';
$selecteddistrict = $_POST['district'] ?? '';
$checkin = $_POST['checkin'] ?? '';
$checkout = $_POST['checkout'] ?? '';

$s = "SELECT st_id FROM states WHERE stname='$selectedState'";
$stid = mysqli_query($conn, $s);
$row2 = mysqli_fetch_assoc($stid);
$sid = $row2['st_id'] ?? '';

$d = "SELECT dt_id FROM district WHERE dt_name='$selecteddistrict'";
$dtid = mysqli_query($conn, $d);
$row3 = mysqli_fetch_assoc($dtid);
$did = $row3['dt_id'] ?? '';

$checkinDate = DateTime::createFromFormat('Y-m-d', $checkin);
$checkoutDate = DateTime::createFromFormat('Y-m-d', $checkout);
$todayDate = new DateTime();
if ($checkinDate > $checkoutDate) {
    echo "error:Check-in date cannot be later than check-out date.";
    exit;
} elseif ($checkinDate <= $todayDate) {
    echo "error:Check-in must be after today.";
    exit;
} 
else {
    $sql = "SELECT * FROM hotels WHERE dt_id = $did AND st_id = $sid AND availability ='available'";
    $res = mysqli_query($conn, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $hotelResultsHTML .= '<h1 style="text-align:center;">Hotels Available</h1>';
        $i = 0;
        while ($hotel = mysqli_fetch_assoc($res)) {
            ob_start(); ?>
            <div class="serv2 ser">
                <?php if ($i % 2 == 0): ?>
                    <div class="ser1 se2" style="background-image: url('hphotos/<?php echo $hotel['himage_url']; ?>');border:4px solid #ffd700;width:35%;"></div>
                    <div class="ser1contentt" style="border:3px solid #246A73;">
                <?php else: ?>
                    <div class="ser1content" style="border:3px solid #246A73;">
                <?php endif; ?>
                    <h2><?php echo $hotel['hotel_name']; ?></h2>
                    <p><?php echo $hotel['description']; ?></p>
                    <p><?php echo $hotel['address']; ?></p>
                    <h2>₹<?php echo $hotel['hprice']; ?></h2>
                    <div class="ratediv">
                        <button style="background-color: #ffd700;border-radius:10px;margin-top:20px;">
                            <a href="rooms.php?hotelName=<?php echo urlencode($hotel['hotel_name']); ?>&hid=<?php echo $hotel['hotel_id']; ?>&selectedState=<?php echo urlencode($selectedState); ?>&selecteddistrict=<?php echo urlencode($selecteddistrict); ?>&sid=<?php echo urlencode($sid); ?>&did=<?php echo urlencode($did); ?>&checkin=<?php echo urlencode($checkin); ?>&checkout=<?php echo urlencode($checkout); ?>" style="text-decoration:none;color:red;background-color:transparent;">Explore Rooms</a>
                        </button>
                        <div class="rating"><?php echo $hotel['rating']; ?> <span>&#9733;</span></div>
                    </div>
                </div>
                <?php if ($i % 2 != 0): ?>
                    <div class="ser1 se2" style="background-image: url('hphotos/<?php echo $hotel['himage_url']; ?>');border:4px solid #ffd700;width:35%;"></div>
                <?php endif; ?>
            </div>
            <?php
            $hotelResultsHTML .= ob_get_clean();
            $i++;
        }
    } else {
        $hotelResultsHTML .= '<h1 style="text-align:center;">No hotels available</h1>';
    }

    echo $hotelResultsHTML;
}

mysqli_close($conn);
?>
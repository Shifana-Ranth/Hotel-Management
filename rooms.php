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
    $selectedState = isset($_GET['selectedState']) ? $_GET['selectedState'] : '';
    $selecteddistrict = isset($_GET['selecteddistrict']) ? $_GET['selecteddistrict'] : '';
    $sid = isset($_GET['sid']) ? $_GET['sid'] : '';
    $did = isset($_GET['did']) ? $_GET['did'] : '';
    $hid = isset($_GET['hid']) ? $_GET['hid'] : '';
    $checkin = isset($_GET['checkin']) ? $_GET['checkin'] : '';
    $checkout= isset($_GET['checkout']) ? $_GET['checkout'] : '';
    $hotelName = isset($_GET['hotelName']) ? $_GET['hotelName'] : '';
    $hotelDetails = null;
    if (!empty($hid)) {
        $query = "SELECT * FROM hotels WHERE hotel_id = $hid ";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $hotelDetails = mysqli_fetch_assoc($result);
        } else {
            echo "<h2>Hotel not found.</h2>";
            exit();
        }
    }
    $rooms = [];
    
    if (!empty($hid)) {
        $roomQuery="SELECT r.*, 
            r.quantity - IFNULL((
                SELECT SUM(b.no_of_rooms) 
                FROM bookings b 
                WHERE b.rid = r.rid 
                AND b.status != 'cancelled'
                AND ('$checkin' < b.checkout AND '$checkout' > b.checkin)
            ), 0) AS available_rooms
            FROM rooms r
            WHERE r.hotel_id = $hid 
            AND r.availability = 'Available'
            HAVING available_rooms > 0";
        $roomResult = mysqli_query($conn, $roomQuery);
        if ($roomResult && mysqli_num_rows($roomResult) > 0) {
            while ($room = mysqli_fetch_assoc($roomResult)) {
                $rooms[] = $room;
            }
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
    <link rel="stylesheet" href="styleroom.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> 
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <style>
        #map{
            position:relative;
            width:70%;
            height:300px;
            margin:auto;
            border:3px solid brown;
            border-radius:20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
<main style="margin-top:6rem;">
    <div class="hotel">
        <img class="himg" src="hphotos/<?php echo $hotelDetails['himage_url']; ?>" alt="<?php echo $hotelDetails['hotel_name']; ?>">
        <div class="hoteldes">
            <h2>
                <b><?php echo $hotelDetails['hotel_name']; ?></b>
                <span style="position:absolute; right:6%; color: #ffd700;">&#9733</span>
                <span style="position:absolute; right:1%; color: black;"><?php echo $hotelDetails['rating']; ?></span>
            </h2>
            <p><?php echo $hotelDetails['description']; ?></p>
            <b>Address:</b><p><?php echo $hotelDetails['address']; ?></p>
            <b>Phone:</b><p><?php echo $hotelDetails['hcontact_number']; ?></p>
            <b>Email:</b><p><?php echo $hotelDetails['hemail']; ?></p>
            <b style="font-size:20px;margin-bottom:20px;">Most popular facilities</b>
            <div class="facility">
                <p><i class="fa-solid fa-person-swimming"></i> Pool</p>
                <p><i class="fa-solid fa-utensils"></i>  BreakFast</p>
                <p><i class="fa-solid fa-taxi"></i> Parking</p>
                <p><i class="fa-solid fa-martini-glass-citrus"></i> Bar</p>
            </div>
        </div>
    </div>
    <br>
    <h1 style="text-align:center;">Location</h1>
    <div id="map"></div>
    <br>
    <center>
        <h2>Available Rooms</h2>
        <input type="hidden" id="hotel-id" value="<?php echo $hid; ?>">
        <?php if (!empty($rooms)): ?>
            <?php foreach ($rooms as $room): ?>
                <div class="room" style="justify-content: space-evenly;width:80%;">
                    <img class="rimg" src="<?php echo $room['rimage']; ?>" alt="Room Image">
                    <div class="roomdes">
                        <b><?php echo $hotelDetails['hotel_name']; ?></b>
                        <h1 style="font-weight:900;"><?php echo $room['r_type']; ?></h1>
                        <p style="text-decoration: underline;color:blue;"><?php echo $selecteddistrict; ?></p>
                        <b>Address:</b><p><?php echo $hotelDetails['address']; ?></p>
                        <b>Phone:</b><p><?php echo $hotelDetails['hcontact_number']; ?></p>
                        <button type="submit" style="background-color:#ffd700;color:white;border-radius:10px;">
                            <a style="background-color:#ffd700;color:white;text-decoration:none;
                            "href="reserv.php?hotelName=<?php echo urlencode($hotelName); ?>&hid=<?php echo urlencode($hid); ?>&checkin=<?php echo urlencode($checkin); ?>&checkout=<?php echo urlencode($checkout); ?>&rid=<?php echo urlencode($room['rid']); ?>">Book Now</a>    
                        <!-- <a href="reserv.phphref="rooms.php?hotelName=' . urlencode($hotelName) . '&hid=' . urlencode($hid) .'&selectedState=' . urlencode($selectedState) . '&selecteddistrict=' . urlencode($selecteddistrict) . '&sid=' . urlencode($sid) . '&did=' . urlencode($did) . '&checkin=' . urlencode($checkin) . '&checkout=' . urlencode($checkout) . '>Check out</a> -->
                        </button>
                    </div>
                    <div class="roomextra" style="text-align:left;padding:10px;">
                        <h2><span style="color: #ffd700;">&#9733</span>
                            <span style="color: black;"><?php echo $room['rating']; ?></span>
                        </h2>
                        <ul class="rul" >
                            <li class="rli">&#x2713 <?php echo $room['bed_type']; ?></li>
                            <li class="rli">&#x2713 With Breakfast</li>
                            <li class="rli">&#x2713 Restaurent</li>
                            <li class="rli">&#x2713 Swimming pool</li>
                            <li class="rli">&#x2713 Best View</li>
                        </ul>
                        <p style="font-weight:800;font-size:30px;">₹ <?php echo $room['price_per_night']; ?></p>
                    </div>
                </div>
                <br>
            <?php endforeach; ?>
        <?php else: ?>
            <p><h1>No rooms available..kindly look for other dates..<h1></p>
        <?php endif; ?>
    </center>
    <hr>
    <div class="guest-reviews">
        <div class="title">Guest reviews</div>
        <div class="rating-section">
            <div class="rating-box">6.8</div>
            <div class="rating-text">Pleasant · 366 reviews</div>
        </div>
    
        <div class="categories">
            <div class="category">
                <span>Staff</span>
                <div class="bar-container"><div class="bar" style="width: 76%;"></div></div>
                <span class="score">7.6</span>
            </div>
            <div class="category">
                <span>Comfort</span>
                <div class="bar-container"><div class="bar" style="width: 76%;"></div></div>
                <span class="score">7.6</span>
            </div>
            <div class="category">
                <span>Facilities</span>
                <div class="bar-container"><div class="bar" style="width: 71%;"></div></div>
                <span class="score">7.1</span>
            </div>
            <div class="category">
                <span>Value for money</span>
                <div class="bar-container"><div class="bar" style="width: 76%;"></div></div>
                <span class="score">7.6</span>
            </div>
            <div class="category">
                <span>Cleanliness</span>
                <div class="bar-container"><div class="bar" style="width: 65%;"></div></div>
                <span class="score">6.5</span>
            </div>
            <div class="category">
                <span>Location</span>
                <div class="bar-container"><div class="bar" style="width: 65%;"></div></div>
                <span class="score">6.5</span>
            </div>
            <div class="category">
                <span class="wifi-text">Free Wifi ↓</span>
                <div class="bar-container"><div class="bar wifi-bar" style="width: 50%;"></div></div>
                <span class="score">5.0</span>
            </div>
        </div>
    </div>
    <hr>
    <h3 style="padding-left:4%;padding-top:2%;">Popular with travelers from India</h3>
    <div class="hoteldiv">
        <div class="ddd">
            <ul>
                <li>New Delhi hotels</li>
                <li>Cochin hotels</li>
                <li>Ahmedabad hotels</li>
                <li>Surat hotels</li>
            </ul>
        </div>
        <div class="ddd">
            <ul>
                <li>Chennai hotels</li>
                <li>Coimbatore hotels</li>
                <li>Madurai hotels</li>
                <li>Trichy hotels</li>
            </ul>
        </div>
        <div class="ddd">
            <ul>
                <li>Mumabi hotels</li>
                <li>Nagpur hotels</li>
                <li>Thane hotels</li>
                <li>Pune hotels</li>
            </ul>
        </div>
        <div class="ddd">
            <ul>
                <li>Duragapur hotels</li>
                <li>Asansol hotels</li>
                <li>Kolkata hotels</li>
                <li>Howrah hotels</li>
            </ul>
        </div>
    </div>
    <hr>
    <div class="pointdiv">
        Countries .
        Regions .
        Cities .
        Districts .
        Airports .
        Hotels .
        Places of interest .
        Vacation Homes .
        Apartments .
        Resorts .
        Villas .
        Hostels
        B&Bs .
        Guest Houses .
        Unique places to stay .
        All destinations .
        All flight destinations .
        All car rental locations .
        All vacation destinations .
        Guides .
        Discover .
        Reviews .
        Discover monthly stays
    </div>
</main>
<?php include 'footer.php'; ?>
<style>
    .foottwo{
        height:300px;
    }
    </style>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script src="latlong.js?"></script>
<script src="getmap.js?v=1"></script>
</body>
</html>
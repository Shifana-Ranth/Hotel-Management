<?php
    session_start();
    include("databasee.php");
    $inactive=300;
    if (isset($_SESSION["last_activity"])) {
        $session_life = time() - $_SESSION["last_activity"];
        
        if ($session_life > $inactive) {
            session_unset();
            session_destroy();
            header("Location: index.php");
            exit();
        }
    }
    $showerror=false;
    $states = [];
    $sqlst="SELECT * FROM states";
    $res=mysqli_query($conn,$sqlst);
    while ($row = mysqli_fetch_assoc($res)) {
        $states[] = $row['stname'];
    }
    $selectedState = isset($_GET['states']) ? $_GET['states'] : ( isset($_POST['states']) ? $_POST['states'] : '');
    $s = "SELECT st_id FROM states WHERE stname='$selectedState'";
    $stid = mysqli_query($conn, $s);
    $row2 = mysqli_fetch_assoc($stid);
    $sid = $row2['st_id'] ?? '';
    $districts = [];
    if ($sid) {
        $sqldt = "SELECT * FROM district WHERE st_id=$sid";
        $res = mysqli_query($conn, $sqldt);
        while ($row = mysqli_fetch_assoc($res)) {
            $districts[] = $row['dt_name'];
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $selectedState = isset($_GET['states']) ? $_GET['states'] : ( isset($_POST['states']) ? $_POST['states'] : '');
    
        $s = "SELECT st_id FROM states WHERE stname='$selectedState'";
        $stid = mysqli_query($conn, $s);
        $row2 = mysqli_fetch_assoc($stid);
        $sid = $row2['st_id'] ?? '';
    
        $districts = [];
        if ($sid) {
            $sqldt = "SELECT * FROM district WHERE st_id=$sid";
            $res = mysqli_query($conn, $sqldt);
            while ($row = mysqli_fetch_assoc($res)) {
                $districts[] = $row['dt_name'];
            }
        }

        if (isset($_POST['search'])) {
            $selecteddistrict = $_POST['district'] ?? '';
            $checkin = $_POST['checkin'] ?? '';
            $checkout = $_POST['checkout'] ?? '';
            $d = "SELECT dt_id FROM district WHERE dt_name='$selecteddistrict'";
            $dtid = mysqli_query($conn, $d);
            $row3 = mysqli_fetch_assoc($dtid);
            $did = $row3['dt_id'] ?? '';
    
            $checkinDate = DateTime::createFromFormat('Y-m-d', $checkin);
            $checkoutDate = DateTime::createFromFormat('Y-m-d', $checkout);
            $todayDate = new DateTime();
    
            if ($checkinDate > $checkoutDate) {
                $showerror = "Check-in date cannot be later than check-out date.";
            } 
            else if($checkinDate <= $todayDate) {
                $showerror = "Check-in date must be after today's date.";
            } 
            else {
                $showerror = false;
                echo  $sid;
                echo $selectedState;
                echo  $did;
                echo $selecteddistrict;
                echo  $checkin;
                echo "helllo";
                echo  $checkout;
                $sql = "SELECT * FROM hotels WHERE dt_id = $did AND st_id = $sid";
                $res = mysqli_query($conn, $sql);
                // header("Location: thanjavur.php?selectedState=$selectedState&selecteddistrict=$selecteddistrict&sid=$sid&did=$did&checkin=$checkin&checkout=$checkout");
                // exit(); 
            }
        }
    }
    $_SESSION["last_activity"] = time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VoyageVista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styletamil.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> 
</head>
<body>
    <header>
        <div class="navbar">
            <div class="sidebar">
                <input type="checkbox" id="check">
                <div class="btnone">
                    <label for="check">
                       <i class="fa-solid fa-bars"></i>
                    </label>
                </div>
                <div class="sidemenu">
                    <div class="name">
                        <h1>VoyageVista</h1>
                    </div>
                    <div class="btntwo">
                        <label for="check">
                          <i class="fa-solid fa-xmark"></i>
                        </label>
                    </div>
                    <div class="menu">
                        <ul>
                        <li><i class="fa-solid fa-house"></i><a href="index.php">Home</a></li>
                        <li><i class="fa-solid fa-address-card"></i><a href="aboutus.php">About us</a></i></li>
                        <li><i class="fa-solid fa-message"></i></i><a href="contactus.php">Contact us</a></li>
                        <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { 
                            echo '<li><i class="fa-solid fa-circle-user"></i><a href="logout.php">Logout</a></li>';
                            echo '<li><i class="fa-solid fa-user"></i><a href="my.php">Profile</a></li>';
                         } 
                        else{
                            echo '<li><i class="fa-solid fa-user"></i><a href="signin.php">Sign in</a></li>
                            <li><i class="fa-solid fa-circle-user"></i><a href="login.php">Login</a></li>';}?>
                        <li class="destli"><i class="fa-solid fa-globe"></i>
                            <details>
                                <summary>Destination</summary>
                                <p class="sump"><a href="tamilnadu.html">Tamil Nadu</a></p>
                                <p class="sump"><a href="#">Maharashtra</a></p>
                                <p class="sump"><a href="#">Delhi</a></p>
                                <p class="sump"><a href="#">West Bengal</a></p>
                            </details>
                        </li>
                        <li><i class="fa-solid fa-question"></i><a href="faq.php">FAQ</a></li>
                        </ul>
                    </div> 
                </div>
            </div>
            <div class="logo">
                <img src="logo.jpeg" >
                <p>VoyageVista</p>
            </div>
            <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { echo '<div class="login"><a href="logout.php">logout</a></div>';}
            else{
                echo '<div class="login"><a href="login.php">login</a></div>';
            } ?>

            <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { ?>
    <h2 class="login"><a href="my.php"><i class="fa-solid fa-user"></i><?php echo " ". ($_SESSION['firstname'] ? $_SESSION['firstname'] : $_SESSION['username']);?></a></h2>
<?php } else { ?>
    <button class="signin"><i class="fa-solid fa-user"></i><a href="signin.php">Sign In</a></button>
<?php } ?>
            <!-- <div class="login"><a href="loginpage.html">login</a></div>
            <button class="signin"><i class="fa-solid fa-user"></i><a href="signin.html">Sign In</a></button> -->
        </div>
    </header>
    <?php 
    if($showerror==false)
    {
        echo '
<div class="alert alert-success alert-dismissible fade show" role="alert" style="top:20px;">
  Successfull search
</div>
';
    }
    if($showerror){
        echo '
<div class="alert alert-danger alert-dismissible fade show" role="alert" style="top:20px;">
  Error '.$showerror.'
</div>
';
    }
?>
<main>
    <div class="abt">
        <p class="abtp">Its great to be at vacation</p>
        <h1 style="margin-bottom:1rem;">Room Details</h1>
        <a href="thanjavur.php">thanjavur</a>
        <div class="searchhotel">
            <form action="tamilnadu.php" method="post">
                <select id="states" name="states" onchange="this.form.submit()" required>
                    <option value="">Select State</option>
                    <?php foreach ($states as $state) { ?>
                        <option value="<?php echo $state; ?>" <?php if ($state == $selectedState) { echo 'selected'; } ?>><?php echo $state; ?></option>
                    <?php } ?>
                </select>
                <select id="district" name="district" required>
                <option value="">Select District</option>
                    <?php foreach ($districts as $district) { ?>
                        <option value="<?php echo $district; ?>"><?php echo $district; ?></option>
                    <?php } ?>
                </select>
                <input type="date" name="checkin" required>
                <input type="date" name="checkout" required>
                <button type="submit" name='search'>Search</button>
            </form>
        </div>
    </div>
    <hr>
               
    <h1 style="padding-left:4%;">Popular Stays</h1>
    <div class="stay">
        <div class="stay1 st" ><div class="st1 stt">
            <div class="rating">
                4
                <span>&#9733;</span> 
                <span>&#9733;</span>
                <span>&#9733;</span>
                <span>&#9733;</span>
                <span>&#9733;</span>
            </div>
        </div><h3>Luxury 5 star hotel</h3><p><i class="fa-solid fa-location-dot"></i>  Lorem ipsum dolor sit amet</p><div style="text-align: center; background-color:transparent ;"><button style="width:30%; background-color: #246A73; margin-bottom: 0.5rem;">Book Now</button></div></div>
        <div class="stay2 st" ><div class="st2 stt">
            <div class="rating">
                4.5
                <span>&#9733;</span> 
                <span>&#9733;</span>
                <span>&#9733;</span>
                <span>&#9733;</span>
                <span>&#9733;</span>
            </div>
        </div><h3>Luxury 5 star hotel</h3><p><i class="fa-solid fa-location-dot"></i>  Lorem ipsum dolor sit amet.</p><div style="text-align: center; background-color:transparent ;"><button style="width:30%; background-color: #246A73; margin-bottom: 0.5rem;">Book Now</button></div></div>
        <div class="stay3 st" ><div class="st3 stt">
            <div class="rating">
                4.7
                <span>&#9733;</span> 
                <span>&#9733;</span>
                <span>&#9733;</span>
                <span>&#9733;</span>
                <span>&#9733;</span>
            </div>
        </div><h3>Luxury 5 star hotel</h3><p><i class="fa-solid fa-location-dot"></i>  Lorem ipsum dolor sit amet.</p><div style="text-align: center; background-color:transparent ;"><button style="width:30%; background-color: #246A73; margin-bottom: 0.5rem;">Book Now</button></div></div>
    </div> 
    <hr>
    <div class="service">
        <h1 style="padding-left:4%;padding-top:2%; margin-bottom: 1%;">Our Services</h1>
        <div class="serv1 serv">
            <div class="ser1contenty "><h2 style="margin-bottom: 3%;">Restaurent & Cafee</h2><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque, debitis!Lorem ipsum dolor, sit amet consectetur adipisicing elit. Adipisci, nonLorem ipsum dolor, sit amet consectetur adipisicing elit. Adipisci, non</p></div>
            <div class="ser11 se11"></div>
        </div>
        <div class="serv2 serv">
            <div class="ser11 se22"></div>
            <div class="ser1contenty  "><h2 style="margin-bottom: 3%;">Swimming Pool</h2><p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Adipisci, non.Lorem ipsum dolor, sit amet consectetur adipisicing elit. Adipisci, nonLorem ipsum dolor, sit amet consectetur adipisicing elit. Adipisci, non</p></div>
        </div>
        <div class="serv3 serv" >
            <div class="ser1contenty "><h2 style="margin-bottom: 3%;">Club</h2><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Culpa, soluta.Lorem ipsum dolor, sit amet consectetur adipisicing elit. Adipisci, nonLorem ipsum dolor, sit amet consectetur adipisicing elit. Adipisci, non</p></div>
            <div class="ser11 se33"></div>
        </div>
    </div>
    <br>
    <hr>
    <h3 style="padding-left:4%;padding-top:2%;">Popular with travelers from India</h3>
    <div class="hoteldiv">
        <div class="ddd">
            <ul>
                <li>Srinagar hotels</li>
                <li>Srinagar hotels</li>
                <li>Srinagar hotels</li>
                <li>Srinagar hotels</li>
            </ul>
        </div>
        <div class="ddd">
            <ul>
                <li>Srinagar hotels</li>
                <li>Srinagar hotels</li>
                <li>Srinagar hotels</li>
                <li>Srinagar hotels</li>
            </ul>
        </div>
        <div class="ddd">
            <ul>
                <li>Srinagar hotels</li>
                <li>Srinagar hotels</li>
                <li>Srinagar hotels</li>
                <li>Srinagar hotels</li>
            </ul>
        </div>
        <div class="ddd">
            <ul>
                <li>Srinagar hotels</li>
                <li>Srinagar hotels</li>
                <li>Srinagar hotels</li>
                <li>Srinagar hotels</li>
            </ul>
        </div>
    </div>
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
<footer>
    <div class="footone">
        <p>Dont Know Which Destination To Choose?</p>
        <h4>Call Us (+91) (9597422515)</h4>
    </div>
    <div class="foottwo">
        <div class="one footdiv" >
            <div class="log">
                <img src="logo.jpeg">
                <p>VoyageVista</p>
            </div>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Cupiditate numquores a culpa sunt qui corporis fugit quam.s</p>
            <div class="socmedia">
                <i class="fa-brands fa-facebook"></i>
                <i class="fa-brands fa-instagram"></i>
                <i class="fa-brands fa-whatsapp"></i>
                <i class="fa-brands fa-youtube"></i>
            </div>
        </div>
        <div class="two footdiv">Destination
            <ul>
                <li class="lili">Tamil Nadu</li>
                <li class="lili">Maharashtra</li>
                <li class="lili">Delhi</li>
                <li class="lili">West Bengal</li>
            </ul>
        </div>
        <div class="three footdiv">Useful Link
            <ul>
                <li class="lili">About us</li>
                <li class="lili">Contact US</li>
                <li class="lili">Home</li>
                <li class="lili">Signin</li>
                <li class="lili">F.A.Q</li>
            </ul>
        </div>
        <div class="four footdiv">Contact
            <ul>
                <li class="lili"><i class="fa-solid fa-phone"></i>   (+91) (9597422515)</li>
                <li class="lili"><i class="fa-solid fa-envelope"></i>    voyagevista@gmail.com</li>
                <li class="lili"><i class="fa-solid fa-location-dot"></i>               No:345,North Street,Delhi</li>
            </ul>
        </div>
    </div>
    <hr>
    <div class="footthree">
        @Copywrite Travel Agency 2025.Design by Shifana
    </div>
</footer>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VoyageVista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styleheadmanager.css">
</head>

<body>
    <header>
        <div class="navbar">
            <div class="sidebar">
                <input type="checkbox" id="check">
                <div class="btnone" style="height:40px;">
                    <label for="check">
                       <i class="fa-solid fa-bars" ></i>
                    </label>
                </div>
                <div class="sidemenu">
                    <div class="name">
                        <h1>Admin Panel</h1>
                    </div>
                    <div class="btntwo">
                        <label for="check">
                          <i class="fa-solid fa-xmark"></i>
                        </label>
                    </div>
                    <div class="menu">
                        <ul>
                        <li><i class="fa-solid fa-house"></i><a href="managerhome.php">Home</a></li>
                        <li><i class="fa-solid fa-user"></i><a href="mymanager.php">Profile</a></li>
                        <li><i class="fa-solid fa-hotel"></i><a href="viewhotel.php">View Hotels</a></i></li>
                        <li><i class="fa-solid fa-money-check-dollar"></i><a href="viewbookingman.php">View Bookings</a></li>
                        <li><i class="fa-solid fa-person-walking-luggage"></i><a href="viewuser.php">View Users</a></li>
                        <li><i class="fa-solid fa-person-booth"></i><a href="viewroom.php">View Rooms</a></li>
                        <li><i class="fa-solid fa-circle-user"  style="background-color:transparent;"></i><a href="logout.php">Logout</a></li>
                        </ul>
                    </div> 
                </div>
            </div>
            <div class="logo">
                <img src="logo.jpeg" >
                <p>VoyageVista</p>
            </div>
            <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { echo '<h2 class="login"><a href="my.php"><i class="fa-solid fa-user" style="color:darkblue;"></i>  '.(isset($_SESSION["firstname"]) ? $_SESSION["firstname"] : $_SESSION["username"]).'</a></h2>';}
            else{
                echo '<div class="login"><i class="fa-solid fa-circle-user" style="color:blue"></i><a href="login.php"  style="color:blue">  login</a></div>';
            } ?>
            <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { ?>
                <div class="login"><a href="logout.php"><i class="fa-duotone fa-solid fa-power-off"></i> logout</a></div>
            <?php } else { ?>
            <button class="signin"><i class="fa-solid fa-user"></i><a href="signin.php">Sign In</a></button>
            <?php } ?>
            </div>
    </header>
</body>

</html>
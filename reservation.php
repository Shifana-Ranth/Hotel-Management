
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking Form</title>
    <link rel="stylesheet" href="stylereserv.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        *{
            font-family: 'Times New Roman', Times, serif;
        }
    </style>
</head>
<body>
<header>
        <div class="navbar">
            <div class="sidebar">
                <input type="checkbox" id="check">
                <div class="btnone" style="height:20px;">
                    <label for="check">
                       <i class="fa-solid fa-bars"></i>
                    </label>
                </div>
                <div class="sidemenu">
                    <div class="name">
                        <h1>VoyyyyVista</h1>
                    </div>
                    <div class="btntwo">
                        <label for="check">
                          <i class="fa-solid fa-xmark"></i>
                        </label>
                    </div>
                    <div class="menu">
                        <ul>
                        <li><i class="fa-solid fa-house"></i><a href="index.php">Home</a></li>
                        <li><i class="fa-solid fa-address-card"></i><a href="aboutus.html">About us</a></i></li>
                        <li><i class="fa-solid fa-message"></i></i><a href="contactus.html">Contact us</a></li>
                        <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { 
                            echo '<li><i class="fa-solid fa-circle-user"></i><a href="logout.php">Logout</a></li>';
                         } 
                        else{
                            echo '<li><i class="fa-solid fa-user"></i><a href="signin.php">Sign in</a></li>
                            <li><i class="fa-solid fa-circle-user"></i><a href="login.php">Login</a></li>';}?>
                        <!-- <li><i class="fa-solid fa-user"></i><a href="signin.php">Sign in</a></li>
                        <li><i class="fa-solid fa-circle-user"></i><a href="login.php">Login</a></li> -->
                        <li class="destli"><i class="fa-solid fa-globe"></i>
                            <details>
                                <summary>Destination</summary>
                                <p class="sump"><a href="tamilnadu.html">Tamil Nadu</a></p>
                                <p class="sump"><a href="#">Maharashtra</a></p>
                                <p class="sump"><a href="#">Delhi</a></p>
                                <p class="sump"><a href="#">West Bengal</a></p>
                            </details>
                        </li>
                        <li><i class="fa-solid fa-question"></i><a href="faq.html">FAQ</a></li>
                        </ul>
                    </div> 
                </div>
            </div>
            <div class="logo">
                <img src="logo.jpeg" >
                <p>VoyyyyVista</p>
            </div>
            <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { echo '<div class="login"><a href="logout.php">logout</a></div>';}
            else{
                echo '<div class="login"><a href="login.php">login</a></div>';
            } ?>

            <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { ?>
    <h2 class="login"><i class="fa-solid fa-user"></i><?php echo " ". $_SESSION['username'];?></h2>
<?php } else { ?>
    <button class="signin"><i class="fa-solid fa-user"></i><a href="signin.php">Sign In</a></button>
<?php } ?>

        </div>
    </header>
    <main >
    Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quia, nesciunt.
    Lorem ipsum dolor sit, amet consectetur adipisicing elit. In enim amet non rerum? Illum deserunt delectus, facilis beatae id neque est harum, fugiat voluptatibus nam nulla corrupti laboriosam iure alias, perspiciatis possimus sequi repellendus assumenda ratione? Labore est nobis explicabo? Quas dignissimos pariatur quis expedita nihil perspiciatis temporibus quo, magni sint repellat laudantium iure saepe eaque itaque quam ex eveniet fugit ullam. Atque, vero aut suscipit sed minus quae optio! Unde optio, commodi id aliquid nulla expedita voluptate cupiditate sint minus numquam repellendus molestiae fuga voluptatum ex harum maxime! Neque doloremque iste nesciunt ipsa a quasi deleniti rem perspiciatis sed!
    </main>
    <!-- <center>
    <h1 style="background-color:powderblue;">Profile Page</h1>
    <div class="container">
        
        <form action="reservation.php" method="post">
            <h2>Kindly,Fill Up Your Details</h2>

            <div class="row">
                <div class="column">
                    <label>Full Name *</label>
                    <input type="text" name="fullname" required>
                </div>
            </div>

            <div class="row">
                <div class="column">
                    <label>Address *</label>
                    <input type="text" name="address" required>
                </div>
            </div>

            <div class="row">
                <div class="column">
                    <label>City *</label>
                    <input type="text" name="city" required>
                </div>
                <div class="column">
                    <label>State *</label>
                    <input type="text" name="state"required>
                </div>
                <div class="column">
                    <label>Pin Code *</label>
                    <input type="number" name="pincode" required>
                </div>
            </div>

            <div class="row">
                <div class="column">
                    <label>Phone *</label>
                    <input type="number" name="phno"required>
                </div>
                <div class="column">
                    <label>Email Address *</label>
                    <input type="email" name="email" required>
                </div>
            </div>

            <button type="submit">Submit</button>
        </form>
    </div>
    </center> -->
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
                    <li>Tamil Nadu</li>
                    <li>Maharashtra</li>
                    <li>Delhi</li>
                    <li>West Bengal</li>
                </ul>
            </div>
            <div class="three footdiv">Useful Link
                <ul>
                    <li>About us</li>
                    <li>Contact US</li>
                    <li>Home</li>
                    <li>Signin</li>
                    <li>F.A.Q</li>
                    <li>Privacy Policy</li>
                </ul>
            </div>
            <div class="four footdiv">Contact
                <ul>
                    <li><i class="fa-solid fa-phone"></i>   (+91) (9597422515)</li>
                    <li><i class="fa-solid fa-envelope"></i>    voyagevista@gmail.com</li>
                    <li><i class="fa-solid fa-location-dot"></i>               No:345,North Street,Delhi</li>
                </ul>
            </div>
        </div>
        <div class="footthree">
            @Copywrite Travel Agency 2025.Design by Shifana
        </div>
    </footer>
</html>
</body>

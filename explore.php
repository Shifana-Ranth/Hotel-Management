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
    $showerror=false;
    $ans=false;
    $states = [];
    $hotelResultsHTML="";
    $sqlst="SELECT * FROM states";
    $res=mysqli_query($conn,$sqlst);
    while ($row = mysqli_fetch_assoc($res)) {
        $states[] = $row['stname'];
    }
    $selectedState = isset($_GET['states']) ? $_GET['states'] : ( isset($_POST['states']) ? $_POST['states'] : '');
    $selecteddistrict = isset($_GET['district']) ? $_GET['district'] : ( isset($_POST['district']) ? $_POST['district'] : '');
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
    <link rel="stylesheet" href="styleexplore.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .card{
    width:80%;
    background:linear-gradient(135deg,#00feba,#5b548a);
    color:#fff;
    border-radius:20px;
    padding:10px;
    display: flex;
    margin:auto;
    flex-direction:row;
    justify-content: space-around;
    align-items: center;
    text-align:center;
}
.weather{
    background-color:transparent;
}
.weather-icon{
    width:100px;
    background-color:transparent;
}
.weather h1{
    font-size:50px;
    font-weight:400;
    background-color:transparent;
    color:White;
}
.weather h2{
    font-size:35px;
    font-weight:300;
    margin-top:-10px;
    background-color:transparent;
    color:White;
}
.details{
    display:flex;
    justify-content:space-between;
    align-items: center;
    width:50%;
    background-color:transparent;
}
.col{
    display:flex;
    align-items: center;
    text-align: left;
    background-color:transparent;
}
.col div{
    background-color:transparent;
    color:white;
}
.col p{
    background-color:transparent;
    color:white;
}
.col img{
    width:90px;
    margin-right:10px;
    background-color:transparent;
}
.humidity,.wind{
    font-size:24px;
    margin-top:-6px;
    background-color:transparent;
}
        </style>
</head>
<body>
<?php include 'header.php'; ?>
<?php 
        echo '
        <div id="error-div" class="alert alert-danger alert-dismissible fade show" role="alert" style="display:none;position:relative;top:60px;color:black;font-weight:700;padding-left:70px;">
        <strong style="background-color:transparent;"><i style="color:red;background-color:transparent;" class="fa-solid fa-circle-exclamation"></i></strong>
        <button style="border:1px solid black;background-color:transparent;position:relative;left:90%;" type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true" style="background-color:transparent;font-size:1.2rem;">&times;</span>
        </button>
        </div>
        ';
?>
<main>
    <div class="abt">
        <p class="abtp">Its great to be at vacation</p>
        <h1 style="margin-bottom:1rem;">Hotel Details</h1>
        <form id="searchhotel" class="searchhotel" method="post">
                <div style="display:flex;flex-direction:column;">
                <label for="checkin">Select State<span style="background-color:transparent;color:red;">*</span></label>
                <select id="states" name="states" required>
                    <option value="">Select State</option>
                    <?php foreach ($states as $state) { ?>
                        <option value="<?php echo $state; ?>" <?php if ($state == $selectedState) { echo 'selected'; } ?>><?php echo $state; ?></option>
                    <?php } ?>
                </select>
                </div>
                <div style="display:flex;flex-direction:column;">
                <label for="checkin">Select District<span style="background-color:transparent;color:red;">*</span></label>
                <select id="district" name="district" required>
                    <option value="">Select District</option>
                    <?php foreach ($districts as $district) { ?>
                        <option value="<?php echo $district; ?>" <?php if ($district == $selecteddistrict) { echo 'selected'; } ?>><?php echo $district; ?></option>
                    <?php } ?>
                </select>
                </div>
                <div style="display:flex;flex-direction:column;">
                <label for="checkin">Check-in<span style="background-color:transparent;color:red;">*</span></label>
                <input type="date" name="checkin" id="checkin" required>
                </div>
                <div style="display:flex;flex-direction:column;">
                <label for="checkin">Check-out<span style="background-color:transparent;color:red;">*</span></label>
                <input type="date" name="checkout" id="checkout" required>
                </div>
                <button type="submit" name='search' style="border-radius:5px;margin-top:22px;">Search</button>
            </form>
    </div>
    <h1 style="text-align:center;"> Weather</h1>
    <div class="card" id="card">
        <div class="weather">
            <img src="images/rain.png" class="weather-icon">
            <h1 class="temp"></h1>
            <h2 class="city"></h2>
        </div>
        <div class="details">
            <div class="col">
                <img src="images/humidity.png">
                <div>
                    <p class="humidity"></p>
                    <p>Humidity</p>
                </div>
            </div>
            <div class="col">
                <img src="images/wind.png">
                <div>
                    <p class="wind"></p>
                    <p>Wind Speed</p>
                </div>
            </div>
        </div>
    </div>
    <div class="search-results" id="search-results"> 
    </div>
    <hr>
    <div class="service">
        <h1 style="padding-left:4%;padding-top:2%; margin-bottom: 1%;">Our Services</h1>
        <div class="serv1 serv">
            <div class="ser1contenty "><h2 style="margin-bottom: 3%;">Restaurent & Cafee</h2><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque, debitis!Lorem ipsum dolor, sit amet consectetur adipisicing elit. Adipisci, nonLorem ipsum dolor, sit amet consectetur adipisicing elit. Adipisci, non</p></div>
            <div class="ser11 se11" style="border-radius:20px;"></div>
        </div>
        <div class="serv2 serv">
            <div class="ser11 se22" style="border-radius:20px;"></div>
            <div class="ser1contenty  "><h2 style="margin-bottom: 3%;">Swimming Pool</h2><p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Adipisci, non.Lorem ipsum dolor, sit amet consectetur adipisicing elit. Adipisci, nonLorem ipsum dolor, sit amet consectetur adipisicing elit. Adipisci, non</p></div>
        </div>
        <div class="serv3 serv" >
            <div class="ser1contenty "><h2 style="margin-bottom: 3%;">Club</h2><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Culpa, soluta.Lorem ipsum dolor, sit amet consectetur adipisicing elit. Adipisci, nonLorem ipsum dolor, sit amet consectetur adipisicing elit. Adipisci, non</p></div>
            <div class="ser11 se33" style="border-radius:20px;"></div>
        </div>
    </div>
    <hr>
    <h1  style="text-align: center;">Features</h1>
    <div class="features">
        <div class="info">
            <div class="info1" style="border-right:1px solid black;">
                <i class="fa-solid fa-bed"></i>
                <b style="font-size: 1.2rem;">Comfort Beds</b>
                <p>Lorem ipsum dolor sit amet.</p>
            </div>
            <div class="info1" style="border-right:1px solid black;">
                <i class="fa-solid fa-umbrella-beach"></i>
                <b style="font-size: 1.2rem;">Beach</b>
                <p>Lorem ipsum dolor sit amet.</p>
            </div>
            <div class="info1" style="border-right:1px solid black;">
                <i class="fa-solid fa-utensils"></i>
                <b style="font-size: 1.2rem;">Dining</b>
                <p>Lorem ipsum dolor sit amet.</p>
            </div>
        </div>
        <div class="info">
            <div class="info1" style="border-right:1px solid black;">
                <i class="fa-solid fa-dumbbell"></i>
                <b style="font-size: 1.2rem;">Parking</b>
                <p>Lorem ipsum dolor sit amet.</p>
            </div>
            <div class="info1" style="border-right:1px solid black;">
                <i class="fa-solid fa-person-swimming"></i>
                <b style="font-size: 1.2rem;">Swimming pool</b>
                <p>Lorem ipsum dolor sit amet.</p>
            </div>
            <div class="info1" style="border-right:1px solid black;">
                <i class="fa-solid fa-dumbbell"></i>
                <b style="font-size: 1.2rem;">Fitness</b>
                <p>Lorem ipsum dolor sit amet.</p>
            </div>
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
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    console.log("huhuuuu");
    const stateSelect = document.getElementById("states");
    const districtSelect = document.getElementById("district");
    stateSelect.addEventListener("change", function() {
        const selectedState = this.value;
        districtSelect.innerHTML = "<option value=''>Loading...</option>";
        if (selectedState !== "") {
            fetch("get_districts.php?state=" + encodeURIComponent(selectedState))
                .then(response => response.json())
                .then(data => {
                    districtSelect.innerHTML = "<option value=''>Select District</option>";
                    data.forEach(function(district) {
                        const option = document.createElement("option");
                        option.value = district;
                        option.textContent = district;
                        districtSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error("Error fetching districts:", error);
                    districtSelect.innerHTML = "<option value=''>Select District</option>";
                });
        } else {
            districtSelect.innerHTML = "<option value=''>Select District</option>";
        }
    });
});
</script>

<script>
document.getElementById("searchhotel").addEventListener("submit", function(e) {
    e.preventDefault();
    console.log("hyytuytyu");
    const state = document.getElementById("states").value;
    const district = document.getElementById("district").value;
    const checkin = document.getElementById("checkin").value;
    const checkout = document.getElementById("checkout").value;

    const formData = new FormData();
    formData.append("states", state);
    formData.append("district", district);
    formData.append("checkin", checkin);
    formData.append("checkout", checkout);
    formData.append("search", "1");

    fetch("search_hotels.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.startsWith("error:")) {
            var errorMessage = data.substring(6);
            console.log(errorMessage);
            const s= document.getElementById('error-div');
            console.log("HELLO",s);
            document.getElementById('error-div').style.display = "block";
            document.getElementById('error-div').innerHTML = ` ${errorMessage}`;
            document.getElementById("search-results").innerHTML = "";
        } else {
            document.getElementById('error-div').style.display = "none";
            document.getElementById("search-results").innerHTML = data;
            var resultsSection = document.getElementById("card");
            if (resultsSection) {
                resultsSection.scrollIntoView({ behavior: "smooth" });
            }
        }
        if(district){
            checkWeather(district);
        }
    })
    .catch(error => {
        console.error("Error:", error);
    });
});
</script>
<script src="weather.js"></script>
</body>
</html>

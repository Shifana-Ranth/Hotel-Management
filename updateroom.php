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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $room_id = $_POST['room_id'];
        $r_type = $_POST['r_type'];
        $price_per_night = $_POST['price_per_night'];
        $availability = $_POST['availability'];
        $bed_type = $_POST['bed_type'];
        $rating = $_POST['rating'];
        $quantity = $_POST['quantity'];
        $q="SELECT * FROM rooms WHERE rid=$room_id ";
        $re=mysqli_query($conn,$q);
        $rm=mysqli_fetch_assoc($re);
        if($rm){
            $exist_image=$rm['rimage'];
        }
        $image_url = ""; 

            if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
                $target_dir = "roomphotos/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);  
                }
                $image_name = basename($_FILES["room_image"]["name"]);
                $target_file = $target_dir .time() . "_" . $image_name;

                if (move_uploaded_file($_FILES["room_image"]["tmp_name"], $target_file)) {
                    $image_url = $target_file; 
                } else {
                    $showerror = "Failed to upload hotel image.";
                }
            }
            if($image_url == "")
            {
                $image_url =$exist_image; 
            }
            $update_sql = "UPDATE rooms 
                        SET r_type = '$r_type', 
                            price_per_night = '$price_per_night',
                            quantity = '$quantity',
                            availability = '$availability',
                            bed_type = '$bed_type',
                            rimage='$image_url' ,
                            rating = '$rating'
                        WHERE rid = '$room_id'";

        if (mysqli_query($conn, $update_sql)) {
            echo "<script>alert('Room details updated successfully!'); window.location.href = 'viewroom.php';</script>";
        } else {
            echo "Error updating room: " . mysqli_error($conn);
        }
    } else {
        header("Location: seeroom.php");
        exit();
    }
?>
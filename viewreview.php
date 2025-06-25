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
    if($_SESSION["loggedin"]==false || $_SESSION["roles"]!=='Admin'){
      header("Location: index.php");
      exit();
    }
    $user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    $sql = "SELECT r.msg, r.created_at
            FROM review r 
            WHERE r.uidd = $user_id 
            ORDER BY r.created_at DESC";
    
    $result = mysqli_query($conn, $sql);
    $_SESSION["last_activity"]=time();
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Reviews - VoyageVista</title>
  <style>
    .vr-container {
      margin: 3rem auto;
      margin-top:4rem;
      max-width: 1000px;
      padding: 20px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #fefefe;
    }

    .vr-title {
      text-align: center;
      color: #2c3e50;
      font-size: 2rem;
      margin-bottom: 30px;
    }

    .vr-review-card {
      border: 1px solid #ddd;
      border-radius: 10px;
      margin-bottom: 20px;
      padding: 20px;
      background-color: #fafafa;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .vr-review-card h3 {
      margin: 0;
      font-size: 1.1rem;
      color: #246A73;
    }

    .vr-review-card small {
      color: #666;
      display: block;
      margin-top: 5px;
      font-size: 0.85rem;
      background-color:transparent;
    }

    .vr-review-comment {
      margin-top: 10px;
      line-height: 1.6;
      color: #333;
      background-color:transparent;
    }

    .vr-back-btn {
      display: inline-block;
      margin-top: 30px;
      padding: 10px 18px;
      background-color: #246A73;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      transition: background 0.3s;
    }

    .vr-back-btn:hover {
      background-color: #1b4e57;
    }
  </style>
</head>
<body>
<?php include 'header.php';?>
<div class="vr-container">
    <h1 class="vr-title">Reviews by User #<?php echo $user_id; ?></h1>

    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="vr-review-card">';
            echo '<small>Reviewed on: ' . htmlspecialchars($row['created_at']) . '</small>';
            echo '<p class="vr-review-comment">' . htmlspecialchars($row['msg']) . '</p>';
            echo '</div>';
        }
    } else {
        echo "<p>No reviews available for this user.</p>";
    }
    ?>
    <a href="managereview.php" class="vr-back-btn">← Back to Review Summary</a>
</div>
<?php include 'footer.php';?>
</body>
</html>
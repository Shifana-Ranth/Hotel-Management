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
    if (isset($_GET['id'])) {
        $booking_id = $_GET['id'];
    
        $query = "SELECT b.*, b.booking_id, b.payment ,b.balance ,u.uname, b.namee, b.phno, b.roomtype, b.no_of_rooms, b.amount, b.status, b.checkin, b.checkout, b.booked_at 
                  FROM bookings b 
                  JOIN users u ON b.uidd = u.uid 
                  WHERE b.booking_id = '$booking_id'";
    
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
    
        if (!$row) {
            echo "Invalid Booking ID.";
            exit;
        }
    } else {
        echo "Booking ID not passed.";
        exit;
    }
    
    $_SESSION["last_activity"]=time();
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Modify Booking - Manager</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <style>
    *{
        font-family: 'Times New Roman', Times, serif;
    }
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fa;
      margin: 0;
      padding: 0;
    }

    .modify-container {
      max-width: 800px;
      margin: 50px auto;
      background-color: #ffffff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .modify-header {
      text-align: center;
      font-size: 28px;
      font-weight: bold;
      color: #2d3436;
      margin-bottom: 25px;
    }

    .form-row {
      display: flex;
      flex-wrap: wrap;
      margin-bottom: 15px;
      gap: 20px;
    }

    .form-group {
      flex: 1;
      min-width: 240px;
    }

    .form-group label {
      display: block;
      font-size: 14px;
      color: #333;
      margin-bottom: 6px;
    }

    input,
    select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }

    .action-buttons {
      text-align: center;
      margin-top: 30px;
    }

    .btn {
      padding: 10px 20px;
      font-size: 15px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin: 0 10px;
    }

    .btn-save {
      background-color: #246A73;
      color: white;
    }

    .btn-cancel {
      background-color: #e74c3c;
      color: white;
    }
  </style>
</head>
<body>
<?php include 'headermanager.php'; ?>
    <?php
    if(isset($_SESSION['showerror'])){
                echo '
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="position:relative;top:70px;color:black;font-weight:700;padding-left:70px;">
                <strong style="background-color:transparent;"><i style="color:red;background-color:transparent;" class="fa-solid fa-circle-exclamation"></i></strong> '. $_SESSION['showerror'] . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>
                ';
                unset($_SESSION['showerror']);
    }
?>
  <div class="modify-container">
    <div class="modify-header">Modify Booking</div>
    <form method="POST" action="updatebooking.php">
    <input type="hidden" name="original_checkin" value="<?php echo $row['checkin']; ?>">
    <input type="hidden" name="original_checkout" value="<?php echo $row['checkout']; ?>">
    <input type="hidden" name="roomtype" value="<?php echo $row['roomtype']; ?>">
    <input type="hidden" name="no_of_rooms" value="<?php echo $row['no_of_rooms']; ?>">
    <input type="hidden" name="payment" value="<?php echo $row['payment']; ?>">
    <input type="hidden" name="balance" value="<?php echo $row['balance']; ?>">
      <div class="form-row">
        <div class="form-group">
          <label>Booking ID</label>
          <input type="text" name="booking_id" value="<?= $row['booking_id'] ?>" readonly />
        </div>
        <div class="form-group">
          <label>User Name</label>
          <input type="text" value="<?= $row['uname'] ?>" disabled />
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" value="<?= $row['namee'] ?>" disabled />
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" value="<?= $row['phno'] ?>" disabled />
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
            <label>Number of Rooms</label>
            <input type="number" value="<?= $row['no_of_rooms'] ?>" disabled />
        </div>
        <div class="form-group">
            <label>Amount</label>
            <input type="text" value="₹<?= $row['amount'] ?>" disabled />
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
            <label>Payment Status:</label>
            <select>
            <option value="Paid" <?= $row['payment'] == 'Paid' ? 'selected' : '' ?>>Paid</option>
            <option value="Payment" <?= $row['payment'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
          </select>
        </div>
        <div class="form-group">
            <label>Balance</label>
            <input type="text" value="₹<?= $row['balance'] ?>" />
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Room Type</label>
          <select disabled>
            <option value="Deluxe" <?= $row['roomtype'] == 'Deluxe' ? 'selected' : '' ?>>Deluxe</option>
            <option value="Standard" <?= $row['roomtype'] == 'Standard' ? 'selected' : '' ?>>Standard</option>
          </select>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status">
            <option value="booked" <?= $row['status'] == 'booked' ? 'selected' : '' ?>>Booked</option>
            <option value="checked In" <?= $row['status'] == 'checked In' ? 'selected' : '' ?>>Checked In</option>
            <option value="checked Out" <?= $row['status'] == 'checked Out' ? 'selected' : '' ?>>Checked Out</option>
            <option value="cancelled" <?= $row['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Check-In Date</label>
          <input type="date" name="checkin_date" value="<?= $row['checkin'] ?>" />
        </div>
        <div class="form-group">
          <label>Check-Out Date</label>
          <input type="date" name="checkout_date" value="<?= $row['checkout'] ?>" />
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Booked At</label>
          <input type="datetime-local" value="<?= date('Y-m-d\TH:i', strtotime($row['booked_at'])) ?>" disabled />
        </div>
      </div>

      <div class="action-buttons">
        <button type="submit" class="btn btn-save">Save Changes</button>
        <a href="viewbookingman.php" class="btn btn-cancel">Cancel</a>
      </div>
    </form>
  </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <?php include 'footer.php'; ?>
</body>
</html>
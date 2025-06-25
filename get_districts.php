<?php
include("databasee.php");

if (isset($_GET['state'])) {
    $state = mysqli_real_escape_string($conn, $_GET['state']);

    // Get state ID
    $sql = "SELECT st_id FROM states WHERE stname = '$state'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $sid = $row['st_id'] ?? '';

    if ($sid) {
        // Get districts for the state
        $sql2 = "SELECT dt_name FROM district WHERE st_id = '$sid'";
        $res = mysqli_query($conn, $sql2);

        $districts = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $districts[] = $row['dt_name'];
        }
        echo json_encode($districts);
    } else {
        echo json_encode([]);
    }
}
?>
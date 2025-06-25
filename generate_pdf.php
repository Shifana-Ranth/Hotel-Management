<?php
require '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;

session_start();
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : '';
$_SESSION['booking_id'] = $booking_id ;
if(!$booking_id){
    die("No booking found.");
}
ob_start();
include("billdownload.php");
$html = ob_get_clean();
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("invoice_VV" . $_SESSION['booking_id'] . ".pdf", array("Attachment" => 1));
?>
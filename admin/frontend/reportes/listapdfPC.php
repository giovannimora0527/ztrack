<?php
session_start();

require_once ('lib/dompdf/autoload.inc.php');

if ($_POST) { 
$repdfpc = $_POST['dfpc']; 
$_SESSION['pdfpc'] = $repdfpc;
}

$repdfpc =$_SESSION['pdfpc'];

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->load_html(utf8_decode($repdfpc));
//$dompdf->load_html($repdfpc);
$dompdf->setPaper('legal', 'landscape'); //$dompdf->setPaper("A4", 'portrait');            
// Render the HTML as PDF
$dompdf->render();
$dompdf->stream('puntoscontrol');

unset($repdfpc);
?>
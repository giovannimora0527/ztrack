<?php
session_start();
//require_once ('lib/dompdf/dompdf_config.inc.php');
require_once ('../lib/dompdf/autoload.inc.php');

if ($_POST) { 
$repdf = $_POST['df']; 
$_SESSION['pdf'] = $repdf;
}

$repdf =$_SESSION['pdf'];
 $html = "<link rel='stylesheet' href='../assets/css/bootstrap.min.css'/>";
 $html.= "<img style='border:0px' src='../img/logo.png' /><hr/>";
 $html.= "<label>Tiempo y Distancia Entre Vehiculos</label>";
/* @var $repdf type */
$repdf = $html.$repdf;

 use Dompdf\Dompdf;
 

$dompdf = new Dompdf();
$dompdf->load_html($repdf);
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('letter', 'landscape'); //$dompdf->setPaper("A4", 'portrait');            
 //$dompdf->page_text(765, 550, "Pagina {PAGE_NUM} de {PAGE_COUNT}", 9, array(0, 0, 0));
// Render the HTML as PDF
$dompdf->render();
$dompdf->stream('tiempo_distancia');

unset($repdf);
?>
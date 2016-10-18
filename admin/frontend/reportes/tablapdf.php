<?php
session_start();

//require_once ('lib/dompdf/dompdf_config.inc.php');
require_once ('lib/dompdf/autoload.inc.php');

if ($_POST) { 
$repdf = $_POST['df']; 
$_SESSION['pdf'] = $repdf;
}

$repdf =$_SESSION['pdf'];

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->load_html($repdf);
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('letter', 'landscape'); //$dompdf->setPaper("A4", 'portrait');            
// Render the HTML as PDF
$dompdf->render();
$dompdf->stream('tiempo');

unset($repdf);

//use Dompdf\Options;
                                 
//$options = new Options();
//$options -> set('isRemoteEnabled', true);
//$options -> set('dpi', '128');
                
//$dompdf = new Dompdf($options);

//$result = $dompdf->output();                                  
/*$dompdf = new DOMPDF();
//$dompdf->Image('img/logo.png',10,8,33);//chove
$dompdf->load_html(utf8_decode($repdf));
$dompdf->setPaper("A4", 'landscape'); // (Opcional) Configurar papel y orientación
$dompdf->render(); // Generar el PDF desde contenido HTML
$pdf = $dompdf->output(); // Obtener el PDF generado
$filename ='ejemplo.pdf';
$dompdf->stream($filename, array("Attachment" =>0));*/

?>

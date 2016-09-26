<?
        require_once '../tools/dompdf/autoload.inc.php';
        
        // reference the Dompdf namespace
        use Dompdf\Dompdf;
        use Dompdf\Options;
                
        function html2pdf($html)
        {                 
                $options = new Options();
                $options -> set('isRemoteEnabled', true);
                $options -> set('dpi', '128');
                
                $dompdf = new Dompdf($options);
                
                $dompdf->loadHtml($html);
                
                // (Optional) Setup the paper size and orientation
                $dompdf->setPaper('A4', 'landscape');
                
                // Render the HTML as PDF
                $dompdf->render();
                
                $result = $dompdf->output();
                                
                return $result;
        }
?>
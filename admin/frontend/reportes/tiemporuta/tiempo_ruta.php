<?php

//Include database connection details
require_once('../../php/connection.php');

//$vecini= array();
//	$j=0;
$vt = 0;
$nomrut = '';
$vtimeru = null; // Variable tiempo de ruta
$vdifre = null; // Variable diferencia llegada - salida
$timeruta = NULL;

$fechac = new DateTime($_POST['fecha']);
$fecha_pto = date_format($fechac, 'Y-m-d H:i:s');

$sqlr = "SELECT tiempo FROM tiemporuta WHERE route_id= " . $_POST["ruta_id"];
$stmr = mysql_query($sqlr);
 while ($colr = mysql_fetch_array($stmr)) {
    $timeruta = $colr[0];
 }


$sql = "SELECT gob.name vehiculo, d.numero_recorrido, SUBSTRING(d.hora_salida,11,9) hora_salida,
        SUBSTRING(d.hora_llegada,11,9) hora_llegada, r.route_name, obd.driver_name, 
        gob.plate_number placa, TIMEDIFF(d.hora_llegada,d.hora_salida)
        FROM despachos d
        INNER JOIN gs_user_objects guo ON guo.object_id = d.vehiculo_id
        INNER JOIN gs_objects gob ON gob.imei = guo.imei
        JOIN gs_user_object_drivers obd ON guo.driver_id = obd.driver_id
        JOIN gs_user_routes r ON r.route_id = d.ruta_id
        WHERE d.imei = " . $_POST["vehiculo_imei"]
        ." and d.hora_salida BETWEEN (select MIN(hora_salida) FROM despachos
                                where hora_salida > (SELECT DATE_FORMAT('$fecha_pto' , '%Y-%m-%d 00:00:00'))) 
                        AND (select MAX(hora_salida) FROM despachos
                                where hora_salida > (SELECT DATE_FORMAT('$fecha_pto' , '%Y-%m-%d 00:00:00')))
        AND r.route_id = " . $_POST["ruta_id"]
        ." ORDER BY d.numero_recorrido, d.hora_salida ASC;";

$stm = mysql_query($sql);
if (mysql_num_rows($stm) > 0) { // If 1
    
    echo"<table class='table table-bordered table-hover table-striped table-condensed table-responsive' style='font-size:12px'>";
    echo"<thead>";
    echo"<tr>";
    echo"<th>Veh&iacute;culo</th>";
    echo"<th>Recorrido</th>";
    echo"<th>H. Salida</th>";
    echo"<th>H. Llegada</th>";
    echo"<th>Ruta</th>";
    echo"<th>Conductor</th>";
    echo "<th>Tiempo en ruta</th>";
    echo "<th>Diferencia</th>";
    echo"</tr>";
    echo"</thead>";
    echo"<tbody>";
   while ($rse = mysql_fetch_array($stm)) { // While 1
    
        echo "<tr ng-if='oresults'>";
        echo "<td>$rse[0]</td>";    // Vehiculo
        echo "<td>$rse[1]</td>";    // Recorrido 
        echo "<td>$rse[2]</td>";    // Salida
        echo "<td>$rse[3]</td>";    // Llegada
        echo "<td>$rse[4]</td>";    // Ruta
        echo "<td>$rse[5]</td>";    // Conductor
        echo "<td>$rse[7]</td>";    // Tiempo en ruta
        
        $vtimeru = new DateTime($timeruta); // Variable tiempo de ruta
        $vdifre = new DateTime($rse[7]); // Variable diferencia llegada - salida
        $ctrlruta = $vdifre->diff($vtimeru); //
        
            if ($timeruta > $rse[7]) {
                echo "<td> + "; print $ctrlruta->format("%H:%I:%S"); echo "</td>";
                    } 
            else {
                echo "<td><span class='red middle bolder'> - "; print $ctrlruta->format("%H:%I:%S");echo "</span></td>";
                    }
        echo "</tr>";
        $nomrut = $rse[4];
   } // Fin While 1

   echo" </tbody>";
    echo"</table>";
    echo "</br>";
    echo "Informaci&oacute;n referente a la Ruta: ";
    echo "<label class='control-label bolder blue'>$nomrut</label>";
    echo "<br>";
    echo "Fecha: "; echo "<label class='control-label bolder blue'>"; print $fechac->format("Y-m-d"); echo "</label>"; 
} // Fin If 1        
else { // Else 1
    echo"</br>";
    echo"<label class='control-label bolder blue'>";
    echo"No se ha encontrado informaci&oacute;n en su solicitud.";
    echo"</label>";
} // Fin Else 1
?>
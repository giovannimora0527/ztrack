<?php
// Conectando, seleccionando la base de datos
$conexion = mysql_connect('localhost', 'root', '')
        or die('No se pudo conectar: ' . mysql_error());
//echo 'Connected successfully';
mysql_select_db('gs') or die('No se pudo seleccionar la base de datos');
?>

<!--DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte Ztrack Tiempo - Distancia</title>
<link rel="stylesheet" href="assets/css/bootstrap.min.css"/>
<link rel="stylesheet" href="assets/css/ace.min1.css" class="ace-main-stylesheet" id="main-ace-style" /> <!-- Mantiene bien la Pagina pero Background pdf
</head>
<body>
<img style="border:0px" src="img/logo.png" /><hr/>-->
<table class="table table-bordered table-hover table-responsive table-striped">
    <thead>
        <tr>                                
            <th>Hora Salida</th>
            <th>Vehículo</th>
            <th>Tiempo en Ruta</th> 
            <th>Posición Actual</th>
            <th>Tiempo (HH:mm:ss)</th>
            <th>Distancia</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Consulta del Despacho
        //$sql='SELECT hora_salida, imei, latitud, longitud FROM despachos WHERE user_id=1 AND route_id=_GET['selrut'] ORDER by despacho_id'; //<JBT> Colocar el estado_id que indica despachado
        $sql = 'SELECT hora_salida, imei, latitud, longitud FROM despachos WHERE user_id=1 ORDER by despacho_id';
        $stm = mysql_query($sql);        
        $dif = NULL;
        $dift = NULL;
        $res = NULL;
        $arreglo = array();
        //$res=NULL;
        
        // Mensaje por si no se encuentra datos
        if (mysql_num_rows($stm) > 0) {
            while ($fila = mysql_fetch_array($stm)) {
            // Consulta de la tabla que se actualiza
            $sql = 'SELECT dt_tracker, lat, lng, name FROM gs_objects WHERE imei=' . $fila[1];
            $stm1 = mysql_query($sql);
            // Mensaje por si no se encuentra datos
            $rs = mysql_fetch_array($stm1);
            if ($dif == NULL) {
                $lecd = new DateTime($fila[0]);
                $lecg = new DateTime($rs[0]);

                $dif = $lecd->diff($lecg);
                $res = $lecg->diff($lecg);

                $sqld = 'SELECT (ACOS(SIN(RADIANS(\'' . $fila[2] . '\')) * SIN(RADIANS(\'' . $rs[1] . '\')) + 
									COS(RADIANS(\'' . $fila[2] . '\')) * COS(RADIANS(\'' . $rs[1] . '\')) * 
									COS(RADIANS(\'' . $fila[3] . '\') - RADIANS(\'' . $rs[2] . '\'))) * 6378)';
                $stm2 = mysql_query($sqld);
                $dis = mysql_fetch_array($stm2);
                $latac = $rs[1];
                $lonac = $rs[2];
            } else {
                //$res="0000-00-00 00:00:00";					

                $lecd = new DateTime($fila[0]);
                $lecg = new DateTime($rs[0]);

                $dift = $lecd->diff($lecg);

                $var_r = $dift->format("%H:%I:%S");
                $var_t = $dif->format("%H:%I:%S");

                $faux = "0000-00-00 ";
                $esta = $faux . $var_r;
                $esta1 = $faux . $var_t;

                $nedift = new DateTime($esta);
                $nedifw = new DateTime($esta1);
                $res = $nedift->diff($nedifw);

                $dif = $dift;

                $sqld = 'SELECT (ACOS(SIN(RADIANS(\'' . $latac . '\')) * SIN(RADIANS(\'' . $rs[1] . '\')) + 
									COS(RADIANS(\'' . $latac . '\')) * COS(RADIANS(\'' . $rs[1] . '\')) * 
									COS(RADIANS(\'' . $lonac . '\') - RADIANS(\'' . $rs[2] . '\'))) * 6378)';
                $stm2 = mysql_query($sqld);
                $dis = mysql_fetch_array($stm2);
            }

            echo "<tr ng-if='oresults'>";
            echo "<td>$fila[0]</td>";
            echo "<td>$rs[3]</td>";
            print "<td>";
            print $dif->format("%H:%I:%S");
            print"</td>";
            echo "<td>$rs[1], $rs[2]</td>";
            print "<td>";
            print $res->format("%H:%I:%S");
            print"</td>";
            echo "<td>";
            echo round($dis[0], 2, PHP_ROUND_HALF_UP);
            echo " Km </td>";
            //$sqlin='INSERT INTO temp_rptediferencia VALUES ('$fila[0]','$fila[1]','$dif','$rs[1]','$rs[2]','$res','$dis')';
        }
        echo "</tr>";
        }
        
        ?>	
    </tbody>
</table>
<!--</body>
</html>-->
<?php
mysql_close($conexion);
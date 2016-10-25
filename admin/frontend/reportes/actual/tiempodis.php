<?php
//Include database connection details
require_once('../../php/connection.php');


/*Consulta del Despacho que me permite saber tiempo y distancia a determinada hora del día
 * de los vehiculas que estan en ruta.
 $sql = "SELECT d.hora_salida, d.imei, d.latitud, d.longitud, gur.route_name 
            FROM despachos d, gs_user_routes AS gur WHERE gur.route_id=d.ruta_id"
            . " AND d.user_id= " . $_POST["user_id"]
            . " AND d.ruta_id = " . $_POST["ruta_id"]
            . " AND d.estado_id=3"
            . " ORDER by d.despacho_id; ";
 */
    $sql = "SELECT d.hora_salida, d.imei, d.latitud, d.longitud, gur.route_name 
            FROM despachos d, gs_user_routes AS gur WHERE gur.route_id=d.ruta_id 
            AND d.hora_salida BETWEEN (SELECT DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00')) AND (SELECT NOW())"
            . " AND d.user_id= " . $_POST["user_id"]
            . " AND d.ruta_id = " . $_POST["ruta_id"]
            . " AND d.estado_id=3"
            . " ORDER by d.despacho_id; ";
        $stm = mysql_query($sql);        
        $dif = NULL;
        $dift = NULL;
        $res = NULL;
        $nomrut = NULL;
        //$res=NULL;
        
        // Mensaje por si no se encuentra datos
        if (mysql_num_rows($stm) > 0) {
            
                echo"<table class='table table-bordered table-hover table-responsive table-striped'>";
                    echo"<thead>";
                        echo"<tr>";
                            echo"<th>Hora Salida</th>";
                            echo"<th>Vehículo</th>";
                            echo"<th>Tiempo en Ruta</th>";
                            echo"<th>Posición Actual</th>";
                            echo"<th>Tiempo (HH:mm:ss)</th>";
                            echo"<th>Distancia</th>";
                        echo"</tr>";
                    echo"</thead>";
                    echo"<tbody>";
             
            
            while ($fila = mysql_fetch_array($stm)) {
            // Consulta de la tabla que se actualiza
                $nomrut=$fila[4];
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
        echo "</tbody>";
        echo "</table>";
        echo "</br>"; 
        echo "Información referente a la Ruta: "; echo "<label>$nomrut</label>";
        }
        
        else {
                echo"</br>";
                echo"No se ha encontrado información en su solicitud.";
                        
                }

?>	

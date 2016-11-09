<?php

//Include database connection details
require_once('../../php/connection.php');

//$vecini= array();
//	$j=0;
$vt = 0;
$nomrut = '';
$difpctz = NULL;
$sumtot = NULL;
$timeruta = NULL;

$fechac = new DateTime($_POST['fecha']);
$fecha_pto = date_format($fechac, 'Y-m-d H:i:s');
//SUBSTRING(d.hora_salida,11,9)
$sql = "SELECT DISTINCT SUBSTRING(d.hora_salida,11,9), gued.imei, gued.obj_name, SUBSTRING(d.hora_llegada,11,9), gur.route_name
            FROM despachos AS d
            INNER JOIN gs_user_events_data AS gued ON d.imei=gued.imei
            INNER JOIN gs_user_routes AS gur ON gur.route_id=d.ruta_id
            WHERE d.hora_salida >= (SELECT MIN(d.hora_salida) FROM despachos d 
				WHERE d.hora_salida > (SELECT DATE_FORMAT('$fecha_pto' , '%Y-%m-%d 00:00:00'))
				AND d.numero_recorrido =" . $_POST["numero_recorrido"]
        . " AND d.ruta_id= " . $_POST["ruta_id"] . ")"
        . " AND gued.dt_tracker BETWEEN (SELECT MIN(d.hora_salida) FROM despachos d 
				WHERE d.hora_salida >= (SELECT DATE_FORMAT('$fecha_pto' , '%Y-%m-%d 00:00:00'))
				AND d.numero_recorrido =" . $_POST["numero_recorrido"]
        . " AND d.ruta_id= " . $_POST["ruta_id"] . ")"
        . " AND (SELECT MAX(d.hora_llegada) FROM despachos d 
				WHERE d.hora_salida >= (SELECT DATE_FORMAT('$fecha_pto' , '%Y-%m-%d 00:00:00'))
				AND d.numero_recorrido =" . $_POST["numero_recorrido"]
        . " AND d.ruta_id= " . $_POST["ruta_id"] . ")"
        . " AND gued.type='zone_in'"
        . " AND gur.route_id = " . $_POST["ruta_id"]
        . " AND d.numero_recorrido =" . $_POST["numero_recorrido"]
        . " AND d.estado_id = 4 "
        . " ORDER BY d.hora_salida, gued.dt_tracker ASC;";

//echo "$sql";
/* WHERE d.hora_salida BETWEEN (SELECT DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00')) AND (SELECT NOW())
  AND gued.dt_tracker BETWEEN (SELECT DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00')) AND (SELECT NOW()) */

$stm = mysql_query($sql);
if (mysql_num_rows($stm) > 0) {

    echo"<table class='table table-bordered table-hover table-striped table-condensed table-responsive' style='font-size:12px'>";
    echo"<thead>";
    echo"<tr>";
    echo"<th>Hora Salida</th>";
    echo"<th>Veh&iacute;culo</th>";

    $arrev = array();
    $arrtz = array();
    // Consulta 2
    $sqlpc = "SELECT guz.zone_id, guz.zone_name, rz.tiempo_zona, t.tiempo  
                                    FROM gs_user_zones AS guz, gs_rutazonas AS rz, tiemporuta AS t 
                                    WHERE rz.zone_id=guz.zone_id AND rz.route_id=t.route_id
                                    AND rz.user_id=" . $_POST["user_id"]
            . " AND rz.route_id=" . $_POST["ruta_id"];

    $stmpc = mysql_query($sqlpc);

    $i = NULL;
    while ($colpc = mysql_fetch_array($stmpc)) {
        echo "<th>$colpc[1]</th>";
        echo "<th>Diferencia</th>";
        $arrev[] = $colpc['zone_name'];
        $arrtz[] = $colpc['tiempo_zona'];
        $i++;
        $timeruta = $colpc['tiempo'];
    }
    echo" <th>Hora Llegada</th>";
    echo"<th>Diferencia Total</th>";
    echo"</tr>";
    echo"</thead>";
    /* echo "<pre>";
      print_r ($arrtz);
      echo "</pre>"; */
    echo"<tbody>";

    while ($rs = mysql_fetch_array($stm)) {
        $nomrut = $rs[4];
        // Consulta 3
        
        $sql = "SELECT DISTINCT TIMEDIFF(gued.dt_tracker, d.hora_salida),
                        MID(gued.event_desc,INSTR(gued.event_desc,'(')+1,(LENGTH(gued.event_desc)-INSTR(gued.event_desc,'('))-1)
                        FROM despachos AS d
                        INNER JOIN gs_user_events_data AS gued ON d.imei=gued.imei
                        INNER JOIN gs_rutazonas AS rz ON d.ruta_id=rz.route_id
                        INNER JOIN gs_user_zones guz ON guz.zone_id=rz.zone_id
                        WHERE d.hora_salida >= (SELECT MIN(d.hora_salida) FROM despachos d 
                                        WHERE d.hora_salida >= (SELECT DATE_FORMAT('$fecha_pto' , '%Y-%m-%d 00:00:00'))
                                        AND d.numero_recorrido =" . $_POST["numero_recorrido"]
                . " AND d.ruta_id= " . $_POST["ruta_id"] . ")"
                . " AND gued.dt_tracker BETWEEN (SELECT MIN(d.hora_salida) FROM despachos d 
                                        WHERE d.hora_salida >= (SELECT DATE_FORMAT('$fecha_pto' , '%Y-%m-%d 00:00:00'))
                                        AND d.numero_recorrido =" . $_POST["numero_recorrido"]
                . " AND d.ruta_id= " . $_POST["ruta_id"] . ")"
                . " AND (SELECT MAX(d.hora_llegada) FROM despachos d 
                                        WHERE d.hora_salida >= (SELECT DATE_FORMAT('$fecha_pto' , '%Y-%m-%d 00:00:00'))
                                        AND d.numero_recorrido =" . $_POST["numero_recorrido"]
                . " AND d.ruta_id= " . $_POST["ruta_id"] . ")"
                . " AND gued.type='zone_in'"
                . " AND rz.route_id= " . $_POST["ruta_id"]
                . " AND d.numero_recorrido =" . $_POST["numero_recorrido"]
                . " AND gued.imei = '" . $rs[1] . "'"
                . " AND d.estado_id= 4 "
                . " AND rz.user_id= " . $_POST["user_id"]
                . " ORDER BY d.hora_salida, gued.dt_tracker ASC;";
        // echo($sql);echo"<br>";
////SELECT DISTINCT TIMEDIFF((DATE_ADD(gued.dt_tracker,INTERVAL 2 HOUR)),d.hora_salida), 
//WHERE d.hora_salida > (SELECT DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00'))
//AND gued.dt_tracker BETWEEN (SELECT DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00')) AND (SELECT NOW())	                     
        /*
          WHERE d.hora_salida  > '2016-11-01 00:00:00'
          AND gued.dt_tracker BETWEEN '2016-11-01 00:00:00' AND '2016-11-01 19:55:00'
         *  */
//Ó
//WHERE d.hora_salida BETWEEN (SELECT DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00'))
//AND gued.dt_tracker BETWEEN (SELECT DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00')) AND (SELECT DATE_SUB(NOW(), INTERVAL 2 HOUR)) 

        $stm1 = mysql_query($sql);
        echo "<tr ng-if='oresults'>";
        echo "<td>$rs[0]</td>";
        echo "<td>$rs[2]</td>";

        $i = 0;
        $j = 0;
        while ($rx = mysql_fetch_array($stm1)) {

            if ($i == 0) {
                if ($rx[1] == $arrev[$j]) {
                    $letz = new DateTime($arrtz[$j]);
                    // echo $rx[0];
                    $lerx = new DateTime($rx[0]);

                    $difpctz = $letz->diff($lerx);

                    echo "<td>$rx[0]</td>";
                    if ($letz > $lerx) {

                        echo "<td> + ";
                        print $difpctz->format("%H:%I:%S");
                        echo "</td>";
                    } else {

                        echo "<td><span class='red middle bolder'> - ";
                        print $difpctz->format("%H:%I:%S");
                        echo "</span></td>";
                    }

                    $var = $rx[0];
                    $suspc = $arrtz[$j]; //Valor para dif punto control
                    $i = 1;
                    $j++;
                    //$dt->add($interval);
                    $difpctz = NULL;
                } else {
                    while ($rx[1] != $arrev[$j]) {
                        echo "<td><label class='control-label bolder blue'> --- </label></td>";
                        echo "<td><label class='control-label bolder blue'> --- </label></td>";
                        $j++;
                    }
                    $letz = NULL;
                    $lerx = NULL;
                    $letz = new DateTime($arrtz[$j]);
                    $lerx = new DateTime($rx[0]);
                    $lesuspc = new DateTime($suspc);

                    $difpctz = $letz->diff($lerx);
                    $difsuspc = $letz->diff($lesuspc);

                    echo "<td>$rx[0]</td>";
                    if ($letz > $lerx) {
                        echo "<td> + ";
                        print $difpctz->format("%H:%I:%S");
                        echo "</td>";
                    } else {
                        echo "<td><span class='red middle bolder'> - ";
                        print $difpctz->format("%H:%I:%S");
                        echo "</span></td>";
                    }
                    $var = $rx[0];
                    $suspc = $arrtz[$j];
                    $i = 1;
                    //$j++;
                    $difpctz = NULL;
                }
            } //Fin If externo
            else {
                if ($rx[1] == $arrev[$j]) {
                    $letz = NULL;
                    $lerx = NULL;
                    $letpc = new DateTime($var); //Lectura Tiempo punto control
                    $lerx = new DateTime($rx[0]); //Letura rx[0]
                    $letz = new DateTime($arrtz[$j]); //Lectura tiempo zona
                    $lesuspc = new DateTime($suspc); //Lectura resta pto control

                    $diftpc = $lerx->diff($letpc); // Diferencia entre punto u punto
                    $difsuspc = $letz->diff($lesuspc); //7


                    $faux = "0000-00-00 ";
                    $var_tpc = $diftpc->format("%H:%I:%S"); // Variable resta entre cada punto
                    $var_pctz = $difsuspc->format("%H:%I:%S");

                    $var_tpc = $faux . $var_tpc;
                    $var_pctz = $faux . $var_pctz;

                    $auxtpc = new DateTime($var_tpc);
                    $auxpctz = new DateTime($var_pctz);
                    $difpctz = $auxtpc->diff($auxpctz);

                    $var = $rx[0];
                    $suspc = $arrtz[$j];
                    echo "<td>";
                    print $diftpc->format("%H:%I:%S");
                    echo "</td>"; // se deja
                    //echo "<td>$diftpc.$var</td>";
                    if ($letz > $lerx) {

                        echo "<td> + ";
                        print $difpctz->format("%H:%I:%S");
                        echo "</td>";
                    } else {

                        echo "<td><span class='red middle bolder'> - ";
                        print $difpctz->format("%H:%I:%S");
                        echo "</span></td>";
                    }
                    //echo "<td>$difpctz.$j</td>"; // Diferencia entre el tiempo gastado y lo que debia gastar
                    $j++;
                    $difpctz = NULL;
                } else {
                    while ($rx[1] != $arrev[$j]) {
                        echo "<td><label class='control-label bolder blue'> --- </label></td>";
                        echo "<td><label class='control-label bolder blue'> --- </label></td>";
                        $j++;
                    }
                    $letz = NULL;
                    $lerx = NULL;
                    $letpc = NULL;

                    $letpc = new DateTime($var);
                    $lerx = new DateTime($rx[0]);
                    $letz = new DateTime($arrtz[$j]);
                    $lesuspc = new DateTime($suspc);

                    //aqui
                    $diftpc = $lerx->diff($letpc); // Diferencia entre punto u punto
                    $difsuspc = $letz->diff($lesuspc); //7


                    $faux = "0000-00-00 ";
                    $var_tpc = $diftpc->format("%H:%I:%S"); // Variable resta entre cada punto
                    $var_pctz = $difsuspc->format("%H:%I:%S");

                    $var_tpc = $faux . $var_tpc;
                    $var_pctz = $faux . $var_pctz;

                    $auxtpc = new DateTime($var_tpc);
                    $auxpctz = new DateTime($var_pctz);
                    $difpctz = $auxtpc->diff($auxpctz);

                    //aqui

                    $var = $rx[0];
                    echo "<td>";
                    print $diftpc->format("%H:%I:%S");
                    echo "</td>";
                    if ($letz > $lerx) {

                        echo "<td> + ";
                        print $difpctz->format("%H:%I:%S");
                        echo "</td>";
                    } else {

                        echo "<td><span class='red middle bolder'> - ";
                        print $difpctz->format("%H:%I:%S");
                        echo "</span></td>";
                    }
                    $j++;
                    // $sumtot = $sumtot + $difpctz; //sumar tiempo
                    $difpctz = NULL;
                } // Fin del Else interno
            }// Fin del Else externo			
        } //Fin 2do While
        $letz = NULL;
        $lerx = NULL;
        $letpc = NULL;
        $let_tr = new DateTime($timeruta);
        $let_s = new DateTime($rs[0]);
        $let_l = new DateTime($rs[3]);
        $sumtot = $let_s->diff($let_l);
        $auxst = $sumtot->format("%H:%I:%S");
        $formst = new DateTime($auxst);
        $diftot = $formst->diff($let_tr);

        echo "<td>$rs[3]</td>"; //Hora llegada
        if ($auxst < $timeruta) {

            echo "<td> + ";
            print $diftot->format("%H:%I:%S");
            echo "</td>";
        } else {

            echo "<td><span class='red middle bolder'> - ";
            print $diftot->format("%H:%I:%S");
            echo "</span></td>";
        }
        //echo "<td>"; print $diftot->format("%H:%I:%S");echo "</td>";
        echo "</tr>";
        $j = 0;
    }// fin del While externo
    echo" </tbody>";
    echo"</table>";
    echo "</br>";
    echo "Informaci&oacute;n referente a la Ruta: ";
    echo "<label class='control-label bolder blue'>$nomrut</label>";
    echo "<br>";
    echo "Fecha: "; echo "<label class='control-label bolder blue'>"; print $fechac->format("Y-m-d"); echo "</label>"; 
}//fin del If General
else {
    echo"</br>";
    echo"<label class='control-label bolder blue'>";
    echo"No se ha encontrado informaci&oacute;n en su solicitud.";
    echo"</label>";
}
?>
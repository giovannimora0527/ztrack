<?php
//Include database connection details
require_once('../../php/connection.php');

/* 1. Consulta que trae la informacion teniendo cuenta la hora del sistema 
respecto a la hora salida.

$sql = "SELECT DISTINCT d.hora_salida, gued.imei, gued.obj_name, d.hora_llegada, gur.route_name
                    FROM despachos AS d
                    JOIN gs_user_events_data AS gued ON d.imei=gued.imei
                    JOIN gs_user_routes AS gur ON gur.route_id=d.ruta_id
                    WHERE d.hora_salida BETWEEN '2016-09-28 08:00:00' AND '2016-09-28 12:00:00'
                    AND gued.dt_tracker >= '2016-09-28 08:00:00' AND gued.dt_tracker  <= '2016-09-28 12:00:00'
                    AND gued.type='zone_in'"
                    . " AND d.user_id= " . $_POST["user_id"]
                    . " AND gur.route_id=" . $_POST["ruta_id"]
                    . " AND d.estado_id=3"
                    . " ORDER BY d.hora_salida, gued.dt_tracker ASC;"; 


3. Consulta que me trae las diferencias entre punto y punto

$sql = "SELECT TIMEDIFF(gued.dt_tracker,d.hora_salida),
			MID(gued.event_desc,INSTR(gued.event_desc,'(')+1,(LENGTH(gued.event_desc)-INSTR(gued.event_desc,'('))-1)
			FROM despachos AS d
			JOIN gs_user_events_data AS gued ON d.imei=gued.imei
			JOIN gs_user_routes AS gur ON gur.route_id=d.ruta_id
			WHERE d.hora_salida BETWEEN '2016-09-28 08:00:00' AND '2016-09-28 12:00:00' 
			AND gued.dt_tracker >= '2016-09-28 08:00:00' AND gued.dt_tracker  <= '2016-09-28 12:00:00'
			AND gued.type='zone_in'"
                        ." AND d.user_id= " . $_POST["user_id"]
			." AND d.ruta_id= " . $_POST["ruta_id"] 
			." AND gued.imei = '".$rs[1]."'"
                        ." AND d.estado_id=3"
			." ORDER BY d.hora_salida, gued.dt_tracker ASC;";
   */
            $j=0;
            $nomrut = '';
            // Consulta 1
            $sql="SELECT DISTINCT d.hora_salida, gued.imei, gued.obj_name, d.hora_llegada, gur.route_name
                FROM despachos AS d
                JOIN gs_user_events_data AS gued ON d.imei=gued.imei
                JOIN gs_user_routes AS gur ON gur.route_id=d.ruta_id
                WHERE d.hora_salida BETWEEN (SELECT DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00')) AND (SELECT NOW())
                AND gued.dt_tracker BETWEEN (SELECT DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00')) AND (SELECT NOW()) 
                AND gued.type='zone_in'"
                . " AND d.user_id= " . $_POST["user_id"]
                . " AND gur.route_id=" . $_POST["ruta_id"]
                . " AND d.estado_id=3"
                . " ORDER BY d.hora_salida, gued.dt_tracker ASC;";
            $stm = mysql_query($sql);
            if (mysql_num_rows($stm) > 0) {
                
                echo"<table class='table table-bordered table-hover table-striped table-condensed table-responsive' style='font-size:12px'>";
                    echo"<thead>";
                        echo"<tr>";
                            echo"<th>Hora Salida</th>";
                            echo"<th>Veh&iacute;culo</th>";
		
                            $arrev = array(); 
                            // Consulta 2
                            $sqlpc="SELECT zone_id, zone_name FROM gs_user_zones WHERE user_id=" . $_POST["user_id"] 
                                . " AND group_id=". $_POST["ruta_id"];
                            $stmpc=mysql_query($sqlpc);

                            $i=NULL;
                            while($colpc = mysql_fetch_array($stmpc)){
                                //echo "<th>$col[0]</th>";
                                echo "<th>$colpc[1]</th>";
                                echo "<th>Dif</th>";
                                $arrev[] = $colpc['zone_name'];
                                $i++;
                            }		
                            echo" <th>Hora Llegada</th>";
                            echo"<th>Diferecia</th>";
                        echo"</tr>";
                    echo"</thead>";
                    echo"<tbody>";
					
                   while($rs = mysql_fetch_array($stm)){
			$nomrut = $rs[4];			
			// Consulta 3
                        $sql= "SELECT TIMEDIFF(gued.dt_tracker,d.hora_salida),
                        MID(gued.event_desc,INSTR(gued.event_desc,'(')+1,(LENGTH(gued.event_desc)-INSTR(gued.event_desc,'('))-1)
                        FROM despachos AS d
                        JOIN gs_user_events_data AS gued ON d.imei=gued.imei
                        JOIN gs_user_routes AS gur ON gur.route_id=d.ruta_id
                        WHERE d.hora_salida BETWEEN (SELECT DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00')) AND (SELECT NOW())
                        AND gued.dt_tracker BETWEEN (SELECT DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00')) AND (SELECT NOW())
                        AND gued.type='zone_in'"
                        ." AND user_id= " . $_POST["user_id"]
                        ." AND d.ruta_id= " . $_POST["ruta_id"] 
                        ." AND gued.imei = '".$rs[1]."'"
                        ." AND d.estado_id=3"
                        ." ORDER BY d.hora_salida, gued.dt_tracker ASC;";
                        
			$stm1 = mysql_query($sql);
			echo "<tr ng-if='oresults'>";
			echo "<td>$rs[0]</td>";
			echo "<td>$rs[2]</td>";
						
			$i = 0; 
			while($rx = mysql_fetch_array($stm1)){
								
                            if($i==0){
				if($rx[1]==$arrev[$j]){
                                    echo "<td>$rx[0]</td>";
                                    echo "<td>$arrev[$j]</td>";
                                    $var=$rx[0];
                                    $i=1;
                                    $j++;
				}
				else {
                                    while($rx[1]!=$arrev[$j]){
					echo "<td> --- </td>";
					echo "<td> --- </td>";
					$j++;
                                    }
                                    echo "<td>$rx[0]</td>";
                                    echo "<td>$arrev[$j]</td>";
                                    $var=$rx[0];
                                    $i=1;
                                    $j++;
				}
                            } //Fin If externo
                            else {
				if($rx[1]==$arrev[$j]){
                                    $diftpc= RestarHoras($var,$rx[0]);
                                    $var=$rx[0];
                                    echo "<td>$diftpc</td>";
                                    echo "<td>$arrev[$j]</td>"; // Diferencia entre el tiempo gastado y lo que debia gastar
                                    $j++;
				}
				else{
                                    while($rx[1]!=$arrev[$j]){
                                        echo "<td> --- </td>";
					echo "<td> --- </td>";
					$j++;
                                    }
                                    $diftpc= RestarHoras($var,$rx[0]);
                                    $var=$rx[0];
                                    echo "<td>$diftpc</td>";
                                    echo "<td>$arrev[$j]</td>";
                                    $j++;
				} // Fin del Else interno
                            }// Fin del Else externo			
			} //Fin 2do While
                        
                        echo "<td>$rs[3]</td>";
                        echo "<td>Tota</td>";
                        echo "</tr>";
                        $j=0;				
                        
                    }// fin del While externo
                    echo" </tbody>";
                echo"</table>";
                echo "</br>"; 
                echo "Información referente a la Ruta: "; echo "<label>$nomrut</label>";
                }//fin del If General
                else {
                      echo"</br>";
                      echo"No se ha encontrado información en su solicitud.";
                }
			
function RestarHoras($horaini, $horafin) {
        $horai = substr($horaini, 0, 2);
        $mini = substr($horaini, 3, 2);
        $segi = substr($horaini, 6, 2);

        $horaf = substr($horafin, 0, 2);
        $minf = substr($horafin, 3, 2);
        $segf = substr($horafin, 6, 2);

        $ini = ((($horai * 60) * 60) + ($mini * 60) + $segi);
        $fin = ((($horaf * 60) * 60) + ($minf * 60) + $segf);
    
        $dif = $fin - $ini;

        $difh = floor($dif / 3600);
        $difm = floor(($dif - ($difh * 3600)) / 60);
        $difs = $dif - ($difm * 60) - ($difh * 3600);
        return date("H:i:s", mktime($difh, $difm, $difs));
   }
?>
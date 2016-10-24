<?php
//Include database connection details
require_once('../php/connection.php');
?>

<table class="table table-bordered table-hover table-condensed table-striped table-responsive">
    <thead>
	<tr>                                
            <th>Hora Salida</th>
            <th>Vehículo</th>
		<?php
                    $arrev = array(); 
				
                    $sqlpc="SELECT zone_id, zone_name FROM gs_user_zones WHERE user_id=" . $_POST["user_id"] 
			. " AND group_id=". $_POST["ruta_id"];
					
                    // echo $sqlpc;
                    $stmpc=mysql_query($sqlpc);

                    $i=NULL;
                    while($colpc = mysql_fetch_array($stmpc))
			{
                            //echo "<th>$col[0]</th>";
                            echo "<th>$colpc[1]</th>";
                            echo "<th>Dif</th>";
                            $arrev[] = $colpc['zone_name'];
                            //$i++;
			}
						
                ?>
            <th>Hora Llegada</th>
            <th>Diferecia</th>
        </tr>
    </thead>
    <tbody>
	<?php
        $j=0;
		$sql = "SELECT DISTINCT d.hora_salida, gued.imei, gued.obj_name, d.hora_llegada
                    FROM despachos AS d
                    JOIN gs_user_events_data AS gued ON d.imei=gued.imei
                    JOIN gs_user_routes AS gur ON gur.route_id=d.ruta_id
                    WHERE d.hora_salida BETWEEN '2016-09-28 08:00:00' AND '2016-09-28 12:00:00'
                    AND gued.dt_tracker >= '2016-09-28 08:00:00' AND gued.dt_tracker  <= '2016-09-28 12:00:00'
                    AND gued.type='zone_in'"
                    . " AND gur.route_id=" . $_POST["ruta_id"]
                    . " ORDER BY d.hora_salida, gued.dt_tracker ASC;";
		
		$stm = mysql_query($sql);
                if (mysql_num_rows($stm) > 0) {
					
                    while($rs = mysql_fetch_array($stm)){
						
			$sql = "SELECT TIMEDIFF(gued.dt_tracker,d.hora_salida),
			MID(gued.event_desc,INSTR(gued.event_desc,'(')+1,(LENGTH(gued.event_desc)-INSTR(gued.event_desc,'('))-1)
			FROM despachos AS d
			JOIN gs_user_events_data AS gued ON d.imei=gued.imei
			JOIN gs_user_routes AS gur ON gur.route_id=d.ruta_id
			WHERE d.hora_salida BETWEEN '2016-09-28 08:00:00' AND '2016-09-28 12:00:00' 
			AND gued.dt_tracker >= '2016-09-28 08:00:00' AND gued.dt_tracker  <= '2016-09-28 12:00:00'
			AND gued.type='zone_in'"
			." AND d.ruta_id= " . $_POST["ruta_id"] 
			." AND gued.imei = '".$rs[1]."'"
			." ORDER BY d.hora_salida, gued.dt_tracker ASC;";
                                                
							$stm1 = mysql_query($sql);
							
							echo "<tr ng-if='oresults'>";
							echo "<td>$rs[0]</td>";
							echo "<td>$rs[2]</td>";
							
							$i = 0; 
							while($rx = mysql_fetch_array($stm1)){
								
								if($i==0){
									//echo"Primer IF:";echo $rx[1];
									//echo"Pri Arr:";echo $arrev[$j]; echo"<br>";
										if($rx[1]==$arrev[$j]){
											echo "<td>$rx[0]</td>";
											//echo "<td>Dif</td>";
											echo "<td>$arrev[$j]</td>";
											$var=$rx[0];
											$i=1;
											$j++;
										}
										else {
												while($rx[1]!=$arrev[$j]){
													//echo"Primer Else: ";echo $rx[1];
													//echo"Pri Arr: "; echo $arrev[$j]; echo"<br>";
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
									}
								else {
										//echo"Else: "; echo $rx[1];
										//echo"Arr Else: "; echo $arrev[$j]; echo"<br>";
									if($rx[1]==$arrev[$j]){
										$diftpc= RestarHoras($var,$rx[0]);
										$var=$rx[0];
										echo "<td>$diftpc</td>";
										//echo "<td>Dif</td>";
										echo "<td>$arrev[$j]</td>";
										$j++;
									}
									else{
											while($rx[1]!=$arrev[$j]){
												//echo $rx[1];
												//echo $arrev[$j]; echo"<br>";
												echo "<td> --- </td>";
												echo "<td> --- </td>";
												$j++;
											}
											$diftpc= RestarHoras($var,$rx[0]);
											$var=$rx[0];
											echo "<td>$diftpc</td>";
											echo "<td>$arrev[$j]</td>";
											$j++;
										}
								}								
								
							}
							//print "<td>";print $rs[3]->format("%H:%I:%S"); print"</td>";
							//$ti= new DateTime($rs[3]);
							//print "<td>"; print $ti->format("%H:%I:%S"); print"</td>";
							echo "<td>$rs[3]</td>";
							echo "<td>Tota</td>";
							echo "</tr>";
							$j=0;
							
							
							
						
					}// fin del While externo
					
                                         }//fin del If
					
				?>
			
			</tbody>
		</table>
<?php

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


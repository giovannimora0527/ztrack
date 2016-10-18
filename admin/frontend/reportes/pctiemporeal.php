<?php
// Conectando, seleccionando la base de datos
$conexion = mysql_connect('localhost', 'root', '')
or die('No se pudo conectar: ' . mysql_error());
//echo 'Connected successfully';
mysql_select_db('gs') or die('No se pudo seleccionar la base de datos');

?>
<!--<link rel="stylesheet" href="assets/css/bootstrap.min.css"/>
<link rel="stylesheet" href="assets/css/ace.min1.css" class="ace-main-stylesheet" id="main-ace-style" /> <!-- Mantiene bien la Pagina pero Background pdf 
		
<img style="border:0px" src="img/logo.png" /><hr/>-->
	<table class="table  table-bordered table-hover table-condensed table-striped">
			<thead>
				<tr>                                
					<th>Hora Salida</th>
					<th>Vehículo</th>
					<?php 
						$sqlev="SELECT event_id, user_id, type, name, zones FROM gs_user_events WHERE user_id=1 AND type='zone_in' AND name='Pto Control'";
						$stmev=mysql_query($sqlev);
						while ($rev=mysql_fetch_array($stmev)) {
							$arrev = explode(',', $rev['zones']);
						}
						asort($arrev);
						//echo "<pre>";
						//print_r ($arrev);
						//echo "</pre>";
						$sqlpc='SELECT zone_id, zone_name FROM gs_user_zones WHERE user_id=1 AND group_id=2';
						$stmpc=mysql_query($sqlpc);
						//$arrdata=array();
						$i=0;
						while($colpc = mysql_fetch_array($stmpc))
						{
							//echo "<th>$col[0]</th>";
							echo "<th>T - $colpc[0]</th>";
							echo "<th>Dif</th>";
							$i++;
						}
					?>
					<th>Hora Llegada</th>
					<th>Diferecia<!--<?php echo "$i";?>	-->
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
					
					$sql = "SELECT DISTINCT d.hora_salida, gued.imei, gued.obj_name, d.hora_llegada
							FROM despachos AS d
							JOIN gs_user_events_data AS gued ON d.imei=gued.imei
							JOIN rutadespachos as rd ON rd.despacho_id=d.despacho_id
							JOIN gs_user_routes AS gur ON gur.route_id=rd.route_id
							WHERE d.hora_salida >= '2016-09-28 08:00:00' AND 
							gued.dt_tracker >= '2016-09-28 08:00:00' AND gued.dt_tracker  <= '2016-09-28 12:00:00'
							AND gued.type='zone_in'
							AND rd.route_id=1
							ORDER BY d.hora_salida, gued.dt_tracker ASC";
					
					$stm = mysql_query($sql);
					while($rs = mysql_fetch_array($stm)){

						$sql = "SELECT TIMEDIFF(gued.dt_tracker,d.hora_salida)
							FROM despachos AS d
							JOIN gs_user_events_data AS gued ON d.imei=gued.imei
							JOIN rutadespachos as rd ON rd.despacho_id=d.despacho_id
							JOIN gs_user_routes AS gur ON gur.route_id=rd.route_id
							WHERE d.hora_salida >= '2016-09-28 08:00:00' AND 
							gued.dt_tracker >= '2016-09-28 08:00:00' AND gued.dt_tracker  <= '2016-09-28 12:00:00'
							AND gued.type='zone_in'
							AND rd.route_id=1 AND gued.imei = '".$rs[1]."'
							ORDER BY d.hora_salida, gued.dt_tracker ASC";
							$stm1 = mysql_query($sql);
							
							echo "<tr ng-if='oresults'>";
							echo "<td>$rs[0]</td>";
							echo "<td>$rs[2]</td>";
							
							$i = 0;
							while($rx = mysql_fetch_array($stm1)){
								
								if($i==0){
										echo "<td>$rx[0]</td>";
										echo "<td>Dif</td>";
										$var=$rx[0];
										$i=1;
									}
								else {
									$diftpc= RestarHoras($var,$rx[0]);
									$var=$rx[0];
									echo "<td>$diftpc</td>";
									echo "<td>Dif</td>";
								}								
								
							}
							//print "<td>";print $rs[3]->format("%H:%I:%S"); print"</td>";
							//$ti= new DateTime($rs[3]);
							//print "<td>"; print $ti->format("%H:%I:%S"); print"</td>";
							echo "<td>$rs[3]</td>";
							echo "<td>Tota</td>";
							echo "</tr>";
							
							
							
							
						
					}
					
					
					
				?>
			
			</tbody>
		</table>
<?php
function RestarHoras($horaini,$horafin)
{	
	$horai=substr($horaini,0,2);
	$mini=substr($horaini,3,2);
	$segi=substr($horaini,6,2);

	$horaf=substr($horafin,0,2);
	$minf=substr($horafin,3,2);
	$segf=substr($horafin,6,2);

	$ini=((($horai*60)*60)+($mini*60)+$segi);
	$fin=((($horaf*60)*60)+($minf*60)+$segf);

	$dif=$fin-$ini;

	$difh=floor($dif/3600);
	$difm=floor(($dif-($difh*3600))/60);
	$difs=$dif-($difm*60)-($difh*3600);
	return date("H:i:s", mktime($difh,$difm,$difs));
}
?>

<?php
mysql_close($conexion);
?>
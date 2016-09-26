<?
        function serverCleanupUsers($days)
        {
		global $ms;
		
                $count = 0;
                
                if ($days == '') return $count;
                
                $q = "SELECT * FROM `gs_users` WHERE `privileges` NOT LIKE ('%super_admin%')
						AND `privileges` NOT LIKE ('%admin%')
						AND `privileges` NOT LIKE ('%manager%')
						AND `privileges` NOT LIKE ('%subuser%')
						AND dt_login < DATE_SUB(UTC_DATE(), INTERVAL ".$days." DAY)";
		$r = mysqli_query($ms, $q); 
		
		while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$user_id = $row["id"];
			$username = $row["username"];
			
			$q2 = 'SELECT * FROM `gs_user_objects` WHERE `user_id`="'.$user_id.'"';
			$r2 = mysqli_query($ms, $q2);
			
			$remove = true;
			
			while ($row2 = mysqli_fetch_array($r2,MYSQL_ASSOC))
			{
				$imei = $row2['imei'];
				
				if (checkObjectActive($imei))
				{
				    $remove = false;
				}
			}
			
			if ($remove == true)
			{
				$count++;
				
				delUser($user_id);
			}
		}
                
                return $count;
        }
        
        function serverCleanupObjectsNotActivated($days)
        {
		global $ms;
		
                $count = 0;
                
                if ($days == '') return $count;
                
                $q = "SELECT * FROM `gs_objects` WHERE `active`='false' AND `active_dt` < DATE_SUB(UTC_DATE(), INTERVAL ".$days." DAY)";
		$r = mysqli_query($ms, $q);
		
		while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
                        $count++;
                        
			$imei = $row["imei"];
			
			delObjectSystem($imei);
		}
                
                return $count;
        }
        
        function serverCleanupObjectsNotUsed()
        {
		global $ms;
		
                $count = 0;
                 
                $q = "SELECT * FROM `gs_objects`";
		$r = mysqli_query($ms, $q);
		
		while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$imei = $row["imei"];
			
			if(!checkObjectExistsUser($imei))
			{
				$count++;
				
				delObjectSystem($imei);
			}
		}
		
                return $count;
        }
        
        function serverCleanupDbJunk()
        {
		global $ms;
		
                $count = 0;
		
		// check for user junk records
		$user_ids = array();
		
		$q = "SELECT * FROM `gs_users` WHERE `privileges` NOT LIKE ('%subuser%')";
		$r = mysqli_query($ms, $q); 
		
		while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$user_ids[] = $row["id"];
		}
		
		if (count($user_ids) > 0)
		{
			// gs_user_objects
			$q = "SELECT * FROM `gs_user_objects`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$user_id = $row['user_id'];
	
				if (!in_array($user_id, $user_ids))
				{
					$q2 = "DELETE FROM `gs_user_objects` WHERE `user_id`='".$user_id."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
			
			// gs_user_object_groups
			$q = "SELECT * FROM `gs_user_object_groups`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$user_id = $row['user_id'];
	
				if (!in_array($user_id, $user_ids))
				{
					$q2 = "DELETE FROM `gs_user_object_groups` WHERE `user_id`='".$user_id."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
			
			// gs_user_object_drivers
			$q = "SELECT * FROM `gs_user_object_drivers`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$user_id = $row['user_id'];
	
				if (!in_array($user_id, $user_ids))
				{
					$q2 = "DELETE FROM `gs_user_object_drivers` WHERE `user_id`='".$user_id."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
			
			// gs_user_object_passengers
			$q = "SELECT * FROM `gs_user_object_passengers`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$user_id = $row['user_id'];
	
				if (!in_array($user_id, $user_ids))
				{
					$q2 = "DELETE FROM `gs_user_object_passengers` WHERE `user_id`='".$user_id."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
			
			// gs_user_object_trailers
			$q = "SELECT * FROM `gs_user_object_trailers`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$user_id = $row['user_id'];
	
				if (!in_array($user_id, $user_ids))
				{
					$q2 = "DELETE FROM `gs_user_object_trailers` WHERE `user_id`='".$user_id."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
			
			// gs_user_places_groups
			$q = "SELECT * FROM `gs_user_places_groups`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$user_id = $row['user_id'];
	
				if (!in_array($user_id, $user_ids))
				{
					$q2 = "DELETE FROM `gs_user_places_groups` WHERE `user_id`='".$user_id."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
			
			// gs_user_markers
			$q = "SELECT * FROM `gs_user_markers`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$user_id = $row['user_id'];
	
				if (!in_array($user_id, $user_ids))
				{
					$q2 = "DELETE FROM `gs_user_markers` WHERE `user_id`='".$user_id."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
			
			// gs_user_routes
			$q = "SELECT * FROM `gs_user_routes`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$user_id = $row['user_id'];
	
				if (!in_array($user_id, $user_ids))
				{
					$q2 = "DELETE FROM `gs_user_routes` WHERE `user_id`='".$user_id."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
			
			// gs_user_zones
			$q = "SELECT * FROM `gs_user_zones`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$user_id = $row['user_id'];
	
				if (!in_array($user_id, $user_ids))
				{
					$q2 = "DELETE FROM `gs_user_zones` WHERE `user_id`='".$user_id."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
			
			// gs_user_cmd
			$q = "SELECT * FROM `gs_user_cmd`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$user_id = $row['user_id'];
	
				if (!in_array($user_id, $user_ids))
				{
					$q2 = "DELETE FROM `gs_user_cmd` WHERE `user_id`='".$user_id."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
			
			// gs_user_events
			$q = "SELECT * FROM `gs_user_events`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$user_id = $row['user_id'];
	
				if (!in_array($user_id, $user_ids))
				{
					$q2 = "DELETE FROM `gs_user_events` WHERE `user_id`='".$user_id."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
			
			// gs_user_events_data
			$q = "SELECT * FROM `gs_user_events_data`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$user_id = $row['user_id'];
	
				if (!in_array($user_id, $user_ids))
				{
					$q2 = "DELETE FROM `gs_user_events_data` WHERE `user_id`='".$user_id."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}		
			
			// gs_user_templates
			$q = "SELECT * FROM `gs_user_templates`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$user_id = $row['user_id'];
				
				if (!in_array($user_id, $user_ids))
				{
					$q2 = "DELETE FROM `gs_user_templates` WHERE `user_id`='".$user_id."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
			
			// gs_user_reports
			$q = "SELECT * FROM `gs_user_reports`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$user_id = $row['user_id'];
				
				if (!in_array($user_id, $user_ids))
				{
					$q2 = "DELETE FROM `gs_user_reports` WHERE `user_id`='".$user_id."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
		}
		
		// check for object junk records
		$object_imeis = array();
		
		$q = "SELECT * FROM `gs_objects`";
		$r = mysqli_query($ms, $q); 
		
		while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$object_imeis[] = $row["imei"];
		}
		
		if (count($object_imeis) > 0)
		{
			// gs_user_objects
			$q = "SELECT * FROM `gs_user_objects`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$imei = $row['imei'];
				
				if (!in_array($imei, $object_imeis))
				{
					$q2 = "DELETE FROM `gs_user_objects` WHERE `imei`='".$imei."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
			
			// gs_object_sensors
			$q = "SELECT * FROM `gs_object_sensors`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$imei = $row['imei'];
				
				if (!in_array($imei, $object_imeis))
				{
					$q2 = "DELETE FROM `gs_object_sensors` WHERE `imei`='".$imei."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
			
			// gs_object_services
			$q = "SELECT * FROM `gs_object_services`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$imei = $row['imei'];
				
				if (!in_array($imei, $object_imeis))
				{
					$q2 = "DELETE FROM `gs_object_services` WHERE `imei`='".$imei."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
		}
		
		// check for event junk records
		$event_ids = array();
		
		$q = "SELECT * FROM `gs_user_events`";
		$r = mysqli_query($ms, $q); 
		
		while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$event_ids[] = $row["event_id"];
		}
		
		if (count($event_ids) > 0)
		{
			// gs_user_events_status
			$q = "SELECT * FROM `gs_user_events_status`";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$event_id = $row['event_id'];
				
				if (!in_array($event_id, $event_ids))
				{
					$q2 = "DELETE FROM `gs_user_events_status` WHERE `event_id`='".$event_id."'";				
					$r2 = mysqli_query($ms, $q2);
					
					$count++;
				}
			}
		}
		
		return $count;
        }
?>
<?
    include ('../init.php');
    
    
    $q = "SELECT * FROM `gs_objects` WHERE `active_dt` BETWEEN UTC_DATE() AND DATE_ADD(UTC_DATE(), INTERVAL 1 YEAR)";
    $r = mysqli_query($ms, $q);
    $num = mysqli_num_rows($r);
    
    echo 'Active during 1 year period: '.$num;
    
?>
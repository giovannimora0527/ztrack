<?
session_start();
include ('../init.php');
include ('../func/fn_common.php');
checkUserSession();
checkUserCPanelPrivileges();
header('Content-Type:text/plain');
?>
[2016-10-01 17:01:17] ::1 [1]admin - User login via http call: successful
[2016-10-03 15:30:58] ::1 [4]giovanni - User login via http call: successful
[2016-10-04 15:27:34] 192.168.0.102 [1]admin - User login: successful
[2016-10-04 15:56:23] 192.168.0.102 [1]admin - User login via http call: successful
[2016-10-05 16:31:54] ::1 [1]admin - User login via http call: successful

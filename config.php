<?
        // ############################################################
        // All listed setting can be changed only by editing this file
        // Other settings can be changed from CPanel/Manage server
        // ############################################################
        
        $gsValues['VERSION_ID'] = 1400;
        $gsValues['VERSION'] = '2.9.1';
        
        $gsValues['HTTP_MODE'] = 'http'; // options: http/https
        
        // lock admin to IP addresses, example $gsValues['ADMIN_IP'] = '127.0.0.1,222.222.222.222,333.333.333.333';
        // if left $gsValues['ADMIN_IP'] = ''; will accept admin login from any IP
        $gsValues['ADMIN_IP'] = '';
        
        $gsValues['SERVER_IP'] = 'http://186.144.86.177/'; // used only as information in CPanel
        $gsValues['URL_SERVER_PORTS'] = '10000';  // used only as information in CPanel
        
        // multi server login
        $gsValues['MULTI_SERVER_LOGIN'] = false; // options: false/true
        $gsValues['MULTI_SERVER_LIST'] = array('' => '');
        
        $gsValues['OBJECT_LIMIT'] = 0; // options: 0 means no limit, number sets limit
        $gsValues['LOGIN_VIA_HTTP'] = false; // options: false/true  (true not recommended)
        $gsValues['LOCATION_FILTER'] = true; // options: false/true
        $gsValues['CURL'] = false; // options: false/true
        
        // path to root of web application
        // if application is installed not in root folder of web server, then folder name must be added, for example we install it in track folder: $_SERVER['DOCUMENT_ROOT'].'/track';
        // very often web servers have no $_SERVER['DOCUMENT_ROOT'] set at all, then direct path should be used, for example c:/wamp/www or any other leading to www or public_html folder
        $gsValues['PATH_ROOT'] = $_SERVER['DOCUMENT_ROOT'] .'/track';
        // url to root of web application, example: $gsValues['URL_ROOT'] = 'YOUR_DOMAIN/track';
        $gsValues['URL_ROOT'] = 'http://localhost/track';
        
        $gsValues['URL_GC'] = array(); // do not remove this line
        $gsValues['URL_GC'][] = 'http://localhost/track/tools/gc/google.php'; // url to geocoder, used for getting addresses, example: $gsValues['URL_GC'][] = 'YOUR_DOMAIN/track/tools/gc/google.php';
        //$gsValues['URL_GC'][] = ''; // another url to geocoder (if needed)
        //$gsValues['URL_GC'][] = ''; // another url to geocoder (if needed)
        
        // hardware key, should be same as in GPS-Server.exe
        $gsValues['HW_KEY'] = '746CAD91167482BE4EAB5C37B4D211DA';
        
        // connection to MySQL database
        $gsValues['DB_HOSTNAME'] = '127.0.0.1'; // database host
        $gsValues['DB_PORT']     = '3306'; // database host
        $gsValues['DB_NAME']	 = 'db'; // database name
        $gsValues['DB_USERNAME'] = 'root'; // database user name
        $gsValues['DB_PASSWORD'] = ''; // database password
?>
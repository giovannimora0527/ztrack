<?
        // disable PHP xdebug module
        if(function_exists('xdebug_disable')) { xdebug_disable(); }
        error_reporting(E_ALL ^ E_DEPRECATED);
        
        // set 0 UTC timezone
        date_default_timezone_set('UTC');
        
        $gsValues = array();
        
        include ('config.custom.php');
        include ('config.php');
        
        // strip server name slashes
        $gsValues['NAME'] = stripcslashes($gsValues['NAME']);
        
        // check for some variables
        if (!isset($gsValues['CONNECTION_TIMEOUT']))
        {
                $gsValues['CONNECTION_TIMEOUT'] = '5';
        }
        
        if (!isset($gsValues['URL_LOGIN']))
        {
            $gsValues['URL_LOGIN'] = $gsValues['URL_ROOT'];
        }
        
        // check for last slash in root path
        if (substr($gsValues['PATH_ROOT'], -1) != '/')
        {
                $gsValues['PATH_ROOT'] .= '/';
        }
        
        // prepare url to logo image
        $gsValues['URL_LOGO'] = $gsValues['URL_ROOT'].'/img/logo.png';
        
        // prepare language array
        $la = array();
        
        // gets language from cookies
        if (isset($_COOKIE['gs_language']))
        {
                $gsValues['LANGUAGE'] = $_COOKIE['gs_language'];
        }
        else
        {
                $expire = time() + 2592000;
                setcookie('gs_language', $gsValues['LANGUAGE'], $expire, '/');
        }
        
        // puts selected language into cookies
        if (isset($_GET['lng']))
        {
                $gsValues['LANGUAGE'] = $_GET['lng'];
                $expire = time() + 2592000;
                setcookie('gs_language', $gsValues['LANGUAGE'], $expire, '/');
        }
        
        // connect to mysql  
        $ms = mysqli_connect($gsValues['DB_HOSTNAME'], $gsValues['DB_USERNAME'], $gsValues['DB_PASSWORD'], $gsValues['DB_NAME'], $gsValues['DB_PORT']);
        
        if (!$ms)
        {
                echo "Error connecting to database.";
                die;
        }
        
        mysqli_set_charset($ms, 'utf8');
        
        $q = "SET SESSION sql_mode = ''";
        $r = mysqli_query($ms, $q);
                
        // security to avoid MySQL injection attacks
        if(isset($_COOKIE))
        {
                foreach ($_COOKIE as $key => $value)
                {
                        if(get_magic_quotes_gpc())
                        {
                                $value = stripslashes($value);
                        }
                        
                        if(!is_array($value))
                        {
                                $value = mysqli_real_escape_string($ms, $value);
                        }
                        
                        $_COOKIE[$key] = $value;
                }
        }
        
        if(isset($_POST))
        {
                foreach ($_POST as $key => $value)
                {
                        if(get_magic_quotes_gpc())
                        {
                                $value = stripslashes($value);
                        }
                        
                        if(!is_array($value))
                        {
                                $value = mysqli_real_escape_string($ms, $value);
                        }
                        
                        $_POST[$key] = $value;
                }
        }
        
        if(isset($_GET))
        {
                foreach ($_GET as $key => $value)
                {
                        if(get_magic_quotes_gpc())
                        {
                                $value = stripslashes($value);
                        }
                        
                        if(!is_array($value))
                        {
                                $value = mysqli_real_escape_string($ms, $value);
                        }
                    
                        $_GET[$key] = $value;
                }
        }
?>
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('../../../init.php');
include ('../../../func/fn_common.php');
session_start();

deleteUserSessionHash($_SESSION["user_id"]);
session_unset();
session_destroy();
session_write_close();
echo $gsValues['URL_LOGIN'];                            
                

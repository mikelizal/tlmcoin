<?php

require("../eng/eng.php");
require("admin.php");

session_start();

$adminsession=check_adminsession();

if (!$adminsession) {
    render_loginform();
    die();
}

$c = input_get_int_parameter('c',0);
$sc = input_get_int_parameter('sc',0);

//print("c: ".$c." sc: ".$sc);
render_logoutlink();

if ($c == 0) {
    print('Admin menu<br>'."\n");

    print('[<a href="?c=1">CREATEDB</a>] - Create database <br>'."\n");

} else if ($c == 1) {
    print("<pre>\n");
    $cdb = new CoinDB();
    $cdb->resetDB();
    
    print("</pre>\n");
}
        

    
    

?>
<?php

require("../eng/eng.php");
require("admin.php");

session_start();

$in_user=input_post_str_parameter('u');
$in_pass=input_post_str_parameter('p');


if ( $in_user == 'mikel' && $in_pass == 'coinmasterpass' ) {
    adminsession_enter();
    print('Login OK [<a href="./">Enter</a>]');
} else {
    adminsession_clear();
    render_loginform("Error unaouthorized");
}

?>

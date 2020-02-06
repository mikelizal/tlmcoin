<?php
    require("../eng/eng.php");
    require("protocol.php");

    $in_srcid = input_get_int_parameter('srcid',null);
    $in_dstid = input_get_int_parameter('dstid',null);
    $in_auth = input_get_str_parameter('auth',null);
    $in_value = input_get_float_parameter('value',null);
    $in_valuestr = input_get_str_parameter('value',null);
    $all=false;
    if ($in_valuestr=="all") {
        $all=true;
    }

    if ($in_srcid==null) response_error_and_die("missing srcid");
    if ($in_dstid==null) response_error_and_die("missing dstid");
    if ($in_auth==null) response_error_and_die("missing auth");
    if ($in_value==null && !$all) response_error_and_die("missing value");

    $cdb = new CoinDB();
    $result= $cdb->transfer($in_srcid,$in_auth,$in_dstid,$in_value);
    
    if (!$result) {
        response_print_and_die(array("ok"=>false,"msg"=>"unknown error"));        
    }

    response_print($result);

?>
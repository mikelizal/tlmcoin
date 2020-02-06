<?php

    require("../eng/eng.php");
    require("protocol.php");

    $in_entropy = input_get_str_parameter('e',null);
    $in_srcid = input_get_int_parameter('srcid',null);
    $in_auth = input_get_str_parameter('auth',null);
    $in_value = input_get_float_parameter('value',null);
    $in_valuestr = input_get_str_parameter('value',null);
    $all=false;
    if ($in_valuestr=="all") {
        $all=true;
    }

    if ($in_srcid==null) response_error_and_die("missing srcid");
    if ($in_auth==null) response_error_and_die("missing auth");
    if ($in_value==null && !$all) response_error_and_die("missing value");

    $cdb = new CoinDB();
    $newcoin = $cdb->createCoinWithValue(0);
    if (!$newcoin) {
        response_result_and_die(1,"cannot create new coin");
    }

    $result= $cdb->transfer($in_srcid,$in_auth,$newcoin["id"],$in_value);

    if (!$result) {
        $cdb->cleanCoin($newcoin["id"]);
        response_result_and_die(1,"transfer failed, new coin deleted");        
    }
    if (!$result['ok']) {
        $cdb->cleanCoin($newcoin["id"]);
        response_result_and_die(1,"transfer failed, new coin deleted");        
    }

    $newcoin['value']=$result['tvalue'];
    response_print($newcoin);

?>
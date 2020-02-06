<?php

    require("../eng/eng.php");
    require("protocol.php");

    $in_id = input_get_int_parameter('coinid',null);

    $in_auth = input_get_str_parameter('auth',null);


    if ($in_id == null) {
        response_error_and_die("missing coinid");
    }

    $cdb = new CoinDB();

    if ( $in_auth===null ) {
        $v = $cdb->getCoinValue($in_id);
        $errortext="coin does not exist";
    } else {
        $v = $cdb->getCoinValueWithAuth($in_id,$in_auth);    
        $errortext="coin does not exist or invalid secret";
    }

    if (!$v) {
        response_error_and_die($errortext);
    }

    $resp = array(
        "coinid" => $in_id,
        "value" => $v
    );

    response_print($resp);

?>
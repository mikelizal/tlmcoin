<?php

    require("../eng/eng.php");
    require("protocol.php");

    $r = array(
        "version" => "1",
        "currency_name" => $CURRENCY_NAME ,
        "currency_name" => $VALUE_NAME ,
        "coin_name" => $COIN_NAME ,
        "coins_name" => $COINS_NAME ,
        "min_coin_value" => $MIN_COIN_VALUE,
        "max_coin_value" => $MAX_COIN_VALUE,        
        "x" => "x"
    );


    response_print($r);
?>
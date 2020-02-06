<?php

function response_print($a) {
    print(json_encode($a)."\n");
}

function response_print_and_die($a) {
    response_print($a);
    die();
}


function response_error($errtext) {
    $r = array(
        "error" => $errtext
    );
    response_print($r);
}

function response_error_and_die($errtext) {
    response_error($errtext);
    die();
}

function response_result($status, $msg) {
    $r = array(
        "problem" => $status,
        "msg" => $msg
    );
    response_print($r);
}

function response_result_and_die($status, $msg) {
    response_result($status, $msg);
    die();
}


?>
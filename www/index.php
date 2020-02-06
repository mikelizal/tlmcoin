<?php

require('conf/defaultconf.php');
require('conf/conf.php');

$page_title=$COIN_NAME . " tools";

$template = file_get_contents('template.html');

$placeholders=array();
$contents=array();

$placeholders[]='##title##';
$contents[]=$page_title;

$placeholders[]='##info##';
$info=file_get_contents('info.html');
//$info=str_replace(array('@coins','@coin','@nombre','@servidor'),array($COINS_NAME,$COIN_NAME,$CURRENCY_NAME,$SERVER),$info);

$contents[]=$info;


$page = str_replace($placeholders,$contents,$template);

$page=str_replace(array('@coins','@coin','@nombre','@servidor'),array($COINS_NAME,$COIN_NAME,$CURRENCY_NAME,$SERVER),$page);


print($page);

?>
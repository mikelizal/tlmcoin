<?php

    require("../eng/eng.php");

    $template = file_get_contents("../api_template.html");
    $info = file_get_contents("../api_info.html");
    $info = str_replace("@servidor",$SERVER,$info);
    $page = str_replace("##info##",$info,$template);

    print($page);
?>
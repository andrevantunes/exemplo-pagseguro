<?php

function pre($data, $stop = true){
    echo '<pre>';
    if($data === true) echo 'TRUE';
    else if($data === false) echo 'FALSE';
    else if(is_string($data) || is_numeric($data)) echo $data;
    else print_r($data);
    echo '</pre>';
    if($stop) exit;
}
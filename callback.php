<?php
// Em teste

$content = [
    'get' => $_GET,
    'post' => $_POST,
];

file_put_contents('./transation-'.time().'.log', json_encode($content));
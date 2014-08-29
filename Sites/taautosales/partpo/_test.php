<?php

$data["a"] = "aaa";
$data["b"] = "bbb";

$data[0]["a"] = "a";
$data[0]["b"] = "b";
$data[0]["c"] = "c";

// var_dump($data);
echo json_encode($data);
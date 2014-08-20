<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "autocomplete"){
    $term = $_GET['term'];

    $qry_name=pg_query("select * from \"VAllCustomers\" WHERE cus_name LIKE '%$term%' OR surname LIKE '%$term%' OR res_id LIKE '%$term%' ORDER BY \"cus_name\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $res_id = $res_name["res_id"];
        $cus_name = trim($res_name["cus_name"]);
        $surname = trim($res_name["surname"]);
        
        $dt['value'] = $res_id."#".$cus_name." ".$surname;
        $dt['label'] = "{$res_id}, {$cus_name} {$surname}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
}
?>
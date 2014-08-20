<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "autocomplete"){
    $term = $_GET['term'];

    $qry_name=pg_query("select * from \"WithdrawalSlip\" WHERE \"wd_id\" LIKE '%$term%' ORDER BY \"wd_id\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $wd_id = $res_name["wd_id"];
      
        $dt['value'] = $wd_id;
        $dt['label'] = "{$wd_id}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
}
?>
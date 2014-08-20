<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_GET['cmd'];
$term = $_GET['term'];

if($cmd == "autocomplete"){
 $qry_name = pg_query(" SELECT * FROM v_cancel_temp_receipt WHERE c_receipt LIKE '%$term%' ORDER BY c_receipt DESC LIMIT 1");								  
    $numrows = pg_num_rows($qry_name);
	
    while($res_name=pg_fetch_array($qry_name)){
        $receipt_no = trim($res_name["c_receipt"]);
        
        $dt['value'] = $receipt_no;
        $dt['label'] = "{$receipt_no}";
        $matches[] = $dt;
    }
	
    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
}
?>
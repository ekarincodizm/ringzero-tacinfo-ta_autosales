<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];
$res_id = $_REQUEST['res_id'];

if($cmd == "cancel_invoice"){
    $term = $_GET['term'];

    $qry_name=pg_query(" SELECT * FROM \"Invoices\" WHERE \"inv_no\" LIKE '%$term%' AND \"res_id\" = '$res_id' AND substr(\"inv_no\",3,1) LIKE 'V' AND \"cancel\" = 't' ORDER BY inv_no DESC ");
	$numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $inv_no = trim($res_name["inv_no"]);
        $dt['value'] = $inv_no;
        $dt['label'] = "{$inv_no} ";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
	
}else if($cmd == "cancel_receipt"){
	$term = $_GET['term'];

	$qry_name = pg_query(" SELECT * FROM v_cancel_receipt WHERE c_receipt LIKE '%$term%'  AND res_id = '$res_id' AND substr(\"c_receipt\",3,1) LIKE 'R' ORDER BY c_receipt DESC ");								  
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
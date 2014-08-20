<?php
include_once("../include/config.php");
	
	$cmd = pg_escape_string($_GET['cmd']);
    $term = pg_escape_string($_GET['term']);
	
	if($cmd == "docacc"){
	
    $qry_name=pg_query("select acb_id from account.\"AccountBookHead\" where \"acb_id\" like '%$term%' AND cancel='FALSE' ORDER BY \"acb_id\" ASC");
	
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $acb_id = $res_name["acb_id"];
        
        $dt['value'] = $acb_id;
        $dt['label'] = "{$acb_id}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
	
	}

	
?>
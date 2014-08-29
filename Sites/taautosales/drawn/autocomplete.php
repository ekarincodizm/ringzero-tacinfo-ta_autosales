<?php
include_once("../include/config.php");
	
	$cmd = $_GET['cmd'];
    $term = $_GET['term'];
	
	if($cmd == "car_plate"){
	
    $qry_name=pg_query("SELECT * FROM \"P_NewCarPlate\"WHERE \"new_plate\" LIKE '%$term%' ORDER BY \"new_plate\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $new_plate = trim($res_name["new_plate"]);
        
        $dt['value'] = $new_plate;
        $dt['label'] = "{$new_plate}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูลเก่า";
    }

    $matches = array_slice($matches, 0, 20);
    print json_encode($matches);
	
	}
	
?>
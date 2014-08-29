<?php
include_once("../include/config.php");
	
	$cmd = $_GET['cmd'];
    $term = $_GET['term'];
	
	if($cmd == "carregis"){
	
    $qry_name=pg_query("select license_plate from \"Cars\" WHERE \"license_plate\" LIKE '%$term%' ORDER BY \"license_plate\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $license_plate = trim($res_name["license_plate"]);
        
        $dt['value'] = $license_plate;
        $dt['label'] = "{$license_plate}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 10);
    print json_encode($matches);
	
	}
	if($cmd == "caridno"){
	
    $qry_name=pg_query("select car_idno from \"Cars\" WHERE \"car_idno\" LIKE '%$term%' ORDER BY \"car_idno\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $car_idno = trim($res_name["car_idno"]);
        
        $dt['value'] = $car_idno;
        $dt['label'] = "{$car_idno}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 10);
    print json_encode($matches);
	
	}
	if($cmd == "carnum"){
	
    $qry_name=pg_query("select car_num from \"Cars\" WHERE \"car_num\" LIKE '%$term%' ORDER BY \"car_num\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $car_num = trim($res_name["car_num"]);
        
        $dt['value'] = $car_num;
        $dt['label'] = "{$car_num}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 10);
    print json_encode($matches);
	
	}
	if($cmd == "marnum"){
	
    $qry_name=pg_query("select mar_num from \"Cars\" WHERE \"mar_num\" LIKE '%$term%' ORDER BY \"mar_num\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $mar_num = trim($res_name["mar_num"]);
        
        $dt['value'] = $mar_num;
        $dt['label'] = "{$mar_num}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 10);
    print json_encode($matches);
	
	}
?>
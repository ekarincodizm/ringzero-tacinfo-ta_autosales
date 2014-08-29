<?php
include_once("../include/config.php");
	
	$cmd = $_GET['cmd'];
    $term = $_GET['term'];
	
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

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
	
	}
	
	if($cmd == "plate"){
	
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

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
	
	}
	if($cmd == "findcars"){
	
    $qry_name=pg_query("select car_id,car_num,mar_num from \"Cars\" WHERE \"car_id\" LIKE '%$term%' or \"car_num\" LIKE '%$term%' or \"mar_num\" LIKE '%$term%' ORDER BY \"car_id\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $car_id = trim($res_name["car_id"]);
        $car_num = trim($res_name["car_num"]);
		$mar_num = trim($res_name["mar_num"]);
		
        $dt['value'] = $car_id;
        $dt['label'] = "{$car_id} , {$car_num} , {$mar_num}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 10);
    print json_encode($matches);
	
	}
	
	
	if($cmd == "repair"){
	
    $qry_name=pg_query("select license_plate from \"Cars\" WHERE \"license_plate\" LIKE '%$term%' AND \"car_status\" = 'P' ORDER BY \"license_plate\" ASC ");
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

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
	
	}
	
?>
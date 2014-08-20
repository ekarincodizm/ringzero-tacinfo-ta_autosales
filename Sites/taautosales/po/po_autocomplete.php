<?php
include_once("../include/config.php");
$cmd = pg_escape_string($_GET['cmd']);
$term = pg_escape_string($_GET['term']);
	
	if($cmd == "autocomplete"){
	
    $qry_name=pg_query("select * from \"Customers\" WHERE \"cus_name\" LIKE '%$term%' ORDER BY \"cus_name\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $cus_id = trim($res_name["cus_id"]);
        $cus_name = trim($res_name["cus_name"]);
        $surname = trim($res_name["surname"]);
        
        $dt['value'] = $cus_id."#".$cus_name." ".$surname;
        $dt['label'] = "{$cus_id} , {$cus_name} {$surname}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
	
	}
	
	//ค้นหาเลขที่ PO
	if($cmd == "po_id"){
		
		$qry_poid=pg_query("SELECT * FROM \"PurchaseOrders\" WHERE approve='FALSE' AND cancel='FALSE' AND (\"po_id\" LIKE '%$term%') ORDER BY \"po_id\" ASC ");
		$numrows = pg_num_rows($qry_poid);
		while($res_name=pg_fetch_array($qry_poid)){
			
			$po_id = trim($res_name["po_id"]);
		
			$dt['value'] = $po_id;
			$dt['label'] = "{$po_id}";
			$matches[] = $dt;
		}

		if($numrows==0){
			$matches[] = "ไม่พบข้อมูล";
		}

		$matches = array_slice($matches, 0, 10);
		print json_encode($matches);
	
	}
	
	//เลือกเฉพาะ รถ มือสอง ที่ ซื้อเข้าใหม่ ตั้งแต่ปี 2014
	if($cmd == "pocon_print"){
		
		$qry_poconid=pg_query("SELECT * FROM \"Cars\" WHERE (\"license_plate\" LIKE '%$term%') AND (\"car_type_id\" in (2,3,4)) AND substr(\"car_idno\",1,2) not in ('US','SC','CP')  ORDER BY \"license_plate\" ASC ");
		$numrows = pg_num_rows($qry_poconid);
		while($res_name=pg_fetch_array($qry_poconid)){
			
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
	
	if($cmd == "vender"){
		
		$qry_vender=pg_query("select distinct e.cus_id,e.cus_name,e.surname from  \"PurchaseOrders\" a 
							left join \"VFinances\" c on a.vender_id=c.finance_id::text 
							left join \"VVenders\" d on a.vender_id=d.vender_id::text
							left join \"Customers\" e on a.vender_id=e.cus_id or c.cus_id=e.cus_id or d.cus_id=e.cus_id
							where a.approve='FALSE' AND a.cancel='FALSE' AND (e.cus_id like '%$term%' or e.cus_name like '%$term%' or e.surname like '%$term%')
							order by e.cus_name");
		
			
		$numrows = pg_num_rows($qry_vender);
		while($res_name=pg_fetch_array($qry_vender)){
			
			$cus_id = trim($res_name["cus_id"]);
			$cus_name = trim($res_name["cus_name"]);
			$surname = trim($res_name["surname"]);
			
			
				$dt['value'] = $cus_id."#".$cus_name." ".$surname;
				$dt['label'] = "{$cus_id} , {$cus_name} {$surname}";
				$matches[] = $dt;
			
		}

		if($numrows==0){
			$matches[] = "ไม่พบข้อมูล";
		}

		$matches = array_slice($matches, 0, 10);
		print json_encode($matches);
	
	}
	if($cmd == "user_sale"){
	
    $qry_name=pg_query("select * from \"fuser\" WHERE \"fullname\" LIKE '%$term%' and user_group = '3' ORDER BY \"fullname\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $id_user = trim($res_name["id_user"]);
        $fullname = trim($res_name["fullname"]);
 
        
        $dt['value'] = $id_user."#".$fullname;
        $dt['label'] = "{$id_user} , {$fullname}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
	
	}
	
	if($cmd == "po_print"){
	
    $qry_name=pg_query("select * from \"PurchaseOrders\" where po_id like '%$term%' and cancel = false order by approve_date ASC");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $po_id = trim($res_name["po_id"]);
    
        $dt['value'] = $po_id;
        $dt['label'] = "{$po_id}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 20);
    print json_encode($matches);
	
	}
	
		// หน้าพิมพ์สัญญาซื้อขายรถยนต์
	if($cmd == "resid_print"){
		
		$qry_resid=pg_query("SELECT * FROM \"v_reprint_buy_contract\" WHERE (\"res_id\" LIKE '%$term%') order by res_id DESC");
				
		$numrows = pg_num_rows($qry_resid);
		while($res_name=pg_fetch_array($qry_resid)){
			
			$res_id = trim($res_name["res_id"]);
			$dt['value'] = $res_id;
			$dt['label'] = "{$res_id}";
			$matches[] = $dt;
		}

		if($numrows==0){
			 $matches[] = "ไม่พบข้อมูล";
		}

		$matches = array_slice($matches, 0, 10);
		print json_encode($matches);
	
	}
	
	// หน้า ขอยกเลิกการส่งมอบรถ
	if($cmd == "cancel_deliveries")
	{
		$qry_resid=pg_query("SELECT * FROM \"Reserves\" WHERE (\"res_id\" LIKE '%$term%') AND reserve_status = '1' ORDER BY res_id ASC ");
				
		$numrows = pg_num_rows($qry_resid);
		while($res_name=pg_fetch_array($qry_resid)){
			
			$res_id = trim($res_name["res_id"]);
			$dt['value'] = $res_id;
			$dt['label'] = "{$res_id}";
			$matches[] = $dt;
		}

		if($numrows==0){
			 $matches[] = "ไม่พบข้อมูล";
		}

		$matches = array_slice($matches, 0, 10);
		print json_encode($matches);
	}
?>
<?php
include_once("../include/config.php");
include_once("../include/function.php");

$car_idno = trim(pg_escape_string($_GET['car_idno']));
$po_con = trim(pg_escape_string($_GET['po_con']));


$qry_po_id = pg_query("select po_id from \"Cars\" where car_idno = '$car_idno'");
$po_id = pg_fetch_result($qry_po_id,0);
$poid_type = substr($po_id,0,4);
if($poid_type != "POSE"){
$car_type_id = getCarTypeID($po_id);

$qry_cus = pg_query("select vender_id,po_date,amount,vat from \"PurchaseOrders\" where po_id='$po_id' ");
$cus_id= pg_fetch_result($qry_cus,0);
$po_date= pg_fetch_result($qry_cus,1);
$po_price = pg_fetch_result($qry_cus,2);
$po_vat = pg_fetch_result($qry_cus,3);

$qry_finance = pg_query("select finance_id from \"Finances\" where cus_id='$cus_id' ");
$vender_id= pg_fetch_result($qry_finance,0);
}else{
	$car_type_id = 2;
	$vender_type = substr($car_idno,0,1);
	if($vender_type == 'C'){
		$vender_id = 2;
	}else if($vender_type == 'L'){
		$vender_id = 1;
	}
	$po_date=date('Y-m-d');
}

$qry_remain = pg_query("select value_remain from waiver where car_id_no = '$car_idno'");
$value_remain = pg_fetch_result($qry_remain,0);

	if($car_type_id == 2 || $car_type_id == 3){
		switch($vender_id){
			case 1: //ไทยเอซ ลิสซิ่ง
			
				$company = "thaiace";
				
				if($value_remain== 0){
				
					include_once("../report/car_cancel_pdf.php"); //หนังสือบอกเลิกสัญญา-PO-THCAP
					
				}else if($value_remain > 0){
					
					include_once("../report/car_waiver_pdf.php"); //หนังสือสละลิทธิ์รถยนต์-PO-THCAP 
					
				}
				
				break;
			case 2: //ไทยเอช แคปปิตอล
			
				$company = "capital";
				
				if($value_remain== 0){
				
					include_once("../report/car_cancel_pdf.php"); //หนังสือบอกเลิกสัญญา-PO-THAIACE
					
				}else if($value_remain > 0){
				
					include_once("../report/car_waiver_pdf.php"); //หนังสือสละลิทธิ์รถยนต์-PO-THAIACE
				}
				
				break;
		}
	}else if($car_type_id == 4){
	
		 $cusname = GetCusName($cus_id);
		 $price = number_format($po_price,2);
		 $vat = number_format($po_vat,2);
		 $po_total = $po_price + $po_vat;
		 $total_price = number_format($po_total,2);
		 
		include_once("../report/car_used_pdf.php"); //หนังสือสัญญาซื้อขายรถยนต์-PO Used CAR 
	}
?>
<?php
$nowyear = date('Y');

function CheckAuth(){
    if(empty($_SESSION["ss_iduser"])){
        return false;
    }else{
        return true;
    }
}
function num2thai($number){
    $t1 = array("ศูนย์", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า");
    $t2 = array("เอ็ด", "ยี่", "สิบ", "ร้อย", "พัน", "หมื่น", "แสน", "ล้าน");
    $zerobahtshow = 0; // ในกรณีที่มีแต่จำนวนสตางค์ เช่น 0.25 หรือ .75 จะให้แสดงคำว่า ศูนย์บาท หรือไม่ 0 = ไม่แสดง, 1 = แสดง
    (string) $number;
    $number = explode(".", $number);
    if(!empty($number[1])){
        if(strlen($number[1]) == 1){
            $number[1] .= "0";
        }elseif(strlen($number[1]) > 2){
            if($number[1]{2} < 5){
                $number[1] = substr($number[1], 0, 2);
            }else{
                $number[1] = $number[1]{0}.($number[1]{1}+1);
            }
        }
    }
    for($i=0; $i<count($number); $i++){
    $countnum[$i] = strlen($number[$i]);
    if($countnum[$i] <= 7){
    $var[$i][] = $number[$i];
    }else{
    $loopround = ceil($countnum[$i]/6);
    for($j=1; $j<=$loopround; $j++){
    if($j == 1){
    $slen = 0;
    $elen = $countnum[$i]-(($loopround-1)*6);
    }else{
    $slen = $countnum[$i]-((($loopround+1)-$j)*6);
    $elen = 6;
    }
    $var[$i][] = substr($number[$i], $slen, $elen);
    }
    }
    $nstring[$i] = "";
    for($k=0; $k<count($var[$i]); $k++){
    if($k > 0) $nstring[$i] .= $t2[7];
    $val = $var[$i][$k];
    $tnstring = "";
    $countval = strlen($val);
    for($l=7; $l>=2; $l--){
    if($countval >= $l){
    $v = substr($val, -$l, 1);
    if($v > 0){
    if($l == 2 && $v == 1){
    $tnstring .= $t2[($l)];
    }elseif($l == 2 && $v == 2){
    $tnstring .= $t2[1].$t2[($l)];
    }else{
    $tnstring .= $t1[$v].$t2[($l)];
    }
    }
    }
    }
    if($countval >= 1){
    $v = substr($val, -1, 1);
    if($v > 0){
    if($v == 1 && $countval > 1 && substr($val, -2, 1) > 0){
    $tnstring .= $t2[0];
    }else{
    $tnstring .= $t1[$v];
    }
    }
    }
    $nstring[$i] .= $tnstring;
    }
    }
    $rstring = "";
    if(!empty($nstring[0]) || $zerobahtshow == 1 || empty($nstring[1])){
    if($nstring[0] == "") $nstring[0] = $t1[0];
    $rstring .= $nstring[0]."บาท";
    }
    if(count($number) == 1 || empty($nstring[1])){
    $rstring .= "ถ้วน";
    }else{
    $rstring .= $nstring[1]."สตางค์";
    }
    return $rstring;
}

function formatDate($date,$subout){
    list($n_year,$n_month,$n_day) = split('-',$date);
    return $n_day."$subout".$n_month."$subout".$n_year;
}

function formatDateThai($date){
    $month = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฏาคม', '08'=>'สิงหาคม' ,'09'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');
    list($n_year,$n_month,$n_day) = split('-',$date);
    return $n_day." ".$month[$n_month]." พ.ศ. ".($n_year+543);
}

function insertZero($inputValue,$digit){
    $str = "" . $inputValue;
    while (strlen($str) < $digit){
        $str = "0" . $str;
    }
    return $str;
}

//==================== ให้โดยดึงจากค่า max แล้วมาบวกกับ 1 =================================//
function GetCusID(){
    $qry = pg_query("SELECT COUNT(\"cus_id\") AS countid FROM \"Customers\" ");
    $res = pg_fetch_array($qry);
    $res_count=$res['countid'];
    if($res_count == 0){
        $res_sn = 1;
    }else{
        $res_sn = $res_count+1;
    }

    $cus_id = "CUS".insertZero($res_sn,5);
    return $cus_id;
}

function GetCarID(){
    $qry = pg_query("SELECT COUNT(\"car_id\") AS countid FROM \"Cars\"");
    $res = pg_fetch_array($qry);
    $res_count=$res['countid'];
    if($res_count == 0){
        $res_sn = 1;
    }else{
        $res_sn = $res_count+1;
    }

    $cus_id = "CAR".insertZero($res_sn,5);
    return $cus_id;
}

function GetRecCusID(){
    $qry = pg_query("SELECT COUNT(\"regular_id\") AS countid FROM \"RegularCustomers\"");
    $res = pg_fetch_array($qry);
    $res_count=$res['countid'];
    if($res_count == 0){
        $res_sn = 1;
    }else{
        $res_sn = $res_count+1;
    }

    $cus_id = "REC".insertZero($res_sn,3);
    return $cus_id;
}

function GetCusName($id){
    $qry = pg_query("SELECT pre_name,cus_name,surname FROM \"Customers\" WHERE cus_id='$id' ");
    if($res = pg_fetch_array($qry)){
        $pre_name=trim($res['pre_name']);
        $cus_name=trim($res['cus_name']);
        $surname=trim($res['surname']);
        return $pre_name." ".$cus_name." ".$surname;
    }else{
        return "";
    }
}

function GetUserName($id){
    $qry = pg_query("SELECT fullname FROM \"fuser\" WHERE id_user='$id' ");
    if($res = pg_fetch_array($qry)){
        $fullname=trim($res['fullname']);
        return $fullname;
    }else{
        return "";
    }
}

function GetUserID(){
    $qry = pg_query("SELECT COUNT(\"id_user\") AS countid FROM \"fuser\"");
    $res = pg_fetch_array($qry);
    $res_count=$res['countid'];
    if($res_count == 0){
        $res_sn = 1;
    }else{
        $res_sn = $res_count+1;
    }

    $cus_id = insertZero($res_sn,3);
    return $cus_id;
}

function GetVender($id){
    if(empty($id)) return "";
    $qry = @pg_query("SELECT pre_name,cus_name,surname FROM \"VVenders\" WHERE vender_id='$id' ");
    if($res = @pg_fetch_array($qry)){
        $pre_name=trim($res['pre_name']);
        $cus_name=trim($res['cus_name']);
        $surname=trim($res['surname']);
        return $pre_name." ".$cus_name." ".$surname;
    }else{
        return "";
    }
}
/*function GetFinance($id){
    if(empty($id)) return "";
    $qry = @pg_query("SELECT pre_name,cus_name,surname FROM \"VFinances\" WHERE vender_id='$id' ");
    if($res = @pg_fetch_array($qry)){
        $pre_name=trim($res['pre_name']);
        $cus_name=trim($res['cus_name']);
        $surname=trim($res['surname']);
        return $pre_name." ".$cus_name." ".$surname;
    }else{
        return "";
    }
}*/
function GetFinance($id){
    if(empty($id)) return "";
    $qry = @pg_query("SELECT pre_name,cus_name,surname FROM \"VFinances\" WHERE cus_id='$id' ");
    if($res = @pg_fetch_array($qry)){
        $pre_name=trim($res['pre_name']);
        $cus_name=trim($res['cus_name']);
        $surname=trim($res['surname']);
        return $pre_name." ".$cus_name." ".$surname;
    }else{
        return "";
    }
}
function GetProductName($id){
    $qry = pg_query("SELECT name FROM \"Products\" WHERE product_id='$id' ");
    if($res = pg_fetch_array($qry)){
        $name=$res['name'];
        return $name;
    }else{
        return "";
    }
}
function GetProductStdName($id){
    $qry = pg_query("SELECT std_proc_name FROM \"std_products\" WHERE std_proc_id='$id' ");
    if($res = pg_fetch_array($qry)){
        $name=$res['std_proc_name'];
        return $name;
    }else{
        return "";
    }
}
function GetCarsName($id){
    if(empty($id)){ return ""; }
    $qry = pg_query("SELECT car_name FROM \"Cars\" WHERE car_id='$id' ");
    if($res = pg_fetch_array($qry)){
        $name=$res['car_name'];
        return $name;
    }else{
        return "";
    }
}

function GetRawMaterialName($id){
    $qry = pg_query("SELECT name FROM \"RawMaterial\" WHERE material_id='$id' ");
    if($res = pg_fetch_array($qry)){
        $name=$res['name'];
        return $name;
    }else{
        return "";
    }
}

function GetWarehousesName($id){
    if($id == "") return false;
    $qry = pg_query("SELECT wh_name FROM \"Warehouses\" WHERE wh_id='$id' ");
    if($res = pg_fetch_array($qry)){
        $name=$res['wh_name'];
        return $name;
    }else{
        return "";
    }
}

function GetWarehousesByOfficeID($id){
    if($id == "") return false;
    $qry = pg_query("SELECT wh_name FROM \"Warehouses\" WHERE office_id='$id' ");
    if($res = pg_fetch_array($qry)){
        $name=$res['wh_name'];
        return $name;
    }else{
        return "";
    }
}

function GetServicesName($id){
    if(empty($id)) return false;
    $qry = pg_query("SELECT name FROM \"Services\" WHERE service_id='$id' ");
    if($res = pg_fetch_array($qry)){
        $name=$res['name'];
        return $name;
    }else{
        return "";
    }
}

function GetServicesConstant($c){
    $service_id=@pg_query("SELECT service_id FROM \"Services\" WHERE constant_var='$c' ");
    $res_id=@pg_fetch_result($service_id,0);
    return $res_id;
}

function GetOfficeName($id){
    $office_id=@pg_query("SELECT office_id FROM fuser WHERE id_user='$id' ");
    $res_id=@pg_fetch_result($office_id,0);

    $qry_branch = pg_query("select branch_name from branch where branch_id='$res_id'");
	$branch_name = pg_fetch_result($qry_branch,0);
	
	return $branch_name;
}

// table ProductService ไม่มีอยู่ใน db (comment bye prae)
function GetProductServiceName($id){
    if(empty($id)) return false;
    $qry = pg_query("SELECT name FROM \"ProductService\" WHERE product_id='$id' ");
    if($res = pg_fetch_array($qry)){
        $name=$res['name'];
        return $name;
    }else{
        return "";
    }
}

function interest_rate($principle,$installment,$pay_time){
    if( empty($principle) OR empty($installment) OR empty($pay_time) ){
        return "ข้อมูลไม่ครบถ้วนไม่สามารถคำนวณได้";
        exit;
    }
    
    $qry = pg_query("select interest_rate('$principle','$installment','$pay_time')");
    $res = pg_fetch_result($qry,0);
    
    return $res;
}

function GetAmountPO($type,$po_id,$product_id){
    if(!empty($product_id)){
        $qry_detail = pg_query("SELECT SUM(amount) as sum_amount FROM \"StockMovement\" WHERE type_inout='$type' AND product_id='$product_id' AND ref_1='$po_id' AND ref_2 IS NULL ");
    }else{
        $qry_detail = pg_query("SELECT SUM(amount) as sum_amount FROM \"StockMovement\" WHERE type_inout='$type' AND ref_1='$po_id' AND ref_2 IS NULL ");
    }
    if($res_detail = pg_fetch_array($qry_detail)){
        $rt = $res_detail['sum_amount'];
    }
    
    if(empty($rt) OR $rt == 0){
        return 0;
    }else{
        return $rt;
    }
}

function GetConstantVar($id){
    $service_id=@pg_query("SELECT constant_var FROM \"ProductService\" WHERE product_id='$id' ");
    $res_id=@pg_fetch_result($service_id,0);
    return $res_id;
}

function GetAmountRawMaterial($id){
    if(empty($id)) return 0;
    $qry_detail2 = pg_query("SELECT SUM(amount) as sumamt FROM \"StockMovement\" WHERE product_id='$id' ");
    if($res_detail2 = pg_fetch_array($qry_detail2)){
        $sum = $res_detail2['sumamt'];
    }
    if(empty($sum)){
        $sum = 0;
    }
    return $sum;
}

// table RawMaterialProduct ไม่มีอยู่ใน db  (comment by prae)
function GetRawMaterialProductName($id){
    $qry = pg_query("SELECT name FROM \"RawMaterialProduct\" WHERE product_id='$id' ");
    if($res = pg_fetch_array($qry)){
        $name=$res['name'];
        return $name;
    }else{
        return "";
    }
}

function GetProductTypeUnit($product_id){
    $qry_detail = pg_query("SELECT unit FROM \"RawMaterial\" WHERE material_id='$product_id' ");
    if($res_detail = pg_fetch_array($qry_detail)){
        return $res_detail['unit'];
    }
}

function GetLighterRoofID(){
    $qry = pg_query("SELECT COUNT(\"lf_id\") AS countid FROM \"P_LighterRoof\"");
    $res = pg_fetch_array($qry);
    $res_count=$res['countid'];
    if($res_count == 0){
        $res_sn = 1;
    }else{
        $res_sn = $res_count+1;
    }

    $cus_id = "LF".insertZero($res_sn,5);
    return $cus_id;
}

function GetLinkTable($id){
    $qry_link_table=pg_query("SELECT link_table FROM \"Products\" WHERE \"product_id\" = '$id' AND cancel='FALSE' ");
    if($res_link_table=pg_fetch_array($qry_link_table)){
        return $res_link_table["link_table"];
    }else{
        return "";
    }
}

function GetTemporaryCustomers(){
    $qry = pg_query("SELECT COUNT(\"tem_id\") AS countid FROM \"TemporaryCustomers\"");
    $res = pg_fetch_array($qry);
    $res_count=$res['countid'];
    if($res_count == 0){
        $res_sn = 1;
    }else{
        $res_sn = $res_count+1;
    }

    $cus_id = "Tem".insertZero($res_sn,5);
    return $cus_id;
}

function GetListForSaleName($id){
    $qry = pg_query("SELECT name FROM \"ListForSale\" WHERE product_id='$id' ");
    if($res = pg_fetch_array($qry)){
        $name=$res['name'];
        return $name;
    }else{
        return "";
    }
}

function getAcTableAcID($id){
    $qry = pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='$id' ");
    if($res = pg_fetch_array($qry)){
        $name=$res['AcID'];
        return $name;
    }else{
        return "";
    }

}

function GetAllProductService($id){
    $qry = pg_query("SELECT name FROM \"VAllProduct_Service\" WHERE product_id='$id' ");
    if($res = pg_fetch_array($qry)){
        $name=trim($res['name']);
        return $name;
    }else{
        return "";
    }
}
function checknull($data){
	if($data == ""){		
		$a1 = "null";	
	}else{	
		$a1 = "'".$data."'";	
	}
	return $a1;
}
function getCarTypeID($poid){

	$carType = substr($poid, 2, 2);
	
	switch($carType){
		case 'NW':
			$carTypeid = "1";
			break;
		case 'RT': 
			$carTypeid = "3";
			break;
		case 'SC': 
			$carTypeid = "2";
			break;
		case 'US': 
			$carTypeid = "4";
			break;
	}
	
	return $carTypeid;
}
function getCusNameFromVender($id,$po_type_id){
	
		if($po_type_id == "PONW" or $po_type_id == "POMA"){
			$Customer = GetVender($id);
		}else{
			$Customer = GetFinance($id);
		}
		if($Customer == ""){
			$Customer = GetCusName($id);
		}
		return $Customer;
}
function nowDate() 
{
	$qryDate = pg_query("select current_date");
	$nowDate = pg_result($qryDate,0);
	return $nowDate;
}

function nowDateTime() 
{
	$qryDateTime = pg_query("select \"nowDateTime\"() ");
	$nowDateTime = pg_result($qryDateTime,0);
	return $nowDateTime;
}

$nowdate = nowDate();
$nowDateTime = nowDateTime();

function getCarColor($id){
	$qry = pg_query("select color_name from \"CarColor\" where color_id='$id' ");
	$color = pg_fetch_result($qry,0);
	
	return $color;
}
function genCarIDNO($poid,$dt_auto_id,$date){
	$year = substr($date,0,4);
	$carregis = substr($poid, 2, 12);
	
	/* gen car_idno ตามจำนวนรถของแต่ละ po
	$count_unit=@pg_query("select count_receive_good('$dt_auto_id')");
	$count_unit=@pg_fetch_result($count_unit,0);
	$count_unit+=1;
	
	if($count_unit<10){
		$number = "0".$count_unit;
	}else{
		$number = "0".$count_unit;
	}
	
	$res = $carregis."-".$number;
	*/ 
	
	$qry_po = pg_query("select * from \"PurchaseOrders\" where po_id='$poid' ");
	if($res = pg_fetch_array($qry_po)){
		$vender_id = trim($res['vender_id']);
		$po_type_id = trim($res['po_type_id']);
	}
	

		$qry_numcar = pg_query("select * from \"NumCar\" where extract(year from \"NumCar\".dateid)= '$year'");
		$numrow = pg_num_rows($qry_numcar);
		
		/*	การ gen_caridno แบบนับจำนวนรถแยกตามปี 
		if($numrow == 0){
			$insert_newrow = pg_query(" insert into \"NumCar\"(dateid) values ('$date') ");
			$new_car = 0;
			$used_car = 0;
		}else{
			if($res_numcar = pg_fetch_array($qry_numcar)){
				$new_car = $res_numcar['new_car'];
				$used_car = $res_numcar['used_car'];
			}
		}
		*/
		
		if($numrow == 0){
			$qry_current = pg_query("select * from \"NumCar\" where dateid in (select max(dateid) from \"NumCar\")");
			$num_current = pg_num_rows($qry_current);
			if($num_current > 0){
				if($res_numcar = pg_fetch_array($qry_current)){
					$new_car_current = $res_numcar['new_car'];
					$used_car_current = $res_numcar['used_car'];
				}
				$new_car = $new_car_current;
				$used_car = $used_car_current;
			}else{
				$new_car = 0;
				$used_car = 0;
			}
			$insert_newrow = pg_query(" insert into \"NumCar\"(dateid) values ('$date') ");
			
		}else{
			if($res_numcar = pg_fetch_array($qry_numcar)){
				$new_car = $res_numcar['new_car'];
				$used_car = $res_numcar['used_car'];
			}
		}
		
		
		$new_car += 1;
		$used_car += 1;
		
		if($po_type_id == "PONW"){
	
			$qry_vender = pg_query("select alphas from \"Venders\" where vender_id = '$vender_id' ");
			if($res_vender = pg_fetch_array($qry_vender)){
				$alphas = $res_vender['alphas'];
			}
		
			$columnType = "new_car";
			$car = $new_car;
			
		}else if($po_type_id == "POSC" or $po_type_id == "PORT"){
			$qry_vender = pg_query("select alphas from \"Finances\" where cus_id = '$vender_id' ");
			if($res_vender = pg_fetch_array($qry_vender)){
				$alphas = $res_vender['alphas'];
			}
		
			$columnType = "used_car";
			$car = $used_car;
			
		}else if($po_type_id == "POUS"){
			
			$alphas = "P";
			$columnType = "used_car";
			$car = $used_car;
		}
	
		$qry_update = pg_query("Update \"NumCar\" set $columnType='$car' where extract(year from \"NumCar\".dateid)= '$year' ");
		
		$car_idno = $alphas."-".$carregis."-".str_pad($car, 5, '0', STR_PAD_LEFT);
		
	return $car_idno;
}
function get_thai_month($mm){
	$thai_month_arr=array(  
    "00"=>"",  
    "01"=>"มกราคม",  
    "02"=>"กุมภาพันธ์",  
    "03"=>"มีนาคม",  
    "04"=>"เมษายน",  
    "05"=>"พฤษภาคม",  
    "06"=>"มิถุนายน",   
    "07"=>"กรกฎาคม",  
    "08"=>"สิงหาคม",  
    "09"=>"กันยายน",  
    "10"=>"ตุลาคม",  
    "11"=>"พฤศจิกายน",  
    "12"=>"ธันวาคม"                    
	); 
	return $thai_month_arr[$mm];
}
function get_Warehouses($id){
	if($id != ""){
		$qry = pg_query("select  wh_name from \"Warehouses\" where wh_id='$id'");
		$wh_name = pg_fetch_result($qry,0);
	return $wh_name;
	}else{
		return "";
	}
}
//============================ ประวัติการซื้อ - ขาย รถยนต์ =============================//
//======  ดึงชื่อ vender (buy from) =======//
function get_vender_name($vender_id){
    if(empty($vender_id)) return false;
    $qry = pg_query("SELECT concat(COALESCE(\"Customers\".pre_name), ' ', COALESCE(\"Customers\".cus_name), ' ', COALESCE(\"Customers\".surname)) AS vender_name
					 FROM \"Customers\" WHERE cus_id='$vender_id' ");
    if($res = pg_fetch_array($qry)){
        $vender_name=$res['vender_name'];
        return $vender_name;
    }else{
        return "";
    }
}

//====== ดึง customer (sale to) =====//
function get_cus_name($idno){
    if(empty($idno)) return false;
    $qry = pg_query(" SELECT  res.cus_id,concat(COALESCE(cus.pre_name), ' ', COALESCE(cus.cus_name), ' ', COALESCE(cus.surname)) AS cus_name
					  FROM \"Reserves\" res
					  LEFT JOIN \"Customers\" cus ON res.cus_id::text = cus.cus_id::text
					  WHERE res.\"IDNO\" = '$idno' ");
    if($res = pg_fetch_array($qry)){
        $cus_name=$res['cus_name'];
        return $cus_name;
    }else{
        return "";
    }
}

//===================== print count of receipt ===================//
function get_print_count($res_id,$receipt_type,$receipt_no){
    $qry = pg_query("SELECT max(print_count)as max FROM receipt_print_logs
					 WHERE receipt_type = '$receipt_type'
					 AND res_id = '$res_id'
					 AND receipt_no = '$receipt_no' ");
    $res = pg_fetch_array($qry);
    $res_count=$res['max'];
    if($res_count == 0){
        $print_count = 1;
    }else{
        $print_count = $res_count+1;
    }
	return $print_count;
}

//นับจำนวนครั้งของการพิมพ์เอกสาร เพิ่มใหม่  17/01/2014 
function print_count($doc_no,$doc_type){
    $qry = pg_query("SELECT max(print_count)as max FROM doc_print_logs
					 WHERE doc_no = '$doc_no'
					 AND doc_type = '$doc_type'");
    $res = pg_fetch_array($qry);
    $res_count=$res['max'];
    if($res_count == 0){
        $print_count = 1;
    }else{
        $print_count = $res_count+1;
    }
	return $print_count;
}

function exec_print_receipt_logs($res_id,$receipt_no,$id_user,$receipt_type,$print_count){
	$nowDateTime = nowDateTime();
	$in_qry = "INSERT INTO receipt_print_logs (res_id,receipt_no,id_user,receipt_type,print_count,print_date) VALUES('$res_id','$receipt_no','$id_user','$receipt_type','$print_count','$nowDateTime')";
	if($in_qry){ $obj_print_status = pg_query($in_qry);$status=0;}
	return $status;
}

//===================== print count of reserve ===================//
function get_print_count_res($res_id,$receipt_type){
    $qry = pg_query("SELECT max(print_count)as max FROM receipt_print_logs
					 WHERE receipt_type = '$receipt_type'
					 AND res_id = '$res_id' ");
    $res = pg_fetch_array($qry);
    $res_count=$res['max'];
    if($res_count == 0){
        $print_count = 1;
    }else{
        $print_count = $res_count+1;
    }
	return $print_count;
}

function exec_print_res_logs($res_id,$id_user,$receipt_type,$print_count){
	$nowDateTime = nowDateTime();
	$in_qry = "INSERT INTO receipt_print_logs (res_id,receipt_no,id_user,receipt_type,print_count,print_date) VALUES('$res_id',null,'$id_user','$receipt_type','$print_count','$nowDateTime')";
	if($in_qry){ $obj_print_status = pg_query($in_qry);$status=0;}
	return $status;
}

// รับชำระเงิน 
function exec_cashier($receipt_no,$chq_amount,$cash_amount,$res_id,$id_user,$discount_amount,$rec_date){
	//$nowDateTime = nowDateTime();
	//ใช้วันที่ รับชำระเงิน
	//$in_qry = "INSERT INTO receipt_tmp(receipt_no,receipt_date,chq_amount,cash_amount,res_id,id_user,discount_amount) 
	//VALUES('$receipt_no','$nowDateTime','$chq_amount','$cash_amount','$res_id','$id_user','$discount_amount')";
	$in_qry = "INSERT INTO receipt_tmp(receipt_no,receipt_date,chq_amount,cash_amount,res_id,id_user,discount_amount) 
								VALUES('$receipt_no','$rec_date','$chq_amount','$cash_amount','$res_id','$id_user','$discount_amount')";
	if($in_qry){ $obj_print_status = pg_query($in_qry);$status=0;}
	return $status;
}

// replace empty text 
function replace_empty_txt($data){
	if($data == "" or $data == 'null' or $data == '0' or $data == '0.00'){		
		$txt = "-";	
	}else{	
		$txt = "".$data."";	
	}
	return $txt;
}

// replace empty text 
function replace_empty_currency($data){
	if($data == "" or $data == 'null' or $data == '0' or $data == '0.00'){		
		$txt = "";	
	}else{	
		$txt = "".$data."";	
	}
	return $txt;
}

// remove comma
 function clean_data($a) {
	$b = str_replace( ',', '', $a );
	if( is_numeric( $b ) ) {
		$a = $b;
	}
     return $a;
}

function date_dmy($date){
	if(trim($date) == ""){
		return "";
	}else{
		$d = explode("-",$date);
		return $d[2]."/".$d[1]."/".$d[0];
	}

}
?>
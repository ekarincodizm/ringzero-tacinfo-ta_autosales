<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_POST['cmd'];
$iduser = $_SESSION["ss_iduser"];
$auto_id = pg_escape_string($_POST['auto_id']);
$remark = pg_escape_string($_POST['remark']);
$status = 0;
$Error = "พบข้อผิดพลาด \n";

pg_query("BEGIN WORK");

	$qry_select = pg_query("select \"car_id\",res_id from \"cancel_deliveries\" where \"canceledID\"='$auto_id' and \"appvStatus\"='9'");
	$num_row = pg_num_rows($qry_select);
	if($num_row>0){
		while($res = pg_fetch_array($qry_select)){
			$car_id = $res['car_id'];
			$res_id = $res['res_id'];
		}
	}else{
		$status++;
		$Error .= "มีการทำรายการอนุมัติไปก่อนหน้านี้แล้ว \n";
	}
		
	if($cmd == "appv")
	{
		// ตรวจสอบก่อนว่า สถานะล่าสุด เป็น ส่งมอบให้ลูกค้า หรือไม่
		$qry_chkS = pg_query("select \"target_go\", \"wh_id\" from \"CarMove\" where \"car_id\" = '$car_id' and \"auto_id\" = (select max(\"auto_id\") from \"CarMove\" where car_id = '$car_id')");
		$target_go = pg_result($qry_chkS,0);
		$wh_id = pg_result($qry_chkS,1);
		if($target_go != "0")
		{
			if($target_go != "")
			{
				$chkWH = "$target_go";
			}
			else
			{
				$chkWH = "$wh_id";
			}
			
			// หาสถานที่
			$qry_Warehouses = pg_query("select \"wh_name\" from \"Warehouses\" where \"wh_id\" = '$chkWH' ");
			$Warehouses = pg_fetch_result($qry_Warehouses,0);
			
			$status++;
			$Error .= "ไม่สามารถอนุมัติได้ เนื่องจาก ไม่ได้อยู่ในสถานะส่งมอบรถ(สถานะคลังปัจจุบัน : $Warehouses) \n";
		}
		
		//update เป็นอนุมัติ
		$qry_up = "update \"cancel_deliveries\" set  \"appvID\"='$iduser',\"appvStamp\"='$nowDateTime',\"appvStatus\"='1',\"appvRemark\"='$remark' where \"canceledID\"='$auto_id' and \"appvStatus\"='9'";
		if(pg_query($qry_up)){
		}else{
			$status++;
			$Error .= "Update ข้อมูลล้มเหลว  $qry_up \n";
		}
		
		//update Cars 
		$qry_up_car = "update \"Cars\" set car_status = 'R' where car_id='$car_id' and car_status = 'S' ";
		if(pg_query($qry_up_car)){
		}else{
			$status++;
			$Error .= "Update ข้อมูลล้มเหลว  $qry_up_car \n";
		}
		
		//update Reserve
		$qry_up_res = "update \"Reserves\" set reserve_status='2' , finance_date = NULL , rpay_date = NULL , due_date = NULL , \"IDNO\" = NULL
						where res_id='$res_id' and reserve_status='1' ";
		if(pg_query($qry_up_res)){
		}else{
			$status++;
			$Error .= "Update ข้อมูลล้มเหลว  $qry_up_res \n";
		}
		
		// สถานะรถจากคลัง ส่งมอบรถ เป็น สนญ.  โดย update ค่า ใน Table  CarMove   ดังนี้
		$qry_up_res = "Update \"CarMove\" set \"date_out\" = NULL , \"target_go\" = NULL , \"car_owner\" = NULL , \"remark\" = 'มีการยกเลิกส่งมอบรถ'  where car_id = '$car_id'
						and \"auto_id\" = (select max(\"auto_id\") from \"CarMove\" where car_id = '$car_id') and \"target_go\" = '0' ";
		if(pg_query($qry_up_res)){
		}else{
			$status++;
			$Error .= "Update ข้อมูลล้มเหลว  $qry_up_res \n";
		}
		
		// Table "CarMoveToCus"  ให้ปรับปรุง fields  status_appv = 2 ,  remark=’ยกเลิกการอนุมัติ’
		$qry_up_res = "Update \"CarMoveToCus\" set status_appv = '2' , \"remark\" = 'มีการยกเลิกส่งมอบรถ'  where car_id = '$car_id' ";
		if(pg_query($qry_up_res)){
		}else{
			$status++;
			$Error .= "Update ข้อมูลล้มเหลว  $qry_up_res \n";
		}
	}
	else if($cmd == "notappv")
	{
		//update ไม่อนุมัติ
		$qry_up = "update \"cancel_deliveries\" set  \"appvID\"='$iduser',\"appvStamp\"='$nowDateTime',\"appvStatus\"='0',\"appvRemark\"='$remark' where \"canceledID\"='$auto_id' and \"appvStatus\"='9'";
		if(pg_query($qry_up)){
		}else{
			$status++;
			$Error .= "Update ข้อมูลล้มเหลว  $qry_up \n";
		}
	}
	
if($status == 0){
	pg_query("COMMIT");
	$data = 1;
}else{
	pg_query("ROLLBACK");
	$data = "ไม่สามารถบันทึกได้! $Error $genidno";
}
echo $data;
?>
<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = pg_escape_string($_POST['cmd']);
$iduser = $_SESSION["ss_iduser"];
$editCarID = pg_escape_string($_POST['editCarID']);
$remark = pg_escape_string($_POST['remark']);
$status = 0;
$Error = "พบข้อผิดพลาด \n";

pg_query("BEGIN WORK");
		
$qry_select = pg_query("select * from \"CarsEditTemp\" where \"editCarID\" = '$editCarID' and \"appvStatus\" = '9'");
$num_row = pg_num_rows($qry_select);
if($num_row>0)
{
	if($res = pg_fetch_array($qry_select))
	{
		$car_id = trim($res['car_id']);
		$license_plate = trim($res['license_plate']);
		$product_id  = trim($res['product_id']);
		$car_num = trim($res['car_num']);
		$mar_num  = trim($res['mar_num']);
		$car_year = trim($res['car_year']);
		$color = trim($res['color']);
	}
}
else
{
	$status++;
	$Error .= "มีการทำรายการอนุมัติไปก่อนหน้านี้แล้ว \n";
}
	
if($cmd == "appv")
{
	//update อนุมัติ
	$qry_up = "update \"CarsEditTemp\" set \"appvID\" = '$iduser', \"appvStamp\" = '$nowDateTime', \"appvStatus\" = '1', \"appvNote\" = '$remark'
				where \"editCarID\" = '$editCarID' and \"appvStatus\" = '9'";
	if(pg_query($qry_up)){
	}else{
		$status++;
		$Error .= "Update ข้อมูลล้มเหลว  $qry_up \n";
	}
	
	//update ข้อมูลจริง
	$in_qry="Update \"Cars\" set \"license_plate\" = '$license_plate', \"product_id\" = '$product_id', \"car_num\" = '$car_num', \"mar_num\"='$mar_num',
				\"car_year\" = '$car_year', \"color\" = '$color' where \"car_id\" = '$car_id' ";
							
	if(!$res=@pg_query($in_qry)){
		$Error .= "Update ไม่สำเร็จ  $in_qry \n";
		$status++;
	}
	
	// ถ้ามีการขอแก้ไขสีรถด้วย จะ update ในตาราง CarMove ด้วย โดยจะ update เฉพาะรายการล่าสุดเท่านั้น
	$qry_upColorCar = "UPDATE \"CarMove\"
			SET \"color\" = '$color', \"car_owner\" = 'มีการขอแก้ไขสีรถ'
			WHERE \"auto_id\" = (select max(\"auto_id\") from \"CarMove\" where \"car_id\" = '$car_id')
			AND \"color\" <> '$color' ";
	if(!$res=@pg_query($qry_upColorCar)){
		$Error .= "Update สีรถ ไม่สำเร็จ  $qry_upColorCar \n";
		$status++;
	}
	
}else if($cmd == "notappv"){

	//update ไม่อนุมัติ
	$qry_up = "update \"CarsEditTemp\" set \"appvID\" = '$iduser', \"appvStamp\" = '$nowDateTime', \"appvStatus\" = '0', \"appvNote\" = '$remark'
				where \"editCarID\" = '$editCarID' and \"appvStatus\" = '9'";
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
        $data = "ไม่สามารถบันทึกได้! $Error";
    }
echo $data;
?>
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
		
		$qry_select = pg_query("select car_id,res_id from \"CarMoveToCus\" where auto_id='$auto_id' and status_appv='9'");
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
		
		//หาสีที่จอง และเช็คว่าขายสด หรือ ผ่อน
		$qry_select = pg_query("select reserve_color,finance_price from \"Reserves\" where res_id='$res_id' ");
		$reserve_color = trim(pg_fetch_result($qry_select,0));
		$finance_price = pg_fetch_result($qry_select,1);
		
		//หา id ของสีรถ
		$qry_color_id =  pg_query("select color_id from \"CarColor\" where color_name='$reserve_color' ");
		$color_id = pg_fetch_result($qry_color_id,0);
		
	if($cmd == "appv"){		
		//GEN IDNO
		if($finance_price == 0){ // ขายสด  SR
			$generate_id=@pg_query("select generate_id('$nowdate',$_SESSION[ss_office_id],14)");
		} else { // ขายผ่อน  SI
			$generate_id=@pg_query("select generate_id('$nowdate',$_SESSION[ss_office_id],2)");
		}
		
		$genidno=@pg_fetch_result($generate_id,0);
		if(@empty($genidno)){
		$Error .=   "cannot to generate idno ";
        $status++;
    }
		
		//update เป็นอนุมัติ
		$qry_up = "update \"CarMoveToCus\" set  appv_id='$iduser',appv_stamp='$nowDateTime',status_appv='1',remark='$remark' where auto_id='$auto_id' and status_appv='9'";
		if(pg_query($qry_up)){
		}else{
			$status++;
			$Error .= "Update ข้อมูลล้มเหลว  $qry_up \n";
		}
		
		//update Cars 
		$qry_up_car = "update \"Cars\" set car_status = 'S',color='$color_id' where car_id='$car_id' ";
		if(pg_query($qry_up_car)){
		}else{
			$status++;
			$Error .= "Update ข้อมูลล้มเหลว  $qry_up_car \n";
		}
		
		//update Reserve
		$qry_up_res = "update \"Reserves\" set \"IDNO\" = '$genidno',reserve_status='1' where res_id='$res_id' ";
		if(pg_query($qry_up_res)){
		}else{
			$status++;
			$Error .= "Update ข้อมูลล้มเหลว  $qry_up_res \n";
		}
		
		$qry_sel_other_res = pg_query("SELECT res_id FROM \"Reserves\" WHERE \"car_id\"='$car_id' ");
		$num_res = pg_num_rows($qry_sel_other_res);
		
		if($num_res>1){
			//update ใบจองอื่น ให้รอเปลี่ยนคัน
			$qry_up_other_res = "update \"Reserves\" set reserve_status='3' where car_id='$car_id' and res_id<>'$res_id' and reserve_status<>'1' ";
			if(pg_query($qry_up_other_res)){
			}else{
				$status++;
				$Error .= "Update ข้อมูลล้มเหลว  $qry_up_car \n";
			}
		}
	}else if($cmd == "notappv"){
		//update ไม่อนุมัติ
		$qry_up = "update \"CarMoveToCus\" set  appv_id='$iduser',appv_stamp='$nowDateTime',status_appv='0',remark='$remark' where auto_id='$auto_id' and status_appv='9'";
		if(pg_query($qry_up)){
		}else{
			$status++;
			$Error .= "Update ข้อมูลล้มเหลว  $qry_up \n";
		}
		//Insert รถกลับเข้าคลัง
		$qry="INSERT INTO \"CarMove\" (car_id,color,wh_id,date_in) values ('$car_id','$color_id','1','$nowdate')";
			if(!$res=@pg_query($qry)){
				$status++;
                $Error = "INSERT CarMove ไม่สำเร็จ $qry";
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


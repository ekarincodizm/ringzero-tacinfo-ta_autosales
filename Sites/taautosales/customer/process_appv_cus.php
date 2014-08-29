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
		
		$qry_select = pg_query("select * from \"Customers_temp\" where auto_id='$auto_id' and status_appv='9'");
		$num_row = pg_num_rows($qry_select);
		if($num_row>0){
			if($res = pg_fetch_array($qry_select)){
				$cus_id = trim($res['cus_id']);
				$pre_name = trim($res['pre_name']);
				$cus_name  = trim($res['cus_name']);
				$surname = trim($res['surname']);
				$address  = trim($res['address']);
				$add_post = trim($res['add_post']);
				$nationality = trim($res['nationality']);
				$birth_date = trim($res['birth_date']);
				$card_type = trim($res['card_type']);
				$card_id = trim($res['card_id']);
				$card_do_date = trim($res['card_do_date']);
				$card_do_by = trim($res['card_do_by']);
				$job = trim($res['job']);
				$contract_add = trim($res['contract_add']);
				$telephone = trim($res['telephone']);
				$change_name = trim($res['change_name']);
				$reg_customer = trim($res['reg_customer']);
				$reg_address = trim($res['reg_address']);
				$reg_post = trim($res['reg_post']);
				$contract_post = trim($res['contract_post']);
				$branch_id = trim($res['branch_id']);
				$cus_type = trim($res['cus_type']);
			}
		}else{
			$status++;
			$Error .= "มีการทำรายการอนุมัติไปก่อนหน้านี้แล้ว \n";
		}
		
	if($cmd == "appv"){
		
		
		//update อนุมัติ
		$qry_up = "update \"Customers_temp\" set  appv_id='$iduser',appv_stamp='$nowDateTime',status_appv='1',remark='$remark' where auto_id='$auto_id' and status_appv='9'";
		if(pg_query($qry_up)){
		}else{
			$status++;
			$Error .= "Update ข้อมูลล้มเหลว  $qry_up \n";
		}
		
		//update ข้อมูลจริง
		$in_qry="Update \"Customers\" set pre_name='$pre_name',cus_name='$cus_name',surname='$surname',address='$address',add_post='$add_post',
										nationality='$nationality',birth_date='$birth_date',card_type='$card_type',card_id='$card_id',card_do_date='$card_do_date',
										card_do_by='$card_do_by',job='$job',contract_add='$contract_add',telephone='$telephone',change_name='$change_name',reg_customer='$reg_customer',
										reg_address='$reg_address',reg_post='$reg_post',contract_post='$contract_post',branch_id='$branch_id',cus_type='$cus_type' 
										where cus_id='$cus_id' ";
								
		if(!$res=@pg_query($in_qry)){
			$Error .= "Update ไม่สำเร็จ  $in_qry \n";
			$status++;
		}
		
	}else if($cmd == "notappv"){
	
		//update ไม่อนุมัติ
		$qry_up = "update \"Customers_temp\" set  appv_id='$iduser',appv_stamp='$nowDateTime',status_appv='0',remark='$remark' where auto_id='$auto_id' and status_appv='9'";
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


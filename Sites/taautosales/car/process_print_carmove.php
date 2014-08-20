<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_POST['cmd'];
$iduser = $_SESSION["ss_iduser"];
$auto_id = pg_escape_string($_POST['auto_id']);
$FDate = pg_escape_string($_POST['FDate']);
$PDate = pg_escape_string($_POST['PDate']);
$FPDate = pg_escape_string($_POST['FPDate']);
$FirstDue = pg_escape_string($_POST['FirstDue']);
$remark = pg_escape_string($_POST['remark']);
$idno = pg_escape_string($_POST['idno']);

//array
$equipt_chk = $_POST['equipt_chk'];
$std_chk = $_POST['std_chk'];

//checknull
$txtFDate = checknull($FDate);
$txtPDate = checknull($PDate);
$txtFPDate = checknull($FPDate);
$txtRemark = checknull($remark);
$txtDueDate = checknull($FirstDue);

$status = 0;
$Error = "พบข้อผิดพลาด \n";

pg_query("BEGIN WORK");
		
	if($cmd == "save"){
	
		// ตรวจสอบสถานะการพิมพ์
		$qry_chk_print_status = pg_query("select \"print_status\" from \"CarMoveToCus\" where \"auto_id\" = '$auto_id' ");
		$chk_print_status = pg_fetch_result($qry_chk_print_status,0);
		if($chk_print_status == "1") // ถ้ามีการทำรายการไปก่อนหน้านี้แล้ว
		{
			$status++;
			$Error .= "มีการทำรายการไปก่อนหน้านี้แล้ว \n";
		}
		else // ถ้ายังไม่มีการทำรายการก่อนหน้านี้
		{
			//update สถานะการพิมพ์
			$qry_up = "update \"CarMoveToCus\" set print_status='1' where auto_id='$auto_id' and print_status='0' returning res_id";
			if($res = pg_query($qry_up)){
				if($res_id = pg_fetch_result($res,0)){
				}else{
					$status++;
					$Error .= "ล้มเหลว returning res_id \n";
				}
			}else{
				$status++;
				$Error .= "ล้มเหลว $qry_up \n";
			}
			
			//Update การจอง
			$qry_up = "update \"Reserves\" set finance_date=$txtFDate,fpay_date=$txtPDate,rpay_date=$txtFPDate,remark=$txtRemark,due_date=$txtDueDate where res_id='$res_id' ";
			if(pg_query($qry_up)){
			}else{
				$status++;
				$Error .= "ล้มเหลว $qry_up \n";
			}
			
			$print_count = print_count($idno,'3');
			$_SESSION['ss_print_count'] = $print_count;
		
			//บันทึกประวัติการพิมพ์
			$in_doc_print_logs = "INSERT INTO doc_print_logs (doc_no,doc_type,print_count,id_user,print_date)
													  VALUES ('$idno','3','$print_count','$_SESSION[ss_iduser]','$nowDateTime')";
			if(!$res=@pg_query($in_doc_print_logs)){
				$txt_error[] = "INSERT doc_print_logs ไม่สำเร็จ $in_doc_print_logs";
				$status++;
			}
			
			
			/* process นี้ยังไม่ได้ใช้เพราะ ยังไม่มี process มารองรับ และยังไม่มีผู้ปฏิบัติงาน
			//update ของแถม
			for($i=0;$i<count($equipt_chk);$i++){
				$qry_up = "update \"gif_detail\" set flag='1' where auto_run='$equipt_chk[$i]' ";
				if(pg_query($qry_up)){
				}else{
					$status++;
					$Error .= "ล้มเหลว $qry_up \n";
				}
			}
			
			//update อุปกรณ์มาตรฐาน
			for($j=0;$j<count($std_chk);$j++){
				$qry_up = "update \"reserve_std_proc\" set flag='1' where auto_run='$std_chk[$j]' ";
				if(pg_query($qry_up)){
				}else{
					$status++;
					$Error .= "ล้มเหลว $qry_up \n";
				}
			}
			*/
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


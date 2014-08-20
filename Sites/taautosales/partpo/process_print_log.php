<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_POST['cmd'];
$iduser = $_SESSION["ss_iduser"];
$remark = pg_escape_string($_POST['remark']);
$po_id = pg_escape_string($_POST['po_id']);

$status = 0;
$Error = "พบข้อผิดพลาด \n";

pg_query("BEGIN WORK");
		
	$doc_type = "6";
	$doc_no = $po_id;
	
	//หาจำนวนครั้งที่พิมพ์
	$print_count = print_count($doc_no,$doc_type);
	
		$in_doc_print_logs = "INSERT INTO doc_print_logs (doc_no,doc_type,print_count,id_user,print_date)
									 VALUES('$doc_no','$doc_type','$print_count','$iduser','$nowDateTime')";
	if(!$res=@pg_query($in_doc_print_logs)){
        $txt_error[] = "INSERT doc_print_logs ไม่สำเร็จ $in_doc_print_logs";
        $status++;
    }
	if(!empty($remark)){
		//บันทึกประวัติการพิมพ์สำเนา
		$in_reprint_logs = "INSERT INTO doc_reprint_logs(doc_no,doc_type,reason,num_of_print,id_user,reprint_date)
												  VALUES('$doc_no','$doc_type','$remark','$print_count','$iduser','$nowDateTime')";
		
		if(!$res=@pg_query($in_reprint_logs)){
			$txt_error[] = "INSERT doc_reprint_logs ไม่สำเร็จ $in_reprint_logs";
			$status++;
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


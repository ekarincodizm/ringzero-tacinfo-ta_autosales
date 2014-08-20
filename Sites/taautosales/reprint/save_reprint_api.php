<?php
include_once("../include/config.php");
include_once("../include/function.php");
$cmd = $_REQUEST['cmd'];
$date = nowDateTime();
pg_query("BEGIN WORK");
$status = 0;
$txt_error = array();
if($cmd == 'save'){
	$reason = pg_escape_string($_POST['reason']);
    $receipt_no = pg_escape_string($_POST['receipt_no']);
	$res_id = pg_escape_string($_POST['res_id']);
	
	$print_count = get_print_count($res_id,'2',$receipt_no); //count ครั้งที่พิมพ์	
	$in_qry = "INSERT INTO receipt_print_logs (res_id,receipt_no,id_user,receipt_type,print_count,print_date) 
										VALUES('$res_id','$receipt_no','$_SESSION[ss_iduser]','2','$print_count','$nowDateTime')";
	if(!$res=@pg_query($in_qry)){
        $txt_error[] = "INSERT receipt_print_logs ไม่สำเร็จ $in_qry";
        $status++;
    }
	
	$_SESSION['ss_print_count'] = $print_count;
	$id_user = $_SESSION["ss_iduser"];
	
    $in_qry = " INSERT INTO receipt_reprint_logs (receipt_no,reason,num_of_print,date,id_user) 
								VALUES ('$receipt_no','$reason','$print_count','$date','$id_user') ";
    	
	if(!$res=@pg_query($in_qry)){
        $txt_error[] = "INSERT receipt_reprint_logs ไม่สำเร็จ $in_qry";
        $status++;
    }
	
}else if($cmd == 'save_reprint_reason'){ //ใบเสร็จรับเงิน/ใบกำกับภาษี
	$doc_no = pg_escape_string($_POST['doc_no']);
	$reason = pg_escape_string($_POST['reason']);
	$str_doc_type = substr($doc_no,2,1);
	
	// 2 คือใบเสร็จรับเงินชั่วคราว  4 คือ ใบเสร็จรับเงิน 5 คือ ใบกำกับภาษี
	if($str_doc_type == 'N'){
		$doc_type = '2';
	}else if($str_doc_type == 'R'){ 
		$doc_type = '4'; 
	}else if($str_doc_type == 'V'){ 
		$doc_type = '5'; 
	}else{
		$doc_type = '3';
		$str_si = substr($doc_no,0,2);
		if( $str_si == 'SI'){
			$doc_type = '3';
		}
	}
	
	//หาจำนวนครั้งที่พิมพ์
	$print_count = print_count($doc_no,$doc_type);
	$_SESSION['ss_print_count'] = $print_count;
	
	//บันทึกประวัติการพิมพ์
	$in_doc_print_logs = "INSERT INTO doc_print_logs (doc_no,doc_type,print_count,id_user,print_date)
									 VALUES('$doc_no','$doc_type','$print_count','$_SESSION[ss_iduser]','$nowDateTime')";
	if(!$res=@pg_query($in_doc_print_logs)){
        $txt_error[] = "INSERT doc_print_logs ไม่สำเร็จ $in_doc_print_logs";
        $status++;
    }
	if(!empty($reason)){
		//บันทึกประวัติการพิมพ์สำเนา
		$in_reprint_logs = "INSERT INTO doc_reprint_logs(doc_no,doc_type,reason,num_of_print,id_user,reprint_date)
												  VALUES('$doc_no','$doc_type','$reason','$print_count','$_SESSION[ss_iduser]','$nowDateTime')";
		
		if(!$res=@pg_query($in_reprint_logs)){
			$txt_error[] = "INSERT doc_reprint_logs ไม่สำเร็จ $in_reprint_logs";
			$status++;
		}
	}	
}

if($status == 0){
    pg_query("COMMIT");
    $data['success'] = true;
    $data['message'] = "บันทึกเรียบร้อยแล้ว  ";
}else{
    pg_query("ROLLBACK");
    $data['success'] = false;
    $data['message'] = "ไม่สามารถบันทึกได้!!!  $txt_error[0] ";
} 
echo json_encode($data);

?>
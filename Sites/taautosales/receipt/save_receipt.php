<?php
include_once("../include/config.php");
include_once("../include/function.php");

    $txt_error = array();
    $status = 0;
    
	$cmd = pg_escape_string($_POST['cmd']);
	
if($cmd == "save"){
	
    $receive_date = pg_escape_string($_POST['receive_date']);
    $car_num = pg_escape_string($_POST['car_num']);
    $cost_val = pg_escape_string($_POST['cost_val']);
    $idno = pg_escape_string($_POST['idno']);
	$fin_receipt_value_amount = clean_data($_POST['fin_receipt_value_amount']); 
	$cus_receipt_value_amount = clean_data($_POST['cus_receipt_value_amount']);
	$fin_cost_val = clean_data($_POST['fin_cost_val']); 
	$cus_cost_val = clean_data($_POST['cus_cost_val']);
	$fin_vat = clean_data($_POST['fin_vat']); 
	$cus_vat = clean_data($_POST['cus_vat']); 
	$num_install = pg_escape_string($_POST['num_install']); 
	
	$reserve_color = pg_escape_string($_POST['reserve_color']); 
	$fin_money_way = pg_escape_string($_POST['fin_money_way']);
	$cus_money_way = pg_escape_string($_POST['cus_money_way']);
	$fin_r_renew = pg_escape_string($_POST['fin_r_renew']);
	$fin_v_renew = pg_escape_string($_POST['fin_v_renew']);
	$cus_r_renew = pg_escape_string($_POST['cus_r_renew']);
	$cus_v_renew = pg_escape_string($_POST['cus_v_renew']);
	$id_user = $_SESSION["ss_iduser"];

	pg_query("BEGIN WORK");
	
	$qry_rec=pg_query("select insert_acc_receipt_new('$idno','$fin_cost_val','$cus_cost_val','$fin_vat','$cus_vat','$num_install',
						'$_SESSION[ss_iduser]','$receive_date','$_SESSION[ss_office_id]','$reserve_color','$fin_money_way','$cus_money_way',
						'$fin_r_renew','$fin_v_renew','$cus_r_renew','$cus_v_renew' ) ");
					

    $res_rec = pg_fetch_result($qry_rec,0);
    if( empty($res_rec) OR $res_rec == "" ){
        $txt_error[] = " insert_acc_receipt_new error ";
        $status++;
    }
	
	$invoice_no = $res_rec;
	$str_replace_v =  str_replace("R", "V", $res_rec);
	
	$arr_receipt_no = explode(",",$res_rec);

	  if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
		$data['receipt_no'] = $res_rec;
		$data['invoice_no'] = $str_replace_v;
        //$data['message'] = "บันทึกเรียบร้อยแล้ว  $fin_cost_val,$cus_cost_val,$fin_vat,$cus_vat ,$num_install $res_rec ";
		$data['message'] = "บันทึกเรียบร้อยแล้ว  เอกสารเลขที่ $res_rec ";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]$fin_cost_val,$cus_cost_val,$fin_vat,$cus_vat ,$num_install ";
    }
    echo json_encode($data);
}
?>
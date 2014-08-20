<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "save_cancel_receipt"){
	$txt_no = trim($_POST['txt_no']);
	$area_memo = $_POST['area_memo'];
    $cb_type = $_POST['cb_type'];

	pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    $stat_ok = 0;

    $sub_id = substr($txt_no, 2, 1);
	
	if($cb_type == 'NRF'){
		$cancel_status = '0'; // ไม่คืนเงิน
	}else if($cb_type == 'CUS'){
		$cancel_status = '2'; //คืนเงิน
	}
	
	if( $sub_id == "R" ){ //ใบเสร็จรับเงิน
        $qry = pg_query("SELECT res_id,amount,vat FROM v_receipt WHERE r_receipt='$txt_no' ");
        if($res = pg_fetch_array($qry)){
            $res_id = $res['res_id'];
            $amount = $res['amount'];
            $vat = $res['vat'];
            $money = $amount+$vat;
        
            $stat_ok++;
		}
    }else if( $sub_id == "N" ){ //ใบเสร็จรับเงินชั่วคราว
        $qry = pg_query(" SELECT res_id,(chq_amount+cash_amount)as amount FROM receipt_tmp WHERE receipt_no='$txt_no'  ");
        if($res = pg_fetch_array($qry)){
            $res_id = $res['res_id'];
			$money = $res['amount'];
            
            $stat_ok++;
        }
	}else {}
	
	if($stat_ok == 0){
        $status++;
        $txt_error[] = "ไม่พบ เลขที่สำคัญ ในระบบ !!! ";
    }else{
		 $qry_cancel_receipt = pg_query(" SELECT c_receipt  FROM \"CancelReceipt\" WHERE c_receipt='$txt_no' ");
		 $num_rows = pg_num_rows($qry_cancel_receipt);
		if($num_rows != 0){
			$status++;
        $txt_error[] = "เลขที่ $txt_no เคยมีการยกเลิกไปแล้ว!!! ";
		}else{
			$in_qry="INSERT INTO \"CancelReceipt\" (c_receipt,res_id,c_date,c_money,return_to,c_memo,postuser) 
											VALUES ('$txt_no','$res_id','$nowdate','$money','$cb_type','$area_memo','$_SESSION[ss_iduser]')";
			if(!$res=@pg_query($in_qry)){
				$txt_error[] = "บันทึก CancelReceipt ไม่สำเร็จ $in_qry";
				$status++;
			}
		}
    }
	
	if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = $txt_error[0];
    }
    echo json_encode($data);
}



/*if($cmd == "save"){
    $txt_no = trim($_POST['txt_no']);
    $area_memo = $_POST['area_memo'];
    $cb_type = $_POST['cb_type'];
    
    if( empty($txt_no) OR empty($area_memo) OR empty($cb_type) ){
            $data['success'] = false;
            $data['message'] = "ข้อมูลไม่ครบถ้วน !";
            echo json_encode($data);
            exit;
    }
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    $stat_ok = 0;

    $sub_id = substr($txt_no, 2, 1);
    
    if( $sub_id == "A" ){
        $qry = pg_query("SELECT res_id,amount,vat FROM \"VReceipt\" WHERE r_receipt='$txt_no' ");
        if($res = pg_fetch_array($qry)){
            $res_id = $res['res_id'];
            $amount = $res['amount'];
            $vat = $res['vat'];
            $money = $amount+$vat;
        
            $stat_ok++;
        }
    }elseif( $sub_id == "R" ){
        $qry = pg_query("SELECT res_id,amount,vat FROM \"VReceipt\" WHERE r_receipt='$txt_no' ");
        if($res = pg_fetch_array($qry)){
            $res_id = $res['res_id'];
            $amount = $res['amount'];
            $vat = $res['vat'];
            $money = $amount+$vat;
        
            $stat_ok++;
        }
    }elseif( $sub_id == "V" ){
        $qry = pg_query("SELECT res_id,amount,vat FROM \"VVat\" WHERE v_receipt='$txt_no' ");
        if($res = pg_fetch_array($qry)){
            $res_id = $res['res_id'];
            $amount = $res['amount'];
            $vat = $res['vat'];
            $money = $amount+$vat;
            
            $stat_ok++;
        }
    }elseif( $sub_id == "N" ){
        $qry = pg_query("SELECT res_id FROM \"VOtherpay2\" WHERE o_receipt='$txt_no' LIMIT 1 ");
        if($res = pg_fetch_array($qry)){
            $res_id = $res['res_id'];
            
            $qry = pg_query("SELECT SUM(amount) AS money FROM \"VOtherpay2\" WHERE o_receipt='$txt_no' ");
            if($res = pg_fetch_array($qry)){
                $money = $res['money'];
            }

            $stat_ok++;
        }
    }elseif( $sub_id == "I" ){
        $qry = pg_query("SELECT r_receipt,o_receipt,res_id,amount,vat FROM \"VAccPayment\" WHERE inv_no='$txt_no' ");
        if($res = pg_fetch_array($qry)){
            $r_receipt = $res['r_receipt'];
            $o_receipt = $res['o_receipt'];  
            $res_id = $res['res_id'];
        
            $qry = pg_query("SELECT SUM(amount) AS money FROM \"InvoiceDetails\" WHERE inv_no='$txt_no' ");
            if($res = pg_fetch_array($qry)){
                $money = $res['money'];
            }
            
            $stat_ok++;
        }else{
            $status++;
            $txt_error[] = "ไม่พบข้อมูลใน VAccPayment";
        }
    }else{
        $status++;
        $txt_error[] = "กรุณาตรวจสอบ เลขที่สำคัญ";
    }
    
    if($stat_ok == 0){
        $status++;
        $txt_error[] = "ไม่พบ เลขที่สำคัญ ในระบบ !";
    }else{
    
        //INSERT
        $in_qry="INSERT INTO \"CancelReceipt\" (c_receipt,res_id,c_date,c_money,return_to,c_memo,postuser) values 
        ('$txt_no','$res_id','$nowdate','$money','$cb_type','$area_memo','$_SESSION[ss_iduser]')";
        if(!$res=@pg_query($in_qry)){
            $txt_error[] = "บันทึก CancelReceipt ไม่สำเร็จ $in_qry";
            $status++;
        }
    
    }
    
    if($status == 0){
        pg_query("COMMIT");
        //pg_query("ROLLBACK");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = $txt_error[0];
    }

    echo json_encode($data);
}*/


?>
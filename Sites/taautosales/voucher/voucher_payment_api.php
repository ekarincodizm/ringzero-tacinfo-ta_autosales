<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "save"){
    
    $nowdate = $_POST['txt_is_date'];
    
    $txt_detail = $_POST['txt_detail'];
    $radio_buy_type = $_POST['radio_buy_type'];
    $txt_cash_amt = $_POST['txt_cash_amt'];
    $cb_cheque_acid = $_POST['cb_cheque_acid'];
    $cb_cheque_type = $_POST['cb_cheque_type'];
    $txt_cheque_id = $_POST['txt_cheque_id'];
    $txt_cheque_date = $_POST['txt_cheque_date'];
    $txt_cheque_payto = $_POST['txt_cheque_payto'];
    $txt_cheque_amt = $_POST['txt_cheque_amt'];

    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();

    $qry="insert into account.\"Vouchers\"(st_date,end_date) values ('$nowdate',DEFAULT)";
    $res=pg_query($qry);
    if(!$res){
        $txt_error[] = "INSERT job_voucher ไม่สำเร็จ $qry";
        $status++;
    }

    $cur_jobid=pg_query("select currval('account.\"Vouchers_job_id_seq\"');");
    $rs_jobid=pg_fetch_result($cur_jobid,0);
    if(empty($rs_jobid)){
        $txt_error[] = "SELECT job_voucher_job_id_seq ไม่สำเร็จ $cur_jobid";
        $status++;
    }

    $rs=pg_query("select account.\"gen_no\"('$nowdate','VP')");
    $vp_id=pg_fetch_result($rs,0);
    if(empty($vp_id)){
        $txt_error[] = "gen_no VP ไม่สำเร็จ";
        $status++;
    }

    if($radio_buy_type == 1){
        $insert_voucher="insert into account.\"VoucherDetails\" (\"vc_id\",\"vc_detail\",\"marker_id\",\"approve_id\",\"receipt_id\",\"cash_amt\",\"chq_acc_no\",\"chque_no\",\"do_date\",\"job_id\",\"vc_type\",\"acb_id\") 
        values ('$vp_id','$txt_detail','$_SESSION[ss_iduser]',DEFAULT,DEFAULT,'$txt_cash_amt',DEFAULT,DEFAULT,'$nowdate','$rs_jobid','P',DEFAULT)";
        $rs_voucher=pg_query($insert_voucher);
        if(!$rs_voucher){
            $txt_error[] = "insert VoucherDetails ไม่สำเร็จ $insert_voucher";
            $status++;
        }
    }elseif($radio_buy_type == 2){
        $insert_chqcom="insert into account.\"ChequeAccDetails\" (\"ac_id\",\"chq_id\",\"date_on_chq\",\"amount\",\"type_pay\",\"do_date\",\"pay_to\") 
        values ('$cb_cheque_acid','$txt_cheque_id','$txt_cheque_date','$txt_cheque_amt','$cb_cheque_type','$nowdate','$txt_cheque_payto')";
        $result_chqcom=pg_query($insert_chqcom);
        if(!$result_chqcom){
            $txt_error[] = "insert ChequeAccDetails ไม่สำเร็จ $insert_chqcom";
            $status++;
        }

        $insert_voucher="insert into account.\"VoucherDetails\" (\"vc_id\",\"vc_detail\",\"marker_id\",\"approve_id\",\"receipt_id\",\"cash_amt\",\"chq_acc_no\",\"chque_no\",\"do_date\",\"job_id\",\"vc_type\",\"acb_id\") 
        values ('$vp_id','$txt_detail','$_SESSION[ss_iduser]',DEFAULT,DEFAULT,DEFAULT,'$cb_cheque_acid','$txt_cheque_id','$nowdate','$rs_jobid','P',DEFAULT)";
        $rs_voucher=pg_query($insert_voucher);
        if(!$rs_voucher){
            $txt_error[] = "insert VoucherDetails ไม่สำเร็จ $insert_voucher";
            $status++;
        }
    }else{
        $txt_error[] = "ไม่พบรูปแบบ การชำระเงิน !";
        $status++;
    }

    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
    echo json_encode($data);

}
?>
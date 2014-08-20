<?php
include_once("../include/config.php");
include_once("../include/function.php");

$type = $_POST['type'];
$chkbox = $_POST['chkbox'];
$arr_chkbox = explode(",", $chkbox);

pg_query("BEGIN WORK");
$status = 0;
$txt_error = array();

foreach($arr_chkbox as $v){

    $arr_v = explode("#",$v);

    if($type == "1"){ //approve
        if($arr_v[0] == "Q"){
            $up_sql=pg_query("UPDATE account.\"Vouchers\" SET \"vcp_finish\"='TRUE',\"end_date\"='$nowdate' WHERE \"job_id\"='$arr_v[2]'");
            if(!$up_sql){
                $status++;
            }
        }
        if(substr($arr_v[1],0,2) == "VR"){
            $up_sql=pg_query("UPDATE account.\"Vouchers\" SET \"vcp_finish\"='TRUE',\"end_date\"='$nowdate' WHERE \"job_id\"='$arr_v[2]'");
            if(!$up_sql){
                $status++;
            }
            $up_sql=pg_query("UPDATE account.\"VoucherDetails\" SET \"receipt_id\"='$arr_v[3]',\"recp_date\"='$arr_v[4]' WHERE \"vc_id\"='$arr_v[1]'");
            if(!$up_sql){
                $status++;
            }
        }
        $up_sql=pg_query("UPDATE account.\"VoucherDetails\" SET \"approve_id\"='$_SESSION[ss_iduser]',\"appv_date\"='$nowdate' WHERE \"vc_id\"='$arr_v[1]'");
        if(!$up_sql){
            $status++;
        }
    }else{ //not approve
        $up_sql=pg_query("UPDATE account.\"Vouchers\" SET \"vcp_finish\"='TRUE',\"cancel\"='TRUE' WHERE \"job_id\"='$arr_v[2]'");
        if(!$up_sql){
            $status++;
        }
        
        $up_sql=pg_query("UPDATE account.\"VoucherDetails\" SET \"approve_id\"='$_SESSION[ss_iduser]',\"receipt_id\"='cancel' WHERE \"vc_id\"='$arr_v[1]'");
        if(!$up_sql){
            $status++;
        }
    }
    
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
?>
<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "save"){
    $new_plate = $_POST['n'];
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $qry = pg_query("SELECT * FROM \"P_NewCarPlate\" WHERE new_plate='$new_plate' AND date_return IS NULL ");
    if($res = pg_fetch_array($qry)){
        $for_sale = $res['for_sale'];
        $memo_use_inhouse = $res['memo_use_inhouse'];
        $license_plate = $res['license_plate'];
    }else{
        $txt_error[] = "ไม่สามารถดึงข้อมูล $new_plate ได้";
        $status++;
    }
    
    if($for_sale == 'f'){
        $qry="UPDATE \"P_NewCarPlate\" SET date_return='$nowdate' WHERE new_plate='$new_plate' AND date_return IS NULL";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "UPDATE P_NewCarPlate ไม่สำเร็จ $qry";
            $status++;
        }
        
        $qry = "INSERT INTO \"P_NewCarPlate\" (new_plate, date_in) VALUES ('$new_plate','$nowdate')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT P_NewCarPlate ไม่สำเร็จ $qry";
            $status++;
        }
    }elseif($for_sale == 't' AND empty($memo_use_inhouse)){
        $qry="UPDATE \"P_NewCarPlate\" SET date_return='$nowdate' WHERE new_plate='$new_plate' AND date_return IS NULL";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "UPDATE P_NewCarPlate ไม่สำเร็จ $qry";
            $status++;
        }
        
        $qry = "INSERT INTO \"P_NewCarPlate\" (new_plate, date_in) VALUES ('$new_plate','$nowdate')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT P_NewCarPlate ไม่สำเร็จ $qry";
            $status++;
        }
    }elseif($for_sale == 't' AND !empty($memo_use_inhouse)){
        /*
        $qry = pg_query("SELECT amount FROM \"VOtherpay2\" WHERE o_receipt='$memo_use_inhouse' AND receive_id='S001' ");
        if($res = pg_fetch_array($qry)){
            $amount = $res['amount'];
        }else{
            $txt_error[] = "SELECT amount FROM VOtherpay2 ไม่สำเร็จ $qry";
            $status++;
        }
        */
        $qry="UPDATE \"P_NewCarPlate\" SET date_return='$nowdate' WHERE new_plate='$new_plate' AND date_return IS NULL";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "UPDATE P_NewCarPlate ไม่สำเร็จ $qry";
            $status++;
        }
        
        $qry = "INSERT INTO \"P_NewCarPlate\" (new_plate, date_in) VALUES ('$new_plate','$nowdate')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT P_NewCarPlate ไม่สำเร็จ $qry";
            $status++;
        }
        /*
        $qry = "INSERT INTO account.\"Vouchers\" (st_date,vcp_finish,end_date,cancel) VALUES ('$nowdate','FALSE',DEFAULT,'FALSE')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT Vouchers ไม่สำเร็จ $qry";
            $status++;
        }
        
        $auto_id=pg_query("select currval('account.\"Vouchers_job_id_seq\"');");
        $job_id=pg_fetch_result($auto_id,0);

        $gen_no=@pg_query("select gen_no('$nowdate','VP')");
        $gen_no=@pg_fetch_result($gen_no,0);
        if(empty($gen_no)){
            $txt_error[] = "gen_no($nowdate,VP) ไม่สำเร็จ !";
            $status++;
        }
        
        $qry = "INSERT INTO account.\"VoucherDetails\" (vc_id,vc_detail,marker_id,approve_id,receipt_id,cash_amt,chq_acc_no,chque_no,do_date,job_id,vc_type,appv_date,recp_date,acb_id) 
        VALUES ('$gen_no','เบิกเงินคืนค่ามัดจำป้ายแดง#$license_plate','$_SESSION[ss_iduser]',DEFAULT,'REC#','$amount',DEFAULT,DEFAULT,'$nowdate','$job_id','P',DEFAULT,DEFAULT,DEFAULT)";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT VoucherDetails ไม่สำเร็จ $qry";
            $status++;
        }
        */
    }
    
    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "$txt_error[0]";
    }
    echo json_encode($data);
    
}
?>
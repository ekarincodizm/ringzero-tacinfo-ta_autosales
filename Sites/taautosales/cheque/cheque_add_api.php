<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "cheque_add"){
    $cb_acc = $_POST['cb_acc'];
    $txt_start = $_POST['txt_start'];
    $txt_unit = $_POST['txt_unit'];

    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();

    for($i = $txt_start; $i < ($txt_start+$txt_unit); $i++){
        $in_qry="INSERT INTO account.\"ChequeAccDetails\" (ac_id,chq_id) values ('$cb_acc','$i')";
        if(!$res=@pg_query($in_qry)){
            $txt_error[] = "INSERT ChequeAccDetails ไม่สำเร็จ $in_qry";
            $status++;
        }
    }
    
    if($status == 0){
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
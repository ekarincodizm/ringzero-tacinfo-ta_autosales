<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$cid = $_POST['cid'];
$typeac = $_POST['typeac'];
$amtdr = $_POST['amtdr'];
$amtcr = $_POST['amtcr'];

$acb_id = $_POST['acb_id'];
$acid = $_POST['acid'];

pg_query("BEGIN WORK");
$status = 0;

$up_sql="UPDATE account.\"AccountBookDetail\" SET \"AcID\"='$typeac',\"AmtDr\"='$amtdr',\"AmtCr\"='$amtcr' WHERE \"acb_id\"='$acb_id' AND \"AcID\"='$acid' ";
if(!$res_up_sql=@pg_query($up_sql)){
    $status++;
}

if($status == 0){
    pg_query("COMMIT");
    $data['success'] = true;
    $data['message'] = "บันทึกข้อมูลเรียบร้อยแล้ว";
}else{
    pg_query("ROLLBACK");
    $data['success'] = false;
    $data['message'] = "ไม่สามารถบันทึกได้!";
}

echo json_encode($data);
?>
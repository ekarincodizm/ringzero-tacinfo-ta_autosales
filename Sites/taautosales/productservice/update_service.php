<?php
include_once("../include/config.php");
include_once("../include/function.php");




$s_id=$_POST['s_id'];
$s_name=$_POST['s_name'];
$s_costprice=$_POST['s_costprice'];

$usevat=$_POST['usevat'];
$type_rec=$_POST['type_rec'];
$rd_cancel=$_POST['rd_cancel'];

pg_query("BEGIN WORK");
$status = 0;
$txt_error = array();


$up_qry="Update   \"Services\" SET \"name\"='$s_name',cost_price='$s_costprice',use_vat='$usevat',type_rec='$type_rec',cancel='$rd_cancel' where service_id='$s_id' ";
if(!$res=@pg_query($up_qry)){
    $txt_error[] = "Update Services ไม่สำเร็จ $up_qry";
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


?>
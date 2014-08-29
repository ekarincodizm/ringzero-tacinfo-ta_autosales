<?php
include_once("../include/config.php");
include_once("../include/function.php");

$p_id=$_POST["p_id"];
$p_name=$_POST['p_name'];
$p_costprice=$_POST['p_costprice'];
$p_saleprice=$_POST['p_saleprice'];
$usevat=$_POST['usevat'];
$type_rec=$_POST['type_rec'];

$p_unit=$_POST['p_unit'];
$rd_cancel=$_POST['rd_cancel'];

pg_query("BEGIN WORK");
$status = 0;
$txt_error = array();

$up_qry="Update \"RawMaterial\" SET \"name\"='$p_name',cost_price='$p_costprice',sale_price='$p_saleprice',use_vat='$usevat',type_rec='$type_rec',unit='$p_unit',cancel='$rd_cancel' where material_id='$p_id' ";
if(!$res=@pg_query($up_qry)){
    $txt_error[] = "Update Products ไม่สำเร็จ $up_qry";
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
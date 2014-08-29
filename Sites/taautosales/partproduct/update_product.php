<?php
include_once("../include/config.php");
include_once("../include/function.php");

$p_code = pg_escape_string($_POST['p_code']);
$p_name = pg_escape_string($_POST['p_name']);
$p_detail = pg_escape_string($_POST['p_detail']);
$p_priceperunit = pg_escape_string($_POST['p_priceperunit']);
$p_unitid = pg_escape_string($_POST['p_unitid']);
$p_svcharge = pg_escape_string($_POST['p_svcharge']);
$p_Type = pg_escape_string($_POST['p_Type']);

pg_query("BEGIN WORK");
$status = 0;
$txt_error = array();

$up_qry="
Update  \"parts\" 
SET 
\"name\"='$p_name',
details='$p_detail',
priceperunit='$p_priceperunit',
unitid='$p_unitid',
svcharge='$p_svcharge',
type='$p_Type'
where code='$p_code' 
";
if(!$res=@pg_query($up_qry)){ //Record Parts Tables
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
//Return to the edit_product.php For AJAX Response


?>
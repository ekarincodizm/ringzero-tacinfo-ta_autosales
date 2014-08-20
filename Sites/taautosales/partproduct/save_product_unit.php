<?php
include_once("../include/config.php");
include_once("../include/function.php");

$xp_num='P001';

$p_unitname = pg_escape_string($_POST['p_unitname']);


pg_query("BEGIN WORK");
$status = 0;
$txt_error = array();


$in_qry="
	INSERT INTO \"parts_unit\" 
	(\"unitname\") 
	values 
	('".$p_unitname."') ;
";
if(!$res=@pg_query($in_qry)){ //Record Parts_unit Tables
    $txt_error[] = "บันทึก Products ไม่สำเร็จ $in_qry";
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
//Return to the add_product_unit.php For AJAX Response

?>
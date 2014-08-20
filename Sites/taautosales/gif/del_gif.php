<?php
include_once("../include/config.php");
include_once("../include/function.php");
$cmd_del_gif = $_REQUEST['cmd_del_gif'];
if($cmd_del_gif == "del_gif"){
	$product_id = pg_escape_string($_POST['product_id']);
	$res_id = pg_escape_string($_POST['res_id']);
	
	pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
	
	$in_qry = "DELETE FROM gif_detail 
			   WHERE product_id = '$product_id'
			   AND res_id = '$res_id' ";
 
	if(!$res=@pg_query($in_qry)){
        $txt_error[] = "ลบรายการข้อมูลไม่สำเร็จ $in_qry";
        $status++;
    }
    
    if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "ลบข้อมูลเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถลบข้อมูลได้! $txt_error[0]";
    }
    echo json_encode($data);
}
 ?>
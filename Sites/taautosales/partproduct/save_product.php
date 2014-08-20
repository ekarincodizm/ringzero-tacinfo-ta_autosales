<?php
include_once("../include/config.php");
include_once("../include/function.php");

$p_code_type = pg_escape_string($_POST['p_code_type']);
$p_name = pg_escape_string($_POST['p_name']);
$p_detail = pg_escape_string($_POST['p_detail']);
$p_priceperunit = pg_escape_string($_POST['p_priceperunit']);
$p_unitid = pg_escape_string($_POST['p_unitid']);
$p_svcharge = pg_escape_string($_POST['p_svcharge']);
$p_Type = pg_escape_string($_POST['p_Type']);
$kny = 0;

//Generate Parts Code ID
$generate_id_StrQuery = "
	select gen_parts_product_type_no(
		'".$p_code_type."', 
		'".$kny."'
	);
";
$generate_id = @pg_query($generate_id_StrQuery);
$gen_parts_code_no = @pg_fetch_result($generate_id,0);
if(empty($gen_parts_code_no)){
    $txt_error[] = "สร้าง gen_rec_no ไม่สำเร็จ";
}
else{
	
}

//For Test $gen_parts_code_no
/*
$data["success"] = true;
$data["message"] = "test";
$data["test"] = $generate_id_StrQuery;
echo json_encode($data);
exit;
*/
//#########

pg_query("BEGIN WORK");
$status = 0;
$txt_error = array();


$in_qry="
	INSERT INTO \"parts\" 
	(code, name, details, priceperunit, unitid, svcharge, type) 
	values 
	('$gen_parts_code_no', '$p_name', '$p_detail', '$p_priceperunit', '$p_unitid', '$p_svcharge', '$p_Type');
"; //Record Parts Tables
if(!$res=@pg_query($in_qry)){
    $txt_error[] = "บันทึก Products ไม่สำเร็จ $in_qry";
    $status++;
}

if($status == 0){
    //pg_query("ROLLBACK");
    pg_query("COMMIT");
    $data['success'] = true;
    $data['message'] = "บันทึกเรียบร้อยแล้ว \nรหัสสินค้าคือ {$gen_parts_code_no}";
	$data['gen_part_code_id'] = $gen_parts_code_no;
}else{
    pg_query("ROLLBACK");
    $data['success'] = false;
    $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
}

echo json_encode($data);
//Return to the add_product.php For AJAX Response
?>
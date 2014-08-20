<?php
include_once("../include/config.php");
include_once("../include/function.php");

$nowDateTime = nowDateTime();
$id_user = $_SESSION["ss_iduser"];

pg_query("BEGIN WORK");
$status = 0;

$txt_error = "";

$reason = pg_escape_string($_POST['reason']);
$res_id = pg_escape_string($_POST['res_id']);
$doc_no = pg_escape_string($_POST['doc_no']);
$car_id = pg_escape_string($_POST['car_id']);

// ตรวจสอบก่อนว่าอยู่ระหว่างการขอยกเลิกหรือไม่
$qry_chkC = pg_query("select * from \"cancel_deliveries\" where \"res_id\" = '$res_id' and \"IDNO\" = '$doc_no' and \"appvStatus\" = '9' ");
$num_chkC = pg_num_rows($qry_chkC);
if($num_chkC > 0)
{
	$txt_error = "เลขที่จอง $res_id อยู่ระหว่างการขอยกเลิกการส่งมอบรถแล้ว";
	$status++;
}
else
{
	$in_qry = "INSERT INTO cancel_deliveries(\"res_id\", \"IDNO\", \"doerID\", \"doerStamp\", \"doerRemark\", \"appvStatus\", \"car_id\")
				VALUES('$res_id', '$doc_no', '$_SESSION[ss_iduser]', '$nowDateTime', '$reason', '9', '$car_id')";
	if(!$res=@pg_query($in_qry)){
		$txt_error = "INSERT cancel_deliveries ไม่สำเร็จ $in_qry";
		$status++;
	}
}

if($status == 0){
    pg_query("COMMIT");
    $data['success'] = true;
    $data['message'] = "บันทึกเรียบร้อยแล้ว  ";
}else{
    pg_query("ROLLBACK");
    $data['success'] = false;
    $data['message'] = "ไม่สามารถบันทึกได้!!!  $txt_error ";
} 
echo json_encode($data);

?>
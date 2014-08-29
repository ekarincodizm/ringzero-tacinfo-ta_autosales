<?php
include_once("../include/config.php");
include_once("../include/function.php");



//gen product id //
$qry_pro=pg_query("select count(*) AS num from \"Services\" ");
$res_pro=pg_fetch_array($qry_pro);
$num_count=$res_pro["num"];


    if($num_count==0)
	{
  		$res_sn=1;
	}
	else
	{
  		$res_sn=$num_count+1;
	}
 
   
	    $product_sn="S".insertZero($res_sn , 3); // products code


// end gen product code //


$s_name=$_POST['s_name'];
$s_costprice=$_POST['s_costprice'];

$usevat=$_POST['usevat'];
$type_rec=$_POST['type_rec'];


pg_query("BEGIN WORK");
$status = 0;
$txt_error = array();


$in_qry="INSERT INTO \"Services\" (service_id,\"name\",cost_price,use_vat,type_rec) values 
('$product_sn','$s_name','$s_costprice','$usevat','$type_rec')";
if(!$res=@pg_query($in_qry)){
    $txt_error[] = "บันทึก Services ไม่สำเร็จ $in_qry";
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
<?php
include_once("../include/config.php");
include_once("../include/function.php");

$xp_num='P001';

//gen product id //
$qry_pro=pg_query("select count(*) AS num from \"Products\" ");
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
 
  
	    $product_sn="P".insertZero($res_sn , 4); // products code


// end gen product code //


$p_name=pg_escape_string($_POST['p_name']);
$p_costprice=pg_escape_string($_POST['p_costprice']);
$p_saleprice=pg_escape_string($_POST['p_saleprice']);
$usevat=pg_escape_string($_POST['usevat']);
$type_rec=pg_escape_string($_POST['type_rec']);
$linktable=pg_escape_string($_POST['linktable']);


pg_query("BEGIN WORK");
$status = 0;
$txt_error = array();


$in_qry="INSERT INTO \"Products\" (product_id,\"name\",cost_price,sale_price,use_vat,type_rec,link_table) values 
('$product_sn','$p_name','$p_costprice','$p_saleprice','$usevat','$type_rec','$linktable')";
if(!$res=@pg_query($in_qry)){
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


?>
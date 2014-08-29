<?php
include_once("../include/config.php");
include_once("../include/function.php");

//gen product id //
$qry_pro=pg_query("select count(*) AS num from \"RawMaterial\" ");
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
 
  
	    $product_sn="M".insertZero($res_sn , 3); // products code


// end gen product code //


$p_name=$_POST['m_name'];
$p_costprice=$_POST['m_costprice'];
$p_saleprice=$_POST['m_saleprice'];
$usevat=$_POST['usevat'];
$type_rec=$_POST['type_rec'];
$p_unit = $_POST['p_unit'];

pg_query("BEGIN WORK");
$status = 0;
$txt_error = array();


$in_qry="INSERT INTO \"RawMaterial\" (material_id,\"name\",cost_price,sale_price,use_vat,type_rec,unit) values 
('$product_sn','$p_name','$p_costprice','$p_saleprice','$usevat','$type_rec','$p_unit')";
if(!$res=@pg_query($in_qry)){
    $txt_error[] = "บันทึก Material ไม่สำเร็จ $in_qry";
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
<?php
include_once("../include/config.php");
include_once("../include/function.php");

$editCarID = pg_escape_string($_GET['editCarID']);

// หาข้อมูลการแก้ไขข้อมูลรถครั้งนั้นๆ
$qry_name = pg_query("select * from \"CarsEditTemp\" WHERE \"editCarID\" = '$editCarID' ");
if($res_cus = pg_fetch_array($qry_name))
{
	$car_id = trim($res_cus['car_id']);
	$new_license_plate = trim($res_cus['license_plate']);
	$new_product_id  = trim($res_cus['product_id']);
	$new_car_num = trim($res_cus['car_num']);
	$new_mar_num  = trim($res_cus['mar_num']);
	$new_car_year = trim($res_cus['car_year']);
	$new_color = trim($res_cus['color']);
	
	// หาชื่อสินค้า
	$qry_new_product_name = pg_query("select \"name\" from \"Products\" where \"product_id\" = '$new_product_id' ");
	$new_product_name = pg_fetch_result($qry_new_product_name,0);
	
	// หาชื่อสี
	$qry_new_color_name = pg_query("select \"color_name\" from \"CarColor\" where \"color_id\" = '$new_color' ");
	$new_color_name = pg_fetch_result($qry_new_color_name,0);
}
	
// หารหัสการทำรายการก่อนหน้านี้ ที่ได้รับการอนุมัติ
$qry_old_editCarID = pg_query("select max(\"editCarID\") from \"CarsEditTemp\" WHERE \"car_id\" = '$car_id' and \"editCarID\" < '$editCarID' and \"appvStatus\" = '1' ");
$old_editCarID = pg_result($qry_old_editCarID,0);

// หาข้อมูลการแก้ไขข้อมูลรถครั้งก่อนหน้า ที่ได้รับการอนุมัติ
$qry_name_old = pg_query("select * from \"CarsEditTemp\" WHERE \"editCarID\" = '$old_editCarID' ");
if($res_cus_old = pg_fetch_array($qry_name_old))
{
	$old_license_plate = trim($res_cus_old['license_plate']);
	$old_product_id  = trim($res_cus_old['product_id']);
	$old_car_num = trim($res_cus_old['car_num']);
	$old_mar_num  = trim($res_cus_old['mar_num']);
	$old_car_year = trim($res_cus_old['car_year']);
	$old_color = trim($res_cus_old['color']);
	
	// หาชื่อสินค้า
	$qry_old_product_name = pg_query("select \"name\" from \"Products\" where \"product_id\" = '$old_product_id' ");
	$old_product_name = pg_fetch_result($qry_old_product_name,0);
	
	// หาชื่อสี
	$qry_old_color_name = pg_query("select \"color_name\" from \"CarColor\" where \"color_id\" = '$old_color' ");
	$old_color_name = pg_fetch_result($qry_old_color_name,0);
}

//----- กำหนดการแสดงการเปลี่ยนแปลง
	$color_old = "#FF8888"; // สีที่ใช้บอกว่ามีการเปลี่ยนแปลง
	if($new_license_plate != $old_license_plate){$license_plate_style = "style=\"background-color:$color_old;\" title=\"$old_license_plate\"";}
	if($new_product_id != $old_product_id){$product_id_style = "style=\"background-color:$color_old;\" title=\"$old_product_name\"";}
	if($new_car_num != $old_car_num){$car_num_style = "style=\"background-color:$color_old;\" title=\"$old_car_num\"";}
	if($new_mar_num != $old_mar_num){$mar_num_style = "style=\"background-color:$color_old;\" title=\"$old_mar_num\"";}
	if($new_car_year != $old_car_year){$car_year_style = "style=\"background-color:$color_old;\" title=\"$old_car_year\"";}
	if($new_color != $old_color){$color_style = "style=\"background-color:$color_old;\" title=\"$old_color_name\"";}
//----- จบการกำหนดการแสดงการเปลี่ยนแปลง

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <title><?php echo $company_name; ?> - <?php echo $page_title; ?></title>
    <LINK href="../images/styles.css" type=text/css rel=stylesheet>

    <link type="text/css" href="../images/jqueryui/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="../images/jqueryui/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="../images/jqueryui/js/jquery-ui-1.8.16.custom.min.js"></script>

</head>
<body>
	<div style="border: 1px dashed #D0D0D0; margin-top:10px; padding:0px; background-color:#F0F0F0">
		<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
			<tr>
				<td width="60%" valign="top">
					<table cellpadding="3" cellspacing="0" border="0" width="100%">
						<tr>
							<td align="right">ทะเบียนรถ : </td>
							<td align="left"><input type="textbox" <?php echo $license_plate_style; ?> value="<?php echo $new_license_plate; ?>" readOnly></td>
						</tr>
						<tr>
							<td align="right">Product : </td>
							<td align="left"><input type="textbox" <?php echo $product_id_style; ?> value="<?php echo $new_product_name; ?>" readOnly></td>
						</tr>
						<tr>
							<td align="right">เลขถัง : </td>
							<td align="left"><input type="textbox" <?php echo $car_num_style; ?> value="<?php echo $new_car_num; ?>" readOnly></td>
						</tr>
						<tr>
							<td align="right">เลขเครื่อง  : </td>
							<td align="left"><input type="textbox" <?php echo $mar_num_style; ?> value="<?php echo $new_mar_num; ?>" readOnly></td>
						</tr>
						<tr>
							<td align="right">ปีรถ (ค.ศ.) : </td>
							<td align="left"><input type="textbox" <?php echo $car_year_style; ?> value="<?php echo $new_car_year; ?>" readOnly></td>
						</tr>
						<tr>
							<td align="right">สีรถ : </td>
							<td align="left"><input type="textbox" <?php echo $color_style; ?> value="<?php echo $new_color_name; ?>" readOnly></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div>
		<table cellpadding="1" cellspacing="10" align="center">
			<tr align="center">
				<td><b>หมายเหตุการอนุมัติ :</b></td>
			</tr>
			<tr align="center">
				<td>
					<textarea id="remark" name="remark" cols="40" rows="5"><?php echo $remark; ?></textarea>
				</td>
			</tr>
			<tr align="center">
				<td><input type="button" name="appv" id="appv" value="อนุมัติ"> <input type="button" name="notappv" id="notappv" value="ไม่อนุมัติ"></td>
			</tr>
		</table>
	</div>
<body>

<script>
$('#appv').click(function(){
	if($('#remark').val() == ""){
		alert('กรุณาระบุหมายเหตุด้วย');
		return false;
	}
	$.post('process_appv_edit_car.php',{
		cmd : 'appv',
		remark : $('#remark').val(),
		editCarID : '<?php echo $editCarID;?>'
	},function(data){
		if(data == 1){
			alert('บันทึกข้อมูลเรียบร้อยแล้ว');
			location.reload();
		}else{
			alert(data);
			location.reload();
		}
	});
});
$('#notappv').click(function(){
	if($('#remark').val() == ""){
		alert('กรุณาระบุหมายเหตุด้วย');
		return false;
	}
	$.post('process_appv_edit_car.php',{
		cmd : 'notappv',
		remark : $('#remark').val(),
		editCarID : '<?php echo $editCarID;?>'
	},function(data){
		if(data == 1){
			alert('บันทึกข้อมูลเรียบร้อยแล้ว');
			location.reload();
		}else{
			alert(data);
			location.reload();
		}
	});
});
</script>

</html>
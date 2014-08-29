<?php
include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}
$page_title = "ฝากซ่อมแซม";
$car_id = pg_escape_string($_REQUEST['car_id']);
if($car_id != ""){
	$qry_car = pg_query("select * from \"Cars\" where car_id = '$car_id' ");
	if($res = pg_fetch_array($qry_car)){
		$car_num = $res['car_num'];
		$mar_num = $res['mar_num'];
		$car_year = $res['car_year'];
		$color = $res['color'];
		$radio_id = $res['radio_id'];
		$product_id_c = $res['product_id'];
		$regis_by = $res['regis_by'];
		$license_plate = $res['license_plate'];
		$regis_date = $res['regis_date'];
	}
}

?>

<div id="detail" style="margin-top:10px">
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0" style="border:1px dashed #D0D0D0">
<tr>
    <td width="120">รุ่น/ยี่ห้อ</td>
    <td>
<select name="cb_product" id="cb_product" onchange="javascript:changeProduct()">
    <option value="">เลือก</option>
<?php
$qry = pg_query("SELECT * FROM \"Products\" WHERE cancel='FALSE' and link_table='Cars' ORDER BY name ASC ");
while($res = pg_fetch_array($qry)){
    $product_id = $res['product_id'];
    $name = $res['name'];
	
	if($product_id == $product_id_c){
		$select_p = "selected";
	}
    echo "<option value=\"$product_id#$name\" $select_p >$name</option>";
}
    echo "<option value=\"other\">อื่นๆ</option>";
?>
</select>
<span id="div_other_product" style="display:none">ระบุ <input type="text" name="txt_other_product" id="txt_other_product" size="20"></span>
    </td>
    <td width="120">สถานที่รับสินค้า</td>
    <td>
<select name="cb_warehouse" id="cb_warehouse">
<?php
$qry = pg_query("SELECT * FROM \"Warehouses\" WHERE wh_id <> '0' AND cancel='FALSE' ORDER BY wh_name ASC ");
while($res = pg_fetch_array($qry)){
    $wh_id = $res['wh_id'];
    $wh_name = $res['wh_name'];
    echo "<option value=\"$wh_id\">$wh_name</option>";
}
?>
</select>      
    </td>
</tr>
<tr>
    <td>เลขถัง</td><td><input type="text" name="txt_carnum" id="txt_carnum" value="<?php echo $car_num;?>"></td>
    <td>เลขเครื่อง</td><td><input type="text" name="txt_marnum" id="txt_marnum" value="<?php echo $mar_num;?>" ></td>
</tr>
<tr>
    <td>ปีรถ</td><td><input type="text" name="txt_caryear" id="txt_caryear" size="10" value="<?php echo $car_year;?>"></td>
    <td>สีรถ</td><td><select name="txt_color" id="txt_color">
						<option value="">กรุณาเลือกสี</option>
						<?php 
							$qry_color = pg_query("select * from \"CarColor\" order by color_name");
							while($res = pg_fetch_array($qry_color)){
							
								$color_id = $res['color_id'];
								$color_name = $res['color_name'];
								
								if($color_id == $color){
									$select_c = "selected";
								}
								
								echo "<option value=\"$color_id\" $select_c>$color_name</option>";
							}
						?>
					</select></td>
</tr>
<tr>
    <td>ทะเบียนรถ</td><td><input type="text" name="txt_license_plate" id="txt_license_plate" size="10" value="<?php echo $license_plate;?>"></td>
    <td>วันจดทะเบียน</td><td><input type="text" name="txt_regis_date" id="txt_regis_date" size="10" value="<?php if($regis_date != "") { echo $regis_date;}else{ echo $nowdate; }?>"></td>
</tr>
<tr>
    <td>จดทะเบียนโดยจังหวัด</td><td><input type="text" name="txt_regis_by" id="txt_regis_by" value="<?php if($regis_by != "") { echo $regis_by;}else{ echo "กรุงงเทพมหานคร"; }?>"></td>
    <td>เลขวิทยุ</td><td><input type="text" name="txt_radio_id" id="txt_radio_id" size="10" value="<?php echo $radio_id; ?>" ></td>
</tr>
</table>  
</div>
<script>
 $("#txt_regis_date").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
});
</script>
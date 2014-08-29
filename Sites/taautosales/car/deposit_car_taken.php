<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "ฝากจอดรถยึด";
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

<div class="roundedcornr_box" style="width:900px">
   <div class="roundedcornr_top"><div></div></div>
      <div class="roundedcornr_content">

<?php
include_once("../include/header_popup.php");
?>

<div id="div_SaleType" style="margin-top:10px"></div>

<div style="margin-top:10px">
    
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0" style="border:1px dashed #D0D0D0">
<tr>
    <td width="120">รุ่น/ยี่ห้อ</td>
    <td>
<select name="cb_product" id="cb_product" onchange="javascript:changeProduct()">
    <option value="">เลือก</option>
<?php
$qry = pg_query("SELECT * FROM \"Products\" WHERE cancel='FALSE' ORDER BY name ASC ");
while($res = pg_fetch_array($qry)){
    $product_id = $res['product_id'];
    $name = $res['name'];
    echo "<option value=\"$product_id#$name\">$name</option>";
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
    <td>เลขถัง</td><td><input type="text" name="txt_carnum" id="txt_carnum" onkeyup="check_car_mar_num();" onkeypress="check_car_mar_num();"></td>
    <td>เลขเครื่อง</td><td><input type="text" name="txt_marnum" id="txt_marnum" onkeyup="check_car_mar_num();" onkeypress="check_car_mar_num();"></td>
</tr>
<tr>
    <td>ปีรถ</td><td><input type="text" name="txt_caryear" id="txt_caryear" size="10"></td>
    <td>สีรถ</td><td><select name="txt_carcolor" id="txt_carcolor">
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
    <td>ทะเบียนรถ</td><td><input type="text" name="txt_license_plate" id="txt_license_plate" size="10"></td>
    <td>วันจดทะเบียน</td><td><input type="text" name="txt_regis_date" id="txt_regis_date" size="10" value="<?php echo $nowdate; ?>"></td>
</tr>
<tr>
    <td>จดทะเบียนโดยจังหวัด</td><td><input type="text" name="txt_regis_by" id="txt_regis_by" value="กรุงเทพมหานคร"></td>
    <td>เลขวิทยุ</td><td><input type="text" name="txt_radio_id" id="txt_radio_id" size="10"></td>
</tr>
</table>
        
</div>

<div style="margin-top:10px; text-align:right">
<input type="button" name="btnSave" id="btnSave" value="บันทึก">
<input type="hidden" name="chk_car_mar" id="chk_car_mar" >
</div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>
$('#div_SaleType').load('deposit_car_taken_api.php?cmd=showFinance');

$("#txt_regis_date").datepicker({
    showOn: 'button',
    buttonImage: '../images/calendar.gif',
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd'
});

$('#btnSave').click(function(){
    
    if( $('#cb_product').val() == "" ){
        alert('กรุณาเลือก รุ่น/ยี่ห้อ !');
        return false;
    }
	
	if( $('#txt_carcolor').val() == "" ){
        alert('กรุณาเลือก สีรถ !');
        return false;
    }
	
	if($('#chk_car_mar').val() == '0'){
		alert('กรุณาตรวจสอบเลขเครื่องหรือเลขตัวถังซ้ำ');
		return false;
	}
	
    $.post('deposit_car_taken_api.php',{
        cmd: 'save',
        cb_finance: $('#cb_finance').val(),
        cb_product: $("#cb_product").val(),
        cb_warehouse: $('#cb_warehouse').val(),
        txt_carnum: $('#txt_carnum').val(),
        txt_marnum: $('#txt_marnum').val(),
        txt_caryear: $('#txt_caryear').val(),
        txt_carcolor: $('#txt_carcolor').val(),
        txt_license_plate: $('#txt_license_plate').val(),
        txt_regis_date: $('#txt_regis_date').val(),
        txt_regis_by: $('#txt_regis_by').val(),
        txt_radio_id: $('#txt_radio_id').val(),
        txt_other_product: $('#txt_other_product').val()
    },
    function(data){
        if(data == 't'){
            alert('บันทึกเรียบร้อยแล้ว');
            location.reload();
        }else{
            alert(data);
        }
    });
});

function changeProduct(){
    if($("#cb_product").val() == "other"){
        $('#div_other_product').show('fast');
    }else{
        $('#txt_other_product').val('');
        $('#div_other_product').hide('fast');
    }
}
function check_car_mar_num(){
	$.post('chkdata.php',{
		txtcarnum: $('#txt_carnum').val(),
		txtmarnum: $('#txt_marnum').val(),
		cmd:'taken'
	},function(data){
		if(data == 't'){
			$('#chk_car_mar').val('1');
		}else if(data == 'f'){
			$('#chk_car_mar').val('0');
		}
	});
}
</script>

</body>
</html>
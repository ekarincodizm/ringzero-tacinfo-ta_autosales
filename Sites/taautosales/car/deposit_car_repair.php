<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "ฝากจอดหรือซ่อมแซม";

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

<div>
<b>ฝากจอดหรือซ่อมแซมโดย :</b>
<input type="radio" name="chkSaleType" id="chkSaleType" value="1" checked> Finance
<input type="radio" name="chkSaleType" id="chkSaleType" value="2"> บุคคลทั่วไป
</div>

<div id="div_SaleType" style="margin-top:10px"></div>

<div style="margin-top:10px">
<b>กรุณาระบุเลขเครื่องหรือเลขตัวถัง เพื่อค้นหารถ :</b>
<input type="text" name="findcars" id="findcars" />
<input type="button" name="select" id="select" value="นำไปใช้">
<input type="button" name="clear" id="clear" value="clear" />
<input type="hidden" name="chk_select" id="chk_select" value=""/>
</div>

<div id="detail_car" style="margin-top:10px">

</div>

<div style="margin-top:10px; text-align:right">
<input type="button" name="btnSave" id="btnSave" value="บันทึก">
</div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>
$("#findcars").autocomplete({
        source: "car_autocomplete.php?cmd=findcars",
        minLength:1
});

$('#div_SaleType').load('deposit_car_repair_api.php?cmd=showFinance');
$('#detail_car').load('deposit_car_repair_detail.php');

$("input[name='chkSaleType']").change(function(){
    if( $('input[id=chkSaleType]:checked').val() == "1" ){
        $('#div_SaleType').empty();
        $.get('deposit_car_repair_api.php?cmd=showFinance', function(data){
            $('#div_SaleType').html(data);
        });
    }else{
         $('#div_SaleType').empty();
         $.get('deposit_car_repair_api.php?cmd=showCustomer', function(data){
            $('#div_SaleType').html(data);
        });
    }
});

$('#btnSave').click(function(){

    if( $('#cb_product').val() == "" ){
        alert('กรุณาเลือก รุ่น/ยี่ห้อ ');
        return false;
    }
	if( $('#txt_carnum').val() == "" ){
        alert('กรุณาระบุเลขตัวถังรถ!');
        return false;
    }
	if( $('#txt_marnum').val() == "" ){
        alert('กรุณาระบุเลขเครื่องรถ!');
        return false;
    }
	if( $('#txt_color').val() == "" ){
        alert('กรุณาเลือกสีรถ!');
        return false;
    }
   $.post('deposit_car_repair_api.php',{
        cmd: 'save',
        chkSaleType: $('input[id=chkSaleType]:checked').val(),
        cb_finance: $('#cb_finance').val(),
        txt_name: $('#txt_name').val(),
        txt_pre_name: $('#txt_pre_name').val(),
        txt_firstname: $('#txt_firstname').val(),
        txt_lastname: $('#txt_lastname').val(),
        txt_address: $('#txt_address').val(),
        txt_post: $('#txt_post').val(),
        chkContact: $('input[id=chkContact]:checked').val(),
        txt_contact: $('#txt_contact').val(),
        txt_phone: $('#txt_phone').val(),
        txt_reg: $('#txt_reg').val(),
        txt_barthdate: $('#txt_barthdate').val(),
        combo_cardtype: $("#combo_cardtype").val(),
        txt_cardother: $('#txt_cardother').val(),
        txt_cardno: $('#txt_cardno').val(),
        txt_carddate: $('#txt_carddate').val(),
        txt_cardby: $('#txt_cardby').val(),
        txt_job: $('#txt_job').val(),
        cb_product: $("#cb_product").val(),
        cb_warehouse: $('#cb_warehouse').val(),
        txt_carnum: $('#txt_carnum').val(),
        txt_marnum: $('#txt_marnum').val(),
        txt_caryear: $('#txt_caryear').val(),
        txt_carcolor: $('#txt_color').val(),
        txt_license_plate: $('#txt_license_plate').val(),
        txt_regis_date: $('#txt_regis_date').val(),
        txt_regis_by: $('#txt_regis_by').val(),
        txt_radio_id: $('#txt_radio_id').val(),
        txt_other_product: $('#txt_other_product').val()
    },
    function(data){
        if(data == 't'){
            alert('บันทึกข้อมูลเรียบร้อยแล้ว');
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
$('#select').click(function(){
	if( $('#findcars').val() == "" ){
        alert('กรุณาระบุข้อมูลรถจากการค้นหา!');
        return false;
    }
	$('#detail_car').load('deposit_car_repair_detail.php?car_id='+$('#findcars').val());
});
$('#clear').click(function(){
	$('#detail_car').load('deposit_car_repair_detail.php');
});
</script>

</body>
</html>
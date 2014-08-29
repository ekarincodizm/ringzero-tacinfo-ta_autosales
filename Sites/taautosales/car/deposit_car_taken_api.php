<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "showFinance"){

echo "<b>เลือก Finance</b> <select name=\"cb_finance\" id=\"cb_finance\">";

$qry = pg_query("SELECT * FROM \"VFinances\" ORDER BY cus_id ASC ");
while($res = pg_fetch_array($qry)){
    $cus_id = $res['cus_id'];
    $pre_name = trim($res['pre_name']);
    $cus_name = trim($res['cus_name']);
    $surname = trim($res['surname']);
    echo "<option value=\"$cus_id\">$pre_name $cus_name $surname</option>";
}
echo "</select>";

}

elseif($cmd == "showCustomer"){
?>
<div>
<b>บุคลลทั่วไป</b> ค้นจากฐานข้อมูลที่มี <input type="text" name="txt_name" id="txt_name" size="50" onkeyup="javascript:CheckNaN()">
</div>

<div id="divnewcus" style="margin-top:10px; display:none">

<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#FFFFDD" style="border:1px dashed #D0D0D0">
<tr>
    <td width="60%" valign="top">

<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr>
    <td width="120">คำนำหน้าชื่อ</td><td><input type="text" name="txt_pre_name" id="txt_pre_name" size="10"></td>
</tr>
<tr>
    <td>ชื่อ</td><td><input type="text" name="txt_firstname" id="txt_firstname"> สกุล <input type="text" name="txt_lastname" id="txt_lastname"></td>
</tr>
<tr>
    <td>ที่อยู่</td><td colspan="3"><textarea name="txt_address" id="txt_address" rows="1" cols="1" style="width:330px; height:70px"></textarea></td>
</tr>
<tr>
    <td>รหัสไปรษณีย์</td><td><input type="text" name="txt_post" id="txt_post" size="10"></td>
</tr>
<tr>
    <td>ที่ติดต่อ</td><td>เหมือนด้านบน <input type="radio" name="chkContact" id="chkContact" value="1" checked> กรอกใหม่ <input type="radio" name="chkContact" id="chkContact" value="2"></td>
</tr>
<tr>
    <td></td><td><div style="display:none" id="divcontact"><textarea name="txt_contact" id="txt_contact" rows="3" cols="40"></textarea></div></td>
</tr>
<tr>
    <td>โทรศัพท์</td><td><input type="text" name="txt_phone" id="txt_phone" size="30"></td>
    <td></td><td></td>
</tr>
</table>
    
    </td>
    <td width="40%" valign="top">
    
<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">สัญชาติ</td><td><input type="text" name="txt_reg" id="txt_reg" size="10" value="ไทย"></td>
</tr>
<tr>
    <td>วันเกิด</td><td><input type="text" name="txt_barthdate" id="txt_barthdate" size="10" value="<?php echo $nowdate; ?>"></td>
</tr>
<tr>
    <td>บัตรที่ใช้แสดงตัว</td>
    <td>
<select name="combo_cardtype" id="combo_cardtype">
  <option value="บัตรประชาชน">บัตรประชาชน</option>
  <option value="บัตรข้าราชการ">บัตรข้าราชการ</option>
  <option value="ใบขับขี่">ใบขับขี่</option>
  <option value="อื่นๆ">อื่นๆ</option>
</select> <span id="span_card" style="display:none"><input type="text" name="txt_cardother" id="txt_cardother" size="10"></span>
    </td>
</tr>
<tr>
    <td>เลขที่บัตร</td><td><input type="text" name="txt_cardno" id="txt_cardno" size="30"></td>
</tr>
<tr>
    <td>วันที่ออกบัตร</td><td><input type="text" name="txt_carddate" id="txt_carddate" size="10" value="<?php echo $nowdate; ?>"></td>
</tr>
<tr>
    <td>สถานที่ออกบัตร</td><td><input type="text" name="txt_cardby" id="txt_cardby" size="30"></td>
</tr>
<tr>
    <td>อาชีพ</td><td><input type="text" name="txt_job" id="txt_job" size="30"></td>
</tr>
</table>

    </td>
</tr>
</table>

</div>

<script>
$(document).ready(function(){
    $("#txt_name").autocomplete({
        source: "reserv_car_new_api.php?cmd=autocomplete",
        minLength:1,
        select: function(event, ui) {
            if(ui.item.value == 'ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่'){
                $('#divnewcus').show('fast');
            }else{
                $('#divnewcus').hide('fast');
            }
        }
    });
    
    $("#txt_barthdate, #txt_carddate").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
    
    $("input[name='chkContact']").change(function(){
        if( $('input[id=chkContact]:checked').val() == "2" ){
            $('#divcontact').show('fast');
        }else{
            $('#divcontact').hide('fast');
        }
    });
    
    $("#combo_cardtype").change(function(){
        if( $("#combo_cardtype").val() == "อื่นๆ" ){
            $('#span_card').show('fast');
        }else{
            $('#span_card').hide('fast');
        }
    });
    
});

function CheckNaN(){
    if( $('#txt_name').val() == '' ){
        $('#divnewcus').hide('fast');
    }
}
</script>

<?php
}

elseif($cmd == "save"){
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = "";
    
    $cb_finance=pg_escape_string($_POST['cb_finance']);
    
    $cb_product=pg_escape_string($_POST['cb_product']);
    $cb_warehouse=pg_escape_string($_POST['cb_warehouse']);
    $txt_carnum=pg_escape_string($_POST['txt_carnum']);
    $txt_marnum=pg_escape_string($_POST['txt_marnum']);
    $txt_caryear=checknull(pg_escape_string($_POST['txt_caryear']));
    $txt_carcolor=checknull(pg_escape_string($_POST['txt_carcolor']));
    $txt_license_plate=checknull(pg_escape_string($_POST['txt_license_plate']));
    $txt_regis_date=checknull(pg_escape_string($_POST['txt_regis_date']));
    $txt_regis_by=checknull(pg_escape_string($_POST['txt_regis_by']));
    $txt_radio_id=checknull(pg_escape_string($_POST['txt_radio_id']));
    
    $txt_other_product=$_POST['txt_other_product'];
    
    $arr_cb_product = explode("#", $cb_product);
    
	
		if($cb_finance == "CUS00001"){
			$subknd = "L";
		}else if($cb_finance == "CUS00002"){
			$subknd = "C";
		}
	
    if(!empty($txt_other_product)){
        $arr_cb_product[0] = "";
        $arr_cb_product[1] = $txt_other_product;
    }
    
    $cus_id = $cb_finance;

    $generate_id=@pg_query("select generate_id('$nowdate',$_SESSION[ss_office_id],13,'$subknd')");
    $ds_id=@pg_fetch_result($generate_id,0);
    if( empty($ds_id) ){
        $txt_error .= "generate_id type 8 ไม่สำเร็จ : $generate_id";
        $status++;
    }
    
    $in_qry="INSERT INTO \"DepositSales\" (\"ds_id\",\"ds_date\",\"cus_id\",\"car_id\",\"product_cost\",\"vat\",\"cancel\",\"memo\") values 
    ('$ds_id','$nowdate','$cus_id','$car_id','0','0',DEFAULT,DEFAULT)";
    if(!$res=@pg_query($in_qry)){
        $txt_error .= "บันทึก DepositSales ไม่สำเร็จ $in_qry";
        $status++;
    }
	
	//ตรวจสอบดูว่ามีรถในระบบหรือยัง
	$sql = pg_query("SELECT car_id FROM \"Cars\" where \"car_num\" = '$txt_carnum' or \"mar_num\" = '$txt_marnum' ");						 
	$row = pg_num_rows($sql);
	
	//
	if($row>0){
		 $qry = "UPDATE \"Cars\" set \"car_year\"=$txt_caryear,\"color\"=$txt_carcolor,\"license_plate\"=$txt_license_plate,\"regis_by\"=$txt_regis_date,\"radio_id\"=$txt_radio_id,\"product_id\"='$arr_cb_product[0]',\"car_idno\"='$ds_id',\"res_id\"='Seize Car',car_status='Y',car_type_id='6' where \"car_num\" = '$txt_carnum' and \"mar_num\" = '$txt_marnum' returning car_id";
		if(!$res=@pg_query($qry)){
			$txt_error  .= "Update Cars ไม่สำเร็จ $qry";
			$status++;
		}else{
			$car_id = pg_fetch_result(pg_query($qry),0);
		}
	}else{
		$car_id = GetCarID();
		
		$qry = "INSERT INTO \"Cars\" (\"car_id\",\"car_num\",\"mar_num\",\"car_year\",\"color\",\"license_plate\",\"regis_by\",\"regis_date\",\"radio_id\",\"product_id\",\"po_auto_id\",\"cancel\",\"res_id\",\"car_name\",car_idno,car_status,car_type_id) VALUES 
		('$car_id','$txt_carnum','$txt_marnum',$txt_caryear,$txt_carcolor,$txt_license_plate,$txt_regis_by,$txt_regis_date,$txt_radio_id,'$arr_cb_product[0]',DEFAULT,DEFAULT,'Seize Car','$arr_cb_product[1]','$ds_id','Y','6')";
		if(!$res=@pg_query($qry)){
			$txt_error .= "INSERT Cars ไม่สำเร็จ $qry";
			$status++;
		}
	}
	
	$qry_carmove = pg_query("select car_id from \"CarMove\" where car_id='$car_id' and date_out is null");
	$num_carmove = pg_num_rows($qry_carmove);
	
	//เช็คว่ามีรถจอดอยู่ในคลังอยู่แล้วหรือไม่ ป้องกันข้อมูลซ้ำ
	if($num_carmove == 0){
		$qry = "INSERT INTO \"CarMove\" (\"car_id\",\"color\",\"wh_id\",\"date_in\") VALUES 
		('$car_id',$txt_carcolor,'$cb_warehouse','$nowdate')";
		if(!$res=@pg_query($qry)){
			$txt_error .= "INSERT CarMove ไม่สำเร็จ $qry";
			$status++;
		}
	}

    if($status == 0){
        pg_query("COMMIT");
        $data = "t";
     
    }else{
        pg_query("ROLLBACK");
       $data = $txt_error;
    }

    echo $data;
    
}
?>
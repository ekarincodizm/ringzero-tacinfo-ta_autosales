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
    $txt_error = array();
    
    $chkSaleType=$_POST['chkSaleType'];
    $cb_finance=$_POST['cb_finance'];
    $txt_name=$_POST['txt_name'];
    
    $txt_pre_name=$_POST['txt_pre_name'];
    $txt_firstname=$_POST['txt_firstname'];
    $txt_lastname=$_POST['txt_lastname'];
    $txt_address=$_POST['txt_address'];
    $txt_post=$_POST['txt_post'];
    $chkContact=$_POST['chkContact'];
    $txt_contact=$_POST['txt_contact'];
    $txt_phone=$_POST['txt_phone'];
    $txt_reg=$_POST['txt_reg'];
    $txt_barthdate=$_POST['txt_barthdate'];
    $combo_cardtype=$_POST['combo_cardtype'];
    $txt_cardother=$_POST['txt_cardother'];
    $txt_cardno=$_POST['txt_cardno'];
    $txt_carddate=$_POST['txt_carddate'];
    $txt_cardby=$_POST['txt_cardby'];
    $txt_job=$_POST['txt_job'];
    
    $cb_product=$_POST['cb_product'];
    $cb_warehouse=$_POST['cb_warehouse'];
    $txt_carnum=$_POST['txt_carnum'];
    $txt_marnum=$_POST['txt_marnum'];
    $txt_caryear=$_POST['txt_caryear'];
    $txt_carcolor=$_POST['txt_carcolor'];
    $txt_license_plate=$_POST['txt_license_plate'];
    $txt_regis_date=$_POST['txt_regis_date'];
    $txt_regis_by=$_POST['txt_regis_by'];
    $txt_radio_id=$_POST['txt_radio_id'];
    
    $txt_other_product=$_POST['txt_other_product'];
    
    $arr_cb_product = explode("#", $cb_product);
    
    if(!empty($txt_other_product)){
        $arr_cb_product[0] = "";
        $arr_cb_product[1] = $txt_other_product;
    }
    
    if($chkSaleType == 1){
        $cus_id = $cb_finance;
    }elseif($chkSaleType == 2){
        if($txt_name == "ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่"){
            $cus_id = GetCusID();

            if($chkContact == 1){ $str_contact = $txt_address; }else{ $str_contact = $txt_contact; }
            if($combo_cardtype != "อื่นๆ"){ $str_cardtype = $combo_cardtype; }else{ $str_cardtype = $txt_cardother; }

            $in_qry="INSERT INTO \"Customers\" (\"cus_id\",\"pre_name\",\"cus_name\",\"surname\",\"address\",\"add_post\",\"nationality\",\"birth_date\",\"card_type\",\"card_id\",\"card_do_date\",\"card_do_by\",\"job\",\"contract_add\",\"telephone\") values 
            ('$cus_id','$txt_pre_name','$txt_firstname','$txt_lastname','$txt_address','$txt_post','$txt_reg','$txt_barthdate','$str_cardtype','$txt_cardno','$txt_carddate','$txt_cardby','$txt_job','$str_contact','$txt_phone')";
            if(!$res=@pg_query($in_qry)){
                $txt_error[] = "บันทึก Customers ไม่สำเร็จ $in_qry";
                $status++;
            }
        }else{
            $arr_txt_name = explode("#",$txt_name);
            $cus_id = $arr_txt_name[0];
        }
    }

    $generate_id=@pg_query("select generate_id('$nowdate',$_SESSION[ss_office_id],6)");
    $ds_id=@pg_fetch_result($generate_id,0);
    if( empty($ds_id) ){
        $txt_error[] = "generate_id type 6 ไม่สำเร็จ : $generate_id";
        $status++;
    }

    $car_id = GetCarID();
    
    $in_qry="INSERT INTO \"DepositSales\" (ds_id,ds_date,cus_id,car_id,product_cost,vat,cancel,memo,product_id) values 
    ('$ds_id','$nowdate','$cus_id','$car_id','0','0',DEFAULT,DEFAULT,'$arr_cb_product[0]')";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "บันทึก DepositSales ไม่สำเร็จ $in_qry";
        $status++;
    }

    $qry = "INSERT INTO \"Cars\" (\"car_id\",\"car_num\",\"mar_num\",\"car_year\",\"color\",\"license_plate\",\"regis_by\",\"regis_date\",\"radio_id\",\"product_id\",\"po_id\",\"po_auto_id\",\"cancel\",\"res_id\",\"car_name\") VALUES 
    ('$car_id','$txt_carnum','$txt_marnum','$txt_caryear','$txt_carcolor','$txt_license_plate','$txt_regis_by','$txt_regis_date','$txt_radio_id','$arr_cb_product[0]','$ds_id',DEFAULT,DEFAULT,DEFAULT,'$arr_cb_product[1]')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT Cars ไม่สำเร็จ $qry";
        $status++;
    }
    
    $qry = "INSERT INTO \"CarMove\" (\"car_id\",\"color\",\"wh_id\",\"date_in\") VALUES 
    ('$car_id','$txt_carcolor','$cb_warehouse','$nowdate')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT CarMove ไม่สำเร็จ $qry";
        $status++;
    }

    if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }

    echo json_encode($data);
    
}
?>
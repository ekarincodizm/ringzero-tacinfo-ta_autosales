<?php
include_once("../include/config.php");
include_once("../include/function.php");
?>

<div>
<b>ผู้จอง</b> ค้นจากฐานข้อมูลที่มี <input type="text" name="txt_name" id="txt_name" size="50" onkeyup="javascript:CheckNaN()">
</div>

<div id="divnewcus" style="margin-top:10px; display:none">

<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
<tr>
    <td width="60%" valign="top">

<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">คำนำหน้าชื่อ</td><td><input type="text" name="txt_pre_name" id="txt_pre_name" size="10"></td>
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

<div class="linedotted"></div>

<div style="margin-top:10px; float:left; width:510px; border-right:1px dotted #E0E0E0">
<b>รูปแบบการจอง</b><br />
<input type="radio" name="radio_resv_type" id="radio_resv_type" value="1" checked> ไม่เจาะจงรถ <input type="radio" name="radio_resv_type" id="radio_resv_type" value="2"> เจาะจงรถ

<div id="divresv1" style="margin-top:10px; margin-left:25px;">
รูปแบบรถ : <select name="resv_car_plan" id="resv_car_plan" onchange="javascript:changePlan()">
<option value="">เลือก</option>
<?php
$qry = pg_query("SELECT * FROM \"Products\" WHERE \"link_table\"='Cars' ORDER BY \"name\" ASC ");
while($res = pg_fetch_array($qry)){
    $product_id = $res['product_id'];
    $name = $res['name'];
    echo "<option value=\"$product_id#$name\">$name</option>";
}
?>
</select>
&nbsp;สีรถ : <input type="radio" name="radio_car_color" id="radio_car_color" value="ฟ้า" checked>ฟ้า <input type="radio" name="radio_car_color" id="radio_car_color" value="เหลือง">เหลือง <input type="radio" name="radio_car_color" id="radio_car_color" value="เขียวเหลือง">เขียวเหลือง 
</div>

<div id="divresv2" style="margin-top:10px; margin-left:25px; display:none">เลือกรถ : 
<select name="combo_car_stock" id="combo_car_stock">
    <option value="">เลือก</option>
<?php
$VStockCars = pg_query("SELECT * FROM \"VStockCars\" WHERE \"IDNO\" IS NULL ORDER BY car_num ASC ");
while($res_VStockCars = pg_fetch_array($VStockCars)){
    $stock_car_id = $res_VStockCars['car_id'];
    $stock_car_num = $res_VStockCars['car_num'];
    $stock_license_plate = $res_VStockCars['license_plate'];
    $stock_product_id = $res_VStockCars['product_id'];
    $stock_product_name = GetProductName($stock_product_id);
?>
    <option value="<?php echo "$stock_product_id#$stock_car_id"; ?>"><?php echo "$stock_product_name $stock_car_num $stock_license_plate";?></option>
<?php
}
?>
</select>
</div>

</div>

<div style="margin-top:10px; float:right; width:300px">
<b>อุปกรณ์ส่วนควบ</b><br />

<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
    <td width="150">ประกันประเภท</td>
    <td>
<select name="cb_insure" id="cb_insure">
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
</select>
    </td>
</tr>
<tr>
    <td>ติดตั้งเครื่องวิทยุสื่อสาร</td>
    <td><input type="radio" name="radio_commu" id="radio_commu" value="1" checked> ติดตั้ง <input type="radio" name="radio_commu" id="radio_commu" value="2"> ไม่ติดตั้ง</td>
</tr>
</table>

</div>

<div style="clear:both"></div>

<div class="linedotted"></div>

<div style="margin-top:10px">
<b>รูปแบบการซื้อ</b><br />
<input type="radio" name="radio_buy_type" id="radio_buy_type" value="1" checked> ซื้อผ่อน <input type="radio" name="radio_buy_type" id="radio_buy_type" value="2"> ซื้อสด

<div id="divbuy2" style="margin-top:10px; margin-left:25px; display:none">
ราคารถ <input type="text" name="txt_buy_price1" id="txt_buy_price1" size="10" onblur="javascript:CheckPlan()" onkeyup="javascript:changeBtn()"><br />
</div>

<div id="divbuy1" style="margin-top:10px; margin-left:25px">
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">ราคารถ</td><td><input type="text" name="txt_buy_price2" id="txt_buy_price2" size="10" onblur="javascript:CheckPlan()"></td>
</tr>
<tr>
    <td>ดาวน์</td><td><input type="text" name="txt_buy_down" id="txt_buy_down" size="10" onkeyup="javascript:changeBtn()"></td>
</tr>
<tr>
    <td>จำนวนงวดผ่อน</td><td><input type="text" name="txt_buy_numdue" id="txt_buy_numdue"size="5"></td>
</tr>
<tr>
    <td>ค่างวด</td><td><input type="text" name="txt_buy_monnydue" id="txt_buy_monnydue" size="10"></td>
</tr>
<tr>
    <td>บริษัท Finance</td>
    <td>
<select name="cb_finance" id="cb_finance">
<?php
$qry = pg_query("SELECT * FROM \"Finances\" ORDER BY \"finance_id\" ASC ");
while($res = pg_fetch_array($qry)){
    $finance_id = $res['finance_id'];
    $cus_id = $res['cus_id'];

    $cus_name = GetCusName($cus_id);

    echo "<option value=\"$cus_id\">$cus_name</option>";
}
?>
</select>
    </td>
</tr>
</table>
</div>

</div>

<div class="linedotted"></div>

<div style="margin-top:10px">
ยอดเงินจอง <input type="text" name="txt_resv_money" id="txt_resv_money" size="10" onkeyup="javascript:Summary(); javascript:changeBtn()">&nbsp;&nbsp;&nbsp;&nbsp;วันที่ต้องการออกรถ <input type="text" name="txt_date_car" id="txt_date_car" size="10" value="<?php echo $nowdate; ?>">&nbsp;&nbsp;&nbsp;&nbsp;ยอดคงเหลือ <b><span id="span_sum">0</span></b> บาท.
</div>

<div style="margin-top:10px">
<b>ชำระโดย</b><br />
<input type="checkbox" name="chk_buy_cash" id="chk_buy_cash"> เงินสด
<span id="divcash" style="display:none">&nbsp;ยอดเงินสด <input type="text" name="txt_cash" id="txt_cash" size="10"></span>
<br />
<input type="checkbox" name="chk_buy_cheque" id="chk_buy_cheque"> เช็ค
<div id="divcheque" style="margin-top:10px; margin-left:25px; display:none">
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">ธนาคาร</td><td><input type="text" name="txt_cheque_bank" id="txt_cheque_bank" size="10"></td>
</tr>
<tr>
    <td>สาขา</td><td><input type="text" name="txt_cheque_banch" id="txt_cheque_banch" size="10"></td>
</tr>
<tr>
    <td>เลขที่เช็ค</td><td><input type="text" name="txt_cheque_no" id="txt_cheque_no"size="10"></td>
</tr>
<tr>
    <td>วันที่บนเช็ค</td><td><input type="text" name="txt_cheque_date" id="txt_cheque_date" size="10" value="<?php echo $nowdate; ?>"></td>
</tr>
<tr>
    <td>ยอดเงินบนเช็ค</td><td><input type="text" name="txt_cheque_monny" id="txt_cheque_monny" size="10"></td>
</tr>
<tr>
    <td>จังหวัดเช็ค</td><td>
<input type="radio" name="radio_cheque_prov" id="radio_cheque_prov" value="1" checked> กรุงเทพ <input type="radio" name="radio_cheque_prov" id="radio_cheque_prov" value="2"> ต่างจังหวัด
    </td>
</tr>
</table>
</div>
</div>

<div class="linedotted"></div>

<div style="margin-top:10px; text-align:right">
<span id="span_btn_out" style="display:none">
<input type="hidden" name="hid_constant_var" id="hid_constant_var" value="">
<input type="button" name="btnSaveOut" id="btnSaveOut" value="ออกรถเลย" onclick="javascript:SaveNewCar('Y')"></input>
<input type="button" name="btnSaveNoOut" id="btnSaveNoOut" value="ยังไม่ออกรถ" onclick="javascript:SaveNewCar('N')"></input>
</span>
<span id="span_btn_normal">
<input type="button" name="btnSaveNormal" id="btnSaveNormal" value="บันทึก" onclick="javascript:SaveNewCar('N')">
</span>
</div>

<script type="text/javascript">
function changeBtn(){
    if( $('input[id=radio_buy_type]:checked').val() == "2" ){
        if( $('#txt_resv_money').val() ==  $('#txt_buy_price1').val() && $('#txt_resv_money').val() != "" && $('#txt_buy_price1').val() != "" ){
            $('#span_btn_out').show('fast');
            $('#span_btn_normal').hide('fast');
            $('#hid_constant_var').val('CARCA');
        }else{
            $('#span_btn_out').hide('fast');
            $('#span_btn_normal').show('fast');
            $('#hid_constant_var').val('CARRE');
        }
    }else{
        if( $('#txt_resv_money').val() ==  $('#txt_buy_down').val() && $('#txt_resv_money').val() != "" && $('#txt_buy_down').val() != "" ){
            $('#span_btn_out').show('fast');
            $('#span_btn_normal').hide('fast');
            $('#hid_constant_var').val('CARDW');
        }else{
            $('#span_btn_out').hide('fast');
            $('#span_btn_normal').show('fast');
            $('#hid_constant_var').val('CARRE');
        }
    }
}
    
$(document).ready(function(){
    
    $("#txt_barthdate, #txt_carddate, #txt_date_car, #txt_cheque_date").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
    
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
    
    $("input[name='radio_resv_type']").change(function(){
        if( $('input[id=radio_resv_type]:checked').val() == "2" ){
            $('#divresv1').hide('fast');
            $('#divresv2').show('fast');
            $('#resv_car_plan').val('');
            $('#txt_buy_price1').val('');
            $('#txt_buy_price2').val('');
        }else{
            $('#divresv1').show('fast');
            $('#divresv2').hide('fast');
        }
        changeBtn();
        Summary();
    });
    
    
    $("input[name='radio_car_color']").change(function(){
        if( $('input[id=radio_car_color]:checked').val() == "ฟ้า" ){
            $('#cb_finance').val('CUS00001');
        }else if( $('input[id=radio_car_color]:checked').val() == "เหลือง" ){
            $('#cb_finance').val('CUS00002');
        }else{
            
        }
    });
    
    $("input[name='radio_buy_type']").change(function(){
        if( $('input[id=radio_buy_type]:checked').val() == "2" ){
            //$('#txt_buy_price1').val('');
            $('#divbuy1').hide('fast');
            $('#divbuy2').show('fast');
        }else{
            //$('#txt_buy_price2').val('');
            $('#txt_buy_down').val('');
            $('#txt_buy_numdue').val('');
            $('#txt_buy_monnydue').val('');
            $('#divbuy1').show('fast');
            $('#divbuy2').hide('fast');
        }
        changeBtn();
        Summary();
    });
    
    $("input[name='chk_buy_cash']").change(function(){
        
        if( $('input[id=chk_buy_cheque]:checked').val() ){
            var m1 = parseFloat( $('#txt_resv_money').val() );
            var m2 = parseFloat( $('#txt_cheque_monny').val() );
            $('#txt_cash').val( m1-m2 );
        }else{
            $('#txt_cash').val( $('#txt_resv_money').val() );
        }
        
        if( $('input[id=chk_buy_cash]:checked').val() ){
            $('#divcash').show('fast');
        }else{
            $('#txt_cash').val('');
            $('#divcash').hide('fast');
        }
    });
    
    $("input[name='chk_buy_cheque']").change(function(){
        
        if( $('input[id=chk_buy_cash]:checked').val() ){
            var m1 = parseFloat( $('#txt_resv_money').val() );
            var m2 = parseFloat( $('#txt_cash').val() );
            $('#txt_cheque_monny').val( m1-m2 );
        }else{
            $('#txt_cheque_monny').val( $('#txt_resv_money').val() );
        }
        
        if( $('input[id=chk_buy_cheque]:checked').val() ){
            $('#divcheque').show('fast');
        }else{
            $('#txt_cheque_monny').val('');
            $('#divcheque').hide('fast');
        }
    });
    /*
    $('#btnSave').click(function(){
        if( $('#txt_name').val() == "" ){
            alert('กรุณาระบุ ผู้จอง !');
            return false;
        }
        
        if( $('input[id=radio_resv_type]:checked').val() == "2" ){
            if( $('#combo_car_stock').val() == "" ){
                alert('กรุณาเลือกรถ !');
                return false;
            }
        }else if( $('input[id=radio_resv_type]:checked').val() == "1" ){
            if( $('#resv_car_plan').val() == "" ){
                alert('กรุณาเลือกรูปแบบรถ !');
                return false;
            }
        }
        
        if( !chkSum() ){
            alert('ยอดเงินสด/เช็ค ไม่ถูกต้อง ยอดรวมต้องเท่ากับ ยอดเงินจอง');
            return false;
        }
        
        $.post('reserv_car_new_api.php',{
            cmd: 'save',
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
            radio_resv_type: $('input[id=radio_resv_type]:checked').val(),
            resv_car_plan: $("#resv_car_plan").val(),
            radio_car_color: $('input[id=radio_car_color]:checked').val(),
            combo_car_stock: $('#combo_car_stock').val(),
            radio_buy_type: $('input[id=radio_buy_type]:checked').val(),
            txt_buy_price1: $('#txt_buy_price1').val(),
            txt_buy_price2: $('#txt_buy_price2').val(),
            txt_buy_down: $('#txt_buy_down').val(),
            txt_buy_numdue: $('#txt_buy_numdue').val(),
            txt_buy_monnydue: $('#txt_buy_monnydue').val(),
            cb_finance: $('#cb_finance').val(),
            cb_insure: $('#cb_insure').val(),
            radio_commu: $('input[id=radio_commu]:checked').val(),
            txt_resv_money: $('#txt_resv_money').val(),
            txt_date_car: $('#txt_date_car').val(),
            chk_buy_cash: $('input[id=chk_buy_cash]:checked').val(),
            txt_cash: $('#txt_cash').val(),
            chk_buy_cheque: $('input[id=chk_buy_cheque]:checked').val(),
            txt_cheque_bank: $('#txt_cheque_bank').val(),
            txt_cheque_banch: $('#txt_cheque_banch').val(),
            txt_cheque_no: $('#txt_cheque_no').val(),
            txt_cheque_date: $('#txt_cheque_date').val(),
            txt_cheque_monny: $('#txt_cheque_monny').val(),
            radio_cheque_prov: $('input[id=radio_cheque_prov]:checked').val(),
            span_sum: $('#span_sum').text()
        },
        function(data){
            if(data.success){
                alert(data.message);
                location.reload();
            }else{
                alert(data.message);
            }
        },'json');
    });*/
});

function Summary(){
    var s1 = 0;
    var a1 = parseFloat($('#txt_resv_money').val());
    var a2 = parseFloat($('#txt_buy_price1').val());
    var a3 = parseFloat($('#txt_buy_price2').val());

    if ( isNaN(a1) || a1 == ""){
        a1 = 0;
    }
    if ( isNaN(a2) || a2 == ""){
        a2 = 0;
    }
    if ( isNaN(a3) || a3 == ""){
        a3 = 0;
    }
    
    if( $('input[id=radio_buy_type]:checked').val() == "2" ){
        s1 = a2-a1;
    }else{
        s1 = a3-a1;
    }

    $('#span_sum').text(s1);
}

function chkSum(){
    var s1 = 0;
    var a1 = parseFloat($('#txt_resv_money').val());
    var a2 = parseFloat($('#txt_cash').val());
    var a3 = parseFloat($('#txt_cheque_monny').val());

    if ( isNaN(a1) || a1 == ""){
        a1 = 0;
    }
    if ( isNaN(a2) || a2 == ""){
        a2 = 0;
    }
    if ( isNaN(a3) || a3 == ""){
        a3 = 0;
    }
    
    if( $('input[id=chk_buy_cash]:checked').val() ){
        s1+=a2;
    }
    if( $('input[id=chk_buy_cheque]:checked').val() ){
        s1+=a3;
    }
    
    if(a1 != s1){
        return false;
    }else{
        return true;
    }
}

function CheckNaN(){
    if( $('#txt_name').val() == '' ){
        $('#divnewcus').hide('fast');
    }
}

function changePlan(){
    if( $('#resv_car_plan').val() == '' ){
        $('#txt_buy_price1').val('');
        $('#txt_buy_price2').val('');
        Summary();
        return false;
    }
    
    var str_plan = $("#resv_car_plan").val();
    var arr_plan = str_plan.split("#");
    
    $.get('reserv_car_new_api.php?cmd=changePlan&id='+arr_plan[0], function(data){
        $('#txt_buy_price1').val(data);
        $('#txt_buy_price2').val(data);
        Summary();
    });
}

function CheckPlan(){
    var str_plan = $("#resv_car_plan").val();
    var arr_plan = str_plan.split("#");

    $.get('reserv_car_new_api.php?cmd=CheckPlan&id='+arr_plan[0], function(data){
        if( $('input[id=radio_buy_type]:checked').val() == "2" ){
            if( parseFloat( data ) > parseFloat( $('#txt_buy_price1').val() ) ){
                alert('ราคารถ ต่ำกว่าต้นทุน ที่จะขายได้ กรุณาใส่ใหม่');
                changePlan();
                $('#txt_buy_price1').focus();
            }
        }else{
            if( parseFloat( data ) > parseFloat( $('#txt_buy_price2').val() ) ){
                alert('ราคารถ ต่ำกว่าต้นทุน ที่จะขายได้ กรุณาใส่ใหม่');
                changePlan();
                $('#txt_buy_price2').focus();
            }
        }
    });
    Summary();
}


function SaveNewCar(t){

        if( $('#txt_name').val() == "" ){
            alert('กรุณาระบุ ผู้จอง !');
            return false;
        }
        
        if( $('input[id=radio_resv_type]:checked').val() == "2" ){
            if( $('#combo_car_stock').val() == "" ){
                alert('กรุณาเลือกรถ !');
                return false;
            }
        }else if( $('input[id=radio_resv_type]:checked').val() == "1" ){
            if( $('#resv_car_plan').val() == "" ){
                alert('กรุณาเลือกรูปแบบรถ !');
                return false;
            }
        }
        
        if( !chkSum() ){
            alert('ยอดเงินสด/เช็ค ไม่ถูกต้อง ยอดรวมต้องเท่ากับ ยอดเงินจอง');
            return false;
        }
        
        $.post('reserv_car_new_api.php',{
            cmd: 'save',
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
            radio_resv_type: $('input[id=radio_resv_type]:checked').val(),
            resv_car_plan: $("#resv_car_plan").val(),
            radio_car_color: $('input[id=radio_car_color]:checked').val(),
            combo_car_stock: $('#combo_car_stock').val(),
            radio_buy_type: $('input[id=radio_buy_type]:checked').val(),
            txt_buy_price1: $('#txt_buy_price1').val(),
            txt_buy_price2: $('#txt_buy_price2').val(),
            txt_buy_down: $('#txt_buy_down').val(),
            txt_buy_numdue: $('#txt_buy_numdue').val(),
            txt_buy_monnydue: $('#txt_buy_monnydue').val(),
            cb_finance: $('#cb_finance').val(),
            cb_insure: $('#cb_insure').val(),
            radio_commu: $('input[id=radio_commu]:checked').val(),
            txt_resv_money: $('#txt_resv_money').val(),
            txt_date_car: $('#txt_date_car').val(),
            chk_buy_cash: $('input[id=chk_buy_cash]:checked').val(),
            txt_cash: $('#txt_cash').val(),
            chk_buy_cheque: $('input[id=chk_buy_cheque]:checked').val(),
            txt_cheque_bank: $('#txt_cheque_bank').val(),
            txt_cheque_banch: $('#txt_cheque_banch').val(),
            txt_cheque_no: $('#txt_cheque_no').val(),
            txt_cheque_date: $('#txt_cheque_date').val(),
            txt_cheque_monny: $('#txt_cheque_monny').val(),
            radio_cheque_prov: $('input[id=radio_cheque_prov]:checked').val(),
            span_sum: $('#span_sum').text(),
            hid_constant_var: $('#hid_constant_var').val(),
            t: t
        },
        function(data){
            if(data.success){
                alert(data.message);
                location.reload();
            }else{
                alert(data.message);
            }
        },'json');

}
</script>
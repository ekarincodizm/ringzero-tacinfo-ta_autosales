<?php
include_once("../include/config.php");
include_once("../include/function.php");
?>

<div>
	<table cellpadding="3" cellspacing="0" border="0" width="100%">
		<tr>
			<td><b>ผู้จอง</b> ค้นจากฐานข้อมูลที่มี <input type="text" name="txt_name" id="txt_name" size="39" onkeyup="javascript:CheckNaN()"></td>
		</tr>
		<tr>
			<td><label>พนักงานขาย</label>&nbsp;&nbsp;
				<input type="text" name="txt_sale" id="txt_sale" size="50" value="<?php echo $_SESSION["ss_iduser"].'#'.$_SESSION["ss_username"]; ?>">
				<input type="hidden" name="hd_sale" id="hd_sale" size="59" value="<?php echo $_SESSION["ss_iduser"] ?>"> 
			</td>
		</tr>
		<tr>
			<td><label>พยาน</label>&nbsp;&nbsp;<input type="text" name="txt_witness" id="txt_witness" size="55" ></td>
		</tr>
	</table>
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

<div style="margin:5px 0px 5px 0px">
<input type="radio" name="radio_carnew_type" id="radio_carnew_type" value="1" checked> รถใหม่ 
<input type="radio" name="radio_carnew_type" id="radio_carnew_type" value="2"> รถใช้แล้ว
</div>

<div id ="div_select_show_car_new">
	<input type="radio" name="radio_resv_type" id="radio_resv_type" value="1" checked> ไม่เจาะจงรถ 
	<input type="radio" name="radio_resv_type" id="radio_resv_type" value="2"> เจาะจงรถ
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>สีรถแท๊กซี่:</label>
	<select name="ddl_car_color" id="ddl_car_color" >
		<option value="not">เลือก</option>
		<option value="ฟ้า">ฟ้า</option>
		<option value="เหลือง">เหลือง</option>		
		<option value="เขียวเหลือง">เขียวเหลือง</option>
		<option value="สีเดิม">สีเดิม</option>
	</select>
</div>
<!-- <div id="div_show_resv_car_type"></div> -->



<!-- รถใหม่ ไม่เจาะจงรถ> -->
<div id="div_show_not_spec_car_new"><br>
<label>รูปแบบรถ : </label> <select name="resv_car_plan" id="resv_car_plan" onchange="javascript:changePlan()">
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
 </div>

<!-- รถใหม่ เจาะจงรถ -->
<div id="div_show_spec_car_new" style="display:none">
<br>
	<label>เลือกรถ :</label> <input type="text" name="txt_spec_car_new" id="txt_spec_car_new" size="70" >
</div>

<div id = "div_show_car_used" style="display:none">
	<label>เลือกรถใช้แล้ว :</label> <input type="text" name="txt_car_used" id="txt_car_used" size="70" >
</div>

</div>


<!-- สิ้นสุด ฟอร์ม 1  -->
<div style="margin-top:10px; float:right; width:300px">
<b>อุปกรณ์ส่วนควบ</b><br />

<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
    <td width="150">ประกันประเภท</td>
    <td>
<select name="cb_insure" id="cb_insure">
	<option value="not">กรุณาเลือก</option>
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
</select>
    </td>
</tr>
<tr>
    <td>ติดตั้งเครื่องวิทยุสื่อสาร</td>
    <td><input type="radio" name="radio_commu" id="radio_commu" value="1"> ติดตั้ง <input type="radio" name="radio_commu" id="radio_commu" value="2"> ไม่ติดตั้ง</td>
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
    <td width="100">ราคารถ</td><td><input type="text" name="txt_buy_price2" id="txt_buy_price2" size="10" onblur="javascript:CheckPlan()" onkeyup="javascript:change_interest_rate()"></td>
</tr>
<tr>
    <td>ดาวน์</td><td><input type="text" name="txt_buy_down" id="txt_buy_down" size="10" onkeyup="javascript:changeBtn(); javascript:change_interest_rate()"></td>
</tr>
<tr>
    <td>จำนวนงวดผ่อน</td><td><input type="text" name="txt_buy_numdue" id="txt_buy_numdue"size="5" onkeyup="javascript:change_interest_rate()"></td>
</tr>
<tr>
    <td>ค่างวด</td><td><input type="text" name="txt_buy_monnydue" id="txt_buy_monnydue" size="10" onkeyup="javascript:change_interest_rate()"> ดอกเบี้ย = <span id="span_interest_rate">0.00</span></td>
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
ยอดเงินดาวน์/เงินจอง <input type="text" name="txt_resv_money" id="txt_resv_money" size="10" onblur="javascript:check_down_price()" onkeyup="javascript:Summary(); javascript:changeBtn()">
&nbsp;&nbsp;&nbsp;&nbsp;วันที่ต้องการออกรถ <input type="text" name="txt_date_car" id="txt_date_car" size="10" value="">
&nbsp;&nbsp;&nbsp;&nbsp;ยอดคงเหลือ <b><span id="span_sum">0</span></b> บาท.
</div>

<div style="margin-top:10px">
    
<!-- <div style="float:left"> -->
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
<td>
<b>หมายเหตุ</b><br />
<textarea name="area_remark_new" id="area_remark_new" rows="3" cols="50"></textarea>
</td>
<td>
<font color="red" > ***เงื่อนไขการจอง***</font>
<br>
<font color="red" > 1.กรณี ลูกค้า ไม่มีเงินจอง รับรถพร้อมจ่ายเงินดาวน์ครบ ให้ใส่ช่องเงินจองเต็มจำนวนเงินดาวน์ หลังจากแคชเชียร์รับชำระเงินแล้วทำการเปลี่ยนแปลงการจอง โดยไม่ต้องใส่จำนวนเงินใดๆทั้งสิ้น</font>
<br>
<font color="red" > 2.กรณี ลูกค้า ไม่มีเงินดาวน์และไม่จอง ให้ใส่จำนวนเงินจองและเงินดาวน์เป็น 0 บาท และทำการเปลี่ยนแปลงการจอง ได้เลย โดยไม่ต้องรอแคชเชียร์ตัดใเสร็จ</font>
<br>
<font color="red" > 3.กรณี มีการให้ส่วนลดเงินดาวน์ ให้ระบุเงินดาวน์เต็มจำนวน แล้วแจ้งส่วนลด ที่ช่อง หมายเหตุ แล้วแจ้งให้แคชเชียร์ทราบ ตอนออกใบเสร็จ</font>
</td>
</tr>

<!-- </div> -->
<div style="float:right">
<!-- RIGHT -->
</div>
<div style="clear:both"></div>
</div>

<div class="linedotted"></div>

<div style="margin-top:10px; text-align:right">
<input type="button" name="btnSaveNormal" id="btnSaveNormal" value="บันทึก" onclick="javascript:SaveNewCar()">
</div>

<script type="text/javascript">    
function changeBtn(){
    /*
    if( $('input[id=radio_buy_type]:checked').val() == "2" ){ //ซื้อสด
        if( $('#txt_resv_money').val() ==  $('#txt_buy_price1').val() && $('#txt_resv_money').val() != "" && $('#txt_buy_price1').val() != "" ){
            $('#span_btn_out').show('fast');
            $('#span_btn_normal').hide('fast');
        }else{
            $('#span_btn_out').hide('fast');
            $('#span_btn_normal').show('fast');
        }
    }else if( $('input[id=radio_buy_type]:checked').val() == "1" ){ //ซื้อผ่อน
        if( $('#txt_resv_money').val() == $('#txt_buy_down').val() ){
            $('#span_btn_out').show('fast');
            $('#span_btn_normal').hide('fast');
        }else{
            $('#span_btn_out').hide('fast');
            $('#span_btn_normal').show('fast');
        }
    }
    */
}

function check_down_price(){
    var down = $('#txt_buy_down').val();
    var txt_resv_money = $('#txt_resv_money').val();
    
    if(down > 0 && txt_resv_money == 0){
        alert('ยอดเงินดาวน์ /เงินจองไม่ถูกต้อง !');
        $('#txt_resv_money').val('');
    }
}
    
$(document).ready(function(){
    //แสดงรูปแบบการจองรถ
	$('#div_show_resv_car_type').load('reserv_car_new_api.php?cmd=Change_Resv_Car_type&t=1');
	
    $("#txt_barthdate, #txt_carddate, #txt_date_car").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        showOn: 'both'
    });
    
    $("#txt_name").autocomplete({
        source: "reserv_car_new_api.php?cmd=autocomplete",
        minLength:1,
        select: function(event, ui) {
            if(ui.item.value == 'ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่'){
				show_customer();
            }else{
               
            }
        }
    });
	
	//========= ค้นหา Sale =========//
	$("#txt_sale").autocomplete({
        source: "reserv_car_new_api.php?cmd=sale_autocomplete",
        minLength:1
    });
	//========= ค้นหาพยาน =========//
	$("#txt_witness").autocomplete({
        source: "reserv_car_new_api.php?cmd=witness_autocomplete",
        minLength:1
    });
	
	//ค้นหารถใหม่เจาะจงรถ
	$("#txt_spec_car_new").autocomplete({
		source: "reserv_car_new_api.php?cmd=car_new",
        minLength:1,
		select: function(event, ui) {
			if(ui.item.value != ''){
				var combo_car_stock = ui.item.value;  //$("#txt_spec_car_new").val();
				var arr_car_stock = combo_car_stock.split("#");
				if(arr_car_stock[5] == "R"){
					show_car_reserve(arr_car_stock[4]);
				}
            }	
        }
    });
	
	//ค้นหารถใช้แล้ว
	$("#txt_car_used").autocomplete({
        source: "reserv_car_new_api.php?cmd=car_used",
        minLength:1,
		select: function(event, ui) {
			if(ui.item.value != ''){
				var combo_car_stock = ui.item.value; 
				var arr_car_stock = combo_car_stock.split("#");
				if(arr_car_stock[5] == "R"){
					show_car_reserve(arr_car_stock[4]);
				}
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
   
 //================== แสดงประเภทรถ ===================================//   
	$("input[name='radio_carnew_type']").change(function(){
        if( $('input[id=radio_carnew_type]:checked').val() == "1" ){ //รถใหม่
			$('#div_select_show_car_new').show(); //เลือก เจาะจงรถ กับ ไม่เจาะจง
			$('#div_show_car_used').hide();
			if( $('input[id=radio_resv_type]:checked').val() == "1" ){ //ไม่เจาะจงรถ
				$('#div_show_not_spec_car_new').show();
			}else{ //เจาะจงรถ
				$('#div_show_car_new').hide();
				$('#div_show_spec_car_new').show(); //รถใหม่เจาะจงรถ
			}
			
		  $('#txt_spec_car_new').val('');	
			
        }else{ //รถใช้แล้ว
		  $('#div_show_car_used').show();
		  $('#div_show_not_spec_car_new').hide();
		  $('#div_select_show_car_new').hide();
		  $('#div_show_spec_car_new').hide();
		  $('#txt_car_used').val('');
		
        }
    });
	
	$("input[name='radio_resv_type']").change(function(){
        if( $('input[id=radio_resv_type]:checked').val() == "2" ){//เจาะจงรถ
            $('#div_show_spec_car_new').show(); //เลือกรถแบบเจาะจง
			$('#div_show_not_spec_car_new').hide(); //เลือกรถแบบไม่เจาะจง
        }else{ //ไม่เจาะจงรถ
			$('#div_show_spec_car_new').hide();
			$('#div_show_not_spec_car_new').show();
			$('#txt_spec_car_new').val('');
			$('#div_show_car_used').hide();
			
        }
    });
	
	
	
    
   /* $("input[name='radio_car_color']").change(function(){
        if( $('input[id=radio_car_color]:checked').val() == "ฟ้า" ){
            $('#cb_finance').val('CUS00001');
        }else if( $('input[id=radio_car_color]:checked').val() == "เหลือง" ){
            $('#cb_finance').val('CUS00002');
        }else{
            
        }
    });*/
    
    $("input[name='radio_buy_type']").change(function(){
        if( $('input[id=radio_buy_type]:checked').val() == "2" ){
            //$('#txt_buy_price1').val('');
            $('#txt_buy_down').val('');
            $('#txt_buy_numdue').val('');
            $('#txt_buy_monnydue').val('');
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
}); // ปิด ready

//======================== แสดง Pupup ข้อมูลลูกค้า ========================//
function show_customer(){
 $('body').append('<div id="div_customer"></div>');
	$('#div_customer').load('../customer/customer_api.php?tab=1');
		$('#div_customer').dialog({ 
			title: 'เพิ่มข้อมูลใหม่  ',
			resizable: false,
			modal: true,  
			width: 850,
			height:600,
		close: function(ev, ui){
				$('#div_customer').remove();
                }
        });
}

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
    if( $('input[id=radio_resv_type]:checked').val() == "1" ){

    // ถ้ารถใหม่
	if( $('input[id=radio_carnew_type]:checked').val() == "1" ){
	var str_plan = $("#resv_car_plan").val();
	}else
	{
	//ถ้ารถใช้แล้ว
	var str_plan = $("#txt_car_used").val();
	}
    if( str_plan == "" ){
        alert('กรุณาเลือกรูปแบบรถ !');
        $('#txt_buy_price1').val('');
        $('#txt_buy_price2').val('');
        return false;
    } 
    
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
    
    }else{
        
		var combo_car_stock = $("#txt_spec_car_new").val();
		if( combo_car_stock == "" ){
            alert('กรุณาเลือกรถ !');
            $('#txt_buy_price1').val('');
            $('#txt_buy_price2').val('');
            return false;
        } 
		
		var arr_car_stock = combo_car_stock.split("#");
        //ตรวจสอบราคารถ
        $.get('reserv_car_new_api.php?cmd=CheckCarsCostPrice&id='+arr_car_stock[1], function(data){
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
    }
    Summary();
}

function SaveNewCar(){
        if( $('#txt_name').val() == "" ){
            alert('กรุณาระบุ ผู้จอง !');
            return false;
        }else if($('#txt_witness').val() == ""){
			alert('กรุณาระบุ พยาน !');
            return false;
		}

		//วันที่ส่งมอบรถ
		
		if( $('#txt_date_car').val() == "" ){
                alert('กรุณาระบุวันที่รับรถ!');
                return false;
        }
		
		//รถใหม่
		if( $('input[id=radio_carnew_type]:checked').val() == "1" ){
			if($('#ddl_car_color').val() == "not"){
			alert('กรุณา  เลือกสีรถแท็กซี่!');
             return false;
			}
		}else
		//รถใช้แล้ว
		{
			if( $('#txt_car_used').val() == "" ){
                alert('กรุณาเลือกรูปแบบรถ !');
                return false;
            } 
		}
        
		if( $('input[id=radio_resv_type]:checked').val() == "1" ){ //เจาะจงรถ
		}
		else{
			{
				if( $('#txt_spec_car_new').val() == "" ){
					alert('กรุณาเลือกรถ !');
					return false;
				}
			}
		}

		
		if( $('input[id=radio_buy_type]:checked').val() == "1" ){ //หากซื้อผ่อนให้ตรวจสอบ จำนวนงวดผ่อน, ค่างวด
            if( $('#txt_buy_numdue').val() > 100 || $('#txt_buy_monnydue').val() < 100 ){
                alert('กรุณาตรวจสอบ\nจำนวนงวดผ่อน ต้องไม่เกิน 100งวด\nค่างวดอย่างน้อย 100บาท');
                return false;
            }
        }
		
		if( $('#cb_insure').val() == "not" ){
                alert('กรุณาเลือกประเภทประกันภัย!');
                return false;
        }
		
		if (!$('input[name=radio_commu]:checked').val() ){ 
			alert('กรุณาเลือกการติดตั้งเครื่องวิทยุสื่อสาร!');
            return false;
		}
		
        SaveNewCar2();
}

function SaveNewCar2(){
		var select_car_color ="";
		var select_car = "";
		var car_color_stock  = "";
		var arr_car_color_stock = "" ;
		
		// รถเก่าเป็น สีเดิม ถ้า รถใหม่เป็นสีที่เลือก
		if( $('input[id=radio_carnew_type]:checked').val() == "1" ){ //รถใหม่
				car_color_stock = $("#txt_spec_car_new").val(); //รูปแบบรถ
				arr_car_color_stock = car_color_stock.split("#"); 
				select_car = $('#txt_spec_car_new').val();
				if($('#ddl_car_color').val()  == "สีเดิม"){
					select_car_color = arr_car_color_stock[3];
				}else{
					select_car_color = $('#ddl_car_color').val();
				}
				
		}
		else{
				car_color_stock = $("#txt_car_used").val(); //รูปแบบรถ
				arr_car_color_stock = car_color_stock.split("#"); 
				select_car_color = arr_car_color_stock[3];
				
		}
		
		select_car =car_color_stock;
	
			
		$('body').append('<div id="divdialogconfirm"></div>');
		$("#divdialogconfirm").text('ต้องการบันทึกการจองใช่หรือไม่ ?');
		$("#divdialogconfirm").dialog({
			title: 'ยืนยัน',
			resizable: false,
			height:140,
			modal: true,
			buttons:{
				"ใช่": function(){
				
					//alert(select_car_color+'%'+select_car);
					
					$.post('reserv_car_new_api.php',{
						cmd: 'save',
						car_type: $('input[id=radio_carnew_type]:checked').val(), //รถใหม่หรือรถใช้แล้ว
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
						resv_car_plan: $("#resv_car_plan").val(), //รถใหม่ ไม่เจาะจงรถ
						radio_car_color: $('input[id=radio_car_color]:checked').val(),
						//car_color: $('#ddl_car_color').val(),
						car_color: select_car_color, //สีของรถแท็กซี่
						combo_car_stock: select_car,
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
						span_sum: $('#span_sum').text(),
						area_remark_new: $('#area_remark_new').val(),
						sale: $('#txt_sale').val(),
						witness: $('#txt_witness').val() //พยาน
					},
						
					
					function(data){
						if(data.success){
							$("#divdialogconfirm").remove();
							alert(data.message);
							location.reload();
						}else{
							alert(data.message);
						}
					},'json');
				},
				ไม่ใช่: function(){
					$( this ).dialog( "close" );
				}
			}
		});
		
       /* $.post('reserv_car_new_api.php',{
            cmd: 'save',
			car_type: $('input[id=radio_carnew_type]:checked').val(), //รถใหม่หรือรถใช้แล้ว
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
            resv_car_plan: $("#resv_car_plan").val(), //รถใหม่ ไม่เจาะจงรถ
            radio_car_color: $('input[id=radio_car_color]:checked').val(),
			//car_color: $('#ddl_car_color').val(),
			car_color: select_car_color, //สีของรถแท็กซี่
            combo_car_stock: $('#txt_spec_car_new').val(),// รถใหม่ เจาะจงรถ
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
            span_sum: $('#span_sum').text(),
            area_remark_new: $('#area_remark_new').val(),
			sale: $('#txt_sale').val(),
			witness: $('#txt_witness').val() //พยาน
        },
        function(data){
            if(data.success){
				alert(data.message);
                location.reload();
            }else{
                alert(data.message);
            }
        },'json');*/
}


/*function changeStock(){
        var combo_car_stock = $("#combo_car_stock").val();
        var arr_car_stock = combo_car_stock.split("#");

        if( arr_car_stock[2] == "ฟ้า" ){
            $('#cb_finance').val('CUS00001');
        }else if( arr_car_stock[2] == "เหลือง" ){
            $('#cb_finance').val('CUS00002');
        }else{
            
        }
}*/

function display_reserve(){
        var combo_car_stock = $("#txt_spec_car_new").val();
		//var combo_car_stock = $("#txt_car_used").val();
        var arr_car_stock = combo_car_stock.split("#");
		if(arr_car_stock[1] == "R"){
			show_car_reserve(arr_car_stock[0]);
		}
     
}
//========= เปิดหน้าจอสำหรับแสดงรายการรถที่ถูกจองแล้ว =========//
function show_car_reserve(car_id){
    $('body').append('<div id="dialog-form"></div>');
    /*$('#dialog-form').load('../report/report_reserve.php');*/
	$('#dialog-form').load('list_car_reserve.php?car_id='+car_id);
		$('#dialog-form').dialog({ 
			title: 'แสดงรายการรถที่ถูกจอง   '+car_id,
			resizable: false,
			modal: true,  
			width: 850,
			height:600,
		close: function(ev, ui){
				$('#dialog-form').remove();
                }
        });
}
function ClosePrtDia(){
    $('#div_print').remove();
    location.reload();
}

function change_interest_rate(){
    $.get('reserv_car_new_api.php?cmd=interest_rate&car_price='+$("#txt_buy_price2").val()+'&down_price='+$("#txt_buy_down").val()+'&installment='+$("#txt_buy_monnydue").val()+'&num_installment='+$("#txt_buy_numdue").val(), function(data){
        $('#span_interest_rate').text( data );
    });
}
</script>
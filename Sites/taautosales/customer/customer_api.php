<?php 
include_once("../include/config.php");
include_once("../include/function.php");
//$nowdate = date('Y-m-d');

?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <title></title>
    <LINK href="../images/styles.css" type=text/css rel=stylesheet>

    <link type="text/css" href="../images/jqueryui/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="../images/jqueryui/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="../images/jqueryui/js/jquery-ui-1.8.16.custom.min.js"></script>
	
	
</head>
<body>

<div  id ="div_customer" style="border: 1px dashed #D0D0D0; margin-top:10px; padding:0px; background-color:#F0F0F0">
	<table cellpadding="5" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="60%" valign="top">

	<table cellpadding="3" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="100">ประเภทลูกค้า</td>
		<td>
			<input type="radio" name="rdo_cus_type" id="rdo_cus_type" value="1" checked> <label>บุคคลธรรมดา</label>
			<input type="radio" name="rdo_cus_type" id="rdo_cus_type" value="2"><label>นิติบุคคล</label>
			<span id="span_branch" style="display:none">
				<label>สาขาที่</label>&nbsp;&nbsp;<input type="text" name="txt_branch_id" id="txt_branch_id" size="5" onkeypress="check_num(event);"> 
			</span>
		</td>
	</tr>
	<tr>
		<td width="100">คำนำหน้าชื่อ</td><td><input type="text" name="txt_pre_name" id="cus_txt_pre_name" size="10"></td>
	</tr>
	<tr>
		<td>ชื่อ</td><td><input type="text" name="txt_firstname" id="cus_txt_firstname"> สกุล <input type="text" name="txt_lastname" id="cus_txt_lastname"></td>
	</tr>
	<tr>
		<td>ที่อยู่ตามบัตรประชาชน</td><td colspan="3"><textarea name="txt_address" id="cus_txt_address" rows="3" cols="1" style="width:330px"></textarea></td>
	</tr>
	<tr>
		<td>รหัสไปรษณีย์</td><td><input type="text" name="txt_post" id="cus_txt_post" size="10" onkeypress="check_num(event);"></td>
	</tr>
	<tr>
		<td>ชื่อ-สกุล<br>ผู้จดทะเบียน</td><td><input type="text" name="txt_name_reg" id="cus_txt_name_reg" size="40"></td>
	</tr>
	<tr>
		<td><label>ที่อยู่ที่จดทะเบียน</label>
		</td>
			<td>
				<!--<input type="radio" name="cus_rdo_reg_address" id="cus_rdo_reg_address" value="1" checked> -->
				<input type="radio" name="cus_rdo_reg_address" id="cus_rdo_reg_address" value="1" checked> ตามที่อยู่ตามบัตรประชาชน
				<input type="radio" name="cus_rdo_reg_address" id="cus_rdo_reg_address" value="2">กรอกใหม่  
			</td>
	</tr>
	<tr>
		<td></td>
							<td colspan="2">
								<div id="div_cus_reg_address" style="display:none">
									<table>
										<tr>
											<td align="right"><label>ที่อยู่</label></td>
											<td><textarea name="txt_address_reg" id="cus_txt_address_reg" rows="3" cols="1" style="width:250px; "></textarea></td>
										</tr>
										<tr>
											<td><label>รหัสไปรษณีย์</label></td>
											<td><input type="text" name="txt_post_reg" id="cus_txt_post_reg" size="15" onkeypress="check_num(event);"></td>
										</tr>
									</table>
									
									 
								</div>
							</td>
						</tr>
	<!--<tr>
		<td>ที่ติดต่อ</td><td>เหมือนด้านบน <input type="radio" name="chkContact" id="chkContact" value="1" checked> กรอกใหม่ <input type="radio" name="chkContact" id="chkContact" value="2"></td>
	</tr>-->
	<tr>
							<td>ที่ติดต่อ</td>
							<td>
								<input type="radio" name="cus_chkContact" id="cus_chkContact" value="1" checked>ตามบัตรประชาชน 
								<input type="radio" name="cus_chkContact" id="cus_chkContact" value="3" >ตามที่อยู่ที่จดทะเบียน
								<input type="radio" name="cus_chkContact" id="cus_chkContact" value="2">กรอกใหม่ 
							</td>
						</tr>
						<tr>
							<td></td>
							<td colspan="2">
							<div style="display:none" id="cus_divcontact">
								<table>
									<tr><td align="center">ที่อยู่</td>
									<td><textarea name="txt_contact" id="cus_txt_contact" rows="3" cols="1" style="width:250px; "></textarea></td></tr>
									<tr><td>รหัสไปรษณีย์</td>
									<td><input type="text" name="txt_post_contract" id="cus_txt_post_contract" size="15" onkeypress="check_num(event);"></td></tr>
								</table>
							</div>
							</td>
						</tr>
	<!--<tr>
		<td></td><td><div style="display:none" id="divcontact"><textarea name="txt_contact" id="txt_contact" rows="3" cols="40"></textarea></div></td>
	</tr>-->
	<tr>
		<td>โทรศัพท์</td><td><input type="text" name="txt_phone" id="cus_txt_phone" size="30"></td>
		<td></td><td></td>
	</tr>
	</table>
		
	</td>
	<td width="40%" valign="top">
		
		<table cellpadding="3" cellspacing="0" border="0" width="100%">
			<tr>
				<td width="100">สัญชาติ</td><td><input type="text" name="txt_reg" id="cus_txt_reg" size="10" value="ไทย"></td>
			</tr>
			<tr>
				<td>วันเกิด</td><td><input type="text" name="txt_barthdate" id="cus_txt_barthdate" size="10" value=""></td>
			</tr>
			<tr>
				<td>บัตรที่ใช้แสดงตัว</td>
				<td>
			<select name="combo_cardtype" id="cus_combo_cardtype">
			  <option value="บัตรประชาชน">บัตรประชาชน</option>
			  <option value="บัตรข้าราชการ">บัตรข้าราชการ</option>
			  <option value="บัตรผู้เสียภาษีอากร">บัตรประจำตัวผู้เสียภาษีอากร</option>
			  <option value="ใบขับขี่">ใบขับขี่</option>
			  <option value="อื่นๆ">อื่นๆ</option>
			<!--</select> <span id="span_card" style="display:none"><input type="text" name="txt_cardother" id="txt_cardother" size="10"></span>-->
				</td>
			</tr>
			<tr>
				<td>เลขที่บัตร</td><td><input type="text" name="txt_cardno" id="cus_txt_cardno" size="30"></td>
			</tr>
			<tr>
				<td>วันที่ออกบัตร</td><td><input type="text" name="txt_carddate" id="cus_txt_carddate" size="10" value=""></td>
			</tr>
			<tr>
				<td>สถานที่ออกบัตร</td><td><input type="text" name="txt_cardby" id="cus_txt_cardby" size="30"></td>
			</tr>
			<tr>
				<td>อาชีพ</td><td><input type="text" name="txt_job" id="cus_txt_job" size="30"></td>
			</tr>
		</table>
	</td>
	</tr>
	</table>
</div>

<div style="text-align:right; margin-top:10px">
	<input type="button" name="btn_save" id="btn_save" value="บันทึก">
</div>

</body>
</html>

<script>
function check_num(e)
{ 
    var key;
    if(window.event)
	{
        key = window.event.keyCode; // IE
		if(key <= 57 && key != 33 && key != 34 && key != 35 && key != 36 && key != 37 && key != 38 && key != 39 && key != 40 && key != 41 && key != 42
			&& key != 43 && key != 44 && key != 45 && key != 47)
		{
			
		}
		else
		{
			window.event.returnValue = false;
		}
    }
	else
	{
        key = e.which; // Firefox       
		if(key <= 57 && key != 33 && key != 34 && key != 35 && key != 36 && key != 37 && key != 38 && key != 39 && key != 40 && key != 41 && key != 42
			&& key != 43 && key != 44 && key != 45 && key != 47)
		{
			
		}
		else
		{
			key = e.preventDefault();
		}
	}
};


	$(document).ready(function(){
	$("#cus_txt_carddate, #cus_txt_barthdate").datepicker({
		showOn: 'button',
		buttonImage: '../images/calendar.gif',
		buttonImageOnly: true,
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd'
	});

	
	$("input[name='rdo_cus_type']").change(function(){
		if( $('input[id=rdo_cus_type]:checked').val() == "2" ){
			$('#span_branch').show('fast');
		}else{
			$('#span_branch').hide('fast');
		}
	});
	$("input[name='cus_chkContact']").change(function(){
		if( $('input[id=cus_chkContact]:checked').val() == "2" ){
			$('#cus_divcontact').show('fast');
		}else{
			$('#cus_divcontact').hide('fast');
		}
	});

	$("input[name='cus_rdo_reg_address']").change(function(){
		if( $('input[id=cus_rdo_reg_address]:checked').val() == "2" ){
			$('#div_cus_reg_address').show('fast');
		}else{
			$('#div_cus_reg_address').hide('fast');
		}
	});
});

$('#btn_save').click(function(){

	if( $('input[id=rdo_cus_type]:checked').val() == "2" ){
			if($('#txt_branch_id').val()  == ""){
				alert("กรุณาระบุ  สาขาที่");
				return false;
			}else{
				if(isNaN($('#txt_branch_id').val())){
					alert(" สาขาที่  ต้องเป็นตัวเลขเท่านั้น");
					$('#txt_branch_id').val('');
					$('#txt_branch_id').focus();
					return false;
				}
			}
	}else if($('#cus_txt_pre_name').val() == ""){
		alert('กรุณาระบุ คำนำหน้าชื่อ');
		return false;
	}else if($('#cus_txt_firstname').val() == ""){
		alert('กรุณาระบุ  ชื่อ');
		return false;
	}else if($('#cus_txt_lastname').val() == ""){
		alert('กรุณาระบุ  นามสกุล');
		return false;
	}else if($('#cus_txt_address').val() == ""){
		alert('กรุณาระบุ  ที่อยู่');
		return false;
	}else if($('#cus_txt_post').val()== ""){
		alert('กรุณาระบุ รหัสไปรษณีย์ ');
		return false;
	}/*else if(isNaN($('#cus_txt_post').val()) == true){
		alert('รหัสไปรษณีย์ต้องเป็นตัวเลขเท่านั้น');
		return false;
	}*/else if($("#cus_txt_barthdate").val() == ""){
		alert('กรุณาระบุข้อมูลวันเกิดด้วย');
		return false;
	}/*else if( validate_date($("#cus_txt_barthdate").val() ) == false  ){
		alert('รูปแบบของวันเกิดไม่ถูกต้อง ต้องกรอกตามรูปแบบ (yyyy/mm/dd) ');
		return false;
	}*/else if($("#cus_txt_carddate").val() == ""){
		alert('กรุณาระบุข้อมูลวันที่ออกบัตรด้วย');
		return false;
	}/*else if(validate_date($("#cus_txt_carddate").val()) == false ){
		alert('รูปแบบของวันที่ออกบัตรไม่ถูกต้อง  ต้องกรอกตามรูปแบบ (yyyy/mm/dd) ');
		return false;
	}*/else if($('#cus_txt_cardno').val()== ""){
		alert('กรุณาระบุ เลขที่บัตร');
		return false;
	}else if($('#cus_txt_name_reg').val()  == ""){
		alert('กรุณาระบุ  ชื่อผู้จดทะเบียน');
		return false;
	}/*else if( $('input[id=cus_chkContact]:checked').val() == "2" ){
		if($('#cus_txt_post_contract').val()== ""){
			alert('กรุณาระบุรหัสไปรษณีที่ติดต่อ');
			return false;
		}else{
			if(isNaN($('#cus_txt_post_contract').val()) == true){
				alert('รหัสไปรษณีที่ติดต่อต้องเป็นตัวเลขเท่านั้น');
				return false;
			}
		}
		if($('#cus_txt_contact').val() ==""){
			alert('กรุณาระบุ  ที่อยู่ที่ติดต่อ');
			return false;
		}
	}else if( $('input[id=cus_rdo_reg_address]:checked').val() == "2" ){
		if($('#cus_txt_post_reg').val()== ""){
				alert('กรุณาระบุรหัสไปรษณีที่จดทะเบียน');
				return false;
			}else{
				if(isNaN($('#cus_txt_post_reg').val()) == true){
					alert('รหัสไปรษณีที่จดทะเบียนต้องเป็นตัวเลขเท่านั้น');
					return false;
				}
			}
		if($('#cus_txt_address_reg').val() ==""){
			alert('กรุณาระบุ  ที่อยู่ที่จดทะเบียน');
			return false;
		}
	}*/
	
	

    $.post('../customer/save_cus_api.php',{
        cmd_save_cus: 'save_customer',
		cus_type: $('input[id=rdo_cus_type]:checked').val(),
        txt_pre_name: $('#cus_txt_pre_name').val(),
        txt_firstname: $('#cus_txt_firstname').val(),
        txt_lastname: $('#cus_txt_lastname').val(),
        txt_address: $('#cus_txt_address').val(), //ที่อยู่ตามบัตรประชาชน
        txt_post: $('#cus_txt_post').val(), //รหัสไปรษณีย์ตามบัตรประชาชน
        chkContact: $('input[id=cus_chkContact]:checked').val(),
		rdo_reg_address: $('input[id=cus_rdo_reg_address]:checked').val(),
        txt_contact: $('#cus_txt_contact').val(), //ที่อยู่ที่ติดต่อได้
		txt_post_contract: $('#cus_txt_post_contract').val(),//รหัสไปรษณีย์ที่ติดต่อได้
        txt_phone: $('#cus_txt_phone').val(),
        txt_reg: $('#cus_txt_reg').val(),
        txt_barthdate: $('#cus_txt_barthdate').val(),
        combo_cardtype: $("#cus_combo_cardtype").val(),
        txt_cardother: $('#cus_txt_cardother').val(),
        txt_cardno: $('#cus_txt_cardno').val(),
        txt_carddate: $('#cus_txt_carddate').val(),
        txt_cardby: $('#cus_txt_cardby').val(),
        txt_job: $('#cus_txt_job').val(),
		txt_name_reg: $('#cus_txt_name_reg').val(),
		txt_address_reg: $('#cus_txt_address_reg').val(),//ที่อยู่ที่จดทะเบียน
		txt_post_reg: $('#cus_txt_post_reg').val()//รหัสไปรษณีย์ที่จดทะเบียน
		
    },
    function(data){
        if(data.success){
            alert(data.message);
			location.reload();
        }else{
            alert(data.message);
        }
    },'json');
});

function clear_all_data(){
		txt_pre_name: $('#cus_txt_pre_name').val('');
        txt_firstname: $('#cus_txt_firstname').val('');
        txt_lastname: $('#cus_txt_lastname').val('');
        txt_address: $('#cus_txt_address').val(''); //ที่อยู่ตามบัตรประชาชน
        txt_post: $('#cus_txt_post').val('');
}

function close_modal(){
    $('#div_customer').remove();
    location.reload();
}

</script>
	


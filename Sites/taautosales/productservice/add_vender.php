<?php
include_once("../include/config.php");
include_once("../include/function.php");
/*
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}
*/
$page_title = "Vender";
?>

   
   
<script type="text/javascript">
$(document).ready(function(){
	
	 $("#txt_barthdate, #txt_carddate").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
	
    $("#txt_name").autocomplete({
        source: "vender_api.php?cmd=autocomplete",
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
	
	
	  $('#btn_save').click(function(){
	
	   $.post('vender_api.php',{
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
            txt_job: $('#txt_job').val()
           
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
	
	
	
});	
</script>
</head>


<?php
//include_once("../include/header_popup.php");
?>

<div style="text-align:left;">&nbsp;&nbsp;</div>

<div>
  <div style="float:left"><b>ADD Vender </b></div><div style="float:right; "></div><br />
  <div>ตรวจสอบข้อมูลที่มี
    <input type="text" name="txt_name" id="txt_name" size="50">
    <input type="hidden" id="vender_id" name="vender_id" />

  </div>
  <div id="divnewcus" style="margin-top:10px;  display:none; ">
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
<div><input type="button" id="btn_save" name="btn_save" value="SAVE"  /></div>
  </div>
</div>
  

</div>
 </div>





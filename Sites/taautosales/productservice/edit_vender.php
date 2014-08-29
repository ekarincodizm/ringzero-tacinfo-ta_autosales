<?php
include_once("../include/config.php");
include_once("../include/function.php");
//echo $_REQUEST["cusid"];
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
   
   
   $("#ts_barthdate, #ts_carddate").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
	
	
	$("input[name='ts_chkContact']").change(function(){
        if( $('input[id=ts_chkContact]:checked').val() == "2" ){
            $('#ts_divcontact').show('fast');
        }else{
            $('#ts_divcontact').hide('fast');
        }
    });
	
	$("#ts_combo_cardtype").change(function(){
        if( $("#ts_combo_cardtype").val() == "อื่นๆ" ){
            $('#ts_span_card').show('fast');
        }else{
            $('#ts_span_card').hide('fast');
        }
    });
	
	
	  $('#btn_update').click(function(){
	
	   $.post('vender_api.php',{
            cmd: 'update',
			m_cusid: $('#m_cusid').val(),
            ts_name: $('#ts_name').val(),
            ts_pre_name: $('#ts_pre_name').val(),
            ts_firstname: $('#ts_firstname').val(),
            ts_lastname: $('#ts_lastname').val(),
            ts_address: $('#ts_address').val(),
            ts_post: $('#ts_post').val(),
            ts_chkContact: $('input[id=ts_chkContact]:checked').val(),
            ts_contact: $('#ts_contact').val(),
            ts_phone: $('#ts_phone').val(),
            ts_reg: $('#ts_reg').val(),
            ts_barthdate: $('#ts_barthdate').val(),
            combo_cardtype: $("#combo_cardtype").val(),
            ts_cardother: $('#ts_cardother').val(),
            ts_cardno: $('#ts_cardno').val(),
            ts_carddate: $('#ts_carddate').val(),
            ts_cardby: $('#ts_cardby').val(),
            ts_job: $('#ts_job').val()
           
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
<body>

   <?php
    $qry_cus=pg_query("select * from  \"Customers\" where cus_id='$_REQUEST[cusid]' ");
    $res_cus=pg_fetch_array($qry_cus);
   
   ?>

        <table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
  <tr>
    <td width="60%" valign="top">

<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">คำนำหน้าชื่อ</td><td><input type="text" name="ts_pre_name" id="ts_pre_name" size="10" value="<?php echo  trim($res_cus["pre_name"]); ?>"></td>
</tr>
<tr>
    <td>ชื่อ</td><td><input type="text" name="ts_firstname" id="ts_firstname" value="<?php echo  trim($res_cus["cus_name"]); ?>"> สกุล <input type="text" name="ts_lastname" id="ts_lastname" value="<?php echo  trim($res_cus["surname"]); ?>"></td>
</tr>
<tr>
    <td>ที่อยู่</td><td colspan="3"><textarea name="ts_address" id="ts_address" rows="1" cols="1" style="width:330px; height:70px"><?php echo  $res_cus["address"]; ?></textarea></td>
</tr>
<tr>
    <td>รหัสไปรษณีย์</td><td><input type="text" name="ts_post" id="ts_post" size="10" value="<?php echo  $res_cus["add_post"]; ?>"></td>
</tr>
<tr>
    <td>ที่ติดต่อ</td>
	<td>
		<input type="radio" name="ts_chkContact" id="ts_chkContact" value="1" checked> เหมือนด้านบน 
		<input type="radio" name="ts_chkContact" id="ts_chkContact" value="2" >กรอกใหม่ 
	</td>
</tr>
<tr>
    <td></td>
	<td>
		<div style="display:none" id="ts_divcontact">
			<textarea name="ts_contact" id="ts_contact" rows="3" cols="40"><?php echo  $res_cus["contract_add"]; ?>
			</textarea>
		</div>
	</td>
</tr>
<tr>
    <td>โทรศัพท์</td><td><input type="text" name="ts_phone" id="ts_phone" size="30" value="<?php echo  $res_cus["telephone"]; ?>"></td>
    <td></td><td></td>
</tr>
</table>
    
    </td>
    <td width="40%" valign="top">
    
<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">สัญชาติ</td><td><input type="text" name="ts_reg" id="ts_reg" size="10" value="<?php echo  $res_cus["nationality"]; ?>"></td>
</tr>
<tr>
    <td>วันเกิด</td><td><input type="text" name="ts_barthdate" id="ts_barthdate" size="10" value="<?php echo $res_cus["birth_date"]; ?>"></td>
</tr>
<tr>
    <td>บัตรที่ใช้แสดงตัว</td>
    <td>
<select name="ts_combo_cardtype" id="ts_combo_cardtype">
  <option value="บัตรประชาชน">บัตรประชาชน</option>
  <option value="บัตรข้าราชการ">บัตรข้าราชการ</option>
  <option value="ใบขับขี่">ใบขับขี่</option>
  <option value="อื่นๆ">อื่นๆ</option>
</select> <span id="ts_span_card" style="display:none"><input type="text" name="ts_cardother" id="ts_cardother" size="10"></span>
    </td>
</tr>
<tr>
    <td>เลขที่บัตร</td><td><input type="text" name="ts_cardno" id="ts_cardno" size="30" value="<?php echo $res_cus["card_id"]; ?>"></td>
</tr>
<tr>
    <td>วันที่ออกบัตร</td><td><input type="text" name="ts_carddate" id="ts_carddate" size="10" value="<?php echo $res_cus["card_do_date"]; ?>"></td>
</tr>
<tr>
    <td>สถานที่ออกบัตร</td><td><input type="text" name="ts_cardby" id="ts_cardby" size="30" value="<?php echo $res_cus["card_do_by"]; ?>"></td>
</tr>
<tr>
    <td>อาชีพ</td><td><input type="text" name="ts_job" id="ts_job" size="30" value="<?php echo $res_cus["job"]; ?>"></td>
</tr>

</table>

    </td>
</tr>
  <input type="hidden" name="m_cusid" id="m_cusid" value="<?php echo $_REQUEST["cusid"]; ?>" >
</table>
<div><input type="button" id="btn_update" name="btn_update" value="SAVE"  /></div>

<?php
include_once("../include/config.php");
include_once("../include/function.php");

$auto_id = pg_escape_string($_GET['auto_id']);
$popupType = pg_escape_string($_GET['popupType']); // ถ้าเป็น "viewOnly" คือ ดูอย่างเดียว

// หาข้อมูลการแก้ไขข้อมูลลูกค้าครั้งนั้นๆ
$qry_name = pg_query("select * from \"Customers_temp\" WHERE \"auto_id\" = '$auto_id' ");
if($res_cus = pg_fetch_array($qry_name))
{
	$cus_id = trim($res_cus['cus_id']);
	$pre_name = trim($res_cus['pre_name']);
	$cus_name  = trim($res_cus['cus_name']);
	$surname = trim($res_cus['surname']);
	$address  = trim($res_cus['address']);
	$add_post = trim($res_cus['add_post']);
	$nationality = trim($res_cus['nationality']);
	$birth_date = trim($res_cus['birth_date']);
	$card_type = trim($res_cus['card_type']);
	$card_id = trim($res_cus['card_id']);
	$card_do_date = trim($res_cus['card_do_date']);
	$card_do_by = trim($res_cus['card_do_by']);
	$job = trim($res_cus['job']);
	$contract_add = trim($res_cus['contract_add']);
	$telephone = trim($res_cus['telephone']);
	$change_name = trim($res_cus['change_name']);
	$reg_customer = trim($res_cus['reg_customer']);
	$reg_address = trim($res_cus['reg_address']);
	$reg_post = trim($res_cus['reg_post']);
	$contract_post = trim($res_cus['contract_post']);
	$branch_id = trim($res_cus['branch_id']);
	$cus_type = trim($res_cus['cus_type']);
	$remark = trim($res_cus['remark']);
}
	
// หารหัสการทำรายการก่อนหน้านี้ ที่ได้รับการอนุมัติ
$qry_auto_id_old = pg_query("select max(\"auto_id\") from \"Customers_temp\" WHERE \"cus_id\" = '$cus_id' and \"auto_id\" < '$auto_id' and \"status_appv\" = '1' ");
$auto_id_old = pg_result($qry_auto_id_old,0); // รหัสการแก้ไขก่อนหน้านี้ || ถ้ามีค่า คือเคยแก้ไขมาก่อนหน้านี้ ที่ได้รับการอนุมัติ || ถ้าไม่มีค่า คือไม่เคยแก้ไขมาก่อน ที่ได้รับการอนุมัติ

// หาข้อมูลก่อนหน้านี้
if($auto_id_old != "")
{ // เคยแก้ไขมาก่อนหน้านี้ ที่ได้รับการอนุมัติ
	// หาข้อมูลการแก้ไขข้อมูลลูกค้าครั้งก่อนหน้า ที่ได้รับการอนุมัติ
	$qry_name_old = pg_query("select * from \"Customers_temp\" WHERE \"auto_id\" = '$auto_id_old' ");
}
else
{ // ไม่เคยแก้ไขมาก่อน ที่ได้รับการอนุมัติ
	// หาข้อมูลหลักของลูกค้า
	$qry_name_old = pg_query("select * from \"Customers\" WHERE \"cus_id\" = '$cus_id' ");
}

if($res_cus_old = pg_fetch_array($qry_name_old))
{
	$cus_id_old = trim($res_cus_old['cus_id']);
	$pre_name_old = trim($res_cus_old['pre_name']);
	$cus_name_old = trim($res_cus_old['cus_name']);
	$surname_old = trim($res_cus_old['surname']);
	$address_old = trim($res_cus_old['address']);
	$add_post_old = trim($res_cus_old['add_post']);
	$nationality_old = trim($res_cus_old['nationality']);
	$birth_date_old = trim($res_cus_old['birth_date']);
	$card_type_old = trim($res_cus_old['card_type']);
	$card_id_old = trim($res_cus_old['card_id']);
	$card_do_date_old = trim($res_cus_old['card_do_date']);
	$card_do_by_old = trim($res_cus_old['card_do_by']);
	$job_old = trim($res_cus_old['job']);
	$contract_add_old = trim($res_cus_old['contract_add']);
	$telephone_old = trim($res_cus_old['telephone']);
	$change_name_old = trim($res_cus_old['change_name']);
	$reg_customer_old = trim($res_cus_old['reg_customer']);
	$reg_address_old = trim($res_cus_old['reg_address']);
	$reg_post_old = trim($res_cus_old['reg_post']);
	$contract_post_old = trim($res_cus_old['contract_post']);
	$branch_id_old = trim($res_cus_old['branch_id']);
	$cus_type_old = trim($res_cus_old['cus_type']);
	$remark_old = trim($res_cus_old['remark']);
}

//----- กำหนดการแสดงการเปลี่ยนแปลง
	$color_old = "#FF8888"; // สีที่ใช้บอกว่ามีการเปลี่ยนแปลง
	if($pre_name != $pre_name_old){$pre_name_style = "style=\"background-color:$color_old;\" title=\"$pre_name_old\"";}
	if($cus_name != $cus_name_old){$cus_name_style = "style=\"background-color:$color_old;\" title=\"$cus_name_old\"";}
	if($surname != $surname_old){$surname_style = "style=\"background-color:$color_old;\" title=\"$surname_old\"";}
	if($address != $address_old){$address_style = "style=\"background-color:$color_old; width:330px;\" title=\"$address_old\"";}else{$address_style = "style=\"width:330px;\"";}
	if($add_post != $add_post_old){$add_post_style = "style=\"background-color:$color_old;\" title=\"$add_post_old\"";}
	if($nationality != $nationality_old){$nationality_style = "style=\"background-color:$color_old;\" title=\"$nationality_old\"";}
	if($birth_date != $birth_date_old){$birth_date_style = "style=\"background-color:$color_old;\" title=\"$birth_date_old\"";}
	if($card_type != $card_type_old){$card_type_style = "style=\"background-color:$color_old;\" title=\"$card_type_old\"";}
	if($card_id != $card_id_old){$card_id_style = "style=\"background-color:$color_old;\" title=\"$card_id_old\"";}
	if($card_do_date != $card_do_date_old){$card_do_date_style = "style=\"background-color:$color_old;\" title=\"$card_do_date_old\"";}
	if($card_do_by != $card_do_by_old){$card_do_by_style = "style=\"background-color:$color_old;\" title=\"$card_do_by_old\"";}
	if($job != $job_old){$job_style = "style=\"background-color:$color_old;\" title=\"$job_old\"";}
	if($contract_add != $contract_add_old){$contract_add_style = "style=\"background-color:$color_old;\" title=\"$contract_add_old\"";}
	if($telephone != $telephone_old){$telephone_style = "style=\"background-color:$color_old;\" title=\"$telephone_old\"";}
	if($change_name != $change_name_old){$change_name_style = "style=\"background-color:$color_old;\" title=\"$change_name_old\"";}
	if($nationality != $nationality_old){$nationality_style = "style=\"background-color:$color_old;\" title=\"$nationality_old\"";}
	if($reg_customer != $reg_customer_old){$reg_customer_style = "style=\"background-color:$color_old;\" title=\"$reg_customer_old\"";}
	if($reg_address != $reg_address_old){$reg_address_style = "style=\"background-color:$color_old; width:250px;\" title=\"$reg_address_old\"";}else{$reg_address_style = "style=\"width:250px;\"";}
	if($reg_post != $reg_post_old){$reg_post_style = "style=\"background-color:$color_old;\" title=\"$reg_post_old\"";}
	if($contract_post != $contract_post_old){$contract_post_style = "style=\"background-color:$color_old;\" title=\"$contract_post_old\"";}
	if($branch_id != $branch_id_old){$branch_id_style = "style=\"background-color:$color_old;\" title=\"$branch_id_old\"";}
	if($remark != $remark_old){$remark_style = "style=\"background-color:$color_old;\" title=\"$remark_old\"";}

	// ประเภทลูกค้า
	if($cus_type != $cus_type_old)
	{
		if($cus_type_old == "1"){$cus_type_style = "style=\"background-color:$color_old;\" title=\"บุคคลธรรมดา\"";}
		elseif($cus_type_old == "1"){$cus_type_style = "style=\"background-color:$color_old;\" title=\"นิติบุคคล\"";}
	}
	
	// ที่อยู่ที่จดทะเบียน
	if($reg_address != $reg_address_old)
	{
		// ตัวเลือกครั้งนี้
		if($address == $reg_address){$chk = "1";}
		elseif($address != $reg_address){$chk = "2";}
		else{$chk = "";}
		
		if($address_old == $reg_address_old && $chk != "1"){$cus_rdo_reg_address_style = "style=\"background-color:$color_old;\" title=\"ตามที่อยู่ตามบัตรประชาชน\"";}
		elseif($address_old != $reg_address_old && $chk != "2"){$cus_rdo_reg_address_style = "style=\"background-color:$color_old;\" title=\"กรอกใหม่\"";}
	}
	
	// ที่ติดต่อ
	if($contract_add != $contract_add_old)
	{
		// ตัวเลือกครั้งนี้
		if($address == $contract_add){$chk = "1";}
		elseif($contract_add == $reg_address){$chk = "2";}
		elseif($address != $contract_add && $contract_add != $reg_address){$chk = "3";}
		else{$chk = "";}
		
		if($address_old == $contract_add_old && $chk != "1"){$cus_chkContact_style = "style=\"background-color:$color_old;\" title=\"ตามบัตรประชาชน\"";}
		elseif($contract_add_old == $reg_address_old && $chk != "2"){$cus_chkContact_style = "style=\"background-color:$color_old;\" title=\"ตามที่อยู่ที่จดทะเบียน\"";}
		elseif($address_old != $contract_add_old && $contract_add_old != $reg_address_old && $chk != "3"){$cus_chkContact_style = "style=\"background-color:$color_old;\" title=\"กรอกใหม่\"";}
	}

//----- จบการกำหนดการแสดงการเปลี่ยนแปลง

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
	<div style="border: 1px dashed #D0D0D0; margin-top:10px; padding:0px; background-color:#F0F0F0">
 <table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
  <tr>
    <td width="60%" valign="top">

<table cellpadding="3" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="100" <?php echo $cus_type_style; ?>>ประเภทลูกค้า</td>
		<td>
			<input type="radio" name="rdo_cus_type" id="rdo_cus_type" value="1" <?php if($cus_type == "1"){echo "checked";}?> disabled > <label>บุคคลธรรมดา</label>
			<input type="radio" name="rdo_cus_type" id="rdo_cus_type" value="2" <?php if($cus_type == "2"){echo "checked";}?> disabled ><label>นิติบุคคล</label>
			<span id="span_branch" style="display:none">
				<label>สาขาที่</label>&nbsp;&nbsp;<input type="text" name="txt_branch_id" id="txt_branch_id" size="5" value="<?php echo $branch_id; ?>"> 
			</span>
		</td>
	</tr>
	<tr>
		<td width="100">คำนำหน้าชื่อ</td><td><input type="text" name="txt_pre_name" id="cus_txt_pre_name" size="10" value="<?php echo $pre_name; ?>" <?php echo $pre_name_style; ?> readonly ></td>
	</tr>
	<tr>
		<td>ชื่อ</td><td><input type="text" name="txt_firstname" id="cus_txt_firstname"  value="<?php echo $cus_name; ?>" <?php echo $cus_name_style; ?> readonly> สกุล <input type="text" name="txt_lastname" id="cus_txt_lastname" value="<?php echo $surname; ?>" <?php echo $surname_style; ?> readonly></td>
	</tr>
	<tr>
		<td>ที่อยู่ตามบัตรประชาชน</td><td colspan="3"><textarea name="txt_address" id="cus_txt_address" rows="3" cols="1" <?php echo $address_style; ?> readonly ><?php echo $address; ?></textarea></td>
	</tr>
	<tr>
		<td>รหัสไปรษณีย์</td><td><input type="text" name="txt_post" id="cus_txt_post" size="10" value="<?php echo $add_post; ?>" <?php echo $add_post_style; ?> readonly></td>
	</tr>
	<tr>
		<td>ชื่อ-สกุล<br>ผู้จดทะเบียน</td><td><input type="text" name="txt_name_reg" id="cus_txt_name_reg" size="40" value="<?php echo $reg_customer; ?>" <?php echo $reg_customer_style; ?> readonly></td>
	</tr>
	<tr>
		<td <?php echo $cus_rdo_reg_address_style; ?>><label>ที่อยู่ที่จดทะเบียน </label>
		</td>
			<td>
				<!--<input type="radio" name="cus_rdo_reg_address" id="cus_rdo_reg_address" value="1" checked> -->
				<input type="radio" name="cus_rdo_reg_address" id="cus_rdo_reg_address" value="1" <?php if($address == $reg_address){ echo "checked";}?> disabled> ตามที่อยู่ตามบัตรประชาชน
				<input type="radio" name="cus_rdo_reg_address" id="cus_rdo_reg_address" value="2" <?php if($address != $reg_address){ echo "checked";}?> disabled >กรอกใหม่  
			</td>
	</tr>
	<tr>
		<td></td>
							<td colspan="2">
								<div id="div_cus_reg_address" >
									<table>
										<tr>
											<td align="right"><label>ที่อยู่</label></td>
											<td><textarea name="txt_address_reg" id="cus_txt_address_reg" rows="3" cols="1" <?php echo $reg_address_style; ?> readonly ><?php echo $reg_address; ?></textarea></td>
										</tr>
										<tr>
											<td><label>รหัสไปรษณีย์</label></td>
											<td><input type="text" name="txt_post_reg" id="cus_txt_post_reg" size="15" value="<?php echo $reg_post; ?>" <?php echo $reg_post_style; ?> readonly ></td>
										</tr>
									</table>
									
									 
								</div>
							</td>
						</tr>
	<!--<tr>
		<td>ที่ติดต่อ</td><td>เหมือนด้านบน <input type="radio" name="chkContact" id="chkContact" value="1" checked> กรอกใหม่ <input type="radio" name="chkContact" id="chkContact" value="2"></td>
	</tr>-->
	<tr>
							<td <?php echo $cus_chkContact_style; ?>>ที่ติดต่อ</td>
							<td>
								<input type="radio" name="cus_chkContact" id="cus_chkContact" value="1" <?php if($address == $contract_add){ echo "checked";}?> disabled>ตามบัตรประชาชน 
								<input type="radio" name="cus_chkContact" id="cus_chkContact" value="3" <?php if($contract_add == $reg_address){ echo "checked";}?> disabled>ตามที่อยู่ที่จดทะเบียน
								<input type="radio" name="cus_chkContact" id="cus_chkContact" value="2"  <?php if($address != $contract_add and $contract_add != $reg_address){ echo "checked";}?> disabled>กรอกใหม่ 
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
							<div  id="cus_divcontact">
								<table>
									<tr><td align="center">ที่อยู่</td><td><textarea name="txt_contact" id="cus_txt_contact" rows="3" cols="40" <?php echo $contract_add_style; ?> readonly><?php echo $contract_add; ?></textarea></td></tr>
									<tr><td>รหัสไปรษณีย์</td><td><input type="text" name="txt_post_contract" id="cus_txt_post_contract" size="10" value="<?php echo $contract_post; ?>" <?php echo $contract_post_style; ?> readonly ></td></tr>
								</table>
							</div>
							</td>
						</tr>
	<!--<tr>
		<td></td><td><div style="display:none" id="divcontact"><textarea name="txt_contact" id="txt_contact" rows="3" cols="40"></textarea></div></td>
	</tr>-->
	<tr>
		<td>โทรศัพท์</td><td><input type="text" name="txt_phone" id="cus_txt_phone" size="30" value="<?php echo $telephone; ?>" <?php echo $telephone_style; ?> readonly ></td>
		<td></td><td></td>
	</tr>
	</table>
		
	</td>
	<td width="40%" valign="top">
		
		<table cellpadding="3" cellspacing="0" border="0" width="100%">
			<tr>
				<td width="100">สัญชาติ</td><td><input type="text" name="txt_reg" id="cus_txt_reg" size="10" value="<?php echo $nationality; ?>" <?php echo $nationality_style; ?> readonly></td>
			</tr>
			<tr>
				<td>วันเกิด</td><td><input type="text" name="txt_barthdate" id="cus_txt_barthdate" size="10" value="<?php echo $birth_date; ?>" <?php echo $birth_date_style; ?> readonly></td>
			</tr>
			<tr>
				<td>บัตรที่ใช้แสดงตัว</td>
				<td>
			<select name="combo_cardtype" id="cus_combo_cardtype" <?php echo $card_type_style; ?> disabled >
			  <option value="บัตรประชาชน" <?php if($card_type == "บัตรประชาชน"){echo "selected"; } ?> >บัตรประชาชน</option>
			  <option value="บัตรข้าราชการ" <?php if($card_type == "บัตรข้าราชการ"){echo "selected"; } ?> >บัตรข้าราชการ</option>
			  <option value="บัตรผู้เสียภาษีอากร" <?php if($card_type == "บัตรผู้เสียภาษีอากร"){echo "selected"; } ?> >บัตรประจำตัวผู้เสียภาษีอากร</option>
			  <option value="ใบขับขี่" <?php if($card_type == "ใบขับขี่"){echo "selected"; } ?> >ใบขับขี่</option>
			  <option value="อื่นๆ" <?php if($card_type == "อื่นๆ"){echo "selected"; } ?> >อื่นๆ</option>
			<!--</select> <span id="span_card" style="display:none"><input type="text" name="txt_cardother" id="txt_cardother" size="10"></span>-->
				</td>
			</tr>
			<tr>
				<td>เลขที่บัตร</td><td><input type="text" name="txt_cardno" id="cus_txt_cardno" size="30" value="<?php echo $card_id; ?>" <?php echo $card_id_style; ?> readonly></td>
			</tr>
			<tr>
				<td>วันที่ออกบัตร</td><td><input type="text" name="txt_carddate" id="cus_txt_carddate" size="10" value="<?php echo $card_do_date; ?>" <?php echo $card_do_date_style; ?> readonly></td>
			</tr>
			<tr>
				<td>สถานที่ออกบัตร</td><td><input type="text" name="txt_cardby" id="cus_txt_cardby" size="30" size="10" value="<?php echo $card_do_by; ?>" <?php echo $card_do_by_style; ?> readonly></td>
			</tr>
			<tr>
				<td>อาชีพ</td><td><input type="text" name="txt_job" id="cus_txt_job" size="30" value="<?php echo $job; ?>" <?php echo $job_style; ?> readonly ></td>
			</tr>
		</table>
	</td>
	</tr>
	</table>
	<input type="hidden" name="m_cusid" id="m_cusid" value="<?php echo $id; ?>" >
</div>
	<div>
		<table cellpadding="1" cellspacing="10" align="center">
			<tr align="center">
				<td><b>หมายเหตุ :</b></td>
			</tr>
			<tr align="center">
				<td>
					<textarea id="remark" name="remark" cols="40" rows="5" <?php if($popupType == "viewOnly"){echo "readOnly";} ?>><?php echo $remark; ?></textarea>
				</td>
			</tr>
			<?php if($popupType != "viewOnly")
			{ // ถ้าไม่ได้มาจากหน้าสำหรับดูรายการเพียงอย่างเดียว
			?>
				<tr align="center">
					<td><input type="button" name="appv" id="appv" value="อนุมัติ"> <input type="button" name="notappv" id="notappv" value="ไม่อนุมัติ"></td>
				</tr>
			<?php
			}
			?>
		</table>
	</div>
	
<script>
$('#appv').click(function(){
	if($('#remark').val() == ""){
		alert('กรุณาระบุหมายเหตุด้วย');
		return false;
	}
	$.post('process_appv_cus.php',{
		cmd : 'appv',
		remark : $('#remark').val(),
		auto_id : '<?php echo $auto_id;?>'
	},function(data){
		if(data == 1){
			alert('บันทึกข้อมูลเรียบร้อยแล้ว');
			location.reload();
		}else{
			alert(data);
			location.reload();
		}
	});
});
$('#notappv').click(function(){
	if($('#remark').val() == ""){
		alert('กรุณาระบุหมายเหตุด้วย');
		return false;
	}
	$.post('process_appv_cus.php',{
		cmd : 'notappv',
		remark : $('#remark').val(),
		auto_id : '<?php echo $auto_id;?>'
	},function(data){
		if(data == 1){
			alert('บันทึกข้อมูลเรียบร้อยแล้ว');
			location.reload();
		}else{
			alert(data);
			location.reload();
		}
	});
});
</script>
<body>
</html>
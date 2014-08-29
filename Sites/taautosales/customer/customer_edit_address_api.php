<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "autocomplete"){
    $term = $_GET['term'];

    $qry_name=pg_query("select * from \"Customers\" WHERE \"cus_name\" LIKE '%$term%' ORDER BY \"cus_name\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $cus_id = trim($res_name["cus_id"]);
        $cus_name = trim($res_name["cus_name"]);
        $surname = trim($res_name["surname"]);
        
        $dt['value'] = $cus_id."#".$cus_name." ".$surname;
        $dt['label'] = "{$cus_id} , {$cus_name} {$surname}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
}

elseif($cmd == "show"){
    $id = $_GET['id'];
    
    $qry_name=pg_query("select * from \"Customers\" WHERE \"cus_id\"='$id' ");
    if($res_cus=pg_fetch_array($qry_name)){
				
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
    }else{
        echo "ไม่พบข้อมูล";
        exit;
    }
?>
<div style="border: 1px dashed #D0D0D0; margin-top:10px; padding:0px; background-color:#F0F0F0">
 <table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
  <tr>
    <td width="60%" valign="top">

<table cellpadding="3" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="100">ประเภทลูกค้า</td>
		<td>
			<input type="radio" name="rdo_cus_type" id="rdo_cus_type" value="1" <?php if($cus_type == "1"){echo "checked";}?> > <label>บุคคลธรรมดา</label>
			<input type="radio" name="rdo_cus_type" id="rdo_cus_type" value="2" <?php if($cus_type == "2"){echo "checked";}?>><label>นิติบุคคล</label>
			<span id="span_branch" style="display:none">
				<label>สาขาที่</label>&nbsp;&nbsp;<input type="text" name="txt_branch_id" id="txt_branch_id" size="5" value="<?php echo $branch_id; ?>"> 
			</span>
		</td>
	</tr>
	<tr>
		<td width="100">คำนำหน้าชื่อ</td><td><input type="text" name="txt_pre_name" id="cus_txt_pre_name" size="10" value="<?php echo $pre_name; ?>"></td>
	</tr>
	<tr>
		<td>ชื่อ</td><td><input type="text" name="txt_firstname" id="cus_txt_firstname"  value="<?php echo $cus_name; ?>"> สกุล <input type="text" name="txt_lastname" id="cus_txt_lastname" value="<?php echo $surname; ?>"></td>
	</tr>
	<tr>
		<td>ที่อยู่ตามบัตรประชาชน</td><td colspan="3"><textarea name="txt_address" id="cus_txt_address" rows="3" cols="1" style="width:330px"><?php echo $address; ?></textarea></td>
	</tr>
	<tr>
		<td>รหัสไปรษณีย์</td><td><input type="text" name="txt_post" id="cus_txt_post" size="10" value="<?php echo $add_post; ?>"></td>
	</tr>
	<tr>
		<td>ชื่อ-สกุล<br>ผู้จดทะเบียน</td><td><input type="text" name="txt_name_reg" id="cus_txt_name_reg" size="40" value="<?php echo $reg_customer; ?>" ></td>
	</tr>
	<tr>
		<td><label>ที่อยู่ที่จดทะเบียน</label>
		</td>
			<td>
				<!--<input type="radio" name="cus_rdo_reg_address" id="cus_rdo_reg_address" value="1" checked> -->
				<input type="radio" name="cus_rdo_reg_address" id="cus_rdo_reg_address" value="1" <?php if($address == $reg_address){ echo "checked";}?>> ตามที่อยู่ตามบัตรประชาชน
				<input type="radio" name="cus_rdo_reg_address" id="cus_rdo_reg_address" value="2" <?php if($address != $reg_address){ echo "checked";}?> >กรอกใหม่  
			</td>
	</tr>
	<tr>
		<td></td>
							<td colspan="2">
								<div id="div_cus_reg_address" >
									<table>
										<tr>
											<td align="right"><label>ที่อยู่</label></td>
											<td><textarea name="txt_address_reg" id="cus_txt_address_reg" rows="3" cols="1" style="width:250px; "><?php echo $reg_address; ?></textarea></td>
										</tr>
										<tr>
											<td><label>รหัสไปรษณีย์</label></td>
											<td><input type="text" name="txt_post_reg" id="cus_txt_post_reg" size="15" value="<?php echo $reg_post; ?>" ></td>
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
								<input type="radio" name="cus_chkContact" id="cus_chkContact" value="1" <?php if($address == $contract_add){ echo "checked";}?> >ตามบัตรประชาชน 
								<input type="radio" name="cus_chkContact" id="cus_chkContact" value="3" <?php if($contract_add == $reg_address){ echo "checked";}?> >ตามที่อยู่ที่จดทะเบียน
								<input type="radio" name="cus_chkContact" id="cus_chkContact" value="2" <?php if($address != $contract_add and $contract_add != $reg_address){ echo "checked";}?> >กรอกใหม่ 
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
							<div  id="cus_divcontact">
								<table>
									<tr><td align="center">ที่อยู่</td><td><textarea name="txt_contact" id="cus_txt_contact" rows="3" cols="40"><?php echo $contract_add; ?></textarea></td></tr>
									<tr><td>รหัสไปรษณีย์</td><td><input type="text" name="txt_post_contract" id="cus_txt_post_contract" size="10" value="<?php echo $contract_post; ?>" ></td></tr>
								</table>
							</div>
							</td>
						</tr>
	<!--<tr>
		<td></td><td><div style="display:none" id="divcontact"><textarea name="txt_contact" id="txt_contact" rows="3" cols="40"></textarea></div></td>
	</tr>-->
	<tr>
		<td>โทรศัพท์</td><td><input type="text" name="txt_phone" id="cus_txt_phone" size="30" value="<?php echo $telephone; ?>" ></td>
		<td></td><td></td>
	</tr>
	</table>
		
	</td>
	<td width="40%" valign="top">
		
		<table cellpadding="3" cellspacing="0" border="0" width="100%">
			<tr>
				<td width="100">สัญชาติ</td><td><input type="text" name="txt_reg" id="cus_txt_reg" size="10" value="<?php echo $nationality; ?>"></td>
			</tr>
			<tr>
				<td>วันเกิด</td><td><input type="text" name="txt_barthdate" id="cus_txt_barthdate" size="10" value="<?php echo $birth_date; ?>"></td>
			</tr>
			<tr>
				<td>บัตรที่ใช้แสดงตัว</td>
				<td>
			<select name="combo_cardtype" id="cus_combo_cardtype">
			  <option value="บัตรประชาชน" <?php if($card_type == "บัตรประชาชน"){echo "selected"; } ?> >บัตรประชาชน</option>
			  <option value="บัตรข้าราชการ" <?php if($card_type == "บัตรข้าราชการ"){echo "selected"; } ?> >บัตรข้าราชการ</option>
			  <option value="บัตรผู้เสียภาษีอากร" <?php if($card_type == "บัตรผู้เสียภาษีอากร"){echo "selected"; } ?> >บัตรประจำตัวผู้เสียภาษีอากร</option>
			  <option value="ใบขับขี่" <?php if($card_type == "ใบขับขี่"){echo "selected"; } ?> >ใบขับขี่</option>
			  <option value="อื่นๆ" <?php if($card_type == "อื่นๆ"){echo "selected"; } ?> >อื่นๆ</option>
			<!--</select> <span id="span_card" style="display:none"><input type="text" name="txt_cardother" id="txt_cardother" size="10"></span>-->
				</td>
			</tr>
			<tr>
				<td>เลขที่บัตร</td><td><input type="text" name="txt_cardno" id="cus_txt_cardno" size="30" value="<?php echo $card_id; ?>" ></td>
			</tr>
			<tr>
				<td>วันที่ออกบัตร</td><td><input type="text" name="txt_carddate" id="cus_txt_carddate" size="10" value="<?php echo $card_do_date; ?>"></td>
			</tr>
			<tr>
				<td>สถานที่ออกบัตร</td><td><input type="text" name="txt_cardby" id="cus_txt_cardby" size="30" size="10" value="<?php echo $card_do_by; ?>"></td>
			</tr>
			<tr>
				<td>อาชีพ</td><td><input type="text" name="txt_job" id="cus_txt_job" size="30" value="<?php echo $job; ?>"></td>
			</tr>
		</table>
	</td>
	</tr>
	</table>
	<input type="hidden" name="m_cusid" id="m_cusid" value="<?php echo $id; ?>" >
</div>
<div>
	<input type="button" name="btnSave" id="btnSave" value="บันทึก">
</div>
<script>
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
// เหลือแก้ไขชื่อ ID ตาม form ใหม่
$('#btnSave').click(function(){

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
	}else if($("#cus_txt_barthdate").val() == ""){
		alert('กรุณาระบุข้อมูลวันเกิดด้วย');
		return false;
	}else if($("#cus_txt_carddate").val() == ""){
		alert('กรุณาระบุข้อมูลวันที่ออกบัตรด้วย');
		return false;
	}else if($('#cus_txt_cardno').val()== ""){
		alert('กรุณาระบุ เลขที่บัตร');
		return false;
	}else if($('#cus_txt_name_reg').val()  == ""){
		alert('กรุณาระบุ  ชื่อผู้จดทะเบียน');
		return false;
	}
    $.post('customer_edit_address_api.php',{
        cmd: 'save',
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
		txt_post_reg: $('#cus_txt_post_reg').val(),//รหัสไปรษณีย์ที่จดทะเบียน
		txt_branch_id: $('#txt_branch_id').val(),
		m_cusid: $('#m_cusid').val()
		
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
</script>

<?php
}

elseif($cmd == "save"){
	
	$iduser = $_SESSION["ss_iduser"];
	
	$cus_type = pg_escape_string($_POST['cus_type']);
    $txt_pre_name = checknull( pg_escape_string($_POST['txt_pre_name']) );
    $txt_firstname = checknull( pg_escape_string($_POST['txt_firstname']) );
    $txt_lastname = checknull( pg_escape_string($_POST['txt_lastname']) );
    $txt_address = pg_escape_string($_POST['txt_address']);
    $txt_post = checknull(pg_escape_string($_POST['txt_post']));
    $chkContact = pg_escape_string($_POST['chkContact']);
    $txt_contact = pg_escape_string($_POST['txt_contact']);
	$txt_post_contract = pg_escape_string($_POST['txt_post_contract']);
    $txt_phone = pg_escape_string($_POST['txt_phone']);

    $txt_reg = checknull( pg_escape_string($_POST['txt_reg']) );
    $txt_barthdate = pg_escape_string($_POST['txt_barthdate']);
    $combo_cardtype = pg_escape_string($_POST['combo_cardtype']);
    $txt_cardother = pg_escape_string($_POST['txt_cardother']);
    $txt_cardno = checknull( pg_escape_string($_POST['txt_cardno']) );
    $txt_carddate = pg_escape_string($_POST['txt_carddate']);
    $txt_cardby = checknull( pg_escape_string($_POST['txt_cardby']) );
    $txt_job =  checknull( pg_escape_string($_POST['txt_job']) );
	
	$rdo_reg_address = pg_escape_string($_POST['rdo_reg_address']);
	$txt_name_reg =  pg_escape_string($_POST['txt_name_reg']);
	$txt_address_reg = pg_escape_string($_POST['txt_address_reg']);
	$txt_post_reg = checknull(pg_escape_string($_POST['txt_post_reg']));
	$txt_branch_id = pg_escape_string($_POST['txt_branch_id']);
		
	$cus_id = pg_escape_string($_POST['m_cusid']);
	
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
	  if($chkContact == 1){ 
		$str_contact = checknull($txt_address);
		$srt_contract_post =  $txt_post;
		}
	else if($chkContact == 2){
		$str_contact = checknull($txt_contact);
		$srt_contract_post = $txt_post;
	}else if($chkContact == 3){
		$str_contact = checknull($txt_address_reg);
		$srt_contract_post = $txt_post_contract;
	}
	if($rdo_reg_address == 2){
		$str_reg_address = checknull($txt_address_reg);
		$str_reg_post = $txt_post_reg;
	}else{
		$str_reg_address = checknull($txt_address);
		$str_reg_post = $txt_post;
	}
	
	if($cus_type == 2){
		$branch_id = "'".$txt_branch_id."'";
	}else{
		$branch_id = "DEFAULT";
	}
    $in_qry="INSERT INTO \"Customers_temp\" (cus_id,pre_name,cus_name,surname,address,add_post,nationality,birth_date,
										card_type,card_id,card_do_date,card_do_by,job,contract_add,contract_post,branch_id,
										telephone,reg_customer,reg_address,reg_post,cus_type,doer_id,doer_stamp,status_appv) 
								VALUES ('$cus_id',$txt_pre_name,$txt_firstname,$txt_lastname,'$txt_address',$txt_post,$txt_reg,'$txt_barthdate',
										'$combo_cardtype',$txt_cardno,'$txt_carddate',$txt_cardby,$txt_job,$str_contact,$srt_contract_post,$branch_id,
										'$txt_phone','$txt_name_reg',$str_reg_address,$str_reg_post,'$cus_type','$iduser','$nowDateTime','9')";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "INSERT Customers_temp ไม่สำเร็จ $in_qry";
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
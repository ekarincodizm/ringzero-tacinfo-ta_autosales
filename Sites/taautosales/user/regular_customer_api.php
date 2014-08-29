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
        $matches[] = "ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
}

elseif($cmd == "show"){
    $id = $_GET['id'];
    
    $qry_name=pg_query("select * from \"Customers\" WHERE \"cus_id\"='$id' ");
    if($res_name=pg_fetch_array($qry_name)){
        $pre_name = trim($res_name["pre_name"]);
        $cus_name = trim($res_name["cus_name"]);
        $surname = trim($res_name["surname"]);
        $address = $res_name["address"];
        $add_post = $res_name["add_post"];
        $nationality = $res_name["nationality"];
        $birth_date = $res_name["birth_date"];
        $card_type = $res_name["card_type"];
        $card_id = $res_name["card_id"];
        $card_do_date = $res_name["card_do_date"];
        $card_do_by = $res_name["card_do_by"];
        $job = $res_name["job"];
        $contract_add = $res_name["contract_add"];
        $telephone = $res_name["telephone"];
    }else{
        echo "ไม่พบข้อมูล";
        exit;
    }
    
    $qry=pg_query("select COUNT(regular_id) as cid from \"RegularCustomers\" WHERE \"cus_id\"='$id' ");
    if($res=pg_fetch_array($qry)){
        $cid = $res["cid"];
    }
?>
<div style="border: 1px dashed #D0D0D0; margin-top:10px; padding:0px; background-color:#F0F0F0">
<table cellpadding="5" cellspacing="0" border="0" width="100%">
<tr>
    <td width="60%" valign="top">

<table cellpadding="5" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100"><b>คำนำหน้าชื่อ :</b></td><td><?php echo $pre_name; ?></td>
</tr>
<tr>
    <td><b>ชื่อ/สกุล :</b></td><td><?php echo "$cus_name $surname"; ?></td>
</tr>
<tr>
    <td><b>ที่อยู่ตามบัตรประชาชน :</b></td><td colspan="3"><?php echo $address; ?></td>
</tr>
<tr>
    <td><b>รหัสไปรษณีย์ :</b></td><td><?php echo $add_post; ?></td>
</tr>
<tr>
    <td><b>ที่ติดต่อ :</b></td><td><?php echo $contract_add; ?></td>
</tr>
<tr>
    <td><b>โทรศัพท์ :</b></td><td><?php echo $telephone; ?></td>
    <td></td><td></td>
</tr>
</table>
    
    </td>
    <td width="40%" valign="top">
    
<table cellpadding="5" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100"><b>สัญชาติ :</b></td><td><?php echo $nationality; ?></td>
</tr>
<tr>
    <td><b>วันเกิด :</b></td><td><?php echo $birth_date; ?></td>
</tr>
<tr>
    <td><b>บัตรที่ใช้แสดงตัว :</b></td><td><?php echo $card_type; ?></td>
</tr>
<tr>
    <td><b>เลขที่บัตร :</b></td><td><?php echo $card_id; ?></td>
</tr>
<tr>
    <td><b>วันที่ออกบัตร :</b></td><td><?php echo $card_do_date; ?></td>
</tr>
<tr>
    <td><b>สถานที่ออกบัตร :</b></td><td><?php echo $card_do_by; ?></td>
</tr>
<tr>
    <td><b>อาชีพ :</b></td><td><?php echo $job; ?></td>
</tr>
</table>

    </td>
</tr>
</table>
</div>

<div style="border: 1px dashed #D0D0D0; margin-top:3px; padding:10px; background-color:#FFFFE1">
<?php
if($cid > 0){
?>
<div style="text-align:center">- ลูกค้ารายนี้ ได้เป็นลูกค้าที่ซื้อสินค้าแล้ว -</div>
<?php
}else{
?>
<div style="text-align:right">เพิ่มเป็นลูกค้าที่ซื้อสินค้า <input type="button" name="btnSave" id="btnSave" value="บันทึก"></div>
<?php
}
?>
</div>

<script>
$('#btnSave').click(function(){
    var str_plan = $("#txt_name").val();
    var arr_plan = str_plan.split("#");
    
    $.post('regular_customer_api.php',{
        cmd: 'save',
        id: arr_plan[0]
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

elseif($cmd == "new"){
?>
<?php //========= GUI สำหรับเพิ่มข้อมูลใหม่ =========?>
<div style="border: 1px dashed #D0D0D0; margin-top:10px; padding:0px; background-color:#F0F0F0">
<table cellpadding="5" cellspacing="0" border="0" width="100%">
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
    <td>ที่อยู่ตามบัตรประชาชน</td><td colspan="3"><textarea name="txt_address" id="txt_address" rows="1" cols="1" style="width:330px; height:70px"></textarea></td>
</tr>
<tr>
    <td>รหัสไปรษณีย์</td><td><input type="text" name="txt_post" id="txt_post" size="10"></td>
</tr>
<tr>
	<td>ชื่อ-สกุล<br>ผู้จดทะเบียน</td><td><input type="text" name="txt_name_reg" id="txt_name_reg" size="40"></td>
</tr>
<tr>
	<td><label>ที่อยู่ที่จดทะเบียน</label>
	</td>
		<td>
			<input type="radio" name="rdo_reg_address" id="rdo_reg_address" value="1" checked> ตามที่อยู่ตามบัตรประชาชน
			<input type="radio" name="rdo_reg_address" id="rdo_reg_address" value="2">กรอกใหม่
		</td>
</tr>
<tr>
	<td></td>
						<td colspan="2">
							<div id="div_reg_address" style="display:none">
								<table>
									<tr>
										<td align="right"><label>ที่อยู่</label></td>
										<td><textarea name="txt_address_reg" id="txt_address_reg" rows="1" cols="1" style="width:250px; height:70px"></textarea></td>
									</tr>
									<tr>
										<td><label>รหัสไปรษณีย์</label></td>
										<td><input type="text" name="txt_post_reg" id="txt_post_reg" size="15"></td>
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
							<input type="radio" name="chkContact" id="chkContact" value="1" checked>ตามที่อยู่ตามบัตรประชาชน 
							<input type="radio" name="chkContact" id="chkContact" value="3" >ตามที่อยู่ที่จดทะเบียน
							<input type="radio" name="chkContact" id="chkContact" value="2">กรอกใหม่ 
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
						<div style="display:none" id="divcontact">
							<table>
								<tr><td align="center">ที่อยู่</td><td><textarea name="txt_contact" id="txt_contact" rows="3" cols="40"></textarea></td></tr>
								<tr><td>รหัสไปรษณีย์</td><td><input type="text" name="txt_post_contract" id="txt_post_contract" size="10"></td></tr>
							</table>
						</div>
						</td>
					</tr>
<!--<tr>
    <td></td><td><div style="display:none" id="divcontact"><textarea name="txt_contact" id="txt_contact" rows="3" cols="40"></textarea></div></td>
</tr>-->
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
<!--</select> <span id="span_card" style="display:none"><input type="text" name="txt_cardother" id="txt_cardother" size="10"></span>-->
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

<script>
$("#txt_carddate, #txt_barthdate").datepicker({
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


$("input[name='rdo_reg_address']").change(function(){
    if( $('input[id=rdo_reg_address]:checked').val() == "2" ){
		$('#div_reg_address').show('fast');
	}else{
		$('#div_reg_address').hide('fast');
	}
});


/*$("#combo_cardtype").change(function(){
    if( $("#combo_cardtype").val() == "อื่นๆ" ){
        $('#span_card').show('fast');
    }else{
        $('#span_card').hide('fast');
    }
});*/


function chkDate(datetxt){
	var str = datetxt;
	var Date_split = str.split("-");
	var chk = 0;
	if(Date_split.length!= 3){
		chk++;
	}else{
	
		var dtYear = parseInt(Date_split[0]);  
		var dtMonth = parseInt(Date_split[1]);
		var dtDay = parseInt(Date_split[2]);
		
		if(isNaN(dtYear) == true){
			chk++;
		}
		if(isNaN(dtMonth) == true){
			chk++;
		}
		if(isNaN(dtDay) == true){
			chk++;
		}
			
		if (dtMonth < 1 || dtMonth > 12){
			chk++;
		}else if (dtDay < 1 || dtDay> 31) {
			chk++;
		}else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31) {
			chk++;
		} else if (dtMonth == 2) {
			var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
			if (dtDay> 29 || (dtDay ==29 && !isleap)) 
            chk++;
		}
	}

	if(chk>0){
		return 0;
	}else{
		return 1;
	}
}

$('#btnSave').click(function(){
	if($('#txt_pre_name').val() == ""){
		alert('กรุณาระบุ คำนำหน้าชื่อ');
		return false;
	}else if($('#txt_firstname').val() == ""){
		alert('กรุณาระบุ  ชื่อ');
		return false;
	}else if($('#txt_lastname').val() == ""){
		alert('กรุณาระบุ  นามสกุล');
		return false;
	}else if($('#txt_address').val() == ""){
		alert('กรุณาระบุ  ที่อยู่');
		return false;
	}else if($('#txt_post').val()== ""){
		alert('กรุณาระบุ รหัสไปรษณีย์ ');
		return false;
	}else if(isNaN($('#txt_post').val()) == true){
		alert('รหัสไปรษณีย์ต้องเป็นตัวเลขเท่านั้น');
		return false;
	}else if($("#txt_barthdate").val() == ""){
		alert('กรุณาระบุข้อมูลวันเกิดด้วย');
		return false;
	}else if(chkDate($("#txt_barthdate").val()) == 0){
		alert('ข้อมูลวันเกิดไม่ถูกต้อง');
		return false;
	}else if($("#txt_carddate").val() == ""){
		alert('กรุณาระบุข้อมูลวันที่ออกบัตรด้วย');
		return false;
	}else if(chkDate($("#txt_carddate").val()) == 0){
		alert('ข้อมูลวันที่ออกบัตรไม่ถูกต้อง');
		return false;
	}else if($('#txt_cardno').val()== ""){
		alert('กรุณาระบุ เลขที่บัตร');
		return false;
	}else if($('#txt_name_reg').val()  == ""){
		alert('กรุณาระบุ  ชื่อผู้จดทะเบียน');
		return false;
	}else if( $('input[id=chkContact]:checked').val() == "2" ){
		if($('#txt_post_contract').val()== ""){
			alert('กรุณาระบุรหัสไปรษณีที่ติดต่อ');
			return false;
		}else{
			if(isNaN($('#txt_post_contract').val()) == true){
				alert('รหัสไปรษณีที่ติดต่อต้องเป็นตัวเลขเท่านั้น');
				return false;
			}
		}
		if($('#txt_contact').val() ==""){
			alert('กรุณาระบุ  ที่อยู่ที่ติดต่อ');
			return false;
		}
	}else if( $('input[id=rdo_reg_address]:checked').val() == "2" ){
		if($('#txt_post_reg').val()== ""){
				alert('กรุณาระบุรหัสไปรษณีที่จดทะเบียน');
				return false;
			}else{
				if(isNaN($('#txt_post_reg').val()) == true){
					alert('รหัสไปรษณีที่จดทะเบียนต้องเป็นตัวเลขเท่านั้น');
					return false;
				}
			}
		if($('#txt_address_reg').val() ==""){
			alert('กรุณาระบุ  ที่อยู่ที่จดทะเบียน');
			return false;
		}
	}
	

    $.post('regular_customer_api.php',{
        cmd: 'savenewcus',
        txt_pre_name: $('#txt_pre_name').val(),
        txt_firstname: $('#txt_firstname').val(),
        txt_lastname: $('#txt_lastname').val(),
        txt_address: $('#txt_address').val(),
        txt_post: $('#txt_post').val(),
        chkContact: $('input[id=chkContact]:checked').val(),
		rdo_reg_address: $('input[id=rdo_reg_address]:checked').val(),
        txt_contact: $('#txt_contact').val(),
		txt_post_contract: $('#txt_post_contract').val(),
        txt_phone: $('#txt_phone').val(),
        txt_reg: $('#txt_reg').val(),
        txt_barthdate: $('#txt_barthdate').val(),
        combo_cardtype: $("#combo_cardtype").val(),
        txt_cardother: $('#txt_cardother').val(),
        txt_cardno: $('#txt_cardno').val(),
        txt_carddate: $('#txt_carddate').val(),
        txt_cardby: $('#txt_cardby').val(),
        txt_job: $('#txt_job').val(),
		txt_name_reg: $('#txt_name_reg').val(),
		txt_address_reg: $('#txt_address_reg').val(),
		txt_post_reg: $('#txt_post_reg').val()
		
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

</div>

<div style="text-align:right; margin-top:10px">
<input type="button" name="btnSave" id="btnSave" value="บันทึก">
</div>

<?php
}

elseif($cmd == "save"){
    $id = $_POST['id'];
    
    $regular_id = GetRecCusID();
    $qry = "INSERT INTO \"RegularCustomers\" (regular_id,cus_id) VALUES ('$regular_id','$id')";
    if($res=@pg_query($qry)){
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้!";
    }
    echo json_encode($data);
}

elseif($cmd == "savenewcus"){
    $txt_pre_name=$_POST['txt_pre_name'];
    $txt_firstname=$_POST['txt_firstname'];
    $txt_lastname=$_POST['txt_lastname'];
    $txt_address=$_POST['txt_address'];
    $txt_post=$_POST['txt_post'];
    $chkContact=$_POST['chkContact'];
    $txt_contact=$_POST['txt_contact'];
	$txt_post_contract=$_POST['txt_post_contract'];
    $txt_phone=$_POST['txt_phone'];

    $txt_reg=$_POST['txt_reg'];
    $txt_barthdate=$_POST['txt_barthdate'];
    $combo_cardtype=$_POST['combo_cardtype'];
    $txt_cardother=$_POST['txt_cardother'];
    $txt_cardno=$_POST['txt_cardno'];
    $txt_carddate=$_POST['txt_carddate'];
    $txt_cardby=$_POST['txt_cardby'];
    $txt_job=$_POST['txt_job'];
	
	$rdo_reg_address = $_POST['rdo_reg_address'];
	$txt_name_reg =  $_POST['txt_name_reg'];
	$txt_address_reg = $_POST['txt_address_reg'];
	$txt_post_reg = $_POST['txt_post_reg'];

    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $cus_id = GetCusID();

    if($chkContact == 1){ 
		$str_contact = $txt_address;
		$srt_contract_post =  $txt_post;
		}
	else if($chkContact == 2){
		$str_contact = $txt_contact;
		$srt_contract_post = $txt_post;
	}else if($chkContact == 3){
		$str_contact = $txt_address_reg;
		$srt_contract_post = $txt_post_reg;
	}
	if($rdo_reg_address == 2){$str_reg_address = $txt_address_reg;$str_reg_post = $txt_post_reg;}else{$str_reg_address = $txt_address;$str_reg_post = $txt_post;}
    /*if($combo_cardtype != "อื่นๆ"){ $str_cardtype = $combo_cardtype; }else{ $str_cardtype = $txt_cardother; }*/
    
    $in_qry="INSERT INTO \"Customers\" (\"cus_id\",\"pre_name\",\"cus_name\",\"surname\",\"address\",\"add_post\",\"nationality\",\"birth_date\",
										\"card_type\",\"card_id\",\"card_do_date\",\"card_do_by\",\"job\",\"contract_add\",\"contract_post\" ,\"telephone\",\"reg_customer\",\"reg_address\",\"reg_post\") 
										values ('$cus_id','$txt_pre_name','$txt_firstname','$txt_lastname','$txt_address','$txt_post','$txt_reg','$txt_barthdate',
										'$combo_cardtype','$txt_cardno','$txt_carddate','$txt_cardby','$txt_job','$str_contact','$srt_contract_post','$txt_phone','$txt_name_reg','$str_reg_address','$str_reg_post')";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "INSERT Customers ไม่สำเร็จ $in_qry";
        $status++;
    }
    
    /*$regular_id = GetRecCusID();
    $qry = "INSERT INTO \"RegularCustomers\" (regular_id,cus_id) VALUES ('$regular_id','$cus_id')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT RegularCustomers ไม่สำเร็จ $qry";
        $status++;
    }*/
    
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
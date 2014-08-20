<?php 
include_once("../include/config.php");
include_once("../include/function.php");
$cmd = $_REQUEST['cmd'];
if($cmd == "addvender"){
?>
<div><b>ค้นหาจากฐานข้อมูล : </b><input type="text" name="txt_name" id="txt_name" size="50" onkeyup="javascript:CheckNaN()"></div>

<div id="divoldcus" style="display:none; margin-top:10px"></div>

<div id="divnewcus" style="display:none; margin-top:10px">
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
<tr>
    <td width="55%" valign="top">
<table cellpadding="3" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="100">ประเภท Vender</td>
		<td>
			<input type="radio" name="rdo_cus_type" id="rdo_cus_type" value="1" checked> <label>บุคคลธรรมดา</label>
			<input type="radio" name="rdo_cus_type" id="rdo_cus_type" value="2"><label>นิติบุคคล</label>
			<span id="span_branch" style="display:none">
				<label>สาขาที่</label>&nbsp;&nbsp;<input type="text" name="txt_branch_id" id="txt_branch_id" size="5"> 
			</span>
		</td>
	</tr>
	<tr>
		<td width="100">คำนำหน้าชื่อ</td><td><input type="text" name="txt_pre_name" id="txt_pre_name" size="10">&nbsp;&nbsp;
		
		<!--<input type="text" name="txt_branch_name" id="txt_branch_name" size="18"> -->
		</td>
	</tr>
	<tr>
		<td>ชื่อ/สกุล</td><td><input type="text" name="txt_firstname" id="txt_firstname" style="width:100px"> / <input type="text" name="txt_lastname" id="txt_lastname" style="width:100px"></td>
	</tr>
	<tr>
		<td>รหัสย่อบริษัท</td>
		<td>
		<select name="alpha" id="alpha" onchange="check_vender();" >
			<option value="">เลือก</option>
			<?php
				$get_cha = pg_query("select alphas from \"Venders\" order by alphas ASC");
				
				//while($cha = pg_fetch_array($get_cha)){
					
					//$char[] = $cha['alphas'];
				//}
				
				
				$alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
				
				for($i=0;$i<sizeof($alphabet);$i++){
					//if(!in_array($alphabet[$i],$char)){
						echo "<option value=\"$alphabet[$i]\">$alphabet[$i]</option>";
					//}
				}
				
				// ยอม ให้เลือกซ้ำได้ เลย comment แบบ ไม่ยอมให้เลือกซ้ำไว้ก่อน
			?>
		</select> 
		</td>
	</tr>
	<tr>
		<td>ที่อยู่ตาม ภ.พ. 20</td><td colspan="3"><textarea name="txt_address" id="txt_address" rows="1" cols="1" style="width:280px; height:70px"></textarea></td>
	</tr>
	<tr>
		<td>รหัสไปรษณีย์</td><td><input type="text" name="txt_post" id="txt_post" size="10"></td>
	</tr>
	<tr>
		<td>ที่ติดต่อ</td>
		<td><input type="radio" name="chkContact" id="chkContact" value="1" checked> เหมือนด้านบน 
		<input type="radio" name="chkContact" id="chkContact" value="2">กรอกใหม่ </td> 
	</tr>
	<tr>
		<td></td>
		<td>
			<div style="display:none" id="divcontact">
				<textarea name="txt_contact" id="txt_contact" rows="3" cols="40"></textarea>
				<label> รหัสไปรษณีย์ </label>&nbsp;&nbsp; <input type="text" name="contract_post" id="contract_post" size="15" value="">
			</div>
		</td>
	</tr>
	<tr>
		<td>โทรศัพท์</td><td><input type="text" name="txt_phone" id="txt_phone" size="30"></td>
		<td></td><td></td>
	</tr>
	<tr>
		<td>ลักษณะขายสินค้า</td>
		<td>
	<select name="cb_type" id="cb_type">
	  <option value="P">สินค้าสำเร็จรูป</option>
	  <option value="M"> วัตถุดิบเริ่มต้น</option>
	  <option value="B">ทั้ง 2 ประเภท</option>
	</select> 
		</td>
	</tr>
	
</table>
    
    </td>
    <td width="45%" valign="top">
    
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
  <option value="บัตรผู้เสียภาษีอากร">บัตรประจำตัวผู้เสียภาษีอากร</option>
  <option value="ใบขับขี่">ใบขับขี่</option>
  <option value="อื่นๆ">อื่นๆ</option>
</select> 
<!--<span id="span_card" style="display:none"><input type="text" name="txt_cardother" id="txt_cardother" size="10"></span>-->
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
    
<div style="margin-top:10px; text-align:right">
<input type="button" id="btn_save" name="btn_save" value="บันทึก">
<input type="hidden" id="chk_vender" name="chk_vender" >
</div>
    
</div>

<script>
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

$("input[name='rdo_cus_type']").change(function(){
	if( $('input[id=rdo_cus_type]:checked').val() == "2" ){
		$('#span_branch').show('fast');
	}else{
		$('#span_branch').hide('fast');
	}		
});

/*$("#combo_cardtype").change(function(){
    if( $("#combo_cardtype").val() == "อื่นๆ" ){
        $('#span_card').show('fast');
    }else{
        $('#span_card').hide('fast');
    }
});*/

$("#txt_name").autocomplete({
    source: "vender_api.php?cmd=autocomplete",
    minLength:1,
        select: function(event, ui) {
        if(ui.item.value == 'ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่'){
            $('#divoldcus').hide();
            $('#divnewcus').show('fast');
        }else{
            $('#divnewcus').hide();
            $('#divoldcus').show('fast');
            var arr_cusid = ui.item.value.split("#");
            $('#divoldcus').load('vender_api.php?cmd=chk_vender&cus_id='+arr_cusid[0]);
        }
    }
});

function CheckNaN(){
    if( $('#txt_name').val() == '' ){
        $('#divoldcus').hide();
        $('#divnewcus').hide();
    }
}

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

$('#btn_save').click(function(){
	
	var chk = 0;
	var errorMessage = "";
	
	if($('#ddl_branch').val() == "not"){
		errorMessage +="กรุณาเลือก สาขา";
		chk++;
	}else if($('#txt_pre_name').val() == ""){
		errorMessage +="กรุณาระบุ คำนำหน้าชื่อ";
		chk++;
	}else if($('#txt_firstname').val() == ""){
		errorMessage +="กรุณาระบุ ชื่อ";
		chk++;
	}else if($('#txt_address').val() == ""){
		errorMessage +="กรุณาระบุ ที่อยู่";
		chk++;
	}else if($('#txt_post').val() == ""){
		errorMessage +="กรุณาระบุ รหัสไปรษณีย์";
		chk++;
	}else if(isNaN($('#txt_post').val()) == true){
		errorMessage +="รหัสไปรษณีย์  ต้องเป็นตัวเลขเท่านั้น";
		chk++;
	}else if($('#txt_barthdate').val() == ""){
		errorMessage +="กรุณาระบุ  ข้อมูลวันเกิด";
		chk++;
	}else if(chkDate($('#txt_barthdate').val()) == 0){
		errorMessage +="ข้อมูลวันเกิด ไม่ถูกต้อง";
		chk++;
	}else if($('#txt_carddate').val() == ""){
		errorMessage +="กรุณาระบุ วันที่ออกบัตร";
		chk++;
	}else if(chkDate($('#txt_carddate').val()) == 0){
		errorMessage +="ข้อมูลวันที่ออกบัตรไม่ถูกต้อง";
		chk++;
	}else if(isNaN($('#txt_branch_id').val()) == true){
		errorMessage +="สาขาที่ ต้องเป็นตัวเลขเท่านั้น";
		chk++;
	}else if($('#cb_type').val() == "P"){
		if($('#alpha').val() == ""){
			errorMessage +="กรุณาระบุ รหัสย่อบริษัท";
			chk++;
		}
	}
	var confirmMsg = '';
	if($('#chk_vender').val() == '1'){
		confirmMsg = 'รหัสย่อบริษัท คือ '+$('#alpha').val()+' ต้องการบันทึกข้อมูลหรือไม่';
	}else{
		confirmMsg = 'รหัสย่อบริษัท คือ '+$('#alpha').val()+' รหัสซ้ำ! ต้องการบันทึกข้อมูลหรือไม่';
	}
	
	if(chk>0){
		alert(errorMessage);
		return false;
	}
		var r = confirm(confirmMsg);
		if(r == true){
		$.post('vender_api.php',{
        cmd: 'save_addvender',
		cus_type: $('input[id=rdo_cus_type]:checked').val(),
		branch_id: $('#txt_branch_id').val(),
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
        cb_type: $('#cb_type').val(),
		contract_post: $('#contract_post').val(),
		txt_aphas: $('#alpha').val()
    },
    function(data){
        if(data.success){
            alert(data.message);
            location.reload();
        }else{
            alert(data.message);
        }
    },'json');
	}else{
		return false;
	}
});
function check_vender(){
	$.post('chkdata.php',{
		txtalpha: $('#alpha').val()
	},function(data){
		if(data == 't'){
			$('#chk_vender').val('1');
		}else if(data == 'f'){
			$('#chk_vender').val('0');
		}
	});
}
</script>
<?php
}

elseif($cmd == "chk_vender"){
    $cus_id = $_GET['cus_id'];
    $qry_ven=pg_query("SELECT COUNT(*) AS count_id FROM  \"Venders\" WHERE cus_id='$cus_id' ");
    $res_ven=pg_fetch_array($qry_ven);
    $count_id = $res_ven['count_id'];
    
    if($count_id > 0){
        echo "รายการที่เลือกเป็น Vender ในระบบแล้ว !";
    }else{
        
        $qry_cus=pg_query("SELECT cus_name,surname,branch_id FROM  \"Customers\" WHERE cus_id='$cus_id' ");
        $res_cus=pg_fetch_array($qry_cus);
        $cus_name = trim($res_cus["cus_name"]);
        $surname = trim($res_cus["surname"]);
        ?>
<div style="padding:10px">
		รายการที่เลือกยังไม่ได้เป็น Vender<br><br>
		ท่านต้องการเพิ่ม <b><?php echo "$cus_id : $cus_name $surname"; ?></b> เป็น Vender ใช่หรือไม่?<br><br>
		
		<label> ลักษณะการขายสินค้า :</label>
				<select name="cb_type" id="cb_type">
				  <option value="P">สินค้าสำเร็จรูป</option>
				  <option value="M"> วัตถุดิบเริ่มต้น</option>
				  <option value="B">ทั้ง 2 ประเภท</option>
				</select> &nbsp;&nbsp;&nbsp;&nbsp;
				
				<label>สาขาที่ :<label> <input type="text" name="up_branch_id" id="up_branch_id" size="10" value="<?php echo  trim($res_cus["branch_id"]); ?>">	
				
					<br><br>
		<input type="button" id="btn_confirm_vender" name="btn_confirm_vender" value="ยืนยันการเพิ่ม Vender">
</div>

<script>
	$('#btn_confirm_vender').click(function(){
		var txt_name = $('#txt_name').val();
		var arr_cusid = txt_name.split("#");
		
		var chk = 0;
		var errorMessage = "";
		 if(isNaN($('#up_branch_id').val()) == true){
			errorMessage +="สาขาที่ ต้องเป็นตัวเลขเท่านั้น";
			chk++;
		}
		
		if(chk>0){
			alert(errorMessage);
			return false;
		}
		
		
	$.post('vender_api.php',{
			cmd: 'save_addoldvender',
			cus_id: arr_cusid[0],
			cb_type: $('#cb_type').val(),
			up_branch_id: $('#up_branch_id').val()
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
}

elseif($cmd == "autocomplete"){
   $term = $_GET['term'];

   $qry_name=pg_query("select * from \"Customers\" WHERE cus_name LIKE '%$term%' ");
   $numrows = pg_num_rows($qry_name);
   while($res_name=pg_fetch_array($qry_name)){
        $cus_id = trim($res_name["cus_id"]);
        $cus_name = trim($res_name["cus_name"]);
        $surname = trim($res_name["surname"]);
		$branch_id = trim($res_name["branch_id"]);
        
        $dt['value'] = $cus_id."#".$cus_name." ".$surname." ".$branch_id;
        $dt['label'] = "{$cus_id} , {$cus_name} {$surname} สาขาที่:{$branch_id}";
        $matches[] = $dt;
   }

   if($numrows==0){
       $matches[] = "ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่";
   }

   $matches = array_slice($matches, 0, 100);
   print json_encode($matches);
}

elseif($cmd == "save_addvender"){

    $txt_name=$_POST['txt_name'];
	$cus_type =$_POST['cus_type'];
	$branch_id = checknull($_POST['branch_id']);
    $txt_pre_name=$_POST['txt_pre_name'];
    $txt_firstname=$_POST['txt_firstname'];
    $txt_lastname=$_POST['txt_lastname'];
    $txt_address=$_POST['txt_address'];
    $txt_post=$_POST['txt_post'];
    $chkContact=$_POST['chkContact'];
    $txt_contact=$_POST['txt_contact'];
    $txt_phone=$_POST['txt_phone'];

    $txt_reg=$_POST['txt_reg'];
    $txt_barthdate=$_POST['txt_barthdate']; $txt_barthdate = ($txt_barthdate == "") ? "DEFAULT" : "'$txt_barthdate'";
    $combo_cardtype=$_POST['combo_cardtype'];
    $txt_cardother=$_POST['txt_cardother'];
    $txt_cardno=$_POST['txt_cardno'];
    $txt_carddate=$_POST['txt_carddate']; $txt_carddate = ($txt_carddate == "") ? "DEFAULT" : "'$txt_carddate'";
    $txt_cardby=$_POST['txt_cardby'];
    $txt_job=$_POST['txt_job'];

    $cb_type = $_POST['cb_type'];
	$contract_post = $_POST['contract_post'];
	$txt_aphas = $_POST['txt_aphas'];
	
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();

    $cus_id = GetCusID();

    $qry_numr=pg_query("select count(*) as numr from \"Venders\" ");
    $res_n=pg_fetch_array($qry_numr);
    if($res_n["numr"]==0){
        $num_id=1;
    }else{
        $num_id=$res_n["numr"]+1;
    }

    $in_vender="INSERT INTO \"Venders\" (vender_id,cus_id,type_ven,alphas)values('$num_id','$cus_id','$cb_type','$txt_aphas') ";
    if(!$resv=@pg_query($in_vender)){
        $txt_error[] = "บันทึก Venders ไม่สำเร็จ $in_vender";
        $status++;
    }

    if($chkContact == 1){ $str_contact = $txt_address; }else{ $str_contact = $txt_contact; }

    $in_qry="INSERT INTO \"Customers\" (cus_id,pre_name,cus_name,surname,address,add_post,
										nationality,birth_date,card_type,
										card_id,card_do_date,card_do_by,job,contract_add,telephone,branch_id,contract_post,cus_type ) 
								VALUES ('$cus_id','$txt_pre_name','$txt_firstname','$txt_lastname','$txt_address','$txt_post',
										'$txt_reg',$txt_barthdate,'$combo_cardtype',
										'$txt_cardno',$txt_carddate,'$txt_cardby','$txt_job','$str_contact','$txt_phone',$branch_id,'$contract_post','$cus_type')";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "บันทึก Customers ไม่สำเร็จ ";
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

elseif($cmd == "save_addoldvender"){

    $cus_id=$_POST['cus_id'];
    $cb_type = $_POST['cb_type'];
	$up_branch_id = $_POST['up_branch_id'];

    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();

    $qry_numr=pg_query("select count(*) as numr from \"Venders\" ");
    $res_n=pg_fetch_array($qry_numr);
    if($res_n["numr"]==0){
        $num_id=1;
    }else{
        $num_id=$res_n["numr"]+1;
    }

    $in_vender="INSERT INTO \"Venders\" (vender_id,cus_id,type_ven)values('$num_id','$cus_id','$cb_type') ";
    if(!$resv=@pg_query($in_vender)){
        $txt_error[] = "บันทึก Venders ไม่สำเร็จ $in_vender";
        $status++;
    }
	
	$update_branch ="UPDATE \"Customers\" SET branch_id = '$up_branch_id'
				WHERE cus_id = '$cus_id' ";
				
				
	if(!$res=pg_query($update_branch)){
		$ts_error[] = "ปรับปรุง ชื่อสาขา ไม่สำเร็จ $update_branch";
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

//======================= ดึงข้อมูลขึ้นมาเพื่อแก้ไข ========================//
elseif($cmd == "editvender"){
    $id = $_GET['id'];
    $qry_cus=pg_query("SELECT * FROM  \"Customers\" WHERE cus_id='$id' ");
    $res_cus=pg_fetch_array($qry_cus);
    
    $qry_ven=pg_query("SELECT * FROM  \"Venders\" WHERE cus_id='$id' ");
    $res_ven=pg_fetch_array($qry_ven);
    $type_ven = $res_ven['type_ven'];
	$alphas = $res_ven['alphas'];
    
    if($type_ven=="P"){
        $str_p_sl = "selected";
    }elseif($type_ven=="M"){
        $str_m_sl = "selected";
    }elseif($type_ven=="B"){
        $str_b_sl = "selected";
    }
?>

<?php //============================== GUI สำหรับการแก้ไขข้อมูล ===================================//?>
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
  <tr>
    <td width="60%" valign="top">

<table cellpadding="3" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="100">ประเภท Vender</td>
		<td>
			<input type="radio" name="ts_cus_type" id="ts_cus_type" value="1"<?php if($res_cus["cus_type"] == 1) echo "checked"; ?>  ><label>บุคคลธรรมดา</label>
			<input type="radio" name="ts_cus_type" id="ts_cus_type" value="2"<?php if($res_cus["cus_type"] == 2) echo "checked"; ?>  ><label>นิติบุคคล</label>
			<span id="span_branch" style="display:none">
				<label>สาขาที่</label>&nbsp;&nbsp;<input type="text" name="ts_branch_id" id="ts_branch_id" size="5" value="<?php echo  trim($res_cus["branch_id"]); ?>">	
			</span>
		</td>
	</tr>
	<tr>
		<td width="100">คำนำหน้าชื่อ</td><td><input type="text" name="ts_pre_name" id="ts_pre_name" size="10" value="<?php echo  trim($res_cus["pre_name"]); ?>"></td>
	</tr>
	<tr>
		<td>ชื่อ</td>
		<td><input type="text" name="ts_firstname" id="ts_firstname" value="<?php echo  trim($res_cus["cus_name"]); ?>"> 
			สกุล <input type="text" name="ts_lastname" id="ts_lastname" value="<?php echo  trim($res_cus["surname"]); ?>">
		</td>
	</tr>
	<tr>
		<td>รหัสย่อบริษัท</td>
		<td>
		<select name="alpha" id="alpha" onchange="check_vender();" >
			<option value="">เลือก</option>
			<?php
				//$get_cha = pg_query("select alphas from \"Venders\" order by alphas ASC");
				
				//while($cha = pg_fetch_array($get_cha)){
					
				//	$char[] = $cha['alphas'];
				//}
				
				$alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
				
				for($i=0;$i<sizeof($alphabet);$i++){
					//if(!in_array($alphabet[$i],$char)){
					if($alphas == $alphabet[$i]){
						echo "<option value=\"$alphabet[$i]\" selected >$alphabet[$i]</option>";
					}else{
						echo "<option value=\"$alphabet[$i]\">$alphabet[$i]</option>";
					}
					//}
				}
			?>
		</select> 
		</td>
	</tr>
	<tr>
		<td>ที่อยู่ตาม ภ.พ. 20</td><td colspan="3"><textarea name="ts_address" id="ts_address" rows="1" cols="1" style="width:330px; height:70px"><?php echo  $res_cus["address"]; ?></textarea></td>
	</tr>
	<tr>
		<td>รหัสไปรษณีย์</td><td><input type="text" name="ts_post" id="ts_post" size="10" value="<?php echo  $res_cus["add_post"]; ?>"></td>
	</tr>
	<tr>
		<td>ที่ติดต่อ</td>
		<td>
			<?php if(!empty($res_cus["contract_add"]) ){
				$chk_new_contract = "checked";
			}else{
				$chk_old_contract = "checked";
			} 
			?>
			<input type="radio" name="ts_chkContact" id="ts_chkContact" value="1" <?php echo $chk_old_contract; ?> >เหมือนด้านบน 
			<input type="radio" name="ts_chkContact" id="ts_chkContact" value="2" <?php echo $chk_new_contract; ?> >กรอกใหม่ 
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<div style="display:none" id="ts_divcontact">
				<textarea name="ts_contact" id="ts_contact" rows="3" cols="40"><?php echo  $res_cus["contract_add"]; ?></textarea>
			<br>
			<label> รหัสไปรษณีย์ </label>&nbsp;&nbsp; <input type="text" name="ts_contract_post" id="ts_contract_post" size="15" value="<?php echo  $res_cus["contract_post"]; ?>">
			</div> 
		</td>
	</tr>
	<tr>
		<td>โทรศัพท์</td><td><input type="text" name="ts_phone" id="ts_phone" size="30" value="<?php echo  $res_cus["telephone"]; ?>"></td>
		<td></td><td></td>
	</tr>
	<tr>
		<td>ลักษณะขายสินค้า</td>
		<td>
	<select name="cb_type" id="cb_type">
	  <option value="P" <?php echo $str_p_sl; ?>>สินค้าสำเร็จรูป</option>
	  <option value="M" <?php echo $str_m_sl; ?>> วัตถุดิบเริ่มต้น</option>
	  <option value="B" <?php echo $str_b_sl; ?>>ทั้ง 2 ประเภท</option>
	</select> 
		</td>
	</tr>
	
	</table>
		
		</td>
		<td width="40%" valign="top">
		
	<table cellpadding="3" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="110">สัญชาติ</td><td><input type="text" name="ts_reg" id="ts_reg" size="10" value="<?php echo  $res_cus["nationality"]; ?>"></td>
	</tr>
	<tr>
		<td>วันเกิด</td><td><input type="text" name="ts_barthdate" id="ts_barthdate" size="10" value="<?php echo $res_cus["birth_date"]; ?>"></td>
	</tr>
	<tr>
		<td>บัตรที่ใช้แสดงตัว</td>
		<td>
	<select name="ts_combo_cardtype" id="ts_combo_cardtype">
	  <option value="บัตรประชาชน"<?php if($res_cus["card_type"]=='บัตรประชาชน'){echo "selected";} ?>>บัตรประชาชน</option>
	  <option value="บัตรข้าราชการ"<?php if($res_cus["card_type"]=='บัตรข้าราชการ'){echo "selected";} ?>>บัตรข้าราชการ</option>
	  <option value="บัตรผู้เสียภาษีอากร"<?php if($res_cus["card_type"]=='บัตรผู้เสียภาษีอากร'){echo "selected";} ?>>บัตรประจำตัวผู้เสียภาษีอากร</option>
	  <option value="ใบขับขี่"<?php if($res_cus["card_type"]=='ใบขับขี่'){echo "selected";} ?>>ใบขับขี่</option>
	  <option value="อื่นๆ"<?php if($res_cus["card_type"]=='อื่นๆ'){echo "selected";} ?>>อื่นๆ</option>
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

<div style="text-align:right; margin-top:10px">
<input type="button" id="btn_update" name="btn_update" value="บันทึก">
<input type="hidden" id="chk_vender" name="chk_vender" >
</div>


<script>
	$("#ts_barthdate, #ts_carddate").datepicker({
		showOn: 'button',
		buttonImage: '../images/calendar.gif',
		buttonImageOnly: true,
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd'
	});

	 if($('input[id=ts_cus_type]:checked').val() == "2" ){
		$('#span_branch').show('fast');
	 }else{
		$('#span_branch').hide('fast');
	 }
	 
	  if($('input[id=ts_chkContact]:checked').val() == "2" ){
		$('#ts_divcontact').show('fast');
	 }else{
		$('#ts_divcontact').hide('fast');
	 }
		
	$("input[name='ts_chkContact']").change(function(){
		if( $('input[id=ts_chkContact]:checked').val() == "2" ){
			$('#ts_divcontact').show('fast');
		}else{
			$('#ts_divcontact').hide('fast');
			$('#ts_contact').val('');
			$('#ts_contract_post').val('');
		}
	});

	$("input[name='ts_cus_type']").change(function(){
		if( $('input[id=ts_cus_type]:checked').val() == "2" ){
			$('#span_branch').show('fast');
		}else{
			$('#span_branch').hide('fast');
			$('#ts_branch_id').val('');
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

		var chk = 0;
		var errorMessage = "";
		 if(isNaN($('#ts_branch_id').val()) == true){
			errorMessage +="สาขาที่ ต้องเป็นตัวเลขเท่านั้น";
			chk++;
		}else if($('#ts_firstname').val() == ""){
			errorMessage +="กรุณาระบุ  ชื่อ";
			chk++;
		}else if($('#ts_lastname').val() == "" ){
			errorMessage +="กรุณาระบุ  นามสกุล";
			chk++;
		}else if($('#ts_address').val() == ""){
			errorMessage +="กรุณาระบุ  ที่อยู่ตาม ภ.พ. 20";
			chk++;
		}else if($('#ts_cardno').val() == ""){
			errorMessage +="กรุณาระบุ  เลขที่บัตร";
			chk++;
		}else if($('#cb_type').val() == "P"){
			if($('#alpha').val() == ""){
				errorMessage +="กรุณาระบุ รหัสย่อบริษัท";
				chk++;
			}
		}
		
		var confirmMsg = '';
		if($('#chk_vender').val() == '1'){
			confirmMsg = 'รหัสย่อบริษัท คือ '+$('#alpha').val()+' ต้องการบันทึกข้อมูลหรือไม่';
		}else{
			confirmMsg = 'รหัสย่อบริษัท คือ '+$('#alpha').val()+' รหัสซ้ำ! ต้องการบันทึกข้อมูลหรือไม่';
		}
	
		if(chk>0){
			alert(errorMessage);
			return false;
		}
			var r = confirm(confirmMsg);
		if(r == true){
			$.post('vender_api.php',{
			cmd: 'update',
			m_cusid: '<?php echo $id; ?>',
			ts_cus_type: $('input[id=ts_cus_type]:checked').val(),
			ts_name: $('#ts_name').val(),
			ts_branch_id: $('#ts_branch_id').val(),
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
			ts_combo_cardtype: $("#ts_combo_cardtype").val(),
			ts_cardother: $('#ts_cardother').val(),
			ts_cardno: $('#ts_cardno').val(),
			ts_carddate: $('#ts_carddate').val(),
			ts_cardby: $('#ts_cardby').val(),
			ts_job: $('#ts_job').val(),
			cb_type: $('#cb_type').val(),
			ts_contract_post: $('#ts_contract_post').val(),
			ts_aphas: $('#alpha').val()
		},
		function(data){
			if(data.success){
				alert(data.message);
				location.reload();
			}else{
				alert(data.message);
			}
		},'json');
		}else{
			return false;
		}
	});
function check_vender(){
	$.post('chkdata.php',{
		txtalpha: $('#alpha').val()
	},function(data){
		if(data == 't'){
			$('#chk_vender').val('1');
		}else if(data == 'f'){
			$('#chk_vender').val('0');
		}
	});
}
</script>
<?php
}
elseif($cmd == "update"){
	$ts_cus_type = $_POST['ts_cus_type'];
	$ts_branch_id = checknull($_POST['ts_branch_id']);
	$ts_name=$_POST['ts_name'];
	$ts_cusid=$_POST['m_cusid'];
	$ts_pre_name=$_POST['ts_pre_name'];
	$ts_firstname=$_POST['ts_firstname'];
	$ts_lastname=$_POST['ts_lastname'];
	$ts_address=$_POST['ts_address'];
	$ts_post=$_POST['ts_post'];
	$chkContact=$_POST['chkContact'];
	$ts_contact=$_POST['ts_contact'];
	$ts_phone=$_POST['ts_phone'];

	$ts_reg=$_POST['ts_reg'];
	$ts_barthdate=$_POST['ts_barthdate']; $ts_barthdate = ($ts_barthdate != "") ? "'$ts_barthdate'" : "DEFAULT";
	$ts_combo_cardtype=$_POST['ts_combo_cardtype'];
	$ts_cardother=$_POST['ts_cardother'];
	$ts_cardno=$_POST['ts_cardno'];
	$ts_carddate=$_POST['ts_carddate']; $ts_carddate = ($ts_carddate != "") ? "'$ts_carddate'" : "DEFAULT";
	$ts_cardby=$_POST['ts_cardby'];
	$ts_job=$_POST['ts_job'];

	$cb_type = $_POST['cb_type'];
	$ts_contract_post = $_POST['ts_contract_post'];
	$ts_aphas = $_POST['ts_aphas'];
	
	pg_query("BEGIN WORK");
	$status = 0;
	$ts_error = array();

	if($chkContact == 1){ $str_contact = $ts_address; }else{ $str_contact = $ts_contact; }
	if($ts_combo_cardtype != "อื่นๆ"){ $str_card_id = $ts_cardno; }else{ $str_card_id = $ts_cardother; }

	$in_qry="UPDATE \"Customers\" SET pre_name = '$ts_pre_name',cus_name = '$ts_firstname',surname = '$ts_lastname',address = '$ts_address',
									  add_post = '$ts_post',nationality = '$ts_reg',birth_date = $ts_barthdate,card_type = '$ts_combo_cardtype',
									  card_id = '$str_card_id',card_do_date = $ts_carddate,card_do_by = '$ts_cardby',job = '$ts_job',
									  contract_add = '$str_contact',telephone = '$ts_phone',branch_id = $ts_branch_id,contract_post = '$ts_contract_post',
									  cus_type = '$ts_cus_type'
					WHERE cus_id = '$ts_cusid' ";
							
	if(!$res=pg_query($in_qry)){
		$ts_error[] = "บันทึก Customers ไม่สำเร็จ  ";
		$status++;
	}

	$in_qry2="UPDATE \"Venders\" SET type_ven='$cb_type',alphas='$ts_aphas' where cus_id = '$ts_cusid' ";
	if(!$res2=pg_query($in_qry2)){
		$ts_error[] = "บันทึก Venders ไม่สำเร็จ ";
		$status++;
	}

	if($status == 0){
		pg_query("COMMIT");
		$data['success'] = true;
		$data['message'] = "แก้ไขเรียบร้อยแล้ว";
	}else{
		pg_query("ROLLBACK");
		$data['success'] = false;
		$data['message'] = "ไม่สามารถแก้ไขได้! $ts_error[0]";
	}
		
	echo json_encode($data);
}
?>
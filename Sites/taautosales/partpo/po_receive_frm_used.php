<?php
if($id != "") {

	$qry = pg_query("SELECT * FROM \"PurchaseOrderDetails\" WHERE auto_id='$id' AND cancel='FALSE' ");
	if($res = pg_fetch_array($qry)){
		$auto_id = $res['auto_id'];
		$po_id = $res['po_id'];
		$product_id = $res['product_id'];
		$product_cost = $res['product_cost'];
		$vat = $res['vat'];
		$unit = $res['unit'];
    
		$cost_val = round($product_cost/$unit,2);
		$cost_vat = round($vat/$unit,2);
    
		$qry_name = pg_query("SELECT name,link_table FROM \"Products\" WHERE product_id='$product_id' ");
		if($res_name = pg_fetch_array($qry_name)){
			$product_name = $res_name['name'];
			$link_table = $res_name['link_table'];
		}
	}
}
?>
	<div>
		<table cellpadding="5" cellspacing="0" border="0" width="100%">
			<tr>
				<td width="25%"><b>Product ID :</b></td><td width="30%"><?php echo $product_id; ?></td>
				<td width="20%"><b>Name :</b></td><td width="35%"><?php echo $product_name; ?></td>    
			</tr>
			<tr>
				<td>เลขตัวถัง :</td><td><input type="text" name="txt_carnum" id="txt_carnum" style="width:200px" onkeyup="check_car_mar_num();" onkeypress="check_car_mar_num();"></td>
				<td>เลขเครื่อง :</td><td><input type="text" name="txt_marnum" id="txt_marnum" style="width:200px" onkeyup="check_car_mar_num();" onkeypress="check_car_mar_num();"></td>
			</tr>
			<tr>
				<td>ทะเบียน :</td><td><input type="text" name="txt_licenseplate" id="txt_licenseplate" style="width:100px"></td>
				<td>สีรถ :</td>
				<td>
					<select name="txt_color" id="txt_color">
						<option value="">กรุณาเลือกสี</option>
						<?php 
							$qry_color = pg_query("select * from \"CarColor\" order by color_name");
							while($res = pg_fetch_array($qry_color)){
							
								$color_id = $res['color_id'];
								$color_name = $res['color_name'];
								
								echo "<option value=\"$color_id\">$color_name</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td >ปีรถ:</td><td colspan="3"><input type="text" name="txt_years" id="txt_years" style="width:100px" onkeypress="check_num(event)" ></td>
			</tr>
			<tr>
				<td>สถานที่รับสินค้า :</td>
				<td>
					<select name="combo_wh" id="combo_wh">
				<?php
					$qry_wh = pg_query("SELECT * FROM \"Warehouses\" WHERE cancel='FALSE' ORDER BY wh_name ASC");
					while( $res_wh = pg_fetch_array($qry_wh) ){
						$wh_id = $res_wh['wh_id'];
						$wh_name = $res_wh['wh_name'];
				?>
					<option value="<?php echo $wh_id; ?>" <?php if($wh_id == 1){ echo "selected"; } ?>><?php echo $wh_name; ?></option>
				<?php
					}
				?>
					</select>
				</td>
				<td>เลขวิทยุ :</td><td><input type="text" name="txt_radio" id="txt_radio" style="width:200px"></td>
			</tr>
			<tr>
				<td>วันจดทะเบียน :</td><td><input type="text" name="date_regis" id="date_regis" style="width:100px"></td>
				<td>จดทะเบียน<br>โดยจังหวัด :</td><td><input type="text" name="province_regis" id="province_regis" style="width:200px"></td>
			</tr>
			<tr>
				<?php if($potype == "PORT") {?>
				<td>มูลค่ารถยนต์<br>ที่เหลือคืน  :</td><td><input type="text" name="car_value" id="car_value" style="width:200px" onkeypress="check_num(event)"></td>
				<?php } ?>
				<?php if($potype != "POUS") { ?>
				<td>ผู้เช่าซื้อ/<br>ผู้สละสิทธิ์  :</td><td><input type="text" name="txt_hire" id="txt_hire" style="width:200px"></td>
				<?php } ?>
			</tr>
			<tr>
				<td colspan="4">
					<div id="divnewcus" style="margin-top:15px; display:none">
	
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
						<td>
							<input type="radio" name="rdo_reg_address" id="rdo_reg_address" value="1" checked> ตามที่อยู่ตามบัตรประชาชน
							<input type="radio" name="rdo_reg_address" id="rdo_reg_address" value="2">กรอกใหม่
						</td>
						</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="3">
							<div id="div_reg_address" style="display:none">
								<label>ที่อยู่</label><textarea name="txt_address_reg" id="txt_address_reg" rows="1" cols="1" style="width:300px; height:70px"></textarea><br>
								<label>รหัสไปรษณีย์</label> <input type="text" name="txt_post_reg" id="txt_post_reg" size="15">
							</div>
						</td>
					</tr>
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
								<tr><td>ที่อยู่</td><td><textarea name="txt_contact" id="txt_contact" rows="3" cols="40"></textarea></td></tr>
								<tr><td>รหัสไปรษณีย์</td><td><input type="text" name="txt_post_contract" id="txt_post_contract" size="10"></td></tr>
							</table>
						</div>
						</td>
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
				</td>
			</tr>
			<tr>
				<td>พนักงานขาย :</td><td><input type="text" name="txt_sale" id="txt_sale" style="width:200px"></td>
				<td>พยาน :</td><td><input type="text" name="txt_attestor" id="txt_attestor" style="width:200px"></td>
			</tr>
			<tr>
				<?php if($potype != "POUS") { ?>
				<td>วันที่ทำสัญญาเช่าซื้อ :</td><td><input type="text" name="txt_condate" id="txt_condate" style="width:100px"></td>
				<?php } ?>
				<td>หมายเหตุ :</td><td><textarea name="note" id="note" cols="30"></textarea></td>
			</tr>
			
		</table>	
		
		<div style="text-align:right; margin-top:10px">
			<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
			<input type="hidden" name="potype" id="potype" value="<?php echo $potype; ?>" />
			<input type="hidden" name="chk_car_mar" id="chk_car_mar" >
		</div>
	</div>
	
<script>
function popU(U,N,T) {
    newWindow = window.open(U, N, T);
}
 $("#date_regis").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
});
 $("#txt_condate").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
});

$('#btnSubmit').click(function(){
	var chk = 0;
	var Error = "-- ข้อมูลไม่ครบ! -- \n";
	var str_txt_hire = $('#txt_hire').val();
	var str_txt_sale = $('#txt_sale').val();
	var str_txt_attestor = $('#txt_attestor').val();
	var array_txt_sale = str_txt_sale.split("#"); 
	var array_txt_attestor = str_txt_attestor.split("#"); 
	
/*	alert(str_txt_hire);
	var chk_sale_code = isNaN(str_txt_sale);
    alert(array_txt_sale[1]);
	alert(chk_sale_code);
	if(chk_sale_code == true){
		Error += 'กรุณาระบุชื่อพนักงานขาย...จากฐานข้อมูล \n';
		chk++;
	}  */
	
	if(array_txt_sale[1] == undefined){
		Error += 'กรุณาระบุชื่อพนักงานขาย...จากฐานข้อมูล \n';
		chk++;
	}
	
	if(array_txt_attestor[1] == undefined){
		Error += 'กรุณาระบุชื่อพยาน...จากฐานข้อมูล \n';
		chk++;
	}
	
	if($('#txt_carnum').val() == ""){
		Error += 'กรุณาระบุเลขตัวถัง \n';
		chk++;
	} 
	
	if($('#txt_marnum').val() == ""){
		Error += 'กรุณาระบุเลขเลขเครื่อง \n';
		chk++;
	}
	
	if($('#txt_color').val() == ""){
		Error += 'กรุณาระบุสีรถ \n';
		chk++;
	}
	if($('#txt_licenseplate').val() == ""){
		Error += 'กรุณาระบุเลขทะเบียนรถ \n';
		chk++;
	}
	if($('#date_regis').val() == ""){
		Error += 'กรุณาระบุวันที่จดทะเบียน\n';
		chk++;
	}
	if($('#province_regis').val() == ""){
		Error += 'กรุณาระบุจังหวัดที่จดทะเบียน\n';
		chk++;
	}
	if($('#potype').val() != "POUS"){
		if($('#txt_hire').val() == ""){
			Error += 'กรุณาระบุผู้เช่าซื้อ/ผู้สละสิทธิ์\n';
			chk++;
		}
	
		if(str_txt_hire != "ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่"){
			if (str_txt_hire.substring(0,3) != "CUS"){
				Error += 'กรุณาระบุผู้เช่าซื้อ/ผู้สละสิทธิ์จากฐานข้อมูล\n';
				chk++;
			}
		}
	}
	if($('#txt_sale').val() == ""){
		Error += 'กรุณาระบุพนักงานขาย \n';
		chk++;
	}
	if($('#txt_attestor').val() == ""){
		Error += 'กรุณาระบุพยาน\n';
		chk++;
	}
	if($('#potype').val() != "POUS"){
		if($('#txt_condate').val() == ""){
			Error += 'กรุณาระบุวันที่ทำสัญญาเช่าซื้อ\n';
			chk++;
		}
	}
	if($('#note').val() == ""){
		Error += 'กรุณาระบุหมายเหตุ \n';
		chk++;
	}
	 if($('#divnewcus').is(":visible")){
		if($('#txt_pre_name').val() == ""){
			Error += 'กรุณาระบุคำนำหน้าชื่อ \n';
			chk++;
		}
		
		if($('#txt_post').val()== ""){
			Error +='กรุณาระบุรหัสไปรษณีย์  \n';
			chk++;
		}else{
			if(isNaN($('#txt_post').val()) == true){
				Error += 'รหัสไปรษณีย์ต้องเป็นตัวเลขเท่านั้น \n';
				chk++;
			}
		}
		
		var birthday = $("#txt_barthdate").val();
		
		if(birthday == ""){
			Error += 'กรุณาระบุข้อมูลวันเกิดด้วย \n';
			chk++;
		}else{
			if(chkDate(birthday) == 0){
				Error += 'ข้อมูลวันเกิดไม่ถูกต้อง \n';
				chk++;
			}
		}
		
		var carddate = $("#txt_carddate").val();
		
		if(carddate == ""){
			Error += 'กรุณาระบุข้อมูลวันที่ออกบัตรด้วย \n';
			chk++;
		}else{
			if(chkDate(carddate) == 0){
				Error += 'ข้อมูลวันที่ออกบัตรไม่ถูกต้อง \n ';
				chk++;
			}
		}
		
		if($('#txt_cardno').val()== ""){
				Error += 'กรุณาระบุ เลขที่บัตร \n';
				chk++;
		}
				
		/*
		 if( $('input[id=rdo_reg_address]:checked').val() == "2" ){
			 if($('#txt_post_reg').val()== ""){
				alert('กรุณาระบุรหัสไปรษณีที่จดทะเบียน');
				return false;
			}else{
				if(isNaN($('#txt_post_reg').val()) == true){
					alert('รหัสไปรษณีที่จดทะเบียนต้องเป็นตัวเลขเท่านั้น');
					return false;
				}
			}
		 }*/
		 
		 
		 if( $('input[id=chkContact]:checked').val() == "2" ){
			if($('#txt_post_contract').val()== ""){
				Error += 'กรุณาระบุรหัสไปรษณีที่ติดต่อ \n';
				chk++;
			}else{
				if(isNaN($('#txt_post_contract').val()) == true){
					Error += 'รหัสไปรษณีที่ติดต่อต้องเป็นตัวเลขเท่านั้น \n';
					chk++;
				}
			}
		}	
   }
		if($('#chk_car_mar').val() == '0'){
			Error += 'กรุณาตรวจสอบเลขเครื่องหรือเลขตัวถังซ้ำ\n';
			chk++;
		}
	if(chk>0){
		alert(Error);
	}else {
    $.post('po_receive_api.php',{
        cmd: 'save',
        product_id: '<?php echo $product_id; ?>',
        product_name: '<?php echo $product_name; ?>',
        po_id: '<?php echo $po_id; ?>',
        po_auto_id: '<?php echo $auto_id; ?>',
        txt_carnum: $('#txt_carnum').val(),
        txt_marnum: $('#txt_marnum').val(),
		txt_licenseplate: $('#txt_licenseplate').val(),
        txt_color: $('#txt_color').val(),
        combo_wh: $('#combo_wh').val(),
        cost_val: '<?php echo $cost_val; ?>',
        cost_vat: '<?php echo $cost_vat; ?>',
		txt_radio: $('#txt_radio').val(),
		date_regis: $('#date_regis').val(),
		province_regis: $('#province_regis').val(),
		car_value: $('#car_value').val(),
		txt_hire: $('#txt_hire').val(),
		txt_sale: $('#txt_sale').val(),
		txt_attestor: $('#txt_attestor').val(),
		note: $('#note').val(),
		txt_condate: $('#txt_condate').val(),
		txt_years: $('#txt_years').val(),
		txt_pre_name: $('#txt_pre_name').val(),
        txt_firstname: $('#txt_firstname').val(),
        txt_lastname: $('#txt_lastname').val(),
        txt_address: $('#txt_address').val(),
        txt_post: $('#txt_post').val(),
		txt_name_reg: $('#txt_name_reg').val(),
		rdo_reg_address: $('input[id=rdo_reg_address]:checked').val(),
		txt_address_reg: $('#txt_address_reg').val(),
        txt_post_reg: $('#txt_post_reg').val(),
        chkContact: $('input[id=chkContact]:checked').val(),
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
        txt_job: $('#txt_job').val()
    },
    function(data){
        if(data.length == 20 || data.length == 19){
			var re = /(?![\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3})./g;
			var caridno = data.replace(re,"");
			CloseDialogChq();
			//$('#divdialogadd').remove();
			$('#list_receive').load('po_list_receive.php',{
				condition: '<?php echo $condition; ?>',
				keyword: '<?php echo $keyword; ?>'
			});
			popU('report_receive.php?car_idno='+caridno,'','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=980,height=550');
			
	   }else{
			alert('ไม่สามารถบันทึกได้ '+ data);
        }
    });
	}
});

function CloseDialogChq(){
   // $('#divdialogadd').remove();
    location.reload();
}

function check_num(e)
{ // ให้พิมพ์ได้เฉพาะตัวเลขและจุด
    var key;
    if(window.event)
	{
        key = window.event.keyCode; // IE
		if(key <= 57 && key != 33 && key != 34 && key != 35 && key != 36 && key != 37 && key != 38 && key != 39 && key != 40 && key != 41 && key != 42
			&& key != 43 && key != 44 && key != 45 && key != 47)
		{
			// ถ้าเป็นตัวเลขหรือจุดสามารถพิมพ์ได้
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
			// ถ้าเป็นตัวเลขหรือจุดสามารถพิมพ์ได้
		}
		else
		{
			key = e.preventDefault();
		}
	}
};
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
$("input[name='chkContact']").change(function(){
    if( $('input[id=chkContact]:checked').val() == "2" ){
		$('#divcontact').show('fast');
	}else{
		$('#divcontact').hide('fast');
	}
});

//========= ซ่อนหรือแสดง div_reg_address =========//
$("input[name='rdo_reg_address']").change(function(){
    if( $('input[id=rdo_reg_address]:checked').val() == "2" ){
		$('#div_reg_address').show('fast');
	}else{
		$('#div_reg_address').hide('fast');
	}
});
$("#txt_hire").autocomplete({
        source: "po_autocomplete.php?cmd=autocomplete",
        minLength:1,
        select: function(event, ui) {
            if(ui.item.value == 'ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่'){
                $('#divnewcus').show('fast');
            }else{
                $('#divnewcus').hide('fast');
            }
        }
});
$("#txt_sale").autocomplete({
        source: "po_autocomplete.php?cmd=user_sale",
        minLength:1
});
$("#txt_attestor").autocomplete({
        source: "po_autocomplete.php?cmd=user_sale",
        minLength:1
});
 $("#txt_barthdate,#txt_carddate,txt_regis_date").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
});
function check_car_mar_num(){
	$.post('chkdata.php',{
		txtcarnum: $('#txt_carnum').val(),
		txtmarnum: $('#txt_marnum').val(),
		cmd:'usedcar'
	},function(data){
		if(data == 't'){
			$('#chk_car_mar').val('1');
		}else if(data == 'f'){
			$('#chk_car_mar').val('0');
		}
	});
}
</script>
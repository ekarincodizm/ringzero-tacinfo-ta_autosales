<?php
include_once("../include/config.php");
include_once("../include/function.php");
	$idno = pg_escape_string($_GET['idno']);
    $resid = pg_escape_string($_GET['resid']);
    $cusid = pg_escape_string($_GET['cusid']);
	// หา margin
	$qry_margin = pg_query(" SELECT * FROM setting_margin WHERE status = '1' ");
	while($res_margin = pg_fetch_array($qry_margin)){
		$margin_sale_down = $res_margin['margin_sale_down'];
		$margin_sale_cash = $res_margin['margin_sale_cash'];
		$rate_down_price = $res_margin['rate_down_price'];
		$vat = $res_margin['vat'];
	}
	
	// ตรวจสอบว่า เคบ มีการออกใบเสร็จ หรือ ยัง  ถ้าเคยมีการออก  $inv_no <> ""
	$inv_no = "";
	$qry = pg_query("SELECT * FROM \"Invoices\" WHERE cancel = 'TRUE' AND res_id = '$resid' AND substr(inv_no,3,1) = 'V' ORDER BY inv_no DESC LIMIT 1");
	while( $res = pg_fetch_array($qry) ){
		$j++;
		$inv_no = $res['inv_no'];
		$is_print = $res['is_print'];
	}
	
    $qry = pg_query("SELECT * FROM v_con_wait_acc WHERE \"IDNO\"='$idno' AND res_id='$resid' AND cus_id='$cusid' ");
    if($res = pg_fetch_array($qry)){
        $IDNO = $res['IDNO'];
        $res_id = $res['res_id'];
        $cus_id = $res['cus_id'];
        $receive_date = $res['receive_date'];
        $pre_name = $res['pre_name'];
        $cus_name = $res['cus_name'];
		$reg_customer = $res['reg_customer'];
        $surname = $res['surname'];
        $car_price = $res['car_price'];
        $down_price = $res['down_price'];
        $finance_price = $res['finance_price'];
        $license_plate = $res['license_plate'];
        $car_num = $res['car_num'];
        $car_id = $res['car_id'];
		$reserve_color = $res['reserve_color']; //สีรถที่จอง
		$takeout_car = $res['appv_stamp']; //วันที่ส่งมอบรถ
		
		$installment = $res['installment'];
		$num_install = $res['num_install'];
		
		$finance_cus_id = $res['finance_cus_id'];
		
		$cost_val = $res['cost_val'];
		$cost_vat = $res['cost_vat'];
    }
	
	//ดึงวันที่ส่งมอบรถ
	/*$qry_car_stamp = pg_query("SELECT * FROM \"CarMoveToCus\" WHERE status_appv = '1' AND res_id='RS1-14010013' ");
	 if($res = pg_fetch_array($qry_car_stamp)){
		$appv_stamp = $res['appv_stamp'];
	 }*/
	 
	//ดึง customer
	// $cus_name = GetCusName($cus_id);  ใช้ reg_customer
	//ดึง ชื่อ Finance 
 	$finance_name = GetFinance($finance_cus_id);
	
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <title><?php echo $company_name; ?> - <?php echo $page_title; ?></title>
    <LINK href="../images/styles.css" type=text/css rel=stylesheet>

    <link type="text/css" href="../images/jqueryui/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="../images/jqueryui/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="../images/jqueryui/js/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="../images/jqueryui/js/jquery.numberformatter-1.2.4.min.js"></script>
	<script type="text/javascript" src="../images/jqueryui/js/jshashset-3.0.js"></script>
	
</head>
<body>
<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr>
    <td  width="130"><b>เลขที่สัญญาส่งมอบรถ :</b></td>
	<td><b><?php echo $idno; ?></b></td>
<tr>
    <td >วันที่ส่งมอบ</td>
	<td ><?php echo date('Y-m-d',strtotime($takeout_car));  ?></td>
	<td >ทะเบียน : &nbsp;&nbsp;&nbsp; <?php echo $license_plate; ?> </td>
	<td ></td>
</tr>
</tr>
<tr>
    <td >เลขที่จอง :</td>
	<td><?php echo $res_id; ?></td>
	<td colspan="2">วันที่ : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="txt_receipt_date" id="txt_receipt_date" value="<?php echo $receive_date; ?>" size="15"   /> </td>
</tr>
<tr>
    <td >ชื่อ-สกุลลูกค้า :</td>
	<td><?php echo replace_empty_txt($reg_customer); ?></td>
	<td colspan="2">ชื่อไฟแนนซ์ :  &nbsp; <?php echo replace_empty_txt($finance_name); ?></td>
	 
</tr>
<tr>
	<td colspan="4"><div style="margin-top:5px; line-height:25px; border:1px dashed #C0C0C0; background-color:#FFFFE8"></td>
</tr>
<tr>
    <td >ราคารถ :</td>
	<td align="right"><?php echo number_format($car_price,2); ?></td>
</tr>
<tr>
    <td>เงินดาวน์ :</td>
	<td align="right"><?php echo number_format($down_price,2); ?></td>
	<td>&nbsp;&nbsp;<label>สีรถที่จอง :</label>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $reserve_color; ?></td>
	<td></td>
</tr>
<tr>
    <td >ค่างวด :</td>
	<td  align="right"><?php echo number_format($installment,2); ?></td>
	<td>&nbsp;&nbsp;<label>จำนวนงวด :</label>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $num_install; ?> </td>
</tr>
<tr>
    <td >ยอดจัดไฟแนนซ์ :</td>
	<td align="left"><input type="text" name="txt_finance_price" id="txt_finance_price" value="<?php echo number_format($finance_price,2); ?>" size="25" /></td>
	<td><input type="hidden" name="txt_vat" id="txt_vat" value="<?php echo $vat; ?>" size="25" /></td>
</tr>
<tr>
    <td >ราคาเช่าซื้อรวม :</td>
	<td align="left"><input type="text" name="txt_total_price" id="txt_total_price" value="" size="25"   onkeypress="check_num(event);"  onkeyup="javascript:a_b_change()"   /></td>
</tr>
<tr>
    <td >ราคาต้นทุนรถยนต์ :</td>
	<td><input type="text" name="txt_price" id="txt_price" value="" size="25" onkeypress="check_num(event);" onkeyup="javascript:a_b_change()"   /></td>
</tr>
<tr>
    <td  width="25%">อัตราราคาขายเปรียบเทียบ :</td>
	<td width="25%"><input type="text" name="txt_saleprice" id="txt_saleprice" value="" size="25" onkeypress="check_num(event);"  onkeyup="javascript:c_change()"  /></td>
	<td width="25%"></td>
	<td width="25%"></td>
</tr>
<tr >
	<td colspan="2" align="right" valign="top"><br>
		<fieldset>
			<legend><b> มูลค่าในใบเสร็จที่ออกให้ไฟแนนซ์ </b></legend>
				<table width="100%" border="0">
					<tr align="right">
						<td><label>มูลค่าสินค้า : </label><input type="text" name="txt_fin_cost_val" id="txt_fin_cost_val" value="" size="25"  onkeypress="check_num(event);"  onkeyup="javascript:cost_val_change()" /> บาท</td>
					</tr>
					<tr align="right">
						<td><label>ภาษีมูลค่าเพิ่ม : </label><input type="text" name="txt_fin_vat" id="txt_fin_vat" value="" size="25" onkeypress="check_num(event);" /> บาท</td>
					</tr>
					<tr align="right">
						<td><label>รวม : </label><input type="text" name="txt_fin_receipt_value_amount" id="txt_fin_receipt_value_amount" value="" size="25" onkeypress="check_num(event);" onkeyup="javascript:receipt_values_change()" /> บาท</td>
					</tr>
					<tr align="left">
						<td><br>
							<input type="checkbox" name="chk_fin_pay" id="chk_fin_pay" checked><label>ออกในนามไฟแนนซ์</label>&nbsp;&nbsp;&nbsp;&nbsp;
							<label>ชำระโดย</label>
							<input type="checkbox" name="chk_fin_pay_cash" id="chk_fin_pay_cash">&nbsp;<label>เงินสด</label>
							<input type="checkbox" name="chk_fin_pay_chq" id="chk_fin_pay_chq" checked>&nbsp;<label>เช็ค</label>
						</td>
					</tr>
					<tr align="left">
						<td>
							<input type="checkbox" name="chk_fin_r_renew" id="chk_fin_r_renew"><label>ออกแทนใบเสร็จที่ยกเลิกเลขที่</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="text" name="txt_fin_r_renew" id="txt_fin_r_renew"  size="15"  /> <br>
							<input type="checkbox" name="chk_fin_v_renew" id="chk_fin_v_renew"><label>ออกแทนใบกำกับที่ยกเลิกเลขที่</label>&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="text" name="txt_fin_v_renew" id="txt_fin_v_renew"  size="15"  />
						</td>
					</tr>
				</table>
		</fieldset>
	</td>
	<td colspan="2" align="right" valign="top"><br>
		<fieldset>
			<legend><b> มูลค่าในใบเสร็จที่ออกในนามลูกค้า</b></legend>
				<table width="100%" border="0">
					<tr align="right">
						<td><label>มูลค่าสินค้า : </label><input type="text" name="txt_cus_cost_val" id="txt_cus_cost_val" value="" size="25" onkeypress="check_num(event);"  onkeyup="javascript:cost_val_change()" /> บาท</td>
					</tr>
					<tr align="right">
						<td><label>ภาษีมูลค่าเพิ่ม : </label><input type="text" name="txt_cus_vat" id="txt_cus_vat" value="" size="25" onkeypress="check_num(event);" /> บาท</td>
					</tr>
					<tr align="right">
						<td><label>รวม : </label><input type="text" name="txt_cus_receipt_value_amount" id="txt_cus_receipt_value_amount" value="" size="25" onkeypress="check_num(event);"  onkeyup="javascript:receipt_values_change()" /> บาท</td>
					</tr>
						<tr align="left">
						<td><br>
							<input type="checkbox" name="chk_cus_pay" id="chk_cus_pay" checked>&nbsp;<label>ออกในนามลูกค้า</label>&nbsp;&nbsp;&nbsp;&nbsp;
							<label>ชำระโดย</label>
							<input type="checkbox" name="chk_cus_pay_cash" id="chk_cus_pay_cash" checked>&nbsp;<label>เงินสด</label>
							<input type="checkbox" name="chk_cus_pay_chq" id="chk_cus_pay_chq">&nbsp;<label>เช็ค</label>
						</td>
					</tr>
					<tr align="left">
						<td>
							<input type="checkbox" name="chk_cus_r_renew" id="chk_cus_r_renew"><label>ออกแทนใบเสร็จที่ยกเลิกเลขที่</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="text" name="txt_cus_r_renew" id="txt_cus_r_renew"  size="15"  /> <br>
							<div>
							<input type="checkbox" name="chk_cus_v_renew" id="chk_cus_v_renew"><label>ออกแทนใบกำกับที่ยกเลิกเลขที่</label>&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="text" name="txt_cus_v_renew" id="txt_cus_v_renew"  size="15"  />
							<!--<input type="text" name="txt_search" id="txt_search" size="60" > -->
							</div>
						</td>
					</tr>
				</table>
		</fieldset>
	</td>
</tr>
</table>
<br>
<div style="text-align:right">
	<input type="button" name="btn_save" id="btn_save" value="บันทึก"  >
</div>
</body>
</html>
<script>

$(document).ready(function(){
 $('#txt_finance_price').attr('style','background:#EEE9E9;text-align:right');
 $('#txt_total_price').attr('style','background:#EEE9E9;text-align:right');
 $('#txt_price').attr('style','background:#EEE9E9;text-align:right');
 $('#txt_saleprice').attr('style','background:#EEE9E9;text-align:right');
 $('#txt_fin_receipt_value_amount').attr('style','background:#EEE9E9;text-align:right');
 $('#txt_cus_receipt_value_amount').attr('style','background:#EEE9E9;text-align:right');
 $('#txt_fin_cost_val').attr('style','background:#EEE9E9;text-align:right');
 $('#txt_fin_vat').attr('style','background:#EEE9E9;text-align:right');
 $('#txt_cus_cost_val').attr('style','background:#EEE9E9;text-align:right');
 $('#txt_cus_vat').attr('style','background:#EEE9E9;text-align:right');
 $('#txt_finance_price').prop('disabled',true);
 
 $('#txt_fin_r_renew').attr('disabled',true);
 $('#txt_cus_r_renew').attr('disabled',true);
 $('#txt_fin_v_renew').attr('disabled',true);
 $('#txt_cus_v_renew').attr('disabled',true);
 
	// fin ให้แสดง textbox สำหรับกรอกเลขที่ใบเสร็จที่เคยยกเลิกไปแล้ว
	$("input[name='chk_fin_r_renew']").change(function(){
		if( $('input[id=chk_fin_r_renew]:checked').val() ){
			$('#txt_fin_r_renew').attr('disabled',false);
		}else{
			$('#txt_fin_r_renew').attr('disabled',true);
			$('#txt_fin_r_renew').val('');
		}
	});
	
	// cus ให้แสดง textbox สำหรับกรอกเลขที่ใบเสร็จที่เคยยกเลิกไปแล้ว
	$("input[name='chk_cus_r_renew']").change(function(){
		if( $('input[id=chk_cus_r_renew]:checked').val() ){
			$('#txt_cus_r_renew').attr('disabled',false);
		}else{
			$('#txt_cus_r_renew').attr('disabled',true);
			$('#txt_cus_r_renew').val('');
		}
	});
	
	//  fin ให้แสดง textbox สำหรับกรอกเลขที่ใบกำกับที่เคยยกเลิกไปแล้ว
	$("input[name='chk_fin_v_renew']").change(function(){
		if( $('input[id=chk_fin_v_renew]:checked').val() ){
			$('#txt_fin_v_renew').attr('disabled',false);
		}else{
			$('#txt_fin_v_renew').attr('disabled',true);
			$('#txt_fin_v_renew').val('');
		}
	});
	
	//  cus ให้แสดง textbox สำหรับกรอกเลขที่ใบกำกับที่เคยยกเลิกไปแล้ว
	$("input[name='chk_cus_v_renew']").change(function(){
		if( $('input[id=chk_cus_v_renew]:checked').val() ){
			$('#txt_cus_v_renew').attr('disabled',false);
		}else{
			$('#txt_cus_v_renew').attr('disabled',true);
			$('#txt_cus_v_renew').val('');
		}
	});
	
	var txt_res_id = '<?php echo $res_id; ?>';
	//autocomplete ของ receipt_no ในนาม finance
	$("#txt_fin_r_renew").autocomplete({
        source: "../autocomplete/autocomplete_cancel_receipt.php?cmd=cancel_receipt&res_id="+txt_res_id,
        minLength:1
    });
	
	//autocomplete ของ receipt_no ในนาม customer
	$("#txt_cus_r_renew").autocomplete({
        source: "../autocomplete/autocomplete_cancel_receipt.php?cmd=cancel_receipt&res_id="+txt_res_id,
        minLength:1
    });
	
	//autocomplete ของ invoice_no ในนาม finance
	$("#txt_fin_v_renew").autocomplete({
        source: "../autocomplete/autocomplete_cancel_receipt.php?cmd=cancel_invoice&res_id="+txt_res_id,
        minLength:1
    });
	
	//autocomplete ของ invoice_no ในนาม customer
	$("#txt_cus_v_renew").autocomplete({
        source: "../autocomplete/autocomplete_cancel_receipt.php?cmd=cancel_invoice&res_id="+txt_res_id,
        minLength:1
    });
	
	/* $("#txt_fin_r_renew").autocomplete({
        source: "../autocomplete/autocomp_cancel_receipt.php?cmd=autocomplete",
        minLength:1
      
    });*/
	
	$("#txt_receipt_date").datepicker({
    showOn: 'button',
    buttonImage: '../images/calendar.gif',
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd'
});
	
	
	cal();
	
});

/* ตรวจสอบ format number */
function checknumber($data){
	if(isNaN($data)){
		alert("กรอกได้เฉพาะตัวเลข!!!");
	}
	return false;
}

/* ใส่คอมม่าให้กับข้อมูลตัวเลข */
function addCommas(nStr)
{ // function สำหรับเพิ่มลูกน้ำ
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1))
	{
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
return x1 + x2;
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

function cal(){
	cal_a();
	cal_b();
	rate_sale_price();
	compare_saleprice();
	exclude_vat();
}

/* ถ้ามีการเปลี่ยนแปลงตัวเลข ราคาเช่าซื้อรวม กับ ราคาต้นทุนรถยนต์ ให้มีการคำนวณ อัตราราคาขายเปรียบเทียบ,มูลค่าในใบเสร็จที่ออกให้ไฟแนนซ์,มูลค่าในใบเสร็จที่ออกในนามลูกค้า */
function a_b_change(){ 
	rate_sale_price();
	compare_saleprice();
	exclude_vat();
}

function c_change(){
	compare_saleprice();
	exclude_vat();
}

function cost_val_change(){
	include_vat();
}

function receipt_values_change(){
	exclude_vat();
}

/* คำนวณราคาเช่าซื้อรวม สูตร A (ค่างวด * จำนวนงวด) */
function cal_a(){
	var total_price = 0.00;
	var installment = 0.00;
	var num_install = 0;
	installment = parseFloat('<?php echo $installment;?>');
	num_install = '<?php echo $num_install;?>';
	total_price = installment * num_install; // ราคาเช่าซื้อรวม
	
	$('#txt_total_price').val( addCommas( parseFloat(total_price).toFixed(2)) );
}

/* คำนวณราคาต้นทุนรถยนต์ สูตร B (cost_val + cost_vat) */
function cal_b(){
	var price = 0.00,cost_val = 0.00,cost_vat = 0.00;
	cost_val = '<?php echo $cost_val;?>';
	cost_vat = '<?php echo $cost_vat;?>';
	price = parseFloat( cost_val.replace(/,/g, '') ) + parseFloat( cost_vat.replace(/,/g, '') );
	$('#txt_price').val( addCommas( parseFloat(price).toFixed(2)) );
}

/* คำนวณอัตราราคาขายเปรียบเทียบ  สูตร C ( ((A-D)*65%)+D ) */
function rate_sale_price(){
	var rate_sale_price = 0.00;
	var a = parseFloat( $('#txt_total_price').val().replace(/,/g, '') ); // ราคาเช่าซื้อรวม (A)
	var d = parseFloat( $('#txt_finance_price').val().replace(/,/g, '') ); //ยอดจัดไฟแนนซ์ (D)
		
	if ( isNaN(a) || a == ""){a = 0;}else{a = a;}
	if ( isNaN(d) || d == ""){d = 0;}else{d = d;}
	
	rate_sale_price = ((a-d)*0.65)+d; 
	
	$('#txt_saleprice').val( addCommas( parseFloat(rate_sale_price).toFixed(2)) );
}

/* เปรียบเทียบ C กับ B */
function compare_saleprice(){
	var fin_receipt_values = 0, cus_receipt_values = 0;
	var num_install = parseInt('<?php echo $num_install;?>');
	var margin_sale_down = parseFloat('<?php echo $margin_sale_down; ?>');
	var margin_sale_cash = parseFloat('<?php echo $margin_sale_cash; ?>');
	var rate_down_price = parseFloat('<?php echo $rate_down_price; ?>');
	var reserve_color = '<?php echo $reserve_color; ?>';
	var a = parseFloat( $('#txt_total_price').val().replace(/,/g, '') ); // ราคาเช่าซื้อรวม (A)
	var b = parseFloat( $('#txt_price').val().replace(/,/g, '') ) ; //ราคาต้นทุนรถยนต์ (B)
	var c = parseFloat( $('#txt_saleprice').val().replace(/,/g, '') ); //อัตราราคาเปรียบเทียบ (C)
	var d = parseFloat('<?php echo $down_price; ?>'); // downprice
	if(num_install != 0){ // กรณีขายผ่อน
		if(c > b){
			if(d == 0){
			fin_receipt_values = (b + margin_sale_down) ;
			cus_receipt_values = 0; }
			else
			{
			fin_receipt_values = (b + margin_sale_down) - rate_down_price ;
			cus_receipt_values = rate_down_price; }
		}else if(c <= b){
			fin_receipt_values = c;
			cus_receipt_values =  (b + margin_sale_down) - c;
		}
	}else{ //กรณีขายสด
		fin_receipt_values = b + margin_sale_cash;
		cus_receipt_values = 0;
	}// กรณีขายสดสีฟ้า ออก ไฟแนนซ์   สีอื่น ออก ลูกค้า 
	if( num_install == 0 && reserve_color != 'ฟ้า'){
		$('#txt_fin_receipt_value_amount').val( addCommas( parseFloat(cus_receipt_values).toFixed(2)) );
		$('#txt_cus_receipt_value_amount').val( addCommas( parseFloat(fin_receipt_values).toFixed(2)) );
	}else{
		$('#txt_fin_receipt_value_amount').val( addCommas( parseFloat(fin_receipt_values).toFixed(2)) );
		$('#txt_cus_receipt_value_amount').val( addCommas( parseFloat(cus_receipt_values).toFixed(2)) );
	}
}

/*  ถอด vat */
function exclude_vat(){
	var fin_cost_val = 0, cus_cost_val = 0, fin_receipt_values = 0, cus_receipt_values = 0;
	var fin_vat = 0, cus_vat = 0;
	db_vat = parseFloat($('#txt_vat').val());
	fin_receipt_values = parseFloat( $('#txt_fin_receipt_value_amount').val().replace(/,/g, '') ); //รวม
	cus_receipt_values = parseFloat( $('#txt_cus_receipt_value_amount').val().replace(/,/g, '') );
	
	if(isNaN(db_vat) || db_vat == ""){ db_vat = 0; }else{ db_vat = db_vat;}
	if(isNaN(fin_receipt_values) || fin_receipt_values == "" ){fin_receipt_values = 0;}else {fin_receipt_values = fin_receipt_values;}
	if(isNaN(cus_receipt_values) || cus_receipt_values == "" ){cus_receipt_values = 0;}else {cus_receipt_values = cus_receipt_values;}
	
	
	//fin_vat = (fin_receipt_values * db_vat)/(db_vat*100);
	//cus_vat = (cus_receipt_values * db_vat)/(db_vat*100);
	//fin_cost_val = (fin_receipt_values * 100/(db_vat+100) );
	
	fin_cost_val =  ( (fin_receipt_values * 100)/(db_vat+100) );
	cus_cost_val = ( (cus_receipt_values * 100)/(db_vat+100) );
	
	fin_vat	 = fin_receipt_values - fin_cost_val;
	cus_vat	 = cus_receipt_values - cus_cost_val;

	$('#txt_fin_cost_val').val( addCommas( parseFloat(fin_cost_val).toFixed(2)) );
	$('#txt_cus_cost_val').val( addCommas( parseFloat(cus_cost_val).toFixed(2)) );
	$('#txt_fin_vat').val( addCommas( parseFloat(fin_vat).toFixed(2)) );
	$('#txt_cus_vat').val( addCommas( parseFloat(cus_vat).toFixed(2)) );
}

/*  รวม Vat */
function include_vat(){
	var fin_cost_val = 0, cus_cost_val = 0, fin_receipt_values = 0, cus_receipt_values = 0;
	var fin_vat = 0, cus_vat = 0;
	db_vat = parseFloat($('#txt_vat').val());
	fin_cost_val = parseFloat( $('#txt_fin_cost_val').val().replace(/,/g, '') );
	cus_cost_val = parseFloat( $('#txt_cus_cost_val').val().replace(/,/g, '') );
	
	if(isNaN(db_vat) || db_vat == ""){ db_vat = 0; }else{ db_vat = db_vat;}
	if(isNaN(fin_cost_val) || fin_cost_val == ""){ fin_cost_val = 0; }else{ fin_cost_val = fin_cost_val;}
	if(isNaN(cus_cost_val) || cus_cost_val == ""){ cus_cost_val = 0; }else{ cus_cost_val = cus_cost_val;}
	
	
	fin_vat = (fin_cost_val * db_vat)/100; // หา vat
	cus_vat = (cus_cost_val * db_vat)/100; // หา vat
	
	fin_receipt_values = fin_cost_val + fin_vat;
	cus_receipt_values = cus_cost_val + cus_vat;

	$('#txt_fin_vat').val( addCommas( parseFloat(fin_vat).toFixed(2)) );
	$('#txt_cus_vat').val( addCommas( parseFloat(cus_vat).toFixed(2)) );
	$('#txt_fin_receipt_value_amount').val( addCommas( parseFloat(fin_receipt_values).toFixed(2)) );
	$('#txt_cus_receipt_value_amount').val( addCommas( parseFloat(cus_receipt_values).toFixed(2)) );
}

$('#btn_save').click(function(){
	
	
	
	
	if($('#txt_finance_price').val() == ""){
		alert('กรุณาระบุ  ยอดจัดไฟแนนซ์');
		return false;
	}else if($('#txt_total_price').val() == ""){
		alert('กรุณาระบุ  ราคาเช่าซื้อรวม');
		return false;
	}else if($('#txt_price').val() == ""){
		alert('กรุณาระบุ  ราคาต้นทุนรถยนต์');
		return false;
	}else if($('#txt_price').val() == ""){
		alert('กรุณาระบุ  อัตราราคาขายเปรียบเทียบ');
		return false;
	}else if($('#txt_fin_receipt_value_amount').val() == ""){
		alert('กรุณาระบุ  มูลค่ารวมในใบเสร็จที่ออกให้ไฟแนนซ์');
		return false;
	}else if($('#txt_cus_receipt_value_amount').val() == ""){
		alert('กรุณาระบุ  มูลค่ารวมในใบเสร็จที่ออกให้ลูกค้า');
		return false;
	}else if($('#txt_fin_cost_val').val() == ""){
		alert('กรุณาระบุ  มูลค่าสินค้าในใบเสร็จที่ออกให้ไฟแนนซ์');
		return false;
	}else if($('#txt_cus_cost_val').val() == ""){
		alert('กรุณาระบุ  มูลค่าสินค้าในใบเสร็จที่ออกให้ลูกค้า');
		return false;
	}else if($('#txt_fin_vat').val() == ""){
		alert('กรุณาระบุ  ภาษีมูลค่าเพิ่มในใบเสร็จที่ออกให้ไฟแนนซ์');
		return false;
	}else if($('#txt_cus_vat').val() == ""){
		alert('กรุณาระบุ  ภาษีมูลค่าเพิ่มในใบเสร็จที่ออกให้ลูกค้า');
		return false;
	}
	
	var fin_money_way = "";
	var cus_money_way = "";
	var fin_pay_cash = $('input[id=chk_fin_pay_cash]:checked').val();
	var fin_pay_chq = $('input[id=chk_fin_pay_chq]:checked').val();
	var cus_pay_cash = $('input[id=chk_cus_pay_cash]:checked').val();
	var cus_pay_chq = $('input[id=chk_cus_pay_chq]:checked').val();
	
	if( (fin_pay_cash == 'on') && (fin_pay_chq == 'on') ){
		fin_money_way = 'CQ'; //เงินสดและเช็ค
	}else if( (fin_pay_cash == 'on') && (fin_pay_chq != 'on') ){
		fin_money_way ='CA'; //เงินสด
	}else if( (fin_pay_cash != 'on') && (fin_pay_chq == 'on') ){
		fin_money_way ='SA'; //เช็ค
	}else{
		alert('ยังไม่ได้เลือกวิธีการชำระเงิน ที่ออกในนามไฟแนนซ์!!!');
		return false;
	}
	
	if( (cus_pay_cash == 'on') && (cus_pay_chq == 'on') ){
		cus_money_way = 'CQ'; //เงินสดและเช็ค
	}else if( (cus_pay_cash == 'on') && (cus_pay_chq != 'on') ){
		cus_money_way ='CA'; //เงินสด
	}else if( (cus_pay_cash != 'on') && (cus_pay_chq == 'on') ){
		cus_money_way ='SA'; //เช็ค
	}else{
		alert('ยังไม่ได้เลือกวิธีการชำระเงิน ที่ออกในนามลูกค้า!!!');
		return false;
	}
	
	var txt_inv_no = '<?php echo $inv_no; ?>';
	var txt_fin_amount = $('#txt_fin_receipt_value_amount').val() ;
	var txt_cus_amount = $('#txt_cus_receipt_value_amount').val() ;
	
	//alert($('#txt_fin_r_renew').val());
	//alert($('#txt_fin_v_renew').val());
				
				
	if( txt_fin_amount!= 0){
		if( $('input[id=chk_fin_r_renew]:checked').val() == 'on' ){
		if( $('#txt_fin_r_renew').val() == ""  ){
			alert('กรุณาระบุ  เลขที่ใบเสร็จรับเงินเดิม');
			return false;
		}
		} else if(txt_inv_no != "") {
			alert('กรุณาระบุ  เลขที่ใบเสร็จรับเงินเดิม เนื่องจากเคยมีการยกเลิก');
			return false;
		}
	
		if( $('input[id=chk_fin_v_renew]:checked').val() == 'on' ){
		if( $('#txt_fin_v_renew').val() == "" ){
			alert('กรุณาระบุ  เลขที่ใบกำกับภาษีเดิม');
			return false;
		}
		}else if(txt_inv_no != "") {
			alert('กรุณาระบุ  เลขที่ใบกำกับภาษีเดิม เนื่องจากเคยมีการยกเลิก');
			return false;
		}
	}

	if(txt_cus_amount != 0){
		if( $('input[id=chk_cus_r_renew]:checked').val() == 'on' ){
			if( $('#txt_cus_r_renew').val() == ""  ){
				alert('กรุณาระบุ  เลขที่ใบเสร็จรับเงินเดิม');
				return false;
			}
		} else if(txt_inv_no != "") {
				alert('กรุณาระบุ  เลขที่ใบเสร็จรับเงินเดิม เนื่องจากเคยมีการยกเลิก');
				return false;
		}
		
		if( $('input[id=chk_cus_v_renew]:checked').val() == 'on' ){
			if( $('#txt_cus_v_renew').val() == ""){
				alert('กรุณาระบุ  เลขที่ใบกำกับภาษีเดิม');
				return false;
			}
		}else if(txt_inv_no != "") {
				alert('กรุณาระบุ  เลขที่ใบกำกับภาษีเดิม เนื่องจากเคยมีการยกเลิก');
				return false;
		}
	}
	
			
				
	
	$('body').append('<div id="divdialogconfirm"></div>');
		$("#divdialogconfirm").text('ต้องการบันทึกใบเสร็จ-ใบกำกับใช่หรือไม่ ?');
		$("#divdialogconfirm").dialog({
			title: 'ยืนยัน',
			resizable: false,
			height:140,
			modal: true,
			buttons:{
				"ใช่": function(){
				$.post('save_receipt.php',{
				cmd: 'save',
				idno: '<?php echo $idno; ?>',
				receive_date: $('#txt_receipt_date').val(),
				car_num: '<?php echo $car_num; ?>',
				cost_val: '<?php echo $cost_val; ?>',
				fin_receipt_value_amount: $('#txt_fin_receipt_value_amount').val(),
				cus_receipt_value_amount: $('#txt_cus_receipt_value_amount').val(),
				fin_cost_val: $('#txt_fin_cost_val').val(),
				cus_cost_val: $('#txt_cus_cost_val').val(),
				fin_vat: $('#txt_fin_vat').val(),
				cus_vat: $('#txt_cus_vat').val(),
				num_install: '<?php echo $num_install; ?>',
				reserve_color: '<?php echo $reserve_color; ?>',
				fin_money_way: fin_money_way,
				cus_money_way: cus_money_way,
				fin_r_renew: $('#txt_fin_r_renew').val(),
				fin_v_renew: $('#txt_fin_v_renew').val(),
				cus_r_renew: $('#txt_cus_r_renew').val(),
				cus_v_renew: $('#txt_cus_v_renew').val()
				},
				function(data){
				if(data.success){
					alert(data.message);
					print_doc(data.receipt_no,data.invoice_no);
					print_logs(data.receipt_no);
					print_logs(data.invoice_no);
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
    
		
});

function print_logs(doc_no){
	var str_doc_no = doc_no
	    $.post('save_receipt_reprint_api.php',{
        cmd: 'save_tax_rec_reprint_reason',
		doc_no: str_doc_no,
		reason: ""
    },
    function(data){
        if(data.success){
            //alert(data.message);
			$('#div_dialog').remove();
			//print_doc_new(str_doc_no);
        }else{
            //alert(data.message);
        }
    },'json');
}

function print_doc(receipt_no,invoice_no){
    $('body').append('<div id="div_prt"></div>');
    $('#div_prt').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br/><br/><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์ใบเสร็จรับเงิน\" onclick=\"javascript:window.open('../report/receipt_pdf.php?receipt_no="+receipt_no+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600');\"> <input type=\"button\" name=\"btn_print_receive\" id=\"btn_print_receive\" value=\"พิมพ์ใบกำกับภาษี\" onclick=\"javascript:window.open('../report/tax_invoice_pdf.php?invoice_no="+invoice_no+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); \">  </div>");
    $('#div_prt').dialog({
        title: 'พิมพ์เอกสาร  ',
        resizable: false,
        modal: true,  
        width: 300,
        height: 150,
        close: function(ev, ui){
            $('#div_prt').remove();
			location.reload();
        }
    });
}

function CloseDialogChq(){
    $('#div_prt').remove();
    location.reload();
}
</script>
<?php

//------------------------------------------------- สั่งซื้ออุปกรณ์อื่นๆ ------------------------------------------------------------
?>

<!-- header -->

<div style="font-size:12px">

<!-- PO type -->
<div style="margin: 10px 0 5px 0">
<b>ประเภทของ PO :</b>
<select name="type" id="type">
	<option value="1">สั่งซื้อของใหม่ (จาก Supplier)</option>
	<option value="2">สั่งซื้อของเก่า (จาก อะไหล่เก่า)</option>
</select>
</div>


<div style="margin: 10px 0 5px 0">
<b>วันที่ใบสั่งซื้อ :</b>
<input type="text" name="date" id="date" class="datepicker" />
</div>


<?php
	// parts_pocode
	// "POPXB-YYMMDDNNN"
	// "POP" follow by "XB" then follow by "-YYMMDDNNN"
	// X = "n" AS PO New (type = 1) or = "u" AS PO Old (type = 2)
	// B = Field: Office_id ---> Table: fuser --> Primary Key is Field : id_user --> Get from HTTP Get : "ss_iduser"
	// YY = Year
	// MM = Month
	// DD = Day
	// NNN = Running Number
?>
<div style="margin: 10px 0 5px 0">
<b>เลขที่ใบสั่งซื้อ :</b>
<input type="text" name="parts_pocode" id="parts_pocode" disabled="disabled" />
จะเห็นเมื่อหลังจากบันทึก
</div>


<div style="margin: 10px 0 5px 0">
<b>เลขที่ใบสั่งซื้อที่ต้องการคัดลอก :</b>
<input type="text" name="copypo_id" id="copypo_id"  value="" />
จะเห็นเมื่อหลังจากบันทึก
</div>

<div style="margin: 10px 0 5px 0">
<b>วันที่นัดส่งของ :</b>
<input type="text" name="app_sentpartdate" id="app_sentpartdate" class="datepicker" />
</div>

<div style="margin: 10px 0 5px 0">
<b>กำหนดชำระเงิน (วัน) :</b>
<!-- <select name="credit_terms" id="credit_terms">
	<option value="15">15</option>
	<option value="30">30</option>
	<option value="60">60</option>
	<option value="90">90</option>
</select> -->
	<input type="text" name="credit_terms" id="credit_terms"  value=""/>
</div>

<div style="margin: 10px 0 5px 0">
<b>ประมาณการวันที่ชำระเงิน :</b>
<input type="text" name="esm_paydate" id="esm_paydate" class="datepicker" />
</div>


<div style="margin: 10px 0 5px 0">
<b>ผู้ขาย :</b>
<select name="vender_id" id="vender_id">
<?php
$qry = pg_query("SELECT * FROM \"VVenders\" WHERE type_ven = 'M' or type_ven='B' ORDER BY pre_name,cus_name ASC");
while( $res = pg_fetch_array($qry) ){
    $vender_id = $res['vender_id'];
    $pre_name = trim($res['pre_name']);
    $cus_name = trim($res['cus_name']);
    $surname = trim($res['surname']);
	$branch_id = trim($res['branch_id']);
?>
    <option value="<?php echo $vender_id; ?>"><?php echo "$pre_name $cus_name $surname";
	if ($branch_id != "") {echo "( $branch_id )";
	}
 ?></option>
<?php
}
?>
</select>
</div>


<div style="margin: 10px 0 5px 0">
<b>ใบสั่งซื้อ มี VAT :</b>
<select name="vat_status" id="vat_status">
	<option value="1">คิด VAT</option>
	<option value="0">ไม่คิด VAT</option>
</select>
</div>

<!-- ##################### Middle ####################### -->

<div style="float:left; margin-top:10px; width:15%">
<b>รายการสั่งซื้อ</b><br />
<input type="button" name="btn_add" id="btn_add" value="+ เพิ่ม"><input type="button" name="btn_del" id="btn_del" value="- ลบ">
</div>

<div style="float:right; margin-top:10px; width:85%">
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:center" bgcolor="#D0D0D0">
	<td width="5%">ลำดับที่</td>
	<td width="15%">รหัสสินค้า</td>
	<td width="30%">ชื่อสินค้า</td>
	<td width="10%">จำนวน</td>
	<td width="10%">หน่วย</td>
	<td width="15%">ราคา/หน่วย</td>
	<td width="15%">จำนวนเงิน</td>
</tr>

<tr bgcolor="#FFFFFF">
	<td>
		1.
		<!-- <input type="hidden" name="idno1" id="idno1" value="1" /> -->
	</td>
	<td>
		<!-- <select id="parts_code1" name="parts_code1" class="parts_code" data-code_id="1"> -->
			<!-- <option value="">เลือกรหัสสินค้า</option> -->
			<script>
				var parts = new Array();
			</script>
<?php
			$strQuery_parts = "
				SELECT *
				FROM \"parts\" 
				ORDER BY code ASC
			";
			$qry_parts=@pg_query($strQuery_parts);
			$numrows_parts = pg_num_rows($qry_parts);
			$parts_data = array();
			while($res_parts=@pg_fetch_array($qry_parts)){
				$parts_data[] = $res_parts;
				$dt['value'] = $res_parts['code'];
				$dt['label'] = $res_parts["code"]." # ".$res_parts["name"];
				$parts_matches[] = $dt;
?>
				<script>
					parts.push(["<?php echo $res_parts['code']; ?>", "<?php echo $res_parts['name']; ?>", "<?php echo $res_parts['unitid']; ?>"]);
				</script>
				<!-- <option value="<?php echo $res_parts['code']; ?>">
					<?php echo $res_parts['code']; ?>
				</option> -->
<?php
			}
			if($numrows_parts == 0){
		        $parts_matches[] = "ไม่พบข้อมูล";
		    }
			$parts_matches = array_slice($parts_matches, 0, 100);
?>
		<!-- </select> -->
		<input type="text" id="parts_code1" name="parts_code1" class="parts_code" data-code_id="1" />
	</td>
	
	<td>
		<span id="parts_name1" class="parts_name"></span>
		<input type="hidden" name="parts_name1" class="parts_name" value="" />
	</td>
	<td align="right">
		<input type="text" name="quantity1" id="quantity1" class="quantity" data-quantity_id="1" disabled="disabled" style="width:100%; text-align:right" />
	</td>
	<td>
		<!-- <input type="text" name="unit1" id="unit1" class="unit" style="width:40px; text-align:right" onkeyup="" onkeypress="" /> -->
		
		<!-- <select id="unit1" name="unit1" class="unit" data-code_id="1" disabled="disabled"> -->
			<!-- <option value="">เลือกหน่วย</option> -->
			<!-- <option value="<?php echo $res_parts_unit['unitid']; ?>"> -->
				<!-- <?php echo $res_parts_unit['unitname']; ?> -->
			<!-- </option> -->
		<!-- </select> -->
		
		<script>
			var parts_unit = new Array();
<?php
			$strQuery_parts_unit = "
				SELECT *
				FROM \"parts_unit\" 
				ORDER BY unitname ASC
			";
			$parts_unit_data = array();
			$qry_parts_unit = @pg_query($strQuery_parts_unit);
			while($res_parts_unit = @pg_fetch_array($qry_parts_unit)){
				$parts_unit_data[] = $res_parts_unit;
?>
				parts_unit.push(["<?php echo $res_parts_unit['unitid']; ?>", "<?php echo $res_parts_unit['unitname']; ?>"]);
<?php
			}
?>
		</script>
		<span id="unit1" class="unit"></span>
		<input type="hidden" name="unit1" class="unit" value="" />
	</td>
	<td>
		<input type="text" name="costperunit1" id="costperunit1" class="costperunit" data-costperunit_id="1" disabled="disabled" style="width:40px; text-align:right" />
	</td>
	<td align="right">
		<span id="total1" class="total" style="font-weight:bold">0</span>
		<input type="hidden" name="total1" class="total" value="0" />
	</td>
</tr>
</table>

<div id="TextBoxesGroup"></div>

<div class="linedotted"></div>

<!-- ############## footer ############## -->


<div style="margin-left: 30%">
<b>เงินรวมก่อนหักส่วนลด : </b>
<span id="dsubtotal">dsubtotal</span>
</div>

<div style="margin-left: 30%">
<b>%ส่วนลด : </b>
<input type="text" name="pcdiscount" id="pcdiscount" class="" />
</div>

<div style="margin-left: 30%">
<b>จำนวนเงินส่วนลด :</b>
<input type="text" name="discount" id="discount" class="" />
</div>

<div style="margin-left: 30%">
<b>จำนวนเงินรวมก่อนภาษีมูลค่าเพิ่ม :</b>
<span id="vsubtotal">vsubtotal</span>
</div>

<div style="margin-left: 30%">
<b>%ภาษีมูลค่าเพิ่ม :</b>
<input type="text" name="pcvat" id="pcvat" class="" value="7" />
</div>

<div style="margin-left: 30%">
<b>จำนวนภาษี :</b>
<span id="vat">vat</span>
</div>

<div style="margin-left: 30%">
<b>จำนวนรวมสุทธิ :</b>
<span id="nettotal">nettotal</span>
</div>

<table>
<div style="float:left">
<b>หมายเหตุ</b><br />
<textarea name="PartsApproved_appr_note" id="PartsApproved_appr_note" rows="2" cols="90"></textarea>
</div>
<div style="float:right">
</table>


<!-- <div style="margin-top:10px">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold">
    <td colspan="4" width="50%" align="right">รวม</td>
    <td align="right" width="15%"><span id="span_sum_all_price">0</span></td>
    <td align="right" width="10%"><span id="span_sum_all_vat">0</span></td>
    <td align="right" width="20%"><span id="span_sum_all_all">0</span></td>
</tr>
</table>
</div> -->

<div style="clear:both"></div>
<div class="linedotted"></div>

<div style="text-align:right; margin-top:10px; margin-bottom: 10px;">
<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
</div>

</div>

<div class="linedotted"></div>

<div style="clear:both"></div>

</div>

<script>
	$(function() {
		$(".datepicker").datepicker({
			dateFormat: 'dd-mm-yy' ,
		});
	}); 
	
	// console.log(parts[1]);
	// console.log(parts[1][0]);
	console.log("parts.length = " + parts.length);
	
	$(".parts_code").live("blur", function(){
		console.log("parts_code = " + $(this).val());
			var this_id = $(this).data("code_id");
			// console.log("this_id = " + this_id);
			
			var i = 0;
			var parts_name_value = "";
			var parts_unitid_value = "";
			var parts_unitname_value = "";
			
			for(i=0; i<parts.length; i++){
				// console.log($.inArray( $(".parts_code").val() , parts[i])); //log this parts_name_value
				if($.inArray($(this).val() , parts[i]) == 0){
					parts_name_value = parts[i][1];
					parts_unitid_value = parts[i][2];
					break;
				}
			}
			console.log("parts_name = " + parts_name_value);
			$(".parts_name#parts_name"+this_id).html(parts_name_value);
			$(".parts_name[name=parts_name"+this_id+"]").val(parts_name_value);
			
			if(parts_name_value == ""){
				$(this).val("");
				// alert("ไม่มีรหัสนี้อยู่ในระบบ")
				$(".quantity#quantity"+this_id).prop("disabled", "disabled");
				$(".costperunit#costperunit"+this_id).prop("disabled", "disabled");
			}
			else{
				$(".quantity#quantity"+this_id).prop("disabled", false);
				$(".costperunit#costperunit"+this_id).prop("disabled", false);
			}
			
			// $(".unit#unit" + this_id + " option[value=" + counter + "]").attr()
// 			
			// $(".unit#unit" + this_id + " option").filter(function() { 
			    // return ($(this).val() == ); //To select Blue
			// }).prop('selected', true);
			
			for(i=0; i<parts_unit.length; i++){
				if($.inArray(parts_unitid_value, parts_unit[i]) == 0){
					parts_unitname_value = parts_unit[i][1];
					break;
				}
			}
			
			console.log("parts_unitname = " + parts_unitname_value);
			$(".unit#unit" + this_id + "").html(parts_unitname_value);
			$(".unit[name=unit" + this_id + "]").val(parts_unitname_value);
	});
	
	var parts_code_autocomplete = <?php echo json_encode($parts_matches); ?>;
	// console.log(parts_code_autocomplete);
	
	
	$(".parts_code").live("focus", function() {
		$(this).autocomplete({
	        source: parts_code_autocomplete,
	        minLength:1,
	        select: function(event, ui) {
				if(ui.item.value == 'ไม่พบข้อมูลเก่า'){
					
				}else{
				   
				}
	        }
	    });
	});
	
	
	//counter = Count how many rows
	var counter = 1;

	$('#btn_add').click(function(){
	    counter++;
	    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);
	
	    table = 
	    '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
	    + ' <tr>'
	    + ' <td width="5%">'
	    + '		'+counter+'.'
	    + '		<!-- <input type="hidden" name="idno' + counter + '" id="idno' + counter + '" value="' + counter + '" /> -->'
	    + '	</td>'
	    + ' <td width="15%">'
	    + ' 	<!-- <select id="parts_code' + counter + '" name="parts_code' + counter + '" class="parts_code" data-code_id="' + counter + '" style="width:100%" onchange=""> -->'
	    + '			<!-- <option value="">เลือกรหัสสินค้า</option> -->'
<?php
				/*
	    		for($i = 0; $i < count($parts_data); $i++){
?>
		+ '			<option value="<?php echo $parts_data[$i]['code']; ?>"><?php echo $parts_data[$i]['name']; ?></option>'
<?php
	    		}
				*/
?>
		+ ' 	<!-- </select> -->'
		+ ' 	<input type="text" id="parts_code' + counter + '" name="parts_code' + counter + '" class="parts_code" data-code_id="' + counter + '" />'
		+ ' </td>'
		+ '	<td width="30%">'
		+ '		<span id="parts_name' + counter + '" class="parts_name"></span>'
		+ '	</td>'
		+ '	<td width="10%" align="right">'
	    + '		<input type="text" name="quantity' + counter + '" id="quantity' + counter + '" class="quantity" data-quantity_id="'+ counter +'" disabled="disabled" style="width:100%; text-align:right" />'
	    + '	</td>'
	    + '	<td width="10%">'
	    + '		<!-- <select id="unit' + counter + '" name="unit' + counter + '" class="unit" data-code_id="1" disabled="disabled" > -->'
	    + '			<!-- <option value="">เลือกหน่วย</option> -->'
<?php
    				// for($i = 0; $i < count($parts_unit_data); $i++){
?>
		+ '				<!-- <option value="<?php echo $parts_unit_data[$i]['unitid']; ?>"><?php echo $parts_unit_data[$i]['unitname']; ?></option> -->'
<?php
    				// }
?>
		+ '		<!-- </select> -->'
		+ '		<span id="unit' + counter + '" name="unit' + counter + '" class="unit"></span>'
		+ '		<input type="hidden" name="unit' + counter + '" class="unit" value="" />'
	    + '	</td>'
	    + '	<td width="15%">'
		+ '		<input type="text" name="costperunit' + counter + '" id="costperunit' + counter + '" class="costperunit" data-costperunit_id="'+ counter +'" disabled="disabled" style="width:40px; text-align:right" />'
		+ '	</td>'
		+ '	<td width="15%" align="right"></td>'
		+ '		<span id="total' + counter + '" class="total" style="font-weight:bold">0</span>'
		+ '		<input type="hidden" name="total' + counter + '" class="total" value="0" />'
		+ '	</td>'
		+ '</tr>'
		+ '</table>';
	
		newTextBoxDiv.html(table);
		newTextBoxDiv.appendTo("#TextBoxesGroup");
});
	
	//############### Delete ###################
	$("#btn_del").click(function(){
		if(counter==1){
		return false;
		}
		$("#TextBoxDiv" + counter).remove();
		counter--;
		SumAll();
	});
	
    //######################## วันที่นัดส่งของ #########################
    
    //วันที่นัดส่งของ
    //app_sentpartdate
    $("#app_sentpartdate").live("change", function(){
    	if($("#credit_terms").val() != ""){
    		Calculate_esm_paydate();
    	}
    });
    
    //กำหนดชำระเงิน (วัน)
    //credit_terms
    $("#credit_terms").live("keyup", function(){
    	if($("#app_sentpartdate").val() != ""){
    		Calculate_esm_paydate();
    	}
    });
    
    //ประมาณการวันที่ชำระเงิน
    //esm_paydate
    $("#esm_paydate").live("change", function(){
    	if($("#app_sentpartdate").val() != ""){
    		var app_sentpartdate_value = $('#app_sentpartdate').datepicker('getDate');
    		var esm_paydate_value = $('#esm_paydate').datepicker('getDate');
    		var credit_terms_value = parseInt(esm_paydate_value.getDate()) - parseInt(app_sentpartdate_value.getDate());
    		console.log("credit_terms = " + credit_terms_value);
    		$("#credit_terms").val(credit_terms_value);
    	}
    });
    
    
    function Calculate_esm_paydate (){
		var credit_terms_value = parseInt($("#credit_terms").val());
		console.log("credit_terms_value = " + credit_terms_value);
		var date2 = $('#app_sentpartdate').datepicker('getDate');
		console.log("app_sentpartdate = " + date2)
		date2.setDate(date2.getDate() + credit_terms_value);
		$('#esm_paydate').datepicker('setDate', date2);
		console.log("esm_paydate = " + $('#esm_paydate').datepicker('getDate'));
    }
	
	
	//######################## Calculate Mode ############################
	
	var dsubtotal = 0;
	var pcdiscount = 0;
	var discount = 0;
	var vsubtotal = 0;
	var pcvat = 7;
	var vat = 0;
	var nettotal = 0;
	
	$(".quantity").live("keyup", function(){
		var id = $(this).data("quantity_id");
		var quantity = $(this).val();
		if(quantity == ""){
			quantity = 0;
		}
		var costperunit = $(".costperunit#costperunit"+id).val();
		if(costperunit == ""){
			costperunit = 0;
		}
		var total_each_row = quantity * costperunit;
		
		$(".total#total"+id).html(total_each_row);
		$(".total[name=total"+id+"]").val(total_each_row);
		
		calculate_total();
	});
	
	$(".costperunit").live("keyup", function(){
		var id = $(this).data("costperunit_id");
		
		var quantity = $(".quantity#quantity"+id).val();
		if(quantity == ""){
			quantity = 0;
		}
		
		var costperunit = $(this).val();
		if(costperunit == ""){
			costperunit = 0;
		}
		
		var total_each_row = quantity * costperunit;
		$(".total#total"+id).html(total_each_row);
		$(".total[name=total"+id+"]").val(total_each_row);
		
		calculate_total();
	});

	
	//pcdiscount
	//%ส่วนลด
	//dsubtotal * pcdiscount = discount
	$("#pcdiscount").live("keyup", function(){
		pcdiscount = $(this).val();
		discount = dsubtotal * (pcdiscount/100.0); 
		
		console.log("discount = " + discount);
		
		$("#discount").val(discount);
		
		calculate_total();
	});
	
	//discount
	//จำนวนเงินส่วนลด
	//pcdiscount = discount / dsubtotal
	$("#discount").live("keyup", function(){
		
		discount = $(this).val();
		
		pcdiscount = discount / dsubtotal; 
		console.log("pcdiscount = " + pcdiscount);
		
		$("#pcdiscount").val((pcdiscount*100.0));
		calculate_total();
	});
	
		
	function calculate_total(){
		var dsubtotal_value = 0;
		var quantity = 0;
		var costperunit = 0;
		var i=0;
		
		for(i=1; i <= counter; i++){
			
			//dsubtotal
			//เงินรวมก่อนหักส่วนลด
			quantity = ($(".quantity#quantity"+i).val());
			if(quantity == ""){
				quantity = 0;
			}
			
			costperunit = ($(".costperunit#costperunit"+i).val());
			if(costperunit == ""){
				costperunit = 0;
			}
			dsubtotal_value += quantity * costperunit; 
			console.log("dsubtotal = " + dsubtotal_value);
		}
		
		$("#dsubtotal").html(dsubtotal_value);
		dsubtotal = dsubtotal_value;
		
		//vsubtotal
		//จำนวนเงินรวมก่อนภาษีมูลค่าเพิ่ม
		//vsubtotal = dsubtotal - discount
		vsubtotal = dsubtotal - discount;
		console.log("vsubtotal = " + vsubtotal);
		$("#vsubtotal").html(vsubtotal);
		
		vat = vsubtotal * (pcvat / 100.0);
		
		//vat
		//จำนวนภาษี
		console.log("vat = " + vat);
		$("#vat").html(vat);
		
		//nettotal
		//จำนวนรวมสุทธิ
		nettotal = vsubtotal - vat;
		console.log("nettotal = " + nettotal);
		$("#nettotal").html(nettotal);
	}
	
	//pcvat
	//% ภาษีมูลค่าเพิ่ม
	//vat = vsubtotal * pcvat
	$("#pcvat").live("keyup", function(){
		pcvat = $(this).val();
		console.log("pcvat = " + pcvat);
		vat = vsubtotal * (pcvat / 100.0);
		
		calculate_total2();
	});
	
	
	//########## Submit ###########
	$('#btnSubmit').click(function(){
		
		//### Header ###
		var _type = $('#type').val();
		var _date = $('#date').val();
		var _parts_pocode = $('#parts_pocode').val();
		var _copypo_id = $('#copypo_id').val();
		var _app_sentpartdate = $('#app_sentpartdate').val();
		var _credit_terms = $('#credit_terms').val();
		var _esm_paydate = $('#esm_paydate').val();
		var _vender_id = $('#vender_id').val();
		var _vat_status = $('#vat_status').val();
		
		var check_validate = 1;
		
		if(_type == ""){
			alert('กรุณาเลือก ประเภทของ PO');
			return false;
		}
		if(_date == ""){
			alert('กรุณากรอก วันที่ใบสั่งซื้อ');
			return false;
		}
		// if(_parts_pocode == ""){
			// alert('กรุณากรอก เลขที่ใบสั่งซื้อ');
			// return false;
		// }
		// if(_copypo_id == ""){
			// alert('กรุณากรอก เลขที่ใบสั่งซื้อที่ต้องการคัดลอก');
			// return false;
		// }
		if(_app_sentpartdate == ""){
			alert('กรุณากรอก วันที่นัดส่งของ');
			return false;
		}
		if(_credit_terms == ""){
			alert('กรุณากรอก กำหนดชำระเงิน (วัน)');
			return false;
		}
		if(_esm_paydate == ""){
			alert('กรุณากรอก ประมาณการวันที่ชำระเงิน');
			return false;
		}
		if(_vender_id == ""){
			alert('กรุณาเลือก ผู้ขาย');
			return false;
		}
		if(_vat_status == ""){
			alert('กรุณาเลือก ใบสั่งซื้อ มี VAT');
			return false;
		}
		
		
		//### Middle ###
		var arradd = [];
		for( i=1; i<=counter; i++ ){
			
			// var _idno = $('#idno'+i).val();
			var _parts_code = $('#parts_code' + i).val();
			var _quantity = $('#quantity' + i).val();
			var _unit = $('.unit[name=unit' + i + "]").val();
			var _costperunit = $('#costperunit' + i).val();
			var _total = $('.total[name=total'+i+']').val();
			
			if(_parts_code == ""){
				alert('กรุณาเลือก เลือกรหัสสินค้า (รายการที่ '+i+')');
				return false;
			}
			if(_quantity == "" || _quantity == 0){
				alert('กรุณากรอกจำนวน (รายการที่ '+i+')');
				return false;
			}
			// if(_unit == ""){
				// alert('กรุณาเลือก หน่วย (รายการที่ '+i+')');
				// return false;
			// }
			if(_costperunit == "" || _costperunit == 0){
				alert('กรุณากรอก ราคา/หน่วย (รายการที่ '+i+')');
				return false;
			}
			if(_total == "" || _total == 0){
				return false;
			}
			arradd[i] = { 
				idno: i, 
				parts_code: _parts_code, 
				quantity: _quantity, 
				unit: _unit, 
				costperunit: _costperunit, 
				total: _total 
			};
		}
		
		//### Footer ###
		var _pcdiscount = $('#pcdiscount').val();
		var _discount = $('#discount').val();
		var _pcvat = $('#pcvat').val();
		var _PartsApproved_appr_note = $('#PartsApproved_appr_note').val();
		
		if(_pcdiscount == ""){
			alert('กรุณากรอก %ส่วนลด');
			return false;
		}
		if(_discount == ""){
			alert('กรุณากรอก จำนวนเงินส่วนลด');
			return false;
		}
		if(_pcvat == ""){
			alert('กรุณากรอก %ภาษีมูลค่าเพิ่ม');
			return false;
		}
		if(_PartsApproved_appr_note == ""){
			alert('กรุณากรอก หมายเหตุ');
			return false;
		}
		
		$.post('po_buy_api_save.php',{
			id_user: "<?php echo $_GET["ss_iduser"] ?>",
			type: $('#type').val(),
			date: $('#date').val(),
			// parts_pocode: $('#parts_pocode').val(),
			copypo_id: $('#copypo_id').val(),
			app_sentpartdate: $('#app_sentpartdate').val(),
			credit_terms: $('#credit_terms').val(),
			esm_paydate: $('#esm_paydate').val(),
			vender_id: $('#vender_id').val(),
			vat_status: $('#vat_status').val(),
			
			purchase_order_parts_details_array: JSON.stringify(arradd),
			
			dsubtotal: dsubtotal,
			pcdiscount: $('#pcdiscount').val(),
			discount: $('#discount').val(),
			vsubtotal: vsubtotal,
			pcvat: $('#pcvat').val(),
			vat: val,
			nettotal: nettotal,
			PartsApproved_appr_note: $('#PartsApproved_appr_note').val(),
			
		},
		function(data){
			if(data.success){
				// ShowPrint(data.po_id);
				console.log("data.success = " + data.success);
				console.log("data.message = " + data.message);
				console.log("data.test = " + data.test);
				//location.reload();
			}else{
				alert(data.message);
				console.log("data.success = " + data.success);
				console.log("data.message = " + data.message);
				console.log("data.test = " + data.test);
			}
		},'json');
	});
	
	//##############################################################################
	
	function ShowPrint(id){
		$('body').append('<div id="divdialogprint"></div>');
		$('#divdialogprint').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/po_buy_mat_pdf.php?po_id="+ id +"','po_id4343423','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
		$('#divdialogprint').dialog({
			title: 'พิมพ์รายงาน : '+id,
			resizable: false,
			modal: true,
			width: 300,
			height: 200,
			close: function(ev, ui){
				for( i=1; i<=counter; i++){
				$('#combo_product'+ i).val("");
				$('#txt_unit'+ i).val("");
				$('#txt_cost'+ i).val("");
				$('#span_price'+ i).text("0");
				$('#txt_vat'+ i).val("");
				$('#span_sum'+ i).text("0");
				}
				$('#span_sum_all_price').text("0");
				$('#span_sum_all_vat').text("0");
				$('#span_sum_all_all').text("0");
				$('#divdialogprint').remove();
			}
		});
	}

	function changePrice(id){
		$.get('po_buy_api.php?cmd=changePriceMaterial&pid='+$('#combo_product'+id).val(), function(data){
		$('#txt_cost'+id).val(data);
		changeUnit(id);
		SumRow(id);
		});
	}
	
	function changeUnit(id){
		var cost = parseFloat($('#txt_cost'+id).val());
		var unit = parseFloat($('#txt_unit'+id).val());
		if ( isNaN(cost) || cost == ""){
		cost = 0;
		}
		if ( isNaN(unit) || unit == ""){
		unit = 0;
		}
		
		var c = cost*unit;
		
		$.get('po_buy_api.php?cmd=ChkUseVat&pid='+$('#combo_product'+id).val(), function(data){
			if(data == "f"){
			var vat = 0;
			var value = parseFloat(c)-parseFloat(vat);
			$('#span_price'+id).text(value.toFixed(2));
			$('#txt_vat'+id).val(vat.toFixed(2));
			SumRow(id);
			}else if(data == "t"){
				var vat = (c*<?php echo $company_vat; ?>)/<?php echo(100 + $company_vat); ?>;
				var value = parseFloat(c) - parseFloat(vat);
				$('#span_price' + id).text(value.toFixed(2));
				$('#txt_vat' + id).val(vat.toFixed(2));
				SumRow(id);
			}
		});
	}
	
	function changeVat(id) {
		var sum = parseFloat($('#span_sum' + id).text());
		var vat = parseFloat($('#txt_vat' + id).val());

		if (isNaN(sum) || sum == "") {
			sum = 0;
		}
		if (isNaN(vat) || vat == "") {
			vat = 0;
		}
		var s1 = sum - vat;
		$('#span_price' + id).text(s1.toFixed(2));
		SumRow(id);
	}
</script>
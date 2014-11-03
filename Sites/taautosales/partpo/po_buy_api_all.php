<?php
	include_once("../include/config.php");
	include_once("../include/function.php");
	include_once("po_buy_api_webservice.php");
?>
<div>
	<!-- PO type -->
	<div style="width: 20%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
		<strong>ประเภทของ PO :</strong>
	</div>
	<div style="width: 78%; float: left;">
		<select name="type" id="type">
			<option value="1">สั่งซื้อของใหม่ (จาก Supplier)</option>
			<option value="2">สั่งซื้อของเก่า (จาก อะไหล่เก่า)</option>
		</select>
	</div>
	<div style="clear: both;"></div>
</div>
<div>
	<div style="width: 20%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
		<b>วันที่ใบสั่งซื้อ :</b>
	</div>
	<div style="width: 78%; float: left;">
		<input type="text" name="date" id="date" class="datepicker" />
	</div>
	<div style="clear: both;"></div>
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
<div>
	<div style="width: 20%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
		<b>เลขที่ใบสั่งซื้อ :</b>
	</div>
	<div style="width: 78%; float: left;">
		<input type="text" name="parts_pocode" id="parts_pocode" disabled="disabled" /> จะเห็นเมื่อหลังจากบันทึก
	</div>
	<div style="clear: both;"></div>
</div>
<script>
<?php
	$parts_pocode_matches = get_PurchaseOrderPart();
?>
	var PurchaseOrderPart = <?php echo json_encode($parts_pocode_matches["result"]); ?>;
</script>
<?php
	if($parts_pocode_matches["numrow"] == 0){
        $parts_pocode_matches["result"] = "ไม่พบข้อมูล";
    }
	// $parts_pocode_matches["result"] = array_slice($parts_pocode_matches["result"], 0, 100);
?>
<div>
	<div style="width: 20%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
		<b>เลขที่ใบสั่งซื้อที่ต้องการคัดลอก :</b>
	</div>
	<div style="width: 78%; float: left;">
		<input type="text" name="copypo_id" id="copypo_id"  value="" />
	</div>
	<div style="clear: both;"></div>
</div>


<div>
	<div style="width: 20%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
		<b>วันที่นัดส่งของ :</b>
	</div>
	<div style="width: 78%; float: left;">
		<input type="text" name="app_sentpartdate" id="app_sentpartdate" class="datepicker" />
	</div>
	<div style="clear: both;"></div>
</div>

<div>
	<div style="width: 20%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
		
	</div>
	<div style="width: 78%; float: left;">
		
	</div>
	<div style="clear: both;"></div>
</div>

<div>
	<div style="width: 20%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
		<b>กำหนดชำระเงิน (วัน) :</b>
	</div>
	<div style="width: 78%; float: left;">
		<input type="text" name="credit_terms" id="credit_terms"  value=""/>
	</div>
	<div style="clear: both;"></div>
</div>

<div>
	<div style="width: 20%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
		<b>ประมาณการวันที่ชำระเงิน :</b>
	</div>
	<div style="width: 78%; float: left;">
		<input type="text" name="esm_paydate" id="esm_paydate" class="datepicker" />
	</div>
	<div style="clear: both;"></div>
</div>

<div>
	<div style="width: 20%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
		
	</div>
	<div style="width: 78%; float: left;">
		
	</div>
	<div style="clear: both;"></div>
</div>

<div>
	<div style="width: 20%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
		<b>ผู้ขาย :</b>
	</div>
	<div style="width: 78%; float: left;">
		<select name="vender_id" id="vender_id">
<?php
		$VVenders = get_view_VVenders();
		foreach ($VVenders as $value) {
?>
		    <option value="<?php echo $value["vender_id"]; ?>"><?php echo $value["pre_name"]." ".$value["cus_name"]." ".$value["surname"];
			if ($value["branch_id"] != "") {
				echo "( ".$value["branch_id"]." )";
			}
			?></option>
<?php
		}
?>
		</select>
	</div>
	<div style="clear: both;"></div>
</div>

<div>
	<div style="width: 20%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
		<b>ใบสั่งซื้อ มี VAT :</b>
	</div>
	<div style="width: 78%; float: left;">
		<select name="vat_status" id="vat_status">
			<option value="1">คิด VAT</option>
			<option value="0">ไม่คิด VAT</option>
		</select>
	</div>
	<div style="clear: both;"></div>
</div>

<div style="font-size:12px">
	<!-- ##################### Middle ####################### -->
	
	<div style="float:left; margin-top:10px; width:15%">
		<b>รายการสั่งซื้อ</b><br />
		<input type="button" name="btn_add" id="btn_add" value="+ เพิ่ม"><input type="button" name="btn_del" id="btn_del" value="- ลบ">
	</div>
	
		<div style="float:right; margin-top:10px; width:85%">
			<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
				<tr style="font-weight:bold; text-align:center; " bgcolor="#D0D0D0">
					<td width="5%">ลำดับที่</td>
					<td width="10%">รหัสสินค้า</td>
					<td width="15%">ชื่อสินค้า</td>
					<td width="30%">รายละเอียดสินค้า</td>
					<td width="10%">จำนวน</td>
					<td width="10%">หน่วย</td>
					<td width="10%">ราคา/หน่วย</td>
					<td width="10%">จำนวนเงิน</td>
				</tr>
				
				<tr bgcolor="#FFFFFF">
					<td>
						1.
					</td>
					<td>
						<input type="text" id="parts_code1" name="parts_code1" class="parts_code" data-code_id="1" />
					</td>
					
					<td>
						<span id="parts_name1" class="parts_name"></span>
						<input type="hidden" name="parts_name1" class="parts_name" value="" />
					</td>
					<td>
						<span id="parts_detail1" class="parts_detail"></span>
					</td>
					<td align="right">
						<input type="text" name="quantity1" id="quantity1" class="quantity" data-quantity_id="1" disabled="disabled" style="width:100%; text-align:right" />
					</td>
					<td align="center">
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
		
			<div style="width: 50%; float: left; margin-left: 50%; height: 30px;">
				<!-- PO type -->
				<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
					<b>เงินรวมก่อนหักส่วนลด : </b>
				</div>
				<div style="width: 48%; float: left; margin-top: 1.2%;">
					<span id="dsubtotal"></span>
				</div>
				<div style="clear: both;"></div>
			</div>
			<div style="clear: both;"></div>
			
			
			<div style="width: 50%; float: left; height: 30px; ">
				<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
					<b>%ส่วนลด : </b>
				</div>
				<div style="width: 48%; float: left;">
					<input type="text" name="pcdiscount" id="pcdiscount" class="" />
				</div>
				<div style="clear: both;"></div>
			</div>
			
			<div style="width: 50%; float: left; height: 30px; ">
				<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
					<b>จำนวนเงินส่วนลด :</b>
				</div>
				<div style="width: 48%; float: left;">
					<input type="text" name="discount" id="discount" class="" />
				</div>
				<div style="clear: both;"></div>
			</div>
			<div style="clear: both;"></div>
			
			
			<div style="width: 50%; float: left; margin-left: 50%; height: 30px; ">
				<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
					<b>จำนวนเงินรวมก่อนภาษีมูลค่าเพิ่ม :</b>
				</div>
				<div style="width: 48%; float: left; margin-top: 1.2%;">
					<span id="vsubtotal"></span>
				</div>
				<div style="clear: both;"></div>
			</div>
			<div style="clear: both;"></div>
			
			
			<div style="width: 50%; float: left; height: 30px; ">
				<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
					<b>%ภาษีมูลค่าเพิ่ม :</b>
				</div>
				<div style="width: 48%; float: left;">
					<input type="text" name="pcvat" id="pcvat" class="" value="7.0" />
				</div>
				<div style="clear: both;"></div>
			</div>
			
			<div style="width: 50%; float: left; height: 30px; ">
				<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
					<b>จำนวนภาษี :</b>
				</div>
				<div style="width: 48%; float: left; margin-top: 1.2%;">
					<span id="vat"></span>
				</div>
				<div style="clear: both;"></div>
			</div>
			<div style="clear: both;"></div>
		
		
			<div style="width: 50%; float: left; margin-left: 50%; height: 30px; ">
				<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
					<b>จำนวนรวมสุทธิ :</b>
				</div>
				<div style="width: 48%; float: left; margin-top: 1.2%;">
					<span id="nettotal"></span>
				</div>
				<div style="clear: both;"></div>
			</div>
			<div style="clear: both;"></div>
			
			
			<div style="width: 100%; float: left; height: 30px; ">
				<div>
					<b>หมายเหตุ</b>
				</div>
				<div>
					<textarea name="PartsApproved_appr_note" id="PartsApproved_appr_note" rows="2" cols="70"></textarea>
				</div>
			</div>
			
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
	//######################## Initial Calculate Variables ############################
	
	var dsubtotal = 0;
	var pcdiscount = 0;
	var discount = 0;
	var vsubtotal = 0;
	var pcvat = 0.07;
	var vat = 0;
	var nettotal = 0;
	
	//################################################################
	
	
	$(function() {
		$(".datepicker").datepicker({
			dateFormat: 'dd-mm-yy' ,
		});
	}); 
	
	//For Test has_PurchaseOrderPart == ?
	// console.log(PurchaseOrderPart[1][0]);
	
	$('#copypo_id').live("blur", function(){
		var copy_id_value = $(this).val();
		var has_PurchaseOrderPart = 0;
		
		if(copy_id_value != ""){
			for(i=0; i < PurchaseOrderPart.length; i++){
				if(PurchaseOrderPart[i]["value"] == copy_id_value){
					console.log("i = " + i);
					has_PurchaseOrderPart = 1;
				}
			}
		}
		
		if(has_PurchaseOrderPart == 0){
			//IF There are no Data after put, deleted it
			$(this).val("");
			
			$("#TextBoxesGroup").html("");
			counter = 1;
			
			$(".parts_code#parts_code1").val("");
			$(".parts_name#parts_name1").html("");
			$(".parts_name[name=parts_name1]").val("");
			$(".parts_detail#parts_detail1").html("");
			$(".quantity#quantity1").val("");
			$(".unit#unit1").html("");
			$(".unit[name=unit]").val();
			$(".costperunit#costperunit1").val("");
			$(".total#total1").html("");
			$(".total[name=total1]").val("");
			
			$("#dsubtotal").html("");
			$("#pcdiscount").val("");
			$("#discount").val("");
			$("#vsubtotal").html("");
			$("#pcvat").val("7.0");
			$("#vat").html("");
			$("#nettotal").html("");
			
			dsubtotal = 0;
			pcdiscount = 0;
			discount = 0;
			vsubtotal = 0;
			pcvat = 0.07;
			vat = 0;
			nettotal = 0;
		}
		else{
			//IF There are some Data after put, Try to load AJAX Post to SELECT Data (Table: PurchaseOrderPart)
			//After that, take that data to write to HTML DOM
			
			
			$.post(
				'po_buy_ajax_query_purchaseorderpart.php',
				{
					parts_pocode: copy_id_value,
				},
				function(data){
					console.log("################");
					if(data.success){
						console.log("data.success = " + data.success);
						console.log("data.message = " + data.message);
						
						//Add to Initial Variables
						dsubtotal = data.purchaseOrderPart_subtotal;
						pcdiscount = data.purchaseOrderPart_pcdiscount;
						discount = data.purchaseOrderPart_discount;
						vsubtotal = data.purchaseOrderPart_bfv_total;
						pcvat = data.purchaseOrderPart_pcvat;
						vat = data.purchaseOrderPart_vat;
						nettotal = data.purchaseOrderPart_nettotal;
						
						//Replace Data From SELECT Query to HTML
						$("#dsubtotal").html(parseFloat(data.purchaseOrderPart_subtotal).toFixed(2));
						$("#pcdiscount").val(parseFloat((data.purchaseOrderPart_pcdiscount)*100.0).toFixed(2));
						$("#discount").val(parseFloat(data.purchaseOrderPart_discount).toFixed(2));
						$("#vsubtotal").html(parseFloat(data.purchaseOrderPart_bfv_total).toFixed(2));
						$("#pcvat").val(parseFloat((data.purchaseOrderPart_pcvat)*100.0).toFixed(2));
						$("#vat").html(parseFloat(data.purchaseOrderPart_vat).toFixed(2));
						$("#nettotal").html(parseFloat(data.purchaseOrderPart_nettotal).toFixed(2));
					}
					else{
						console.log("data.success = " + data.success);
						console.log("data.message = " + data.message);
					}
				},
				'json'
			);
			
			$.post(
				'po_buy_ajax_query_purchaseorderpartsdetails.php',
				{
					parts_pocode: copy_id_value,
				},
				function(data){
					console.log("################");
					if(data.success){
						console.log("data.success = " + data.success);
						console.log("data.message = " + data.message);
						
						//Split data to Array
						var purchaseOrderPartsDetails_idno = data.purchaseOrderPartsDetails_idno.split("###");
						var purchaseOrderPartsDetails_parts_code = data.purchaseOrderPartsDetails_parts_code.split("###");
						var purchaseOrderPartsDetails_quantity = data.purchaseOrderPartsDetails_quantity.split("###");
						var purchaseOrderPartsDetails_unit = data.purchaseOrderPartsDetails_unit.split("###");
						var purchaseOrderPartsDetails_costperunit = data.purchaseOrderPartsDetails_costperunit.split("###");
						var purchaseOrderPartsDetails_total = data.purchaseOrderPartsDetails_total.split("###");
						
						console.log("###############################");
						console.log("data.purchaseOrderPartsDetails_idno = " + data.purchaseOrderPartsDetails_idno);
						console.log("purchaseOrderPartsDetails_idno = " + purchaseOrderPartsDetails_idno[0]);
						console.log("###############################");
						
						//######################################
						//Replace Data From SELECT Query to HTML
						//########## For The 1st Row ###########
						
						$(".parts_code#parts_code1").val(purchaseOrderPartsDetails_parts_code[0]);
						$("#TextBoxesGroup").html("");
						counter = 1;
						
						//########################## parts_code1 ##############################
						console.log("parts_code = " + $(".parts_code#parts_code1").val());
						var this_id = $(".parts_code#parts_code1").data("code_id");
						
						var i = 0;
						var parts_name_value = "";
						var parts_unitid_value = "";
						var parts_unitname_value = "";
						var parts_detail_value = "";
						
						for(i=0; i<parts.length; i++){
							if($.inArray($(".parts_code#parts_code1").val() , parts[i]) == 0){
								parts_name_value = parts[i][1];
								parts_unitid_value = parts[i][2];
								parts_detail_value = parts[i][3];
								break;
							}
						}
						$(".parts_name#parts_name"+this_id).html(parts_name_value);
						$(".parts_name[name=parts_name"+this_id+"]").val(parts_name_value);
						$(".parts_detail#parts_detail"+this_id).html(parts_detail_value);
						
						if(parts_name_value == ""){
							$(".parts_code#parts_code1").val("");
							$(".quantity#quantity"+this_id).prop("disabled", "disabled");
							$(".costperunit#costperunit"+this_id).prop("disabled", "disabled");
						}
						else{
							$(".quantity#quantity"+this_id).prop("disabled", false);
							$(".costperunit#costperunit"+this_id).prop("disabled", false);
						}
						for(i=0; i<parts_unit.length; i++){
							if($.inArray(parts_unitid_value, parts_unit[i]) == 0){
								parts_unitname_value = parts_unit[i][1];
								break;
							}
						}
						$(".unit#unit" + this_id + "").html(parts_unitname_value);
						$(".unit[name=unit" + this_id + "]").val(parts_unitid_value);
						//####################### End parts_code1 ##########################
						
						$(".quantity#quantity1").val(purchaseOrderPartsDetails_quantity[0]);
						$(".unit#unit1").val(purchaseOrderPartsDetails_unit[0]);
						$(".costperunit#costperunit1").val(purchaseOrderPartsDetails_costperunit[0]);
						$(".total#total1").html(purchaseOrderPartsDetails_total[0]);
						$(".total[name=total1]").val(purchaseOrderPartsDetails_total[0]);
						
						//####################### End Row 1 ##########################
						
						
						//######################################
						//Replace Data From SELECT Query to HTML
						//########## For The 2nd Row ###########
						
						// console.log(purchaseOrderPartsDetails_idno.length);
						
						var j = 0;
						if(purchaseOrderPartsDetails_idno.length > 1){
							j = 0;
							
							for(j = 0; j < purchaseOrderPartsDetails_idno.length - 1; j++){
								document.getElementById("btn_add").click();
							}
							
							for(j = 2; j <= purchaseOrderPartsDetails_idno.length; j++){
								
								$(".parts_code#parts_code"+j).val(purchaseOrderPartsDetails_parts_code[j-1]);
								
								//########################## parts_code j ##############################
								console.log("***** j = " + j + "*****");
								console.log("parts_code = " + $(".parts_code#parts_code"+j).val());
								var this_id = $(".parts_code#parts_code"+j).data("code_id");
								// console.log("this_id = " + this_id);
								
								var i = 0;
								var parts_name_value = "";
								var parts_unitid_value = "";
								var parts_unitname_value = "";
								var parts_detail_value = "";
								
								for(i=0; i<parts.length; i++){
									if($.inArray($(".parts_code#parts_code"+j).val() , parts[i]) == 0){
										parts_name_value = parts[i][1];
										parts_unitid_value = parts[i][2];
										parts_detail_value = parts[i][3];
										break;
									}
								}
								$(".parts_name#parts_name"+this_id).html(parts_name_value);
								$(".parts_name[name=parts_name"+this_id+"]").val(parts_name_value);
								$(".parts_detail#parts_detail"+this_id).html(parts_detail_value);
								
								if(parts_name_value == ""){
									$(".parts_code#parts_code"+j).val("");
									$(".quantity#quantity"+this_id).prop("disabled", "disabled");
									$(".costperunit#costperunit"+this_id).prop("disabled", "disabled");
								}
								else{
									$(".quantity#quantity"+this_id).prop("disabled", false);
									$(".costperunit#costperunit"+this_id).prop("disabled", false);
								}
								for(i=0; i<parts_unit.length; i++){
									if($.inArray(parts_unitid_value, parts_unit[i]) == 0){
										parts_unitname_value = parts_unit[i][1];
										break;
									}
								}
								$(".unit#unit" + this_id + "").html(parts_unitname_value);
								$(".unit[name=unit" + this_id + "]").val(parts_unitid_value);
								//####################### End parts_code j ##########################
								
								$(".quantity#quantity"+j).val(purchaseOrderPartsDetails_quantity[j-1]);
								$(".unit#unit"+j).val(purchaseOrderPartsDetails_unit[j-1]);
								$(".costperunit#costperunit"+j).val(purchaseOrderPartsDetails_costperunit[j-1])
								$(".total#total"+j).html(purchaseOrderPartsDetails_total[j-1]);
								$(".total[name=total"+j+"]").val(purchaseOrderPartsDetails_total[j-1]);
								
								//####################### End Row j ##########################
							}
						}
					}
					else{
						console.log("data.success = " + data.success);
						console.log("data.message = " + data.message);
					}
				},
				'json'
			);
		}
	});
	
	var parts_unit = new Array();
<?php
	$parts_unit_data = get_parts_unit();
	foreach ($parts_unit_data as $value) {
?>
		parts_unit.push(["<?php echo $value['unitid']; ?>", "<?php echo $value['unitname']; ?>"]);
<?php
	}
?>
	
	
	var parts = new Array();
<?php
	$parts_matches = get_parts();
	foreach ($parts_matches["result"] as $value) {
?>
		parts.push(["<?php echo $value['code']; ?>", "<?php echo $value['name']; ?>", "<?php echo $value['unitid']; ?>", "<?php echo $value['details']; ?>"]);
<?php
	}
	$parts_matches["result"] = array_slice($parts_matches["result"], 0, 100);
?>
	
	
	var parts_pocode_autocomplete = PurchaseOrderPart;
	
	$("#copypo_id").live("focus", function() {
		$(this).autocomplete({
	        source: parts_pocode_autocomplete,
	        minLength:1,
	        select: function(event, ui) {
				if(ui.item.value == 'ไม่พบข้อมูลเก่า'){
					
				}else{
					console.log("dsubtotal = "+dsubtotal);
					console.log("pcdiscount = "+pcdiscount);
					console.log("discount = "+discount);
					console.log("vsubtotal = "+vsubtotal);
					console.log("pcvat = "+pcvat);
					console.log("vat = "+vat);
					console.log("nettotal = "+nettotal);
				}
	        }
	    });
	});
	
	
	$(".parts_code").live("blur", function(){
		console.log("parts_code = " + $(this).val());
		var this_id = $(this).data("code_id");
		// console.log("this_id = " + this_id);
		
		var i = 0;
		var parts_name_value = "";
		var parts_unitid_value = "";
		var parts_unitname_value = "";
		var parts_detail_value = "";
		
		for(i=0; i<parts.length; i++){
			// console.log($.inArray( $(".parts_code").val() , parts[i])); //log this parts_name_value
			if($.inArray($(this).val() , parts[i]) == 0){
				parts_name_value = parts[i][1];
				parts_unitid_value = parts[i][2];
				parts_detail_value = parts[i][3];
				break;
			}
		}
		console.log("parts_name = " + parts_name_value);
		$(".parts_name#parts_name"+this_id).html(parts_name_value);
		$(".parts_name[name=parts_name"+this_id+"]").val(parts_name_value);
		$(".parts_detail#parts_detail"+this_id).html(parts_detail_value);
		
		
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
		
		for(i=0; i<parts_unit.length; i++){
			if($.inArray(parts_unitid_value, parts_unit[i]) == 0){
				parts_unitname_value = parts_unit[i][1];
				break;
			}
		}
		
		console.log("parts_unitname = " + parts_unitname_value);
		$(".unit#unit" + this_id + "").html(parts_unitname_value);
		$(".unit[name=unit" + this_id + "]").val(parts_unitid_value);
	});
	
	var parts_code_autocomplete = <?php echo json_encode($parts_matches["result"]); ?>;
	
	//On Key (Parts_code) Enter For close Autocomplete, and Key on this Quantity
	$(".parts_code").live("keydown", function(event) {
		var code_id = $(this).data("code_id");
		if(event.keyCode == 13){
			$(this).autocomplete( "close" );
			$(".quantity#quantity"+code_id).prop("disabled", false);
			$(".quantity#quantity"+code_id).focus();
	    }
	});
	
	//On Key (quantity) Enter For close Autocomplete, and Key on this costperunit
	$(".quantity").live("keydown", function(event) {
		var code_id = $(this).data("quantity_id");
		if(event.keyCode == 13){
			$(".costperunit#costperunit"+code_id).focus();
	    }
	});
	
	//On Key (costperunit) Enter For close Autocomplete, and Key on Next Parts_code
	$(".costperunit").live("keydown", function(event) {
		var code_id = $(this).data("costperunit_id");
		if(event.keyCode == 13){
			$(".parts_code#parts_code"+(code_id+1)).focus();
	    }
	});
	
	
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
	    + '	</td>'
	    + ' <td width="10%">'
		+ ' 	<input type="text" id="parts_code' + counter + '" name="parts_code' + counter + '" class="parts_code" data-code_id="' + counter + '" />'
		+ ' </td>'
		+ '	<td width="15%">'
		+ '		<span id="parts_name' + counter + '" class="parts_name"></span>'
		+ '	</td>'
		+ '	<td  width="30%">'
		+ '		<span id="parts_detail' + counter + '" class="parts_detail"></span>'
		+ '	</td>'
		+ '	<td width="10%" align="right">'
	    + '		<input type="text" name="quantity' + counter + '" id="quantity' + counter + '" class="quantity" data-quantity_id="'+ counter +'" disabled="disabled" style="width:100%; text-align:right" />'
	    + '	</td>'
	    + '	<td width="10%" align="center">'
		+ '		<span id="unit' + counter + '" name="unit' + counter + '" class="unit"></span>'
		+ '		<input type="hidden" name="unit' + counter + '" class="unit" value="" />'
	    + '	</td>'
	    + '	<td width="10%">'
		+ '		<input type="text" name="costperunit' + counter + '" id="costperunit' + counter + '" class="costperunit" data-costperunit_id="'+ counter +'" disabled="disabled" style="width:40px; text-align:right" />'
		+ '	</td>'
		+ '	<td width="10%" align="right">'
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
		calculate_total();
	});
	
    //######################## วันที่นัดส่งของ #########################
    // การคำนวณ "ประมาณการวันที่ชำระเงิน : " 
	// - หากระบุ "กำหนดชำระเงิน (วัน)" ให้คำนวณ "ประมาณการวันที่ชำระเงิน : " —> Finished
	// - หรือ หากระบุ "ประมาณการวันที่ชำระเงิน : " ให้คำนวณ "กำหนดชำระเงิน (วัน)" —> Finished

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
    
    //กำหนดชำระเงิน (วัน) : ให้ใส่ได้ เฉพาะตัวเลข
    $(function() {
		$("#credit_terms").live("keydown", function(e){
			// Allow: backspace, delete, tab, escape, enter and .
		    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
		         // Allow: Ctrl+A
		        (e.keyCode == 65 && e.ctrlKey === true) || 
		         // Allow: home, end, left, right
		        (e.keyCode >= 35 && e.keyCode <= 39)) {
		             // let it happen, don't do anything
		             return;
		    }
		    // Ensure that it is a number and stop the keypress
		    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
		        e.preventDefault();
		    }
		});
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
    
	$(function() {
		$("#vat_status").live("change", function(){
			var vat_status_value = $(this).val();
			console.log("vat_status" + vat_status_value);
			if(vat_status_value == "1"){
				$("#pcvat").prop("disabled", false);
				$("#pcvat").val("7.0");
				pcvat = 0.07;
				vat_status_change();
			}
			else if(vat_status_value == "0"){
				$("#pcvat").prop("disabled", true);
				$("#pcvat").val("0.0");
				pcvat = 0.0;
				vat_status_change();
			}
		});
	});
	
	function vat_status_change(){
		console.log("vsubtotal = " + vsubtotal);
		vat = vsubtotal * pcvat;
		$("#vat").html(numberWithCommas(parseFloat(vat).toFixed(2)));
		nettotal = vsubtotal + vat;
		$("#nettotal").html(numberWithCommas(parseFloat(nettotal).toFixed(2)));
	}
	
	
	//######################## Calculate Mode ############################
	
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
		pcdiscount = ($(this).val()/100.0);
		discount = dsubtotal * (pcdiscount); 
		
		console.log("discount = " + discount);
		
		$("#discount").val(discount);
		
		calculate_total();
	});
	
	//discount
	//จำนวนเงินส่วนลด
	//pcdiscount = discount / dsubtotal
	$("#discount").live("change", function(){
		
		discount = $(this).val();
		if(discount == ""){
			discount = 0;
		}
		
		pcdiscount = discount / dsubtotal; 
		console.log("pcdiscount = " + pcdiscount);
		
		$("#pcdiscount").val((pcdiscount*100.0));
		calculate_total();
	});
	
	//pcvat
	//% ภาษีมูลค่าเพิ่ม
	//vat = vsubtotal * pcvat
	$("#pcvat").live("keyup", function(){
		pcvat = ($(this).val()/100.0);
		console.log("pcvat = " + pcvat);
		vat = vsubtotal * (pcvat);
		
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
		
		$("#dsubtotal").html(numberWithCommas(dsubtotal_value));
		dsubtotal = dsubtotal_value;
		
		console.log("################$$$$$$$$$$$$$$$$");
		console.log("pcdiscount = " + pcdiscount);
		console.log("################$$$$$$$$$$$$$$$$");
		
		//pcdiscount
		//%ส่วนลด
		//dsubtotal * pcdiscount = discount
		discount = parseFloat(dsubtotal * pcdiscount).toFixed(2); 
		console.log("discount = " + discount);
		$("#discount").val(discount);
		
		
		//vsubtotal
		//จำนวนเงินรวมก่อนภาษีมูลค่าเพิ่ม
		//vsubtotal = dsubtotal - discount
		vsubtotal = dsubtotal - discount;
		console.log("vsubtotal = " + vsubtotal);
		$("#vsubtotal").html(numberWithCommas(vsubtotal));
		
		vat = parseFloat(vsubtotal * (pcvat)).toFixed(2);
		
		//vat
		//จำนวนภาษี
		console.log("vat = " + vat);
		$("#vat").html(numberWithCommas(vat*1.0));
		
		//nettotal
		//จำนวนรวมสุทธิ
		nettotal = parseFloat((vsubtotal * 1.0) + (vat * 1.0)).toFixed(2);
		console.log("nettotal = " + numberWithCommas(nettotal));
		$("#nettotal").html(numberWithCommas(nettotal));
		
		
		//Result Cost before Submit
		console.log("dsubtotal = "+dsubtotal);
		console.log("pcdiscount = "+pcdiscount);
		console.log("discount = "+discount);
		console.log("vsubtotal = "+vsubtotal);
		console.log("pcvat = "+pcvat);
		console.log("vat = "+vat);
		console.log("nettotal = "+nettotal);
	}
	
	function numberWithCommas(x) {
	    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	
	//########## Submit ###########
	$('#btnSubmit').click(function(){
		
		//Result Cost before Submit
		console.log("dsubtotal = "+dsubtotal);
		console.log("pcdiscount = "+pcdiscount);
		console.log("discount = "+discount);
		console.log("vsubtotal = "+vsubtotal);
		console.log("pcvat = "+pcvat);
		console.log("vat = "+vat);
		console.log("nettotal = "+nettotal);
		
		var chk = 0;
		
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
			chk++;
			return false;
		}
		if(_date == ""){
			alert('กรุณากรอก วันที่ใบสั่งซื้อ');
			chk++;
			return false;
		}
		if(_app_sentpartdate == ""){
			alert('กรุณากรอก วันที่นัดส่งของ');
			chk++;
			return false;
		}
		if(_credit_terms == ""){
			alert('กรุณากรอก กำหนดชำระเงิน (วัน)');
			chk++;
			return false;
		}
		if(_esm_paydate == ""){
			alert('กรุณากรอก ประมาณการวันที่ชำระเงิน');
			chk++;
			return false;
		}
		if(_vender_id == ""){
			alert('กรุณาเลือก ผู้ขาย');
			chk++;
			return false;
		}
		if(_vat_status == ""){
			alert('กรุณาเลือก ใบสั่งซื้อ มี VAT');
			chk++;
			return false;
		}
		
		
		//### Middle ###
		var arradd = new Array();
		for( i=1; i<=counter; i++ ){
			
			// var _idno = $('#idno'+i).val();
			var _parts_code = $('#parts_code' + i).val();
			var _quantity = $('#quantity' + i).val();
			var _unit = $('.unit[name=unit' + i + "]").val();
			var _costperunit = $('#costperunit' + i).val();
			var _total = $('.total[name=total'+i+']').val();
			
			if(_parts_code == ""){
				alert('กรุณาเลือก เลือกรหัสสินค้า (รายการที่ '+i+')');
				chk++;
				return false;
			}
			if(_quantity == "" || _quantity == 0){
				alert('กรุณากรอกจำนวน (รายการที่ '+i+')');
				chk++;
				return false;
			}
			if(_costperunit == "" || _costperunit == 0){
				alert('กรุณากรอก ราคา/หน่วย (รายการที่ '+i+')');
				chk++;
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
			chk++;
			return false;
		}
		if(_discount == ""){
			alert('กรุณากรอก จำนวนเงินส่วนลด');
			chk++;
			return false;
		}
		if(_pcvat == ""){
			alert('กรุณากรอก %ภาษีมูลค่าเพิ่ม');
			chk++;
			return false;
		}
		if(_PartsApproved_appr_note == ""){
			alert('กรุณากรอก หมายเหตุ');
			chk++;
			return false;
		}
		
		if(chk == 0){
			if(!confirm('ต้องการบันทึก สั่งซื้ออะไหล่/อุปกรณ์ หรือไม่')){
				return false;
			}
		
			$.post(
				'po_buy_api_save.php',
				{
					id_user: "<?php echo $_GET["ss_iduser"] ?>",
					type: $('#type').val(),
					date: $('#date').val(),
					copypo_id: $('#copypo_id').val(),
					app_sentpartdate: $('#app_sentpartdate').val(),
					credit_terms: $('#credit_terms').val(),
					esm_paydate: $('#esm_paydate').val(),
					vender_id: $('#vender_id').val(),
					vat_status: $('#vat_status').val(),
					
					purchase_order_parts_details_array: JSON.stringify(arradd),
					
					dsubtotal: dsubtotal,
					pcdiscount: pcdiscount,
					discount: discount,
					vsubtotal: vsubtotal,
					pcvat: pcvat,
					vat: vat,
					nettotal: nettotal,
					PartsApproved_appr_note: $('#PartsApproved_appr_note').val(),
					
				},
				function(data){
					if(data.success){
						ShowPrint(data.parts_pocode);
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
				},'json'
			);
		}
		
	}); //End BtnClick
	
	//##############################################################################
	
	function ShowPrint(id){
		$('body').append('<div id="divdialogprint"></div>');
		$('#divdialogprint').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('./po_buy_mat_pdf.php?po_id="+ id +"','po_id4343423','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
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
<!-- header -->
<br />
<?php
$parts_pocode = pg_escape_string($_GET["parts_pocode"]);
$purchaseOrderPart_type = "";

$purchaseOrderPart_strQuery = "
	SELECT
		\"parts_pocode\",\"date\",\"type\",\"copypo_id\",
		\"credit_terms\",\"app_sentpartdate\",\"esm_paydate\",\"vender_id\",
		\"subtotal\",\"pcdiscount\",\"discount\",\"bfv_total\",
		\"pcvat\",\"vat\",\"nettotal\",\"status\",\"paid\"
	FROM
		\"PurchaseOrderPart\"
	WHERE 
		\"parts_pocode\" = '{$parts_pocode}'; 
";
$purchaseOrderPart_query = pg_query($purchaseOrderPart_strQuery);
while ($purchaseOrderPart_result = pg_fetch_array($purchaseOrderPart_query)) {
?>
	<div style="width: 50%; float:left; ">
		
		<div>
			<!-- PO type -->
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<strong>ประเภทของ PO :</strong>
			</div>
			<div style="width: 58%; float: left;">
				<?php 
				$purchaseOrderPart_type = $purchaseOrderPart_result["type"];
				if($purchaseOrderPart_type == 1){
					?>สั่งซื้อของใหม่ (จาก Supplier)<?php
				}
				elseif($purchaseOrderPart_type == 2){
					?>สั่งซื้อของเก่า (จาก อะไหล่เก่า)<?php
				}
				?> 
			</div>
			<div style="clear: both;"></div>
		</div>
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>วันที่ใบสั่งซื้อ :</b>
			</div>
			<div style="width: 58%; float: left;">
				<?php echo $purchaseOrderPart_result["date"]; ?>
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
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>เลขที่ใบสั่งซื้อ :</b>
			</div>
			<div style="width: 58%; float: left;">
				<?php echo $purchaseOrderPart_result["parts_pocode"]; ?>
			</div>
			<div style="clear: both;"></div>
		</div>
		
		
		<script>
			var PurchaseOrderPart = new Array();
		<?php 
			$strQuery_PurchaseOrderPart = "
				SELECT 
					\"parts_pocode\"
				FROM
					\"PurchaseOrderPart\"
			";
			$query_PurchaseOrderPart = @pg_query($strQuery_PurchaseOrderPart);
			$numrows_parts = pg_num_rows($query_PurchaseOrderPart);
			while($result_PurchaseOrderPart = @pg_fetch_array($query_PurchaseOrderPart)){
				$dt["value"] = $result_PurchaseOrderPart["parts_pocode"];
				$dt["label"] = $result_PurchaseOrderPart["parts_pocode"];
				$parts_pocode_matches[] = $dt;
		?>
				PurchaseOrderPart.push(["<?php echo $result_PurchaseOrderPart['parts_pocode']; ?>"]);
		<?php
			}
		?>
		</script>
		<?php
			if($numrows_parts == 0){
		        $parts_pocode_matches[] = "ไม่พบข้อมูล";
		    }
			$parts_pocode_matches = array_slice($parts_pocode_matches, 0, 100);
		?>
		
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>วันที่รับสินค้า :</b>
			</div>
			<div style="width: 58%; float: left;">
				<input type="text" name="app_sentpartdate" id="app_sentpartdate" class="datepicker" value="<?php echo date("d-m-Y", strtotime($purchaseOrderPart_result["app_sentpartdate"])); ?>" />
			</div>
			<div style="clear: both;"></div>
		</div>
		
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>กำหนดชำระเงิน (วัน) :</b>
			</div>
			<div style="width: 58%; float: left;">
				<input type="text" name="credit_terms" id="credit_terms"  value="<?php echo $purchaseOrderPart_result["credit_terms"]; ?>"/>
			</div>
			<div style="clear: both;"></div>
		</div>
		
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>วันที่ชำระเงิน :</b>
			</div>
			<div style="width: 58%; float: left;">
				<input type="text" name="esm_paydate" id="esm_paydate" class="datepicker" value="<?php echo date("d-m-Y", strtotime($purchaseOrderPart_result["esm_paydate"])); ?>" />
			</div>
			<div style="clear: both;"></div>
		</div>
		
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				
			</div>
			<div style="width: 58%; float: left;">
				
			</div>
			<div style="clear: both;"></div>
		</div>
		
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>ผู้ขาย :</b>
			</div>
			<div style="width: 58%; float: left;">
<?php
				$qry = pg_query("SELECT * FROM \"VVenders\" WHERE type_ven = 'M' or type_ven='B' ORDER BY pre_name,cus_name ASC");
				while( $res = pg_fetch_array($qry) ){
				    $vender_id = $res['vender_id'];
				    $pre_name = trim($res['pre_name']);
				    $cus_name = trim($res['cus_name']);
				    $surname = trim($res['surname']);
					$branch_id = trim($res['branch_id']);
					echo "$pre_name $cus_name $surname";
				}
?>
			</div>
			<div style="clear: both;"></div>
		</div>
		
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>ใบสั่งซื้อ มี VAT :</b>
			</div>
			<div style="width: 58%; float: left;">
				<select name="vat_status" id="vat_status">
					<option value="1" <?php 
						if($purchaseOrderPart_result["vat_status"] == "1"){
							echo "selected='selected'"; 
						}
					?>>คิด VAT</option>
					<option value="0" <?php 
						if($purchaseOrderPart_result["vat_status"] == "0"){
							echo "selected='selected'"; 
						}
					?>>ไม่คิด VAT</option>
				</select>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	<div style="width: 50%; float:left; ">
		
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>เลขที่ใบส่งของ / ใบแจ้งหนี้ :</b>
			</div>
			<div style="width: 58%; float: left;">
				<input type="text" name="inv_no" id="inv_no" class="" value="" />
			</div>
			<div style="clear: both;"></div>
		</div>
		
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>เลขที่ใบเสร็จ / ใบกำกับ :</b>
			</div>
			<div style="width: 58%; float: left;">
				<input type="text" name="receipt_no" id="receipt_no" class="" value="" />
			</div>
			<div style="clear: both;"></div>
		</div>
		
	</div>
	<div style="clear: both;"></div>
	
	
	<div style="font-size:12px">
		<!-- ##################### Middle ####################### -->
		
			<div style="margin-top:10px; ">
				<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
					<tr style="font-weight:bold; text-align:center; " bgcolor="#D0D0D0">
						<td width="5%">ลำดับที่</td>
						<td width="10%">รหัสสินค้า</td>
						<td width="15%">ชื่อสินค้า</td>
						<td width="20%">รายละเอียดสินค้า</td>
						<td width="5%">จำนวน</td>
						<td width="5%">หน่วย</td>
						<td width="10%">ราคา/หน่วย</td>
						<td width="5%">จำนวนรับ</td>
						<td width="10%">จำนวนเงิน</td>
						<td width="10%">คลัง</td>
						<td width="5%">ชั้นวาง</td>
					</tr>
<?php
					
					//Find purchaseOrderPart Parts_name, Parts_Detail
					function find_Parts_value($parts_code, $output){
						$purchaseOrderPart_strQuery = "
							SELECT
								\"code\",
								\"name\",
								\"details\"
							FROM
								\"parts\"
							WHERE 
								\"code\" = '{$parts_code}'; 
						";
						$purchaseOrderPart_query = pg_query($purchaseOrderPart_strQuery);
						while ($purchaseOrderPart_result = pg_fetch_array($purchaseOrderPart_query)) {
							echo $purchaseOrderPart_result[$output];
						}
					}
					
					function find_Parts_unit_value($unitid){
						$strQuery_parts_unit = "
							SELECT unitname
							FROM \"parts_unit\" 
							WHERE
								unitid = '{$unitid}'
						";
						$qry_parts_unit = @pg_query($strQuery_parts_unit);
						while($res_parts_unit = @pg_fetch_array($qry_parts_unit)){
							echo $res_parts_unit['unitname'];
						}
					}
					
					$warehouses_strQuery = "
						SELECT 
							\"wh_id\",
							\"wh_name\"
						FROM
							\"Warehouses\"
						WHERE
							wh_group = 1
					";
					$warehouses_query = pg_query($warehouses_strQuery);
					$warehouses_result = pg_fetch_all($warehouses_query);
					
					$locate_strQuery = "
						SELECT 
							\"locate_id\",
							\"locate_name\"
						FROM
							\"Locate\"
					";
					$locate_query = pg_query($locate_strQuery);
					$locate_result = pg_fetch_all($locate_query);
					
										
					$purchaseOrderPartsDetails_strQuery = "
						SELECT
							\"idno\",
							\"parts_code\",
							\"quantity\",
							\"unit\",
							\"costperunit\",
							\"total\"
						FROM
							\"PurchaseOrderPartsDetails\"
						WHERE 
							\"parts_pocode\" = '{$parts_pocode}'; 
					";
					$purchaseOrderPartsDetails_query = pg_query($purchaseOrderPartsDetails_strQuery);
					$purchaseOrderPartsDetails_numrows = pg_num_rows($purchaseOrderPartsDetails_query);
					while ($purchaseOrderPartsDetails_result = pg_fetch_array($purchaseOrderPartsDetails_query)) {
						$idno = $purchaseOrderPartsDetails_result["idno"];
?>
						<tr bgcolor="#FFFFFF">
							<td>
								<?php echo $idno; ?>.
							</td>
							<td>
								<span id="parts_code<?php echo $idno; ?>"><?php echo $purchaseOrderPartsDetails_result["parts_code"]; ?></span>
								<input type="hidden"  name="parts_code<?php echo $idno; ?>" class="parts_code" data-code_id="<?php echo $idno; ?>" value="<?php echo $purchaseOrderPartsDetails_result["parts_code"]; ?>" />
							</td>
							
							<td>
								<span id="parts_name<?php echo $idno; ?>" class="parts_name"><?php echo find_Parts_value($purchaseOrderPartsDetails_result["parts_code"], "name"); ?></span>
								<input type="hidden" name="parts_name<?php echo $idno; ?>" class="parts_name" value="<?php echo find_Parts_value($purchaseOrderPartsDetails_result["parts_code"], "name"); ?>" />
							</td>
							<td>
								<span id="parts_detail<?php echo $idno; ?>" class="parts_detail"><?php echo find_Parts_value($purchaseOrderPartsDetails_result["parts_code"], "details"); ?></span>
							</td>
							<td align="center">
<?php
								// Get the Used Quantity
								$rcv_quantity_count = 0;
						    	$received_quantity_strQuery = "
									select 
										parts_code,
										SUM(rcv_quantity) AS rcv_quantity_count
									from 
										\"PartsReceivedDetails\" 
									where 
										parts_rcvcode IN 
										(
											select parts_rcvcode 
											from \"PartsReceived\" 
											where parts_pocode = '".$purchaseOrderPart_result["parts_pocode"]."'
										) 
										AND
										parts_code = '".$purchaseOrderPartsDetails_result['parts_code']."'
									group by parts_code ;
								";
								$received_quantity_query = pg_query($received_quantity_strQuery);
				    			while($received_quantity_result = pg_fetch_array($received_quantity_query)){
				    				$rcv_quantity_count = $received_quantity_result["rcv_quantity_count"];
								}
?>
								<input type="hidden" name="quantity<?php echo $idno; ?>" id="quantity<?php echo $idno; ?>" class="quantity" data-quantity_id="<?php echo $idno; ?>" value="<?php echo $purchaseOrderPartsDetails_result["quantity"] - $rcv_quantity_count; ?>" />
								<span id="quantity<?php echo $idno; ?>" class="quantity" ><?php echo $purchaseOrderPartsDetails_result["quantity"] - $rcv_quantity_count; ?></span>
							</td>
							<td align="center">
								<input type="hidden" name="unit<?php echo $idno; ?>" class="unit" value="<?php echo $purchaseOrderPartsDetails_result["unit"]; ?>" />
								<span id="unit<?php echo $idno; ?>" class="unit"><?php echo find_Parts_unit_value($purchaseOrderPartsDetails_result["unit"]); ?></span>
							</td>
							<td>
								<input type="text" name="costperunit<?php echo $idno; ?>" id="costperunit<?php echo $idno; ?>" class="costperunit" data-costperunit_id="<?php echo $idno; ?>" value="<?php echo $purchaseOrderPartsDetails_result["costperunit"] ; ?>" style="width:80px; text-align:right" />
							</td>
							<td align="right">
								<input type="text" name="rcv_quantity<?php echo $idno; ?>" id="rcv_quantity<?php echo $idno; ?>" data-rcv_quantity_id="<?php echo $idno; ?>" class="rcv_quantity" value="<?php echo $purchaseOrderPartsDetails_result["quantity"] - $rcv_quantity_count; ?>" style="width:40px; text-align:right; " />
							</td>
							<td align="right">
								<span id="total<?php echo $idno; ?>" class="total" style="font-weight:bold"><?php echo number_format($purchaseOrderPartsDetails_result["total"], 2); ?></span>
								<input type="hidden" name="total<?php echo $idno; ?>" class="total" value="<?php echo $purchaseOrderPartsDetails_result["total"]; ?>" />
							</td>
							<td>
								<select name="wh_id<?php echo $idno; ?>" id="wh_id<?php echo $idno; ?>" class="wh_id">
									<option value="">โปรดเลือกคลัง</option>
<?php
									foreach ($warehouses_result as $warehouses_data) {
										?><option value="<?php echo $warehouses_data["wh_id"] ?>"><?php echo $warehouses_data["wh_name"] ?></option><?php
									}
?>
								</select>
							</td>
							<td>
								<select name="locate_id<?php echo $idno; ?>" id="locate_id<?php echo $idno; ?>" class="locate_id">
									<option value="">โปรดเลือกชั้นวาง</option>
<?php
									foreach ($locate_result as $locate_data) {
										?><option value="<?php echo $locate_data["locate_id"] ?>"><?php echo $locate_data["locate_name"] ?></option><?php
									}
?>
								</select>
							</td>
						</tr>
<?php
					}
?>
				</table>
				
				<div class="linedotted"></div>
			
				<!-- ############## footer ############## -->
<?php
				
?>
				<div style="width: 50%; float: left; margin-left: 50%; height: 30px;">
					<!-- PO type -->
					<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
						<b>เงินรวมก่อนหักส่วนลด : </b>
					</div>
					<div style="width: 48%; float: left; margin-top: 1.2%;">
						<span id="dsubtotal"><?php echo number_format($purchaseOrderPart_result["subtotal"], 2); ?></span>
					</div>
					<div style="clear: both;"></div>
				</div>
				<div style="clear: both;"></div>
				
				
				<div style="width: 50%; float: left; height: 30px; ">
					<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
						<b>%ส่วนลด : </b>
					</div>
					<div style="width: 48%; float: left;">
						<input type="text" name="pcdiscount" id="pcdiscount" value="<?php echo $purchaseOrderPart_result["pcdiscount"]; ?>" />
					</div>
					<div style="clear: both;"></div>
				</div>
				
				<div style="width: 50%; float: left; height: 30px; ">
					<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
						<b>จำนวนเงินส่วนลด :</b>
					</div>
					<div style="width: 48%; float: left;">
						<input type="text" name="discount" id="discount" class="" value="<?php echo $purchaseOrderPart_result["discount"]; ?>" />
					</div>
					<div style="clear: both;"></div>
				</div>
				<div style="clear: both;"></div>
				
				
				<div style="width: 50%; float: left; margin-left: 50%; height: 30px; ">
					<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
						<b>จำนวนเงินรวมก่อนภาษีมูลค่าเพิ่ม :</b>
					</div>
					<div style="width: 48%; float: left; margin-top: 1.2%;">
						<span id="vsubtotal"><?php echo number_format($purchaseOrderPart_result["bfv_total"], 2); ?></span>
					</div>
					<div style="clear: both;"></div>
				</div>
				<div style="clear: both;"></div>
				
				
				<div style="width: 50%; float: left; height: 30px; ">
					<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
						<b>%ภาษีมูลค่าเพิ่ม :</b>
					</div>
					<div style="width: 48%; float: left;">
						<input type="text" name="pcvat" id="pcvat" class="" value="<?php echo $purchaseOrderPart_result["pcvat"]; ?>" />
					</div>
					<div style="clear: both;"></div>
				</div>
				
				<div style="width: 50%; float: left; height: 30px; ">
					<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
						<b>จำนวนภาษี :</b>
					</div>
					<div style="width: 48%; float: left; margin-top: 1.2%;">
						<span id="vat"><?php echo number_format($purchaseOrderPart_result["vat"], 2); ?></span>
					</div>
					<div style="clear: both;"></div>
				</div>
				<div style="clear: both;"></div>
			
			
				<div style="width: 50%; float: left; margin-left: 50%; height: 30px; ">
					<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
						<b>จำนวนรวมสุทธิ :</b>
					</div>
					<div style="width: 48%; float: left; margin-top: 1.2%;">
						<span id="nettotal"><?php echo number_format($purchaseOrderPart_result["nettotal"], 2); ?></span>
					</div>
					<div style="clear: both;"></div>
				</div>
				<div style="clear: both;"></div>
				
				
				<div style="width: 100%; float: left; height: 30px; ">
					<div>
						<b>หมายเหตุ</b>
					</div>
					<div>
						<textarea name="recv_remark" id="recv_remark" rows="2" cols="70"></textarea>
					</div>
				</div>
				
				
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
<?php
	// $dsubtotal = number_format($purchaseOrderPart_result["subtotal"], 2);
	// $pcdiscount = $purchaseOrderPart_result["pcdiscount"];
	// $discount = $purchaseOrderPart_result["discount"];
	// $vsubtotal = number_format($purchaseOrderPart_result["bfv_total"], 2);
	// $pcvat = $purchaseOrderPart_result["pcvat"];
	// $vat = number_format($purchaseOrderPart_result["vat"], 2);
	// $nettotal = number_format($purchaseOrderPart_result["nettotal"], 2);
	
	$dsubtotal = $purchaseOrderPart_result["subtotal"];
	$pcdiscount = $purchaseOrderPart_result["pcdiscount"];
	$discount = $purchaseOrderPart_result["discount"];
	$vsubtotal = $purchaseOrderPart_result["bfv_total"];
	$pcvat = $purchaseOrderPart_result["pcvat"];
	$vat = $purchaseOrderPart_result["vat"];
	$nettotal = $purchaseOrderPart_result["nettotal"];
}
		/*
		//######################## Initial Calculate Variables ############################
		var dsubtotal = <?php echo number_format($purchaseOrderPart_result["subtotal"], 2); ?>;
		var pcdiscount = <?php echo $purchaseOrderPart_result["pcdiscount"]; ?>;
		var discount = <?php echo $purchaseOrderPart_result["discount"]; ?>;
		var vsubtotal = <?php echo number_format($purchaseOrderPart_result["bfv_total"], 2); ?>;
		var pcvat = <?php echo $purchaseOrderPart_result["pcvat"]; ?>;
		var vat = <?php echo number_format($purchaseOrderPart_result["vat"], 2); ?>;
		var nettotal = <?php echo number_format($purchaseOrderPart_result["nettotal"], 2); ?>;
		//################################################################
		*/
?>
<script>
	//######################## Initial Calculate Variables ############################
	
	var dsubtotal = 0;
	var pcdiscount = 0;
	var discount = 0;
	var vsubtotal = 0;
	var pcvat = 7;
	var vat = 0;
	var nettotal = 0;
	
	dsubtotal = <?php echo $dsubtotal; ?>*1.0;
	pcdiscount = <?php echo $pcdiscount; ?>*0.01;
	discount = <?php echo $discount; ?>*1.0;
	vsubtotal = <?php echo $vsubtotal; ?>*1.0;
	pcvat = <?php echo $pcvat; ?>*0.01;
	vat = <?php echo $vat; ?>*1.0;
	nettotal = <?php echo $nettotal; ?>*1.0;
	
	console.log("dsubtotal = " + dsubtotal);
	console.log("pcdiscount = " + pcdiscount);
	console.log("discount = " + discount);
	console.log("vsubtotal = " + vsubtotal);
	console.log("pcvat = " + pcvat);
	console.log("vat = " + vat);
	console.log("nettotal = " + nettotal);

	
	//################################################################
	
	$(function() {
		$(".datepicker").datepicker({
			dateFormat: 'dd-mm-yy' ,
		});
	}); 
	
	//For Test has_PurchaseOrderPart == ?
	// console.log(PurchaseOrderPart[1][0]);
	
	// console.log(parts[1]);
	// console.log(parts[1][0]);
	// console.log("parts.length = " + parts.length);
	
	
	//counter => Count how many rows
	var counter = <?php echo $purchaseOrderPartsDetails_numrows; ?>;
	
	//############### Delete ###################
	
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
    			$("#pcvat").val("7");
    			pcvat = 0.07;
    			vat_status_change();
    		}
    		else if(vat_status_value == "0"){
    			$("#pcvat").prop("disabled", true);
    			$("#pcvat").val("0");
    			pcvat = 0.0;
    			vat_status_change();
    		}
    	});
	});
	
	// parseFloat(vat).toFixed(2)
	
	function vat_status_change(){
		console.log("vsubtotal = " + vsubtotal);
		vat = vsubtotal * pcvat;
		$("#vat").html(numberWithCommas(parseFloat(vat).toFixed(2)));
		nettotal = vsubtotal + vat;
		$("#nettotal").html(numberWithCommas(parseFloat(nettotal).toFixed(2)));
	}
	
	//######################## Calculate Mode ############################
	
	$(".rcv_quantity").live("keyup", function(){
		var id = $(this).data("rcv_quantity_id");
		var rcv_quantity = $(this).val();
		if(rcv_quantity == ""){
			rcv_quantity = 0;
		}
		var costperunit = $(".costperunit#costperunit"+id).val();
		if(costperunit == ""){
			costperunit = 0;
		}
		var total_each_row = rcv_quantity * costperunit;
		
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
		pcvat = $(this).val();
		console.log("pcvat = " + pcvat);
		vat = vsubtotal * (pcvat / 100.0);
		
		calculate_total();
	});
	
	
	function calculate_total(){
		var dsubtotal_value = 0;
		var rcv_quantity = 0;
		var costperunit = 0;
		var i=0;
		
		for(i=1; i <= counter; i++){
			
			//dsubtotal
			//เงินรวมก่อนหักส่วนลด
			rcv_quantity = ($(".rcv_quantity#rcv_quantity"+i).val());
			if(rcv_quantity == ""){
				rcv_quantity = 0;
			}
			
			costperunit = ($(".costperunit#costperunit"+i).val());
			if(costperunit == ""){
				costperunit = 0;
			}
			dsubtotal_value += rcv_quantity * costperunit; 
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
		
		vat = parseFloat(vsubtotal * (pcvat / 100.0)).toFixed(2);
		
		//vat
		//จำนวนภาษี
		console.log("vat = " + vat);
		$("#vat").html(numberWithCommas(vat));
		
		//nettotal
		//จำนวนรวมสุทธิ
		nettotal = parseFloat((vsubtotal * 1.0) + (vat * 1.0)).toFixed(2);
		console.log("nettotal = " + numberWithCommas(nettotal));
		$("#nettotal").html(numberWithCommas(nettotal));
	}
	
	function numberWithCommas(x) {
	    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	
	//########## Submit ###########
	$('#btnSubmit').click(function(){
		
		//### Header ###
		var _app_sentpartdate = $('#app_sentpartdate').val();
		var _credit_terms = $('#credit_terms').val();
		var _esm_paydate = $('#esm_paydate').val();
		var _vat_status = $('#vat_status').val();
		var _inv_no = $('#inv_no').val();
		var _receipt_no = $('#receipt_no').val();
		
		
		var check_validate = 1;
		
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
		if(_vat_status == ""){
			alert('กรุณาเลือก ใบสั่งซื้อ มี VAT');
			return false;
		}
		if(_inv_no == ""){
			alert('กรุณากรอก เลขที่ใบส่งของ / ใบแจ้งหนี้');
			return false;
		}
		if(_receipt_no == ""){
			alert('กรุณากรอก เลขที่ใบเสร็จ / ใบกำกับ');
			return false;
		}
		
		
		//### Middle ###
		var arradd = new Array();
		for( i=1; i<=counter; i++ ){
			
			// var _idno = $('#idno'+i).val();
			var _parts_code = $('.parts_code[name=parts_code' + i + ']').val();
			var _quantity = $(".quantity#quantity" + i).val();
			var _rcv_quantity = $('#rcv_quantity' + i).val();
			var _costperunit = $('#costperunit' + i).val();
			var _total = $('.total[name=total'+i+']').val();
			var _unit = $(".unit[name=unit" + i + "]").val();
			var _wh_id = $(".wh_id#wh_id" + i).val();
			var _locate_id = $(".locate_id#locate_id" + i).val();
			
			// if(_parts_code == ""){
				// alert('กรุณากรอกจำนวน _parts_code (รายการที่ '+i+')');
				// return false;
			// }
			if(_rcv_quantity == "" || _rcv_quantity == 0){
				alert('กรุณากรอกจำนวน (รายการที่ '+i+')');
				return false;
			}
			else if(_quantity > _rcv_quantity){
				alert('กรุณากรอกจำนวนที่ต้องการ โดยที่จะไม่เกิน จำนวนที่มีอยู่ (รายการที่ '+i+')');
				return false;
			}
			
			if(_costperunit == "" || _costperunit == 0){
				alert('กรุณากรอก ราคา/หน่วย (รายการที่ '+i+')');
				return false;
			}
			if(_total == "" || _total == 0){
				return false;
			}
			
			if(_wh_id == "" || _wh_id == 0){
				alert('กรุณาเลือก คลัง (รายการที่ '+i+')');
				return false;
			}
			if(_locate_id == "" || _locate_id == 0){
				alert('กรุณาเลือก ชั้นวาง (รายการที่ '+i+')');
				return false;
			}
			
			arradd[i-1] = {
				idno: i, 
				parts_code: _parts_code, 
				unit: _unit,
				costperunit: _costperunit, 
				rcv_quantity: _rcv_quantity, 
				total: _total,
				wh_id: _wh_id,
				locate_id: _locate_id,
			};
		}
		
		//### Footer ###
		var _pcdiscount = $('#pcdiscount').val();
		var _discount = $('#discount').val();
		var _pcvat = $('#pcvat').val();
		var _recv_remark = $('#recv_remark').val();
		
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
		if(_recv_remark == ""){
			alert('กรุณากรอก หมายเหตุ');
			return false;
		}
		
		$.post('po_receive_detail_save.php',{
			type : "<?php echo $purchaseOrderPart_type; ?>",
			parts_pocode: "<?php echo $parts_pocode; ?>",
			app_sentpartdate: _app_sentpartdate,
			credit_terms: _credit_terms,
			esm_paydate: _esm_paydate,
			vat_status: _vat_status,
			inv_no: _inv_no,
			receipt_no: _receipt_no,
			
			parts_received_details_array: JSON.stringify(arradd),
			
			dsubtotal: dsubtotal,
			pcdiscount: _pcdiscount,
			discount: _discount,
			vsubtotal: vsubtotal,
			pcvat: _pcvat,
			vat: vat,
			nettotal: nettotal,
			recv_remark: _recv_remark,
			
		},
		function(data){
			if(data.success){
				ShowPrint(data.parts_pocode);
				console.log("data.success = " + data.success);
				console.log("data.message = " + data.message);
				console.log("data.test = " + data.test);
				//location.reload();
			}else{
				// alert(data.message);
				console.log("data.success = " + data.success);
				console.log("data.message = " + data.message);
				console.log("data.test = " + data.test);
			}
		},'json');
	});
	
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
<?php
if(isset($_POST["display_type"])){
	$display_type = pg_escape_string($_POST["display_type"]);
}
if(!isset($display_type)){
	$display_type = 1;
}

if($display_type == 2){
	$display_type2 = pg_escape_string($_POST["display_type2"]);
}
elseif($display_type == 3){
	$po_id = pg_escape_string($_POST["po_id"]);
}

// $function = new Model_po_receive();

?>
<div style="width:870px; overflow-y: hidden; overflow-x: auto; ">
	<form action="po_receive.php" method="post" accept-charset="UTF-8" >
		<fieldset>
			<legend>ค้นหาข้อมูล</legend>
			<div class="search div1">
				<select id="display_type" name="display_type">
					<option value="1" <?php 
						if($display_type == 1){
							?>selected='selected'<?php
						}
					?>>แสดงทั้งหมด</option>
					<option value="2" <?php 
						if($display_type == 2){
							?>selected='selected'<?php
						}
					?>>ประเภทใบสั่งซื้อ</option>
					<option value="3" <?php 
						if($display_type == 3){
							?>selected='selected'<?php
						}
					?>>เลขที่ใบสั่งซื้อ</option>
				</select>
			</div>
			<div class="search div2">
<?php
				if($display_type == 2){
?>
					<select id="display_type2" name="display_type2">
						<option value="0">เลือกประเภท</option>
						<option value="1" <?php
							if($display_type2 == 1){
								?>selected='selected'<?php
							}
						?>>มาจากการสั่งซื้อ</option>
						<option value="2" <?php
							if($display_type2 == 2){
								?>selected='selected'<?php
							}
						?>>มาจากการสั่งประกอบชิ้นงาน</option>
					</select>
<?php
				}
				elseif ($display_type == 3) {
?>
					<input type="text" name="po_id" value="<?php echo $po_id; ?>" style="width: 70%; " />
<?php
				}
?>
			</div>
			<div class="search div3">
				<input type="submit" value="ค้นหา" />
			</div>
		</fieldset>
	</form>
	<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
		<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
			<td width="80"><b>รหัสสั่งซื้อ</b></td>
			<td width="50"><b>วันที่ใบสั่งซื้อ</b></td>
			<!-- <td width="50"><b>กำหนดชำระเงิน(วัน)</b></td> -->
			<td width="50"><b>วันที่นัดส่งของ</b></td>
			<!-- <td width="50"><b>ประมาณการวันที่ชำระเงิน</b></td> -->
			
			
			<!-- <td width="50">รหัสสินค้า</td> -->
		    <!-- <td width="50">ชื่อสินค้า</td> -->
		    <td width="100">รายละเอียดสินค้า</td>
		    <td width="50">จำนวน</td>
		    <td width="50">หน่วย</td>
		    <!-- <td width="50">ราคา/หน่วย</td> -->
		    <!-- <td width="50">จำนวนเงิน</td> -->
			
			<!-- <td width="50"><b>เงินรวมก่อนหักส่วนลด</b></td> -->
			<!-- <td width="50"><b>ส่วนลด</b></td> -->
			<!-- <td width="50"><b>เงินรวมก่อนภาษีมูลค่าเพิ่ม</b></td> -->
			<!-- <td width="50"><b>จำนวนภาษีมูลค่าเพิ่ม</b></td> -->
			<td width="50"><b>จำนวนรวมสุทธิ</b></td>
			<td width="50" style="text-align:center;">ทำรายการรับ</td>
		</tr>
		
		<?php
		$j = 0;
		
		if($display_type == 1){
			$purchaseOrderPart = get_purchaseOrderPart($display_type);
		}
		elseif($display_type == 2){
			$purchaseOrderPart = get_purchaseOrderPart($display_type, $display_type2);
		}
		elseif($display_type == 3){
			$purchaseOrderPart = get_purchaseOrderPart($display_type, '', $po_id);
		}
		
		if($purchaseOrderPart != ""){
			foreach ($purchaseOrderPart as $purchaseOrderPart_result) {
				
				// Check Received Quantity Count
				if(received_quantity_check($purchaseOrderPart_result["parts_pocode"]) == 0){
	?>
					<tr bgcolor="#E1F0FF" style="font-weight:bold">
					    <td><?php echo $purchaseOrderPart_result["parts_pocode"]; ?></td>
					    <td><?php echo $purchaseOrderPart_result["date"]; ?></td>
					    <!-- <td><?php echo $purchaseOrderPart_result["credit_terms"]; ?></td> -->
					    <td><?php echo $purchaseOrderPart_result["app_sentpartdate"]; ?></td>
					    <!-- <td><?php echo $purchaseOrderPart_result["esm_paydate"]; ?></td> -->
					    
					    <td colspan="3"></td>
					    
					    <!-- <td><?php echo number_format($purchaseOrderPart_result["subtotal"], 2); ?></td> -->
					    <!-- <td><?php echo number_format($purchaseOrderPart_result["discount"], 2); ?></td> -->
					    <!-- <td><?php echo number_format($purchaseOrderPart_result["bfv_total"], 2); ?></td> -->
					    <!-- <td><?php echo number_format($purchaseOrderPart_result["vat"], 2); ?></td> -->
					    <td><?php echo number_format($purchaseOrderPart_result["nettotal"], 2); ?></td>
					    <td>
					    	<input type="image" src="../images/viewdetails.gif" value="แสดงรายละเอียด" onclick="javascript:ShowDetail('po_receive_detail.php', '<?php echo $purchaseOrderPart_result["parts_pocode"]; ?>')" style="cursor:pointer;" alt="ทำรายการรับ" title="ทำรายการรับ" />
					    </td>
					</tr>
	<?php
					$purchaseOrderPartsDetails = get_purchaseOrderPartsDetails($purchaseOrderPart_result["parts_pocode"]);
					foreach ($purchaseOrderPartsDetails as $purchaseOrderPartsDetails_result) {
				    	
				    	$j++;
						
						$received_quantity = get_received_quantity(
							$purchaseOrderPart_result["parts_pocode"],
							$purchaseOrderPartsDetails_result['parts_code']
						);
						
						$received_quantity_numrow = $received_quantity["numrow"];
						$rcv_quantity_count = $received_quantity["rcv_quantity_count"][0]["rcv_quantity_count"];
						
						if($received_quantity_numrow == 0){
							$rcv_quantity_count = 0;
						}
						
		?>
						<tr bgcolor="#FFFFFF">
						    <td colspan="1"></td>
						    
						    <td><?php echo $purchaseOrderPartsDetails_result['parts_code']; ?></td>
						    <td><?php echo $purchaseOrderPartsDetails_result['name']; ?></td>
						    <td><?php echo $purchaseOrderPartsDetails_result['details']; ?></td>
						    <td><?php echo $purchaseOrderPartsDetails_result['quantity']; ?></td>
						    <td><?php echo read_Parts_Unit($purchaseOrderPartsDetails_result['unit']); ?></td>
						    <!-- <td><?php echo number_format($purchaseOrderPartsDetails_result['costperunit'], 2); ?></td> -->
						    <!-- <td><?php echo number_format($purchaseOrderPartsDetails_result['total'], 2); ?></td> -->
						    
						    <td colspan="1"></td>
						    
						    <td>
	<?php
								echo $rcv_quantity_count."/".$purchaseOrderPartsDetails_result['quantity'];
	?>
						    </td>
						</tr>
		<?php
					}
				} // End Check Received Quantity Count
			}
		}
		if($j == 0){
		    echo "<tr><td colspan=6 align=center>- ไม่พบข้อมูล -</td></tr>";
		}
?>
	</table>
</div>

<script>
function ShowDetail(url, id){
    $('body').append('<div id="divdialogadd"></div>');
    $('#divdialogadd').load(url + '?parts_pocode=' + id + '');
    $('#divdialogadd').dialog({
        title: 'แสดงรายละเอียด parts POcode : ' + id,
        resizable: false,
        modal: true,  
        width: 900,
        height: 600,
        close: function(ev, ui){
            $('#divdialogadd').remove();
        }
    });
}

$(".search.div1 #display_type").live("change", function(){
	var type = $(this).val();
	if(type == 1){
		var str = '';
	}
	else if(type == 2){
		var str = 
		'<select id="display_type2" name="display_type2">'+
		'	<option value="0">เลือกประเภท</option>'+
		'	<option value="1">มาจากการสั่งซื้อ</option>'+
		'	<option value="2">มาจากการสั่งประกอบชิ้นงาน</option>'+
		'</select>';
	}
	else if(type == 3){
		var str = 
		'<input type="text" name="po_id" style="width: 70%; " />';
	}
	$(".search.div2").html(str);
});
</script>

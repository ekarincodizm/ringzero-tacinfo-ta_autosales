<div style="width:870px; overflow-y: hidden; overflow-x: auto; ">

	<table cellpadding="3" cellspacing="1" border="0" width="2000" bgcolor="#F0F0F0">
		<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
			<td width="80"><b>รหัสสั่งซื้อ</b></td>
			<td width="50"><b>วันที่ใบสั่งซื้อ</b></td>
			<td width="50"><b>กำหนดชำระเงิน(วัน)</b></td>
			<td width="50"><b>วันที่นัดส่งของ</b></td>
			<td width="50"><b>ประมาณการวันที่ชำระเงิน</b></td>
			
			
			<td width="50">รหัสสินค้า</td>
		    <td width="50">ชื่อสินค้า</td>
		    <td width="50">รายละเอียดสินค้า</td>
		    <td width="50">จำนวน</td>
		    <td width="50">หน่วย</td>
		    <td width="50">ราคา/หน่วย</td>
		    <td width="50">จำนวนเงิน</td>
			
			<td width="50"><b>เงินรวมก่อนหักส่วนลด</b></td>
			<td width="50"><b>ส่วนลด</b></td>
			<td width="50"><b>เงินรวมก่อนภาษีมูลค่าเพิ่ม</b></td>
			<td width="50"><b>จำนวนภาษีมูลค่าเพิ่ม</b></td>
			<td width="50"><b>จำนวนรวมสุทธิ</b></td>
			<td width="50" style="text-align:center;">แก้ไข</td>
		</tr>
		
		<?php
		$j = 0;
		
		function read_Parts_Unit($unitid){ //Return UnitName
			$parts_unit_strQuery = "
				SELECT 
					\"unitname\"
				FROM 
					\"parts_unit\"
				WHERE
					\"unitid\" = '".$unitid."'
			";
			$parts_unit_query = pg_query($parts_unit_strQuery);
			while($parts_unit_result = pg_fetch_array($parts_unit_query)){
				echo $parts_unit_result["unitname"];
			}
		}
		
		$purchaseOrderPart_strQuery = "
			SELECT * 
			FROM 
				\"PurchaseOrderPart\" 
			WHERE 
				\"status\" = '2'
			ORDER BY parts_pocode ASC 
		";
		$purchaseOrderPart_query = pg_query($purchaseOrderPart_strQuery);
		
		while($purchaseOrderPart_result = pg_fetch_array($purchaseOrderPart_query)){
		    $j++;
		?>
		<tr bgcolor="#E1F0FF" style="font-weight:bold">
		    <td><?php echo $purchaseOrderPart_result["parts_pocode"]; ?></td>
		    <td><?php echo $purchaseOrderPart_result["date"]; ?></td>
		    <td><?php echo $purchaseOrderPart_result["credit_terms"]; ?></td>
		    <td><?php echo $purchaseOrderPart_result["app_sentpartdate"]; ?></td>
		    <td><?php echo $purchaseOrderPart_result["esm_paydate"]; ?></td>
		    
		    <td colspan="7"></td>
		    
		    <td><?php echo number_format($purchaseOrderPart_result["subtotal"], 2); ?></td>
		    <td><?php echo number_format($purchaseOrderPart_result["discount"], 2); ?></td>
		    <td><?php echo number_format($purchaseOrderPart_result["bfv_total"], 2); ?></td>
		    <td><?php echo number_format($purchaseOrderPart_result["vat"], 2); ?></td>
		    <td><?php echo number_format($purchaseOrderPart_result["nettotal"], 2); ?></td>
		    <td>
		    	<input type="button" value="แสดงรายละเอียด" onclick="javascript:ShowDetail('po_receive_detail.php', '<?php echo $purchaseOrderPart_result["parts_pocode"]; ?>')" style="cursor:pointer;" alt="แก้ไขรายละเอียด" title="แก้ไขรายละเอียด" />
		    </td>
		</tr>
<?php
			//แสดง Details
			$purchaseOrderPartsDetails_strQuery = "
				SELECT 
					* 
				FROM 
					\"PurchaseOrderPartsDetails\" 
				JOIN 
					\"parts\"
				ON
					\"parts\".code = \"PurchaseOrderPartsDetails\".parts_code
				WHERE 
					\"PurchaseOrderPartsDetails\".parts_pocode = '".$purchaseOrderPart_result["parts_pocode"]."'
				ORDER BY auto_id ASC 
			";
		    $purchaseOrderPartsDetails_query = pg_query($purchaseOrderPartsDetails_strQuery);
		    while($purchaseOrderPartsDetails_result = pg_fetch_array($purchaseOrderPartsDetails_query)){
?>
				<tr bgcolor="#FFFFFF">
				    <td colspan="5"></td>
				    
				    <td><?php echo $purchaseOrderPartsDetails_result['parts_code']; ?></td>
				    <td><?php echo $purchaseOrderPartsDetails_result['name']; ?></td>
				    <td><?php echo $purchaseOrderPartsDetails_result['details']; ?></td>
				    <td><?php echo $purchaseOrderPartsDetails_result['quantity']; ?></td>
				    <td><?php echo read_Parts_Unit($purchaseOrderPartsDetails_result['unit']); ?></td>
				    <td><?php echo number_format($purchaseOrderPartsDetails_result['costperunit'], 2); ?></td>
				    <td><?php echo number_format($purchaseOrderPartsDetails_result['total'], 2); ?></td>
				    
				    <td colspan="5"></td>
				    
				    <td>
<?php
				    	/*
				    	$received_strQuery = "
				    		SELECT 
				    			* 
				    		FROM 
				    			v_parts_received__quantity_2
				    		WHERE
				    			parts_pocode = ''
				    	";
						*/
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
		    				echo $received_quantity_result["rcv_quantity_count"];
						}
?>
						/ <?php echo $purchaseOrderPartsDetails_result['quantity']; ?>
				    </td>
				</tr>
<?php
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
</script>

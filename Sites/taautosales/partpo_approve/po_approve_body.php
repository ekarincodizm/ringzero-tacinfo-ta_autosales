<div class="roundedcornr_box" style="width: 900px; ">
	<div class="roundedcornr_content">

<?php
		include_once("../include/header_popup.php");
?>
		
		<!-- <div style="text-align:left; padding-top:0px;"> -->
		<!-- <input type="button" name="btnNew" id="btnNew" value="เพิ่มสินค้า" onclick="window.location='add_product.php'">&nbsp; -->
		<!-- </div> -->
		
		<div style="margin-top:5px; font-weight: bold">รายการสินค้า</div>
		<div style="width:870px; overflow-y: hidden; overflow-x: auto; ">
			<table  border="1" bordercolor="#FFFFFF" cellspacing="0" cellpadding="4" style="width: 830px;">
				<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center; background-color: #D0D0D0;">
				    <td width="80" ><b>รหัสสินค้า</b></td>
				    <td width="50"><b>วันที่ใบสั่งซื้อ</b></td>
				    <td width="50"><b>กำหนดชำระเงิน(วัน)</b></td>
				    <td width="50"><b>วันที่นัดส่งของ</b></td>
				    <td width="50"><b>ประมาณการวันที่ชำระเงิน</b></td>
				    <td width="50"><b>เงินรวมก่อนหักส่วนลด</b></td>
				    <td width="50"><b>ส่วนลด</b></td>
				    <td width="50"><b>เงินรวมก่อนภาษีมูลค่าเพิ่ม</b></td>
				    <td width="50"><b>จำนวนภาษีมูลค่าเพิ่ม</b></td>
				    <td width="50"><b>จำนวนรวมสุทธิ</b></td>
				    <td width="50" style="text-align:center;">ทำรายการ</td>
				</tr>
<?php 
				$a=0;	
				$parts_pocode_strQuery = "
					SELECT 
						*	
					FROM 
						\"PurchaseOrderPart\"
					WHERE
						\"status\" = 1 
					ORDER BY parts_pocode
				";
				$parts_pocode_query = @pg_query($parts_pocode_strQuery);
				while($parts_pocode_query_result = @pg_fetch_array($parts_pocode_query)){ //Query the parts' products
				    $a++; //For count how many products' records
?>
					<tr class="<?php
						if($a % 2 == 0){
							?>odd<?php
						}
						else{
							?>even<?php
						}
					?>"><!-- style="background-color:<?php echo $bg; ?>" -->
					    <td><?php echo $parts_pocode_query_result["parts_pocode"]; ?></td>
					    <td><?php echo $parts_pocode_query_result["date"]; ?></td>
					    <td align="right"><?php echo $parts_pocode_query_result["credit_terms"]; ?></td>
					    <td><?php echo $parts_pocode_query_result["app_sentpartdate"]; ?></td>
					    <td><?php echo $parts_pocode_query_result["esm_paydate"]; ?></td>
					    <td align="right"><?php echo number_format($parts_pocode_query_result["subtotal"], 2); ?></td>
					    <td align="right"><?php echo number_format($parts_pocode_query_result["discount"], 2); ?></td>
					    <td align="right"><?php echo number_format($parts_pocode_query_result["bfv_total"], 2); ?></td>
					    <td align="right"><?php echo number_format($parts_pocode_query_result["vat"], 2); ?></td>
					    <td align="right"><?php echo number_format($parts_pocode_query_result["nettotal"], 2); ?></td>
					    <td style="text-align:center;">
					    	<a href="../partpo/po_buy_mat_pdf.php?po_id=<?php echo $parts_pocode_query_result["parts_pocode"]; ?>" target="_blank" ><img src="../images/zoom.png" border="0" width="16" alt="แสดงใบสั่งซื้อ" title="แสดงใบสั่งซื้อ" /></a>
					    	<span style="margin-left: 10px;"></span>
					    	<!-- <a href="" onclick="javascript:popU('po_approve_edit.php','','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=750,height=300')" style="cursor:pointer;" ><img src="../images/icon-edit.png" border="0" /></a> -->
					    	<a href="#" onclick="javascript:ShowDetail('po_approve_edit.php', '<?php echo $parts_pocode_query_result["parts_pocode"]; ?>')" style="cursor:pointer;" ><img src="../images/icon-edit.png" border="0" alt="ทำรายการอนุมัติ" title="ทำรายการอนุมัติ" /></a>
					    </td>
					</tr>
<?php
				}
?>
			</table>
		</div>
    </div>
</div>
<script>
	function ShowDetail(url, id){
	    $('body').append('<div id=\"divdialogadd\"></div>');
	    $('#divdialogadd').load(url + '?parts_pocode=' + id);
	    $('#divdialogadd').dialog({
	        title: 'แก้ไขรายการ Part PO : '+id,
	        resizable: false,
	        modal: true,  
	        width: 700,
	        height: 500,
	        close: function(ev, ui){
	            $('#divdialogadd').remove();
	        }
	    });
	}
	
	function popU(U, N, T) {
		newWindow = window.open(U, N, T);
		// if (!newWindow.opener)
			// newWindow.opener = self;
	}
</script>
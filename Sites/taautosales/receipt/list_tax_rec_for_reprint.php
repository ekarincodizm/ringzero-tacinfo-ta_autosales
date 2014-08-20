<?php
include_once("../include/config.php");
include_once("../include/function.php");

	$j = 0;
	$condition = pg_escape_string($_POST['condition']);
	$keyword = pg_escape_string($_POST['keyword']);
	
	if( ($condition == 'all') or (empty($keyword)) ){ //แสดงข้อมูลทั้งหมด
		$qry = pg_query(" SELECT * FROM v_print_rec_inv
							ORDER BY receipt_no DESC ");	
	}else{
		$qry = "SELECT * FROM v_print_rec_inv ";
		if($condition == 'receipt_no'){
			$qry .= " WHERE receipt_no like '%$keyword%' ";
		}else if($condition == 'invoice_no'){
			$qry .= " WHERE invoice_no like '%$keyword%' ";
		}else if($condition == 'receipt_date'){
			$arr_date = explode ( ",", $keyword );
			$qry .= " WHERE r_prndate between '$arr_date[0]' and '$arr_date[1]' ";
		}else{}
		$qry .= "ORDER BY receipt_no DESC";
		$qry = pg_query($qry);
	}
	$num_rows = pg_num_rows($qry);
?>

<span style="color:#0080C0;font-weight:bold">จำนวนรายการ : <?php echo $num_rows; ?><label>&nbsp;รายการ</label></span>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
	<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
		<td>เลขที่ใบเสร็จรับเงิน</td>
		<td>เลขที่ใบกำกับภาษี</td>
		<td>วันที่ใบเสร็จ</td>
		<td>มูลค่าสินค้า ไม่รวมภาษี</td>
		<td>ภาษีมูลค่าเพิ่ม</td>
		<td>มูลค่าสินค้า ที่รวมภาษี</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>

	<?php
	while($res = pg_fetch_array($qry)){
		$j++;
	?>
	<tr bgcolor="#FFFFFF">
		<td><?php echo $res['receipt_no']; ?></td>
		<td><?php echo $res['invoice_no']; ?></td>
		<td><?php echo $res['r_prndate']; ?></td>
		<td align="right"><?php echo number_format($res['amount'],2); ?></td>
		<td align="right"><?php echo number_format($res['vat'],2); ?></td>
		<td align="right"><?php echo number_format(($res['amount'] + $res['vat']) ,2); ?></td>
		<td align="center">
			<img src="../images/print.png" border="0" width="15" height="15" alt="พิมพ์สำเนาใบเสร็จรับเงิน" title="พิมพ์สำเนาใบเสร็จรับเงิน" style="cursor:pointer" onclick = "javascript:show_dialog('<?php echo $res['receipt_no']; ?>')">
		</td>
		<td align="center">
			<img src="../images/print.png" border="0" width="15" height="15" alt="พิมพ์สำเนาใบกำกับภาษี" title="พิมพ์สำเนาใบกำกับภาษี" style="cursor:pointer" onclick = "javascript:show_dialog('<?php echo $res['invoice_no']; ?>')">
		</td>
	</tr>
	<?php
	}

	if($j == 0){
		echo "<tr><td colspan=\"6\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
	}
	?>

	</table>
	<br><br><br>
	<div>
		<label><b>คำอธิบาย</b></label>
	<hr>
	<img src="../images/print.png" border="0" width="15" height="15" title="พิมพ์สำเนาใบเสร็จรับเงิน" style="cursor:pointer"><label> คือ  พิมพ์สำเนาใบเสร็จรับเงิน </label>&nbsp;&nbsp;
	<img src="../images/print.png" border="0" width="15" height="15" title="พิมพ์สำเนาใบกำกับภาษี" style="cursor:pointer"><label> คือ พิมพ์สำเนาใบกำกับภาษี </label>
	</div>
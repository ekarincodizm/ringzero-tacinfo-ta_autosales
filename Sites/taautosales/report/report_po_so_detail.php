<?php
include_once("../include/config.php");
include_once("../include/function.php");


$po_id = $_GET['po_id'];
$idno = $_GET['idno'];


$qry = pg_query(" SELECT  * FROM v_car_history  
				  WHERE po_id = '$po_id'
				  AND idno = '$idno' ");

?>
<table cellpadding="3" cellspacing="1" border="0" width="100%">
	<?php 
		while($res = pg_fetch_array($qry)){
	?>
	<tr>
		<td colspan="2" width="50%" valign="top">
			<table cellpadding="2" cellspacing="1" border="0" width="100%">
				<tr>
					<td align="right"><b>เลขที่ใบสั่งซื้อ: </b></td>
					<td><?php echo $res['po_id'];?></td>
				</tr>
				<tr>
					<td align="right">วันที่สั่งซื้อ: </td>
					<td><?php echo $res['po_date'];?></td>
				</tr>
				<tr>
					<td align="right">สั่งซื้อจาก: </td>
					<?php
						$buy_from = get_vender_name($res['vender_id']);
					?>
					
					<td><?php echo $buy_from; ?></td>
				</tr>
				<tr>
					<td align="right">เลขที่รับสินค้า: </td>
					<td><?php //echo $res['po_id'];?></td>
				</tr>
			</table>
		</td>
		<td colspan ="2" width="50%" valign="top">
			<table cellpadding="2" cellspacing="1" border="0" width="100%">
				<tr>
					<td align="right"><b>เลขที่สัญญาขาย: </b></td>
					<td><?php echo $res['idno'];?></td>
				</tr>
				<tr>
					<td align="right">วันที่ขาย: </td>
					<td><?php echo $res['finance_date']; ?></td>
				</tr>
				<tr>
					<td align="right">ขายให้: </td>
					<?php 
						$sale_to = get_cus_name($idno);
					?>
					<td><?php echo $sale_to; ?></td>
				</tr>
				<tr>
					<td align="right"></td>
					<td><?php //echo $res['po_id'];?></td>
				</tr>
			</table>
		
		</td>
		
	</tr>
	<tr>
		<td  colspan="4" align="center" bgcolor="#F0F0F0"><b>รายละเอียดรถยนต์ที่มีการซื้อ - ขาย </b></td>
	</tr>
	<tr>
		<td align ="right">รูปแบบรถ: </td>
		<td><?php echo $res['car_name']; ?></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td align ="right">เลขทะเบียน: </td>
		<td><?php echo $res['license_plate']; ?></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td align ="right">เลขเครื่อง: </td>
		<td><?php echo $res['mar_num']; ?></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td align ="right">เลขตัวถัง: </td>
		<td><?php echo $res['car_num']; ?></td>
		<td></td>
		<td></td>
	</tr>
	<?php
	}
	?>

</table>
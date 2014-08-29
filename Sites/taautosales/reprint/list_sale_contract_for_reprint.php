<?php
include_once("../include/config.php");
include_once("../include/function.php");
$page_title = "พิมพ์สำเนาสัญญาซื้อขายรถยนต์  ";
$res_id = $_GET['res_id'];
$j = 0;
if($res_id != ""){
	$qry = pg_query(" SELECT *,appv_stamp::date as appv_date FROM \"v_reprint_buy_contract\" where \"res_id\" LIKE '%$res_id%' order by idno DESC ");
	$num_rows = pg_num_rows($qry);
}

?>

<span style="color:#0080C0;font-weight:bold">จำนวนรายการ : <?php echo $num_rows; ?><label>&nbsp;รายการ</label></span>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
	<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
		<td>ลำดับที่</td>
		<td>เลขที่สัญญา</td>
		<td>เลขที่จอง</td>
		<td>สต๊อกรถ</td>
		<td>ชื่อสกุล</td>
		<td>พนักงานขาย</td>
		<td>วันเวลาที่อนุมัติ</td>
		<td>C#1</td>
		<td>C#2</td>
		<td>R#1</td>
	</tr>

	<?php
	
	while($res = pg_fetch_array($qry)){
		$j++;
		$auto_id = $res['auto_id'];
		$idno = $res['idno'];
		//$car_id = $res['car_id'];
		$car_idno = $res['car_idno'];
		$res_id = $res['res_id'];
		$doer_id = $res['doer_id'];
		$doer_name = GetUserName($doer_id);
		$doer_stamp = $res['doer_stamp'];
		$appv_id = $res['appv_id'];
		//$appv_name = GetUserName($appv_id);
		$appv_date = $res['appv_date'];
		$pre_name = $res['pre_name'];
		$cus_name = $res['cus_name'];
		$surname = $res['surname'];
	?>
	<tr bgcolor="#FFFFFF">
		<td><?php echo $j; ?></td>
		<td><?php echo $idno; ?></td>
		<td><?php echo $res_id ;?></td>
		<td><?php echo $car_idno; ?></td>
		<td><?php echo "$pre_name $cus_name $surname"; ?></td>
		<td><?php echo $doer_name; ?></td>
		<td><?php echo $appv_date; ?></td>
		<td align="center">
			<img src="../images/print.png" border="0" width="15" height="15" alt="พิมพ์สำเนาสัญญาจะซื้อจะขายรถยนต์" title="พิมพ์สำเนาสัญญาจะซื้อจะขายรถยนต์" style="cursor:pointer" onclick = "javascript:show_dialog('<?php echo $idno; ?>')">
		</td>
		<td align="center">
			<img src="../images/print.png" border="0" width="15" height="15" alt="พิมพ์สัญญาแบบ2" title="พิมพ์สัญญาแบบ2" style="cursor:pointer" onclick = "javascript:show2_dialog('<?php echo $idno; ?>')">
		</td>	
		<td align="center">
			<img src="../images/print.png" border="0" width="15" height="15" alt="พิมพ์ใบจอง" title="พิมพ์ใบจอง" style="cursor:pointer" onclick = "javascript:show3_dialog('<?php echo $res_id; ?>')">
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
	<img src="../images/print.png" border="0" width="15" height="15" title="พิมพ์สำเนาหนังสือสัญญาจะซื้อจะขายรถยนต์ (C#1 :แบบที่1 ,C#2:แบบที่2) , พิมพ์ใบจอง (R#1)" style="cursor:pointer"><label> คือ  พิมพ์สำเนาหนังสือสัญญาจะซื้อจะขายรถยนต์  (C#1 :แบบที่1 ,C#2:แบบที่2) , พิมพ์ใบจอง (R#1)</label>&nbsp;&nbsp;
	</div>
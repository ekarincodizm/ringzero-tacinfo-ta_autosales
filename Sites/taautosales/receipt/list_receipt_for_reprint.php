<?php
include_once("../include/config.php");
include_once("../include/function.php");
$j = 0;
$condition = pg_escape_string($_POST['condition']);
$keyword = pg_escape_string($_POST['keyword']);
if( ($condition == 'all') or (empty($keyword)) ){ //แสดงข้อมูลทั้งหมด  เฉพาะ รายการ ที่ ใบเสร็จ ปกติ   status = 1
	$qry = pg_query(" SELECT 
				    receipt_no,receipt_date,res_id,(chq_amount+cash_amount)AS amount,status
					FROM receipt_tmp
					WHERE status = '1'
					ORDER BY  receipt_no DESC ");	
	}else{
		$qry = "SELECT receipt_no,receipt_date,res_id,(chq_amount+cash_amount)AS amount,status
				FROM receipt_tmp
				WHERE status = '1' ";
		if($condition == 'receipt_no'){
			$qry .= " AND receipt_no like '%$keyword%' ";
						
		}else if($condition == 'receipt_date'){
			$arr_date = explode ( ",", $keyword );
			$qry .= " AND receipt_date between '$arr_date[0]' and '$arr_date[1]' ";
		}else{}
		$qry .= "ORDER BY receipt_no DESC";
		$qry = pg_query($qry);
	}
	$num_rows = pg_num_rows($qry);
?>

<span style="color:#0080C0;font-weight:bold">จำนวนรายการ : <?php echo $num_rows; ?><label>&nbsp;รายการ</label></span>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
	<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
		<td>เลขที่ใบเสร็จรับเงินชั่วคราว</td>
		<td>ชื่อลูกค้า</td>
		<td>ชื่อผู้จดทะเบียน</td>
		<td>วันที่ใบเสร็จ</td>
		<td>เลขที่การจอง</td>
		<td>จำนวนเงิน</td>
		<td>&nbsp;</td>
		
	</tr>

	<?php
	while($res = pg_fetch_array($qry)){
		$j++;
		$res_id = $res['res_id'];
		if (substr($res_id,0,2)=='RS')
			{
				$qrycusname = pg_query("SELECT * FROM v_reserve WHERE res_id='$res_id' ");
				if($rescusname = pg_fetch_array($qrycusname)){
					$cusname = $rescusname['cus_name'];
					$reg_cusname = $rescusname['reg_customer']; }
			}
		else
			{
			$qrycusname = pg_query("SELECT * FROM repair_history WHERE res_id='$res_id' ");
			if($rescusname = pg_fetch_array($qrycusname)){
				$cus_id = $rescusname['cus_id'];
				$cusname = GetCusName($cus_id);
				$reg_cusname = $cusname; }		
			}		
	?>
	<tr bgcolor="#FFFFFF">
		<td><?php echo $res['receipt_no']; ?></td>
		<td><?php echo $cusname; ?></td>
		<td><?php echo $reg_cusname; ?></td>
		<td><?php echo $res['receipt_date']; ?></td>
		<td><?php echo $res_id; ?></td>
		<td align="right"><?php echo number_format($res['amount'],2); ?></td>
		<td><?php echo $cancel; ?></td>
		<td align="center">
			<img src="../images/print.png" border="0" width="15" height="15" alt="พิมพ์สำเนา" title="พิมพ์สำเนาใบเสร็จ" style="cursor:pointer" onclick = "javascript:show_dialog('<?php echo $res['receipt_no']; ?>','<?php echo $res['res_id']; ?>')">
		</td>
	</tr>
	<?php
	}

	if($j == 0){
		echo "<tr><td colspan=\"6\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
	}
	?>

	</table>
	
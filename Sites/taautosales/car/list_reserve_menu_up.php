<?php
include_once("../include/config.php");
include_once("../include/function.php");


	// ----------------------------------ยอดเงินจองที่รับชำระ จากแคชเชียร์-----------------------//
	$j = 0;
	$condition = $_POST['condition'];
	$keyword = $_POST['keyword'];
	$option = $_POST['option'];
	
	if( ($condition == 'all') or (empty($keyword)) ){ //แสดงข้อมูลทั้งหมด
		$qry = pg_query(" SELECT res.res_id,
								res.user_id,
								res.cus_id, 
								res.car_id, 
								res.reserve_date, 
								res.receive_date, 
								concat(COALESCE(cus.pre_name), ' ', COALESCE(cus.cus_name), ' ', COALESCE(cus.surname)) AS cus_name
							   FROM \"Reserves\" res
							   LEFT JOIN \"Customers\" cus ON cus.cus_id::bpchar = res.cus_id::bpchar
							   WHERE (reserve_status = '2' or reserve_status ='3')  
							   ORDER BY res_id DESC ");	
	}else{
		if($condition == 'reserve_status'){
			$str_condition = 'WHERE reserve_status = ';
		}else if($condition == 'res_id'){
			$str_condition = 'WHERE res_id = ';
		}else{}
		$qry = pg_query(" SELECT res.res_id,
								res.user_id,
								res.cus_id, 
								res.car_id, 
								res.reserve_date, 
								res.receive_date, 
								concat(COALESCE(cus.pre_name), ' ', COALESCE(cus.cus_name), ' ', COALESCE(cus.surname)) AS cus_name
							   FROM \"Reserves\" res
							   LEFT JOIN \"Customers\" cus ON cus.cus_id::bpchar = res.cus_id::bpchar
							   $str_condition '$keyword'
							   ORDER BY res_id DESC ");
	}
	
	//$str_condition '$keyword'
	$num_rows = pg_num_rows($qry);
?>
<span style="color:#0080C0;font-weight:bold">จำนวนรายการ : <?php echo $num_rows; ?><label>&nbsp;รายการ</label></span>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
	<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
		<td>เลขที่จอง</td>
		<td>ชื่อผู้จอง</td>
		<td>วันที่จอง</td>
		<td>ยอดเงินที่ชำระ</td>
		<td>ชื่อผู้รับจอง</td>
		<td>&nbsp;</td>
		
	</tr>

	<?php
	while($res = pg_fetch_array($qry)){
		$res_id = $res['res_id'];
		$cus_id = $res['cus_id'];
		$cus_name = GetCusName($cus_id);
		$user_id = $res['user_id'];
		$reserve_date = $res['reserve_date'];
		$receive_date = $res['receive_date'];
	
		//หายอดเงินสด
		$qry_resdt = pg_query("SELECT SUM(amount) as amount FROM \"VAccPayment\" WHERE res_id='$res_id' AND o_receipt IS NOT NULL AND constant_var IS NOT NULL");
		if($res_resdt = pg_fetch_array($qry_resdt)){
			$amount = $res_resdt['amount'];
		}
		$j++;
	?>
	<tr bgcolor="#FFFFFF">
		<td><?php echo "$res_id"; ?></td>
		<td><?php echo $cus_name; ?></td>
		<td><?php echo $reserve_date; ?></td>
		<td align="right"><?php echo number_format($amount,2); ?></td>
		<td><?php echo GetUserName($user_id);echo $cmd; ?></td> <?php // ชื่อ sale ?>
		<?php if($option == 'change_reserve'){?>
		<td align="center">
			<img src="../images/viewdetails.gif" border="0" width="15" height="15" alt="เปลี่ยนแปลงการจอง" title="เปลี่ยนแปลงการจอง" style="cursor:pointer" onclick = "javascript:show_dialog('<?php echo $res_id; ?>')">
		</td>
		<?php }else{ ?>
		<td align="center">
			<img src="../images/viewdetails.gif" border="0" width="15" height="15" alt="ตั้งค่าใช้จ่ายอื่นๆ" title="ตั้งค่าใช้จ่ายอื่นๆ" style="cursor:pointer" onclick = "javascript:show_dialog('<?php echo $res_id; ?>')">
		</td>
		<?php }?>
	</tr>
	<?php
	}

	
	if($j == 0){
		echo "<tr><td colspan=\"6\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
	}
	?>

	</table>
	
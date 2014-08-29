<?php
include_once("../include/config.php");
include_once("../include/function.php");

	// ----------------------------------ยอดเงินจองที่รับชำระ จากแคชเชียร์-----------------------//
	$j = 0;
	$keyword = $_POST['keyword'];
	
	if(empty($keyword) ){
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
							   ORDER BY res_id,cus_name asc ");	
	}else{
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
							   AND res_id = '$keyword'
							   ORDER BY res_id,cus_name asc");
	}
?>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
	<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
		<td>เลขที่จอง</td>
		<td>ชื่อผู้จอง</td>
		<td>วันที่จอง</td>
		<td>ยอดเงินที่ชำระ</td>
		<td>ชื่อผู้รับจอง</td>
		<td>&nbsp;</td>
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
		
		/* เช็คว่ามีการชำระเงินหรือยัง */
		$check_payment = pg_query("SELECT * FROM receipt_tmp WHERE res_id = '$res_id'");
		$num_rows = pg_num_rows($check_payment);
		
		$j++;
	?>
	<tr bgcolor="#FFFFFF">
		<td><?php echo "$res_id"; ?></td>
		<td><?php echo $cus_name; ?></td>
		<td><?php echo $reserve_date; ?></td>
		<td align="right"><?php echo number_format($amount,2); ?></td>
		<td><?php echo GetUserName($user_id);echo $cmd; ?></td> <?php // ชื่อ sale ?>
		<td align="center">
			<img src="../images/viewdetails.gif" border="0" width="15" height="15" alt="ทำรายการ" title="ทำรายการ" style="cursor:pointer" onclick = "javascript:ShowDialog('<?php echo $res_id; ?>')">
		</td>
		<td align="center">
		<?php// if($num_rows != 0){?>
			<img src="../images/print.png" border="0" width="15" height="15" alt="พิมพ์ใบจอง" title="พิมพ์ใบจอง" style="cursor:pointer" onclick = "javascript:window.open('../report/reserve_car_down_pdf_1.php?res_id=<?php echo $res_id; ?>','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600');">
		<?php //}?>
		</td>
	</tr>
	<?php
	}

	if($j == 0){
		echo "<tr><td colspan=\"6\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
	}
	?>

	</table>
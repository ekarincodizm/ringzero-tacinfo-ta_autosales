<?php
include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    exit();
}

$yy = pg_escape_string($_GET['yy']);
$mm = pg_escape_string($_GET['mm']);

$thaimonth=array("มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม ","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน ","ธันวาคม");
?>

<style type="text/css">
.odd{
    background-color:#EDF8FE;
    font-size:12px
}
.even{
    background-color:#D5EFFD;
    font-size:12px
}
.sum{
    background-color:#FFC0C0;
    font-size:12px
}
</style>

<div style="float:left">รายงานการติดตั้งแก๊ส ประจำเดือน <b><?php echo $mm; ?></b> ปี <b><?php echo $yy; ?></b> <a href="install_gas_pdf_2.php?mm=<?php echo $mm; ?>&yy=<?php echo $yy; ?>" target="_blank"><font color="#0000FF"><b><u>(พิมพ์รายงาน)</u></b></font></a></div>
<div style="float:right"></div>
<div style="clear:both"></div>

<div style="float:center; width:100%;">
	<table width="100%" border="0" cellSpacing="1" cellPadding="3" bgcolor="#F0F0F0">
		<tr style="font-weight:bold;" valign="top" bgcolor="#79BCFF" align="center">
			<td>วันที่ใบแจ้งหนี้</td>
			<td>เลขที่ใบแจ้งหนี้</td>
			<td>ทะเบียนรถในสต๊อก</td>
			<td>รุ่น/สี</td>
			<td>บ.ที่ติดตั้ง</td>
			<td>ประเภทที่ติดตั้ง</td>
			<td>เลขที่ใบวิศวะ</td>
			<td>เลขที่ใบจอง</td>
			<td>ชื่อลูกค้า</td>
			<td>วันที่ลูกค้ารับรถ</td>
			<td>ค่าติดตั้งแก๊ส</td>
			<td>ค่าน้ำมัน</td>
			<td>วันที่จ่ายเงิน</td>
		</tr>

		<?php
		$nub = 0;
		$query=pg_query("SELECT a.*, b.\"gasInvoiceDate\", b.\"payDate\", b.\"Vender\"
						FROM \"installGasDetail\" a, \"installGas\" b
						WHERE a.\"gasInvoiceNo\" = b.\"gasInvoiceNo\"
						AND (EXTRACT(MONTH FROM b.\"gasInvoiceDate\")='$mm') AND (EXTRACT(YEAR FROM b.\"gasInvoiceDate\")='$yy')
						ORDER BY b.\"gasInvoiceDate\", a.\"gasInvoiceNo\" ");
		while($resvc=pg_fetch_array($query))
		{
			$nub++;
			$auto_id_CarMove = $resvc['auto_id_CarMove']; // รหัสรายการใน CarMove
			$gasInvoiceDate = $resvc['gasInvoiceDate']; // วันที่ใบแจ้งหนี้
			$gasInvoiceNo = $resvc['gasInvoiceNo']; // เลขที่ใบแจ้งหนี้
			$car_idno = $resvc['car_idno']; // ทะเบียนรถในสต๊อก
			$car_name = $resvc['car_name']; // รุ่น
			$Vender = $resvc['Vender']; // บ.ที่ติดตั้ง
			$eng_cert = $resvc['eng_cert']; // เลขที่ใบวิศวะ
			$res_id = $resvc['res_id']; // เลขที่ใบจอง 
			$received_date = $resvc['received_date']; // วันที่ลูกค้ารับรถ
			$install_amount = $resvc['install_amount']; // ค่าติดตั้งแก๊ส
			$oil_amount = $resvc['oil_amount']; // ค่าน้ำมัน
			$payDate = $resvc['payDate']; // วันที่จ่ายเงิน
			
			// หารหัสรถ ใน CarMove
			$qry_car = pg_query("select \"car_id\", \"color\" from \"CarMove\" where \"auto_id\" = '$auto_id_CarMove' ");
			$car_id = pg_fetch_result($qry_car,0);
			$color_id = pg_fetch_result($qry_car,1);
			
			// หาชื่อสี
			$qry_color = pg_query("select \"color_name\" from \"CarColor\" where \"color_id\" = '$color_id' ");
			$color_name = pg_fetch_result($qry_color,0);
			
			// หารหัสประเภทติดตั้ง
			$qry_GasTypeID = pg_query("select a.\"GasTypeID\" from \"CarMove\" a
										where a.\"auto_id\" = (select max(b.\"auto_id\") from \"CarMove\" b where b.\"car_id\" = '$car_id' and b.\"auto_id\" < '$auto_id_CarMove' and b.\"GasTypeID\" is not null)");
			$GasTypeID = pg_fetch_result($qry_GasTypeID,0);

			// หาชื่อประเภทติดตั้ง
			$qry_GasTypeName = pg_query("select \"GasTypeName\" from \"GasType\" where \"GasTypeID\" = '$GasTypeID' ");
			$GasTypeName = pg_fetch_result($qry_GasTypeName,0);
			
			// หารหัสลูกค้า
			if($res_id != "")
			{
				$qry_cus_id = pg_query("select \"cus_id\" from \"Reserves\" where \"res_id\" = '$res_id' ");
				$cus_id = pg_fetch_result($qry_cus_id,0);
			}
			else
			{
				$cus_id = "";
			}
			
			if($cus_id == "")
			{
				$qry_cus_id = pg_query("select \"cus_id\" from \"Reserves\" where \"car_id\" = '$car_id' and \"reserve_status\" != '0' ");
				$cus_id = pg_fetch_result($qry_cus_id,0);
			}
			
			// หาชื่อลูกค้า
			if($cus_id != "")
			{
				$qry_reg_customer = pg_query("select \"reg_customer\" from \"Customers\" where \"cus_id\" = '$cus_id' ");
				$reg_customer = pg_fetch_result($qry_reg_customer,0);
			}
			else
			{
				$reg_customer = "";
			}

			$i+=1;
			if($i%2==0){
				echo "<tr class=\"odd\" align=\"left\">";
			}else{
				echo "<tr class=\"even\" align=\"left\">";
			}
		?>
				<td align="center"><?php echo $gasInvoiceDate; ?></td>
				<td align="center"><?php echo $gasInvoiceNo; ?></td>
				<td align="center"><?php echo $car_idno; ?></td>
				<td align="center"><?php echo "$car_name / $color_name"; ?></td>
				<td align="center"><?php echo $Vender; ?></td>
				<td align="center"><?php echo $GasTypeName; ?></td>
				<td align="center"><?php echo $eng_cert; ?></td>
				<td align="center"><?php echo $res_id; ?></td>
				<td align="center"><?php echo $reg_customer; ?></td>
				<td align="center"><?php echo $received_date; ?></td>
				<td align="right"><?php echo number_format($install_amount,2); ?></td>
				<td align="right"><?php echo number_format($oil_amount,2); ?></td>
				<td align="center"><?php echo $payDate; ?></td>
			</tr>
		<?php
			$sum_install_amount += $install_amount;
			$sum_oil_amount += $oil_amount;
		}
		?>
		<tr>
			<td colspan="9" align="left"><b>จำนวน <?php echo "$nub"; ?> รายการ</b></td>
			<td align="right"><b>รวมทั้งสิ้น</b></td>
			<td align="right"><b><?php echo number_format($sum_install_amount,2); ?></b></td>
			<td align="right"><b><?php echo number_format($sum_oil_amount,2); ?></b></td>
			<td></td>
		</tr>
	</table>
</div>
<?php
include_once("../include/config.php");
include_once("../include/function.php");
$page_title = "พิมพ์สำเนาสัญญาซื้อขายรถยนต์  ";
$res_id = $_GET['res_id'];
$j = 0;
if($res_id != ""){
	$qry = pg_query(" SELECT *,appv_stamp::date as appv_date FROM \"v_reprint_buy_contract\" where \"res_id\" LIKE '%$res_id%'
					AND \"res_id\" in(select distinct \"res_id\" from \"Reserves\" where reserve_status = '1') order by idno DESC ");
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
		<td>ขอยกเลิก</td>
	</tr>

	<?php
	
	while($res = pg_fetch_array($qry)){
		$j++;
		$auto_id = $res['auto_id'];
		$idno = $res['idno'];
		$car_id = $res['car_id'];
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
		
		// ตรวจสอบว่ามีการขอยกเลิกไปแล้วหรือยัง
		$qry_chkC = pg_query("select * from \"cancel_deliveries\" where \"res_id\" = '$res_id' and \"IDNO\" = '$idno' and \"appvStatus\" = '9' ");
		$num_chkC = pg_num_rows($qry_chkC);
	?>
	<tr bgcolor="#FFFFFF">
		<td><?php echo $j; ?></td>
		<td><?php echo $idno; ?></td>
		<td align="center"><span id="R_id" onclick="ShowDetailres1('<?php echo $res_id;?>');" style="cursor:pointer;"><font color="blue"><u><?php echo $res_id ;?></font></u></td>
		<td><?php echo $car_idno; ?></td>
		<td><?php echo "$pre_name $cus_name $surname"; ?></td>
		<td><?php echo $doer_name; ?></td>
		<td><?php echo $appv_date; ?></td>
		<?php
		if($num_chkC > 0)
		{
		?>
			<td align="center">
				<font color="#AA0000">อยู่ระหว่างการขอยกเลิก</font>
			</td>
		<?php
		}
		else
		{
		?>
			<td align="center">
				<img src="../images/close_button.png" border="0" width="15" height="15" alt="ขอยกเลิก" title="ขอยกเลิก" style="cursor:pointer" onclick = "javascript:show_dialog('<?php echo $res_id; ?>','<?php echo $idno; ?>','<?php echo $car_id; ?>')">
			</td>
		<?php
		}
		?>
	</tr>
	<?php
	}

	if($j == 0){
		echo "<tr><td colspan=\"8\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
	}
	?>

	</table>
	
<script>

function ShowDetailres1(id){
		$('body').append('<div id="divdetail"></div>');
		$('#divdetail').load('../report/report_reserve_api.php?cmd=showdetail&id='+id);
		$('#divdetail').dialog({
			title: 'แสดงรายละเอียด : '+id,
			resizable: false,
			modal: true,  
			width: 800,
			height: 450,
			close: function(ev, ui){
				$('#divdetail').remove();
			}
		});
}

</script>
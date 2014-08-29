<?php
include_once("../include/config.php");
include_once("../include/function.php");
?>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <th>เลขที่ใบแจ้งหนี้</th>
    <th>ชื่อสกุลลูกค้า</th>
    <th>วันที่ตั้งหนี้</th>
    <th>วันที่ครบกำหนดชำระ</th>
    <th>ประเภท ค่าใช้จ่าย</th>
	<th>มูลค่า</th>
    <th>vat</th>
    <th>ยอดรวม</th>
    <th>ผู้ทำรายการ</th>
	<th>วันเวลาที่ทำรายการ</th>
</tr>
<?php

$j = 0;
$qry = pg_query("SELECT * FROM \"Invoices_account\" WHERE \"is_print\" = '0' AND \"cancel\" = false ORDER BY \"maturity_date\", \"doer_stamp\" ");
while($res = pg_fetch_array($qry))
{
    $j++;
    $inv_no = $res['inv_no']; // เลขที่ใบแจ้งหนี้
    $cus_id = $res['cus_id']; // รหัสลูกค้า
    $inv_date = $res['inv_date']; // วันที่ตั้งหนี้
    $maturity_date = $res['maturity_date']; // วันที่ครบกำหนดชำระ
    $user_id = $res['user_id']; // รหัสพนักงานที่ทำรายการ
    $doer_stamp = $res['doer_stamp']; // วันเวลาที่ทำรายการ
    $chargesType = $res['chargesType']; // ประเภท ค่าใช้จ่าย
	
	// หาชื่อสกุลลูกค้า
	$qry_nameCus = pg_query("select * from \"Customers\" where \"cus_id\" = '$cus_id' ");
	$res_nameCus = pg_fetch_array($qry_nameCus);
	$pre_name = $res_nameCus['pre_name']; // คำนำหน้า
	$cus_name = $res_nameCus['cus_name']; // ชื่อ
	$surname = $res_nameCus['surname']; // สกุล
	$fullnameCus = "$pre_name$cus_name $surname"; // ชื่อเต็ม ลูกค้า
	
	// หาชื่อพนักงาน
	$qry_nameUser = pg_query("select \"fullname\" from \"fuser\" where \"id_user\" = '$user_id' ");
	$fullnameUser = pg_fetch_result($qry_nameUser,0);
	
	// ประเภท ค่าใช้จ่าย
	if($chargesType == "P"){$chargesTypeName = "ค่าสินค้า";}
	elseif($chargesType == "S"){$chargesTypeName = "ค่าบริการ";}
	else{$chargesTypeName = "";}
	
	// หาจำนวนเงิน
	$qry_money = pg_query("select sum(\"amount\") as \"sum_amount\", sum(\"vat\") as \"sum_vat\"
						from \"InvoiceDetails_account\" where \"inv_no\" = '$inv_no' ");
	$sum_amount = pg_fetch_result($qry_money,0); // มูลค่า ก่อน vat
	$sum_vat = pg_fetch_result($qry_money,1); // ยอดรวม vat
	$sum_all = $sum_amount + $sum_vat; // ยอดรวม
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>

    <td align="center"><a href="javascript:show_detail('<?php echo $inv_no; ?>')" title="รายละเอียด ออกใบเสร็จ"><u><?php echo $inv_no; ?></u></a></td>
    <td align="left"><?php echo $fullnameCus; ?></td>
    <td align="center"><?php echo $inv_date; ?></td>
    <td align="center"><?php echo $maturity_date; ?></td>
	<td align="center"><?php echo $chargesTypeName; ?></td>
	<td align="right"><?php echo number_format($sum_amount,2); ?></td>
    <td align="right"><?php echo number_format($sum_vat,2); ?></td>
	<td align="right"><?php echo number_format($sum_all,2);; ?></td>
    <td align="left"><?php echo $fullnameUser; ?></td>
    <td align="center"><?php echo $doer_stamp; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=10 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>

</table>

<script>
	function show_detail(inv_no)
	{
		$('body').append('<div id="div_print"></div>');
		$('#div_print').load('frm_receipt_detail.php?inv_no='+inv_no);
		$('#div_print').dialog({
			title: 'รายละเอียด ออกใบเสร็จค่าอื่นๆ (บัญชี) : '+inv_no,
			resizable: false,
			modal: true,  
			width: 780,
			height: 620,
			close: function(ev, ui){
				$('#div_print').remove();
			}
		});
	}
</script>
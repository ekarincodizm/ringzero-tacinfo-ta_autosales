<?php
include_once("../include/config.php");
include_once("../include/function.php");
	$res_id =  pg_escape_string($_REQUEST["res_id"]);
	$qry = pg_query("SELECT 
					  inv.res_id, 
					  inv_detail.amount, 
					  inv_detail.vat, 
					  inv_detail.cancel, 
					  inv.cancel, 
					  inv.inv_no, 
					  inv_detail.service_id, 
					  sv.name as service_name
					FROM 
					  public.\"InvoiceDetails\" inv_detail
					  left join public.\"Invoices\"  inv on inv.inv_no = inv_detail.inv_no
					  left join public.\"Services\" sv  on sv.service_id = inv_detail.service_id
					WHERE inv_detail.service_id NOT IN ('S002','S003','S004')
					AND inv.cancel = FALSE AND inv.status is null
					AND inv.res_id = '$res_id' ");			
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <title><?php echo $company_name; ?> - <?php echo $page_title; ?></title>
    <LINK href="../images/styles.css" type=text/css rel=stylesheet>

    <link type="text/css" href="../images/jqueryui/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="../images/jqueryui/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="../images/jqueryui/js/jquery-ui-1.8.16.custom.min.js"></script>
	<script>
	</script>
	
</head>
<body>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>ลำดับ</td>
	<td>เลขที่ตั้งหนี้</td>
    <td>รายการ</td>
	<td>มูลค่า</td>
    <td>ภาษีมูลค่าเพิ่ม</td>
	<td>ยอดรวม</td>
	<td></td>
	
	
</tr>
<?php
$j = 0;


while($res = pg_fetch_array($qry)){
$j++;
	$res_id = $res['res_id'];
	$service_name = $res['service_name'];
	$amount = $res['amount'];
	$vat = $res['vat'];
    $total = $amount + $vat;
	$inv_no = $res['inv_no'];
        if($j%2==0){
            echo "<tr class=\"odd\">";
        }else{
            echo "<tr class=\"even\">";
        }
?>
    
    <td align="center"><?php echo $j; ?> </td>
	<td align="center"><?php echo $inv_no; ?> </td>
    <td><?php echo $res['service_name'];  ?></td>
	<td align="right"><?php echo number_format($amount,2); ?></td>
	<td align="right"><?php echo number_format($vat,2);?></td>
    <td align="right"><?php echo number_format($total,2); ?></td>
	<td><input type="button" name="btn_delete" id="btn_delete" value="ยกเลิก" onclick="javascript:cancel_invoice1('<?php echo $inv_no; ?>')"></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=\"8\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{}
?>
</table>

</body>
</html>





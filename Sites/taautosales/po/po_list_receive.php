<?php
include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}
$page_title = "รับสินค้าตาม Po";
	$condition = pg_escape_string($_POST["condition"]);
	$keyword =  pg_escape_string($_POST["keyword"]);
	if(empty($conditon) and empty($keyword)){
		$select = "SELECT * FROM \"PurchaseOrders\" WHERE approve='TRUE' AND cancel='FALSE' AND receive_all='FALSE' ORDER BY po_id ASC ";
	}
	
	if ($condition == "all"){
		$select = "SELECT * FROM \"PurchaseOrders\" WHERE approve='TRUE' AND cancel='FALSE' AND receive_all='FALSE' ORDER BY po_id ASC ";
	}
	else if($condition == "po_type"){
		$select = "SELECT * FROM \"PurchaseOrders\" WHERE approve='TRUE' AND cancel='FALSE' AND receive_all='FALSE' AND po_type_id = '$keyword' ORDER BY po_id ASC ";
	}else if($condition == "po_id"){
		$select = "SELECT * FROM \"PurchaseOrders\" WHERE approve='TRUE' AND cancel='FALSE' AND receive_all='FALSE' AND po_id = '$keyword' ORDER BY po_id ASC ";
	}else if($condition == "po_date"){
		$arr_date = explode ( " ", $keyword );
		$select = "SELECT * FROM \"PurchaseOrders\" WHERE approve='TRUE' AND cancel='FALSE' AND receive_all='FALSE' AND po_date between '$arr_date[0]' and '$arr_date[1]' ORDER BY po_id ASC ";
	}else if ($condition == "vender"){
		$select = "SELECT * FROM \"PurchaseOrders\" WHERE approve='TRUE' AND cancel='FALSE' AND receive_all='FALSE' AND cus_id = '$keyword' ORDER BY po_id ASC ";
	}
	
	
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

</head>
<body>
<div id="dev_edit">

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>เลขที่ใบสั่งซื้อ</td>
    <td>วันที่สั่งซื้อ</td>
    <td>ผู้ขาย</td>
    <td>ชื่อสินค้า</td>
    <td>ยอดเงิน</td>
    <td>ภาษีมูลค่าเพิ่ม</td>
    <td>&nbsp;</td>
</tr>

<?php
$j = 0;
$qry = pg_query($select);

while($res = pg_fetch_array($qry)){
    $j++;
    $po_id = $res['po_id'];
    $po_date = $res['po_date'];
    $vender_id = $res['vender_id'];
    $amount = $res['amount'];
    $vat = $res['vat'];
    
    $vender_name = GetVender($vender_id);
	
	if($vender_name == ""){
		$vender_name = GetCusName($vender_id);
	}
?>
<tr bgcolor="#E1F0FF" style="font-weight:bold">
    <td><?php echo $po_id; ?></td>
    <td><?php echo $po_date; ?></td>
    <td><?php echo $vender_name; ?></td>
    <td></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
    <td align="right"><?php echo number_format($vat,2); ?></td>
    <td></td>
</tr>
<?php
    //แสดง Details
    $qry_detail = pg_query("SELECT * FROM \"PurchaseOrderDetails\" WHERE po_id='$po_id' AND cancel='FALSE' ORDER BY auto_id ASC");
    while($res_detail = pg_fetch_array($qry_detail)){
        $dt_auto_id = $res_detail['auto_id'];
        $dt_po_id = $res_detail['po_id'];
        $dt_product_id = $res_detail['product_id'];
        $dt_product_cost = $res_detail['product_cost'];
        $dt_vat = $res_detail['vat'];
        $dt_unit = $res_detail['unit'];
        
		$poType = substr($dt_po_id, 0, 4);
		
        if($poType  != "POMA"){
		
            $link_table = GetLinkTable($dt_product_id);
			
            if($link_table == "Cars"){
                $dt_product_name = GetProductName($dt_product_id);
                $count_unit=@pg_query("select count_receive_good('$dt_auto_id')");
                $count_unit=@pg_fetch_result($count_unit,0);
            }else{
                $count_unit = GetAmountPO("I",$dt_po_id,$dt_product_id);
                $dt_product_name = GetRawMaterialProductName($dt_product_id);
            }
        }else{
            $link_table = "MAT";
            $count_unit = GetAmountPO("I",$dt_po_id,$dt_product_id);
            $dt_product_name = GetRawMaterialProductName($dt_product_id);
        }

?>
<tr bgcolor="#FFFFFF">
    <td colspan="3"></td>
    <td><?php echo "$dt_product_name"; ?></td>
    <td align="right"><?php echo number_format($dt_product_cost,2); ?></td>
    <td align="right"><?php echo number_format($dt_vat,2); ?></td>
    <td align="center">
<?php
if($count_unit < $dt_unit){
?>
    <input style="font-size:11px" type="button" name="btnShow" id="btnShow" value="แสดงรายการ (<?php echo "$count_unit/$dt_unit"; ?> unit)" onclick="javascript:ShowDetail('<?php echo $dt_auto_id; ?>','<?php echo $link_table; ?>','<?php echo $poType; ?>','<?php echo $condition; ?>','<?php echo $keyword; ?>')">
<?php
}else{
?>
    <input style="font-size:11px" disabled type="button" name="btnShow" id="btnShow" value="แสดงรายการ (<?php echo "$count_unit/$dt_unit"; ?> unit)">
<?php
}
?>
    </td>
</tr>
<?php
    }

}

if($j == 0){
    echo "<tr><td colspan=6 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>

</table>

</div>

<script>
function ShowDetail(id,t,type,cond,key){
	var width,hight;
	if(type == "POUS" || type == "PORT" || type == "POSC"){
		width = 695;
		height = 500;
	}else if(type == "PONW"){
		width = 695;
		height = 500;
	}else{
		width = 600;
		height = 300; //สินค้าใหม่
	}
    $('body').append('<div id="divdialogadd"></div>');
    $('#divdialogadd').load('po_receive_frm.php?cmd=divdialog&id='+id+'&t='+t+'&potype='+type+'&condition='+cond+'&keyword='+key);
    $('#divdialogadd').dialog({
        title: 'แสดงรายละเอียด'+type,
        resizable: false,
        modal: true,  
        width: width,
        height: height,
        close: function(ev, ui){
            $('#divdialogadd').remove();
        }
    });
}
</script>
</body>
</html>
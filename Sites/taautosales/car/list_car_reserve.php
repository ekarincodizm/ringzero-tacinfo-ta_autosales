<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}
	$car_id =  pg_escape_string($_GET["car_id"]);
	
	$qry = pg_query("SELECT * FROM \"Reserves\" WHERE car_id = '$car_id' and reserve_status<>'1'");
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
	//========= เปิดหน้าจอสำหรับแสดงรายละเอียดการจอง =========//
	function ShowDetail(id){
		$('body').append('<div id="divdetail"></div>');
		$('#divdetail').load('../report/report_reserve_api.php?cmd=showdetail&id='+id);
		$('#divdetail').dialog({
			title: 'รายละเอียดการจอง : '+id,
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
	
</head>
<body>



<div class="roundedcornr_box" style="width:900px"></div>
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>res_id</td>
    <td>วันที่จอง</td>
    <td>ชื่อลูกค้า</td>
    <td>ยี่ห้อรถ</td>
    <td>ราคารถ</td>
    <td>ต้องการดาวน์</td>
    <td>ยอดเงินจองที่ชำระ</td>
    <td>วันที่ชำระครบ</td>
</tr>
<?php
$j = 0;
$sum = 0;
while($res = pg_fetch_array($qry)){
    $j++;
    $res_id = $res['res_id'];
    $reserve_date = $res['reserve_date'];
    $receive_date = $res['receive_date'];
    $cus_id = $res['cus_id'];
    $cus_name = GetCusName($cus_id);
    //$product_id = $res['product_id'];
    $car_id = $res['car_id'];
    $car_name = GetCarsName($car_id);
        
    $car_price = $res['car_price'];
    $down_price = $res['down_price'];
    $cancel = $res['cancel'];
    
    $qry_resdt = pg_query("SELECT SUM(amount) as amount FROM \"VAccPayment\" WHERE res_id='$res_id' AND constant_var IS NOT NULL ");
    if($res_resdt = pg_fetch_array($qry_resdt)){
        $amount = $res_resdt['amount'];
    }
    
    $sum_car_price += $car_price;
    $sum_down_price += $down_price;
    $sum_amount += $amount;

    if($cancel == 't'){
        echo "<tr bgcolor=\"#ffcccc\">";
    }else{
        if($j%2==0){
            echo "<tr class=\"odd\">";
        }else{
            echo "<tr class=\"even\">";
        }
    }
?>
    <td><a href="javascript:ShowDetail('<?php echo $res_id; ?>')"><u><?php echo $res_id; ?></u></td>
    <td align="center"><?php echo $reserve_date; ?></td>
    <td><?php echo $cus_name; ?></td>
    <td><?php echo $car_name; ?></td>
    <td align="right"><?php echo number_format($car_price,2); ?></td>
    <td align="right"><?php echo number_format($down_price,2); ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
    <td align="center"><?php echo $receive_date; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=\"8\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{
  /* echo "<tr bgcolor=\"#FFFFD9\">
    <td colspan=\"4\"><b>รวม</b></td>
    <td align=\"right\"><b>".number_format($sum_car_price,2)."</b></td>
    <td align=\"right\"><b>".number_format($sum_down_price,2)."</b></td>
    <td align=\"right\"><b>".number_format($sum_amount,2)."</b></td>
    <td></td>
    </tr>";*/
}
?>
</table>






</body>
</html>


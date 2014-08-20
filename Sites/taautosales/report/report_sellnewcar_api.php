<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "show"){
    $yy = $_GET['yy'];
    $mm = $_GET['mm'];
    
    if(empty($yy) OR empty($mm)){
        exit;
    }
?>

<div style="float:left"></div>
<div style="float:right">
<a href="report_sellnewcar_pdf.php?mm=<?php echo $mm; ?>&yy=<?php echo $yy; ?>" target="_blank">
<span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span>
</a>
</div>
<div style="clear:both"></div>

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#79BCFF" style="font-weight:bold; text-align:center">
    <td width="50">ลำดับ</td>
    <td>ผู้ซื้อ</td>
    <td>ผู้ขาย</td>
    <td>สต๊อก</td>
    <td>เลขที่สัญญา</td>
    <td>สี</td>
    <td>ชนิด</td>
    <td>ศูนย์</td>
    <td>เลขถัง</td>
    <td>เลขเครื่อง</td>
    <td>ชำระเงิน</td>
</tr>
<?php
$count_blue=0;
$count_yellow=0;
$count_greenyellow=0;
$count_other=0;

$nub = 0;
$query=pg_query("SELECT * FROM \"VSaleNewCar\" 
WHERE (EXTRACT(MONTH FROM receive_date)='$mm') AND (EXTRACT(YEAR FROM receive_date)='$yy') ORDER BY car_id ASC ");
while($resvc=pg_fetch_array($query)){
    $nub++;
    $car_id = $resvc['car_id'];
    $car_num = $resvc['car_num'];
    $mar_num = $resvc['mar_num'];
    $color = trim($resvc['color']);
    $license_plate = $resvc['license_plate'];
    $product_id = $resvc['product_id'];
    $po_id = $resvc['po_id'];
    $vender_id = $resvc['vender_id'];
    $receive_date = $resvc['receive_date'];
    $IDNO = $resvc['IDNO'];
    $cus_id = $resvc['cus_id'];
    $user_id = $resvc['user_id'];
            
    $car_name = "";
    $query_bookbuy=pg_query("SELECT * FROM \"Cars\" WHERE car_id='$car_id' ");
    if($resvc_bookbuy=pg_fetch_array($query_bookbuy)){
        $car_name = $resvc_bookbuy['car_name'];
    }
    
    $buy_from = "";
    $query_bookbuy=pg_query("SELECT * FROM account.\"BookBuy\" WHERE to_hp_id='$car_id' ");
    if($resvc_bookbuy=pg_fetch_array($query_bookbuy)){
        $buy_from = $resvc_bookbuy['buy_from'];
    }
    
    if($color == "ฟ้า"){
       $count_blue++; 
    }elseif($color == "เหลือง"){
        $count_yellow++;
    }elseif($color == "เขียวเหลือง"){
        $count_greenyellow++; 
    }else{
        $count_other++; 
    }

    if($nub%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><?php echo $nub; ?></td>
    <td><?php echo GetCusName($cus_id); ?></td>
    <td><?php echo GetUserName($user_id); ?></td>
    <td><?php echo $license_plate; ?></td>
    <td><?php echo $IDNO; ?></td>
    <td><?php echo $color; ?></td>
    <td><?php echo $car_name; ?></td>
    <td><?php echo $buy_from; ?></td>
    <td><?php echo $car_num; ?></td>
    <td><?php echo $mar_num; ?></td>
    <td><?php echo $receive_date; ?></td>
</tr>
<?php
}

if($nub == 0){
    echo "<tr><td colspan=11 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
    echo "<tr style=\"font-weight:bold\" bgcolor=\"#FFFACD\"><td colspan=11>สรุปจำนวน : สีฟ้า $count_blue รายการ | สีเหลือง $count_yellow รายการ | สีเขียวเหลือง $count_greenyellow รายการ | สีอื่นๆ $count_other รายการ</td></tr>";
}
?>
</table>

<?php
}
?>
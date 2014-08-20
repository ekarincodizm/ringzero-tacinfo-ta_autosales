<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "show"){
    $yy = $_GET['yy'];
    $mm = $_GET['mm'];
?>

<div style="float:left"></div>
<div style="float:right">
<a href="report_buynewcar_pdf.php?mm=<?php echo $mm; ?>&yy=<?php echo $yy; ?>" target="_blank">
<span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span>
</a>
</div>
<div style="clear:both"></div>

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#79BCFF" style="font-weight:bold; text-align:center">
    <td width="50">ลำดับ</td>
    <td>สต๊อก</td>
    <td>วัน</td>
    <td>ศูนย์</td>
    <td>ชนิด</td>
    <td>เลขถัง</td>
    <td>เลขเครื่อง</td>
</tr>
<?php
$nub = 0;
$query=pg_query("SELECT * FROM account.\"AccountBookHead\" 
WHERE (EXTRACT(MONTH FROM \"acb_date\")='$mm') AND (EXTRACT(YEAR FROM \"acb_date\")='$yy') AND ref_id = 'VATB' AND type_acb='AP' AND cancel='FALSE' ORDER BY acb_date,acb_id ASC ");
while($resvc=pg_fetch_array($query)){
    $nub++;
    $acb_id = trim($resvc['acb_id']);
    $acb_date = $resvc['acb_date'];
    $acb_detail = $resvc['acb_detail'];
    
    $buy_from = "";
    
    $car_num = "";
    $mar_num = "";
    $car_name = "";
            
    $query_bookbuy=pg_query("SELECT * FROM account.\"BookBuy\" WHERE bh_id='$acb_id' ");
    if($resvc_bookbuy=pg_fetch_array($query_bookbuy)){
        $buy_from = $resvc_bookbuy['buy_from'];
    }/*else{
        $buy_from = "not found - buy_from";
    }*/
    
    $mr = "";
    if(strpos($acb_detail, 'M') === false){
        $mr = "";
    }else{
        $pos = strpos($acb_detail, 'M');
        $mr = trim(substr($acb_detail, $pos, 17));
        
        $query_cars=pg_query("SELECT * FROM \"Cars\" WHERE car_num='$mr' ");
        if($resvc_cars=pg_fetch_array($query_cars)){
            $car_num = $resvc_cars['car_num'];
            $mar_num = $resvc_cars['mar_num'];
            $car_name = $resvc_cars['car_name'];
            $license_plate = $resvc_cars['license_plate'];
        }/*else{
            $car_num = "not found - car_num";
            $mar_num = "not found - mar_num";
            $car_name = "not found - car_name";
        }*/
    }

    //echo "$acb_id|$mr<br>";

    if($nub%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><?php echo $nub; ?></td>
    <td><?php echo $license_plate; ?></td>
    <td align="center"><?php echo "$acb_date"; ?></td>
    <td><?php echo $buy_from; ?></td>
    <td><?php echo $car_name; ?></td>
    <td><?php echo $car_num; ?></td>
    <td><?php echo $mar_num; ?></td>
</tr>
<?php
}

if($nub == 0){
    echo "<tr><td colspan=7 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>

<?php
}
?>
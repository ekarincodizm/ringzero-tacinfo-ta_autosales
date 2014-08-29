<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "autocomplete"){
    $term = $_GET['term'];

    $qry_name=pg_query("select * from \"VAllCustomers\" WHERE \"IDNO\" LIKE '%$term%' ORDER BY \"IDNO\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $IDNO = $res_name["IDNO"];
        $pre_name = trim($res_name["pre_name"]);
        $cus_name = trim($res_name["cus_name"]);
        $surname = trim($res_name["surname"]);
            $full_name = "$pre_name $cus_name $surname";
        
        $dt['value'] = $IDNO."#".$full_name;
        $dt['label'] = "{$IDNO} , {$full_name}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
}

elseif($cmd == "divshow"){
    $idno = $_GET['idno'];
    
    if($idno == "ไม่พบข้อมูล"){
        echo "กรุณากรอกข้อมูล !";
        exit;
    }
?>
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>เลขที่</td>
    <td>เลขที่จอง</td>
    <td>วันที่</td>
    <td>ชื่อสกุล</td>
    <td>ราคารถ</td>
    <td>เงินดาวน์</td>
    <td>ยอดจัดไฟแนนท์</td>
    <td>ทะเบียน</td>
</tr>
<?php
$j = 0;
$qry = pg_query("SELECT * FROM \"VReceipt\" WHERE \"IDNO\"='$idno' ");
while($res = pg_fetch_array($qry)){
    $j++;
    $r_receipt = $res['r_receipt'];
    $res_id = $res['res_id'];
    $r_date = $res['r_date'];
    $pre_name = $res['pre_name'];
    $cus_name = $res['cus_name'];
    $surname = $res['surname'];
        $full_name = "$pre_name $cus_name $surname";
    $car_id = $res['car_id'];
    
    $qry_resv = pg_query("SELECT car_price,down_price,finance_price FROM \"Reserves\" WHERE res_id='$res_id' ");
    if($res_resv = pg_fetch_array($qry_resv)){
        $car_price= $res_resv['car_price'];
        $down_price= $res_resv['down_price'];
        $finance_price= $res_resv['finance_price'];
    }
    
    $qry_car = pg_query("SELECT license_plate FROM \"Cars\" WHERE car_id='$car_id' ");
    if($res_car = pg_fetch_array($qry_car)){
        $license_plate= $res_car['license_plate'];
    }
    
if($j%2==0){
    echo "<tr class=\"odd\">";
}else{
    echo "<tr class=\"even\">";
}
?>
    <td align="center"><a href="../report/receipt.php?rec_id=<?php echo $r_receipt; ?>" target="_blank" title="พิมพ์"><u><b><?php echo $r_receipt; ?></b></u></a></td>
    <td align="center"><?php echo $res_id; ?></td>
    <td align="center"><?php echo $r_date; ?></td>
    <td><?php echo $full_name; ?></td>
    <td align="right"><?php echo number_format($car_price,2); ?></td>
    <td align="right"><?php echo number_format($down_price,2); ?></td>
    <td align="right"><?php echo number_format($finance_price,2); ?></td>
    <td align="center"><?php echo $license_plate; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=\"20\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>
<?php
}
?>
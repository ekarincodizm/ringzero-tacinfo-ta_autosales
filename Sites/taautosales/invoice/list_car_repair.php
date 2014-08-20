<?php
include_once("../include/config.php");
include_once("../include/function.php");
$keyword = pg_escape_string($_GET['keyword']);
?>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Car ID</td>
	<td>ทะเบียน</td>
    <td>Product</td>
    <td>ชื่อลูกค้า</td>
    <td>วันที่ฝากซ่อม</td>
    <td>สีรถ</td>
    <td>คลัง</td>
	<td></td>
</tr>
<?php
$j = 0;
//$txt_qry = "SELECT * FROM \"VStockCars\" where license_plate like '%$keyword%' AND car_status = 'P' ORDER BY substr(car_idno,14,5) DESC ";

$txt_qry = "SELECT c.car_id,c.product_id,c.car_num,c.mar_num,c.car_year,c.color,c.license_plate,m.date_out,m.wh_id ,c.car_status,r.res_id,r.rp_date,c.car_idno,r.cus_id
FROM \"VStockCars\" c
LEFT JOIN \"CarMove\" m ON m.car_id::text = c.car_id::text
LEFT JOIN \"repair_history\" r ON c.car_idno::text = r.rp_id::text
where c.license_plate like '%$keyword%' AND m.date_out is null AND car_status = 'P' ORDER BY substr(car_idno,14,5) DESC"; 


$qry = pg_query($txt_qry);
while($res = pg_fetch_array($qry)){
    $j++;
    $car_id = $res['car_id'];
    $product_id = $res['product_id'];
    $car_num = $res['car_num'];
    $mar_num = $res['mar_num'];
    $car_year = $res['car_year'];
    $color = $res['color'];
    $license_plate = $res['license_plate'];
	$wh_name = GetWarehousesName($res['wh_id']);
	$cus_id = $res['cus_id'];
    $cus_name = GetCusName($cus_id);
	$rp_date = $res['rp_date'];
    
    $car_idno = $res['car_idno'];
	//$res_id = substr($car_idno,2,6).'-'.substr($car_idno,13,5) ;
	$res_id = $res['res_id'];
    $product_name = GetProductName($product_id);
    
	$qry_res = pg_query("SELECT * FROM \"V_CarsReserve\" WHERE \"car_id\"='$car_id'");
	$num = pg_num_rows($qry_res);
	
	
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><a href="javascript:ShowDetail('<?php echo $car_id; ?>')"><u><?php echo $car_id; ?></u></a></td>
	<td><?php echo $license_plate; ?></td>
    <td><?php echo $product_name; ?></td>
    <td><?php echo $cus_name; ?></td>
    <td><?php echo $rp_date; ?></td>
    <!--<td><?php echo $res_id; ?></td>-->
    <td><?php echo getCarColor($color); ?></td>
	<td><?php echo $wh_name; ?></td>  
	<td align="center">
			<img src="../images/viewdetails.gif" border="0" width="15" height="15" alt="ตั้งค่าใช้จ่ายอื่นๆ" title="ตั้งค่าใช้จ่ายอื่นๆ" style="cursor:pointer" onclick = "javascript:show_rpdialog('<?php echo $res_id; ?>')">
	</td>
 </tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=9 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>

<script>
function popU(U,N,T) {
    newWindow = window.open(U, N, T);
}
function show_car_reserve(car_id){
	popU('../car/list_car_reserve.php?car_id='+car_id,'','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=980,height=550');
}
function ShowDetail(id){
    $('body').append('<div id="divdialogadd"></div>');
    $('#divdialogadd').load('../car/movement_car_api.php?cmd=divshow&btn=1&id='+id);
    $('#divdialogadd').dialog({
        title: 'แสดงรายละเอียดการเคลื่อนไหว : '+id,
        resizable: false,
        modal: true,  
        width: 600,
        height: 350,
        close: function(ev, ui){
            $('#divdialogadd').remove();
        }
    });
}

</script>


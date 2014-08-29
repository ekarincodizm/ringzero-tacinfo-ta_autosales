<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = pg_escape_string($_REQUEST['cmd']);
$id = pg_escape_string($_GET['id']);

$qry_name=pg_query("SELECT * FROM \"V_repaircar\" WHERE res_id = '$id' ");
    if($res_name=pg_fetch_array($qry_name)){
        $car_id = $res_name['car_id'];
		$cus_id = $res_name['cus_id'];
		$cus_name = GetCusName($res_name['cus_id']);
        $license_plate = $res_name["license_plate"];
        $car_num = $res_name['car_num'];
        $mar_num = $res_name['mar_num'];
        $color = getCarColor($res_name['color']);
        $car_name = $res_name['name'];
		$product_id = $res_name['product_id'];
		$car_idno = $res_name['car_idno'];
		$wh_name = get_Warehouses($res_name['wh_id']);
		$rp_date = $res_name['rp_date'];
				
    }
  
?>
<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr>
    <td width="140">Car ID : </td><td><?php echo "$car_id"; ?></td>
</tr><tr>
    <td>เลขที่รถซ่อม : </td><td><?php echo "$id"; ?></td>
</tr><tr>
    <td>ชื่อลูกค้า : </td><td><?php echo "$cus_name"; ?></td>
</tr><tr>
    <td>ทะเบียนรถ : </td><td><?php echo "$license_plate"; ?></td>	
</tr><tr>
    <td>ทะเบียนรถในสต๊อก : </td><td><?php echo "$car_idno"; ?></td>
</tr><tr>
    <td>แบบรถ : </td><td><?php echo "$car_name"; ?></td>
</tr><tr>
    <td>เลขตัวถัง : </td><td><?php echo "$car_num"; ?></td>
</tr><tr>
    <td>เลขเครื่อง : </td><td><?php echo "$mar_num"; ?></td>
</tr><tr>
    <td>สีรถ : </td><td><?php echo "$color"; ?></td>
</tr><tr>
    <td>คลังรถ : </td><td><?php echo "$wh_name"; ?></td>
</tr><tr>
    <td>วันที่เข้าฝากจอดซ่อม : </td><td><?php echo "$rp_date"; ?></td>


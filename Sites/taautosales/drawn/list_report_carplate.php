<?php
include_once("../include/config.php");
include_once("../include/function.php");

$page_title = "รายงานป้ายเหล็ก";

$condition = pg_escape_string($_GET['condition']);
$car_plate = pg_escape_string($_GET['car_plate']);

	if($condition == "all" ||$condition == "" ){
		$where = "";
		$param_pdf = "condition=all";
	}else if($condition == "carplate"){
		$where = "where new_plate='$car_plate'";
		$param_pdf = "condition=carplate&car_plate=$car_plate";
	}
	$qry_list = "SELECT * FROM \"P_NewCarPlate\" $where ORDER BY new_plate , date_out , date_return ASC";
?>
<div style="float:right">
<a href="../report/report_carplate_pdf.php?<?php echo $param_pdf; ?>" target="_blank">
<span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span>
</a>
</div>
<div style="clear:both">
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>เลขป้ายแดง</td>
    <td>วันที่รับป้ายไป</td>
    <td>ทะเบียนรถในสต๊อก</td>
    <td>เลขที่สัญญา SI</td>
    <td>วันที่คืน</td>
	<td>รถขาย/ใช้ภายใน</td>
	<td>หมายเหตุใช้ภายใน</td>
</tr>

<?php
$j = 0;
$qry = pg_query($qry_list);
while($res = pg_fetch_array($qry)){
    $j++;
    $new_plate = $res['new_plate'];
    $car_idno = $res['car_idno'];
    $for_sale = $res['for_sale'];
	$date_in = $res['date_in'];
    $date_out = $res['date_out'];
	$date_return = $res['date_return'];
    $memo_use_inhouse = $res['memo_use_inhouse'];
    
	if($car_idno != ""){
		$qry_car = pg_query("select car_id from \"Cars\" where car_idno = '$car_idno' ");
		$car_id = pg_fetch_result($qry_car,0);
	}else{
		$car_id = "";
	}
	
	if($car_id != ""){
		$qry_reserve = pg_query("select res_id,\"IDNO\" from \"Reserves\" where car_id='$car_id' ");
		$res_id = pg_fetch_result($qry_reserve,0);
		$IDNO = pg_fetch_result($qry_reserve,1);
		
		$txt_res_id = "<span id=\"R_id\" onclick=\"ShowDetailres('$res_id');\" style=\"cursor:pointer;\"><font color=\"blue\"><u>".$res_id."</u></font></span>";
	}else{
		$IDNO = "";
		$res_id = "";
	}
        if($for_sale == 'f' OR $for_sale == 'false'){
            $for_sale_txt = "ใช้ภายใน";
        }elseif($for_sale == 't' OR $for_sale == 'true'){
            $for_sale_txt = "รถขาย";
        }else{
            $for_sale_txt = "N/A";
        }

    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><?php echo $new_plate; ?></td>
    <td align="center"><?php echo  $date_out; ?></td>
    <td align="center"><?php echo $car_idno; ?></td>
    <td align="center"><?php echo $IDNO; ?></td>
	<td align="center"><?php echo $date_return; ?></td>
    <td><?php echo $for_sale_txt; ?></td>
	<td><?php echo $memo_use_inhouse; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=5 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>

</table>
</div>
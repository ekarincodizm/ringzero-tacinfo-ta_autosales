<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = pg_escape_string($_REQUEST['cmd']);

if($cmd == "show"){
	$date = $_GET['date'];
?>
<div id="maintabs">
    <ul>
        <li><a href="report_carmovement_api.php?cmd=showdivsubcolor&date=<?php echo $date; ?>">แบบสรุป</a></li>
        <li><a href="report_carmovement_api.php?cmd=showdiv&date=<?php echo $date; ?>">แบบแสดงรายละเอียด</a></li>
    </ul>
</div>

<script>
$(function(){
    $( "#maintabs" ).tabs({
        ajaxOptions: {
            error: function( xhr, status, index, anchor ) {
                $( anchor.hash ).html("ไม่สามารถโหลดเนื้อหาได้");
            }
        }
    });
});
</script>
<?php
}

elseif($cmd == "showdiv"){
    $date = $_GET['date'];
?>

<div style="float:left; margin:10px 0 0 0; font-weight:bold; color:#808040">รายงานรถเข้า วันที่ <?php echo $date; ?></div>

<div style="float:right">
<a href="report_carmovement_pdf.php?cmd=2&date=<?php echo $date; ?>" target="_blank">
<span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span>
</a>
</div>
<div style="clear:both"></div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#DEDEBE" style="font-weight:bold; text-align:center">
     <td width="25%">มาจาก</td>
    <td width="15%">ทะเบียน</td>
	<td width="15%">ทะเบียนในสต๊อก</td>
    <td width="35%">รุ่นรถ</td>
    <td width="20%">สีรถ</td>
	<td width="15%"> </td>
</tr>
<?php
$j = 0;
$k = 0;
$sum = 0;
$nub = 0;
$qry = pg_query("SELECT
					CASE
						WHEN \"target_go\" = '1' THEN \"wh_id\"
						ELSE (select \"target_go\" from \"CarMove\" where \"auto_id\" = (select max(\"auto_id\") from \"CarMove\" where \"car_id\" = \"VCarMovement\".\"car_id\" and \"auto_id\" < \"VCarMovement\".\"auto_id\"))
					END AS \"from_id\",
					CASE
						WHEN \"target_go\" = '1' THEN \"wh_name\"
						ELSE (select \"target_name\" from \"VCarMovement\" where \"auto_id\" = (select max(\"auto_id\") from \"CarMove\" where \"car_id\" = \"VCarMovement\".\"car_id\" and \"auto_id\" < \"VCarMovement\".\"auto_id\"))
					END AS \"from_name\",
					\"license_plate\",
					\"name\",
					\"color\",
					\"car_idno\",
					\"auto_id\"
				FROM
					\"VCarMovement\"
				WHERE
					((\"date_in\" = '$date' and \"wh_id\" = '1') OR (\"date_out\" = '$date' and \"target_go\" = '1')) AND
					\"auto_id\" NOT IN(select \"auto_id\"
							from \"CarMove\" a
							where a.\"wh_id\" = '1' and
							(select \"target_go\" from \"CarMove\" where \"auto_id\" = (select max(\"auto_id\") from \"CarMove\" where \"car_id\" = a.\"car_id\" and \"auto_id\" < a.\"auto_id\")) = '1')
				ORDER BY 2,3 ");
while($res = pg_fetch_array($qry)){
    $j++;
    $k++;
    $from_id = $res['from_id']; if($j == 1){ $tmp_wh_id = $from_id; }
    $from_name = $res['from_name'];
    $license_plate = $res['license_plate'];
    $name = $res['name'];
    $color = $res['color'];
	$car_idno = $res['car_idno'];
	$auto_id = $res['auto_id'];
	
	if($from_name == ""){$from_name = "รับรถเข้าสต๊อก";}

    if($from_id != $tmp_wh_id){
    ?>
    <tr bgcolor="#FFFFD9" style="font-weight:bold">
        <td colspan="6" align="right"><?php echo $tmp_from_name; ?> | รวม <?php echo $nub; ?> รายการ</td>
    </tr>
    <?php
        $nub = 0;
        $k = 1;
    }

    if($k%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $from_name; ?></td>
    <td><?php echo $license_plate; ?></td>
	<td><?php echo $car_idno; ?></td>
    <td><?php echo $name; ?></td>
    <td><?php echo getCarColor($color); ?></td>
	<td align="center"><img src="../images/print.png" onclick="javascript:Save_printcar_log('<?php echo $car_idno;?>','<?php echo $auto_id;?>','in')" style="cursor:pointer;"/></td>
	
</tr>
<?php
    $nub++;
    $tmp_wh_id = $from_id;
    $tmp_from_name = $from_name;
}

if($j == 0){
    echo "<tr><td colspan=6 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
?>
    <tr bgcolor="#FFFFD9" style="font-weight:bold">
        <td colspan="6" align="right"><?php echo $tmp_from_name; ?> | รวม <?php echo $nub; ?> รายการ</td>
    </tr>
<?php
}
?>
</table>

<div style="margin:10px 0 0 0; font-weight:bold; color:#5454A7">รายงานรถออก วันที่ <?php echo $date; ?></div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#C9C9E4" style="font-weight:bold; text-align:center">
    <td width="25%">อยู่ที่</td>
	<td width="20%">ย้ายไปที่</td>
    <td width="10%">ทะเบียน</td>
	<td width="10%">ทะเบียนในสต๊อก</td>
    <td width="30%">รุ่นรถ</td>
    <td width="20%">สีรถ</td>
	<td width="15%"> </td>
</tr>
<?php
$j = 0;
$k = 0;
$sum = 0;
$nub = 0;
$qry = pg_query("SELECT * FROM \"VCarMovement\" WHERE date_out='$date' and target_go <> '1' ORDER BY wh_name, target_name, license_plate ASC ");

while($res = pg_fetch_array($qry)){
    $j++;
    $k++;
    $wh_id = $res['wh_id']; if($j == 1){ $tmp_wh_id = $wh_id; }
    $wh_name = $res['wh_name'];
    $license_plate = $res['license_plate'];
    $name = $res['name'];
    $color = $res['color'];
	$target_go = $res['target_go'];
	$car_idno = $res['car_idno'];
	$auto_id = $res['auto_id'];
	
    if($wh_id != $tmp_wh_id){
    ?>
    <tr bgcolor="#FFFFD9" style="font-weight:bold">
        <td colspan="7" align="right"><?php echo $tmp_wh_name; ?> | รวม <?php echo $nub; ?> รายการ</td>
    </tr>
    <?php
        $nub = 0;
        $k = 1;
    }

    if($k%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $wh_name; ?></td>
	<td><?php echo get_Warehouses($target_go); ?></td>
    <td><?php echo $license_plate; ?></td>
	<td><?php echo $car_idno; ?></td>
    <td><?php echo $name; ?></td>
    <td><?php echo getCarColor($color); ?></td>
	<td align="center"><img src="../images/print.png" onclick="javascript:Save_printcar_log('<?php echo $car_idno;?>','<?php echo $auto_id;?>','out')" style="cursor:pointer;"/></td>
</tr>
<?php
    $nub++;
    $tmp_wh_id = $wh_id;
    $tmp_wh_name = $wh_name;
}

if($j == 0){
    echo "<tr><td colspan=7 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
?>
    <tr bgcolor="#FFFFD9" style="font-weight:bold">
        <td colspan="7" align="right"><?php echo $tmp_wh_name; ?> | รวม <?php echo $nub; ?> รายการ</td>
    </tr>
<?php
}
?>
</table>

<?php
}

elseif($cmd == "showdivsubcolor"){
   $date = $_GET['date'];
?>

<div style="float:left; margin:10px 0 0 0; font-weight:bold; color:#808040">รายงานรถเข้า วันที่ <?php echo $date; ?></div>

<div style="float:right">
<a href="report_carmovement_pdf.php?cmd=1&date=<?php echo $date; ?>" target="_blank">
<span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span>
</a>
</div>
<div style="clear:both"></div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#DEDEBE" style="font-weight:bold; text-align:center">
     <td width="50%">มาจาก</td>
    <td width="30%">สีรถ</td>
    <td width="20%">จำนวนคัน</td>
</tr>
<?php
$j = 0;
$k = 0;
$sum = 0;
$nub = 0;
$unit = 0;

$qry = pg_query("SELECT
					CASE
						WHEN \"target_go\" = '1' THEN \"wh_id\"
						ELSE (select \"target_go\" from \"CarMove\" where \"auto_id\" = (select max(\"auto_id\") from \"CarMove\" where \"car_id\" = \"VCarMovement\".\"car_id\" and \"auto_id\" < \"VCarMovement\".\"auto_id\"))
					END AS \"from_id\",
					CASE
						WHEN \"target_go\" = '1' THEN \"wh_name\"
						ELSE (select \"target_name\" from \"VCarMovement\" where \"auto_id\" = (select max(\"auto_id\") from \"CarMove\" where \"car_id\" = \"VCarMovement\".\"car_id\" and \"auto_id\" < \"VCarMovement\".\"auto_id\"))
					END AS \"from_name\",
					\"color\",
					count(*) AS \"unit\"
				FROM
					\"VCarMovement\"
				WHERE
					((\"date_in\" = '$date' and \"wh_id\" = '1') OR (\"date_out\" = '$date' and \"target_go\" = '1')) AND
					\"auto_id\" NOT IN(select \"auto_id\"
									from \"CarMove\" a
									where a.\"wh_id\" = '1' and
									(select \"target_go\" from \"CarMove\" where \"auto_id\" = (select max(\"auto_id\") from \"CarMove\" where \"car_id\" = a.\"car_id\" and \"auto_id\" < a.\"auto_id\")) = '1')
				GROUP BY 1, 2, 3
				ORDER BY 2,3 ");
while($res = pg_fetch_array($qry))
{
    $j++;
    $k++;
    $from_id = $res['from_id'];
    $from_name = $res['from_name'];
    $color = $res['color'];
    $unit = $res['unit'];
	
	if($from_name == ""){$from_name = "รับรถเข้าสต๊อก";}
	
    if($j == 1){
		$tmp_from_id = $from_id;
    }

    if($from_id != $tmp_from_id){
    ?>
    <tr bgcolor="#FFFFD9" style="font-weight:bold">
        <td colspan="3" align="right"><?php echo $tmp_wh_name; ?> | ยอดรวม <?php echo $nub; ?> รายการ</td>
    </tr>
    <?php
        $nub = 0;
        $k = 1;
    }

    if($k%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $from_name; ?></td>
    <td><?php echo getCarColor($color); ?></td>
    <td align="right"><?php echo $unit; ?></td>
</tr>
<?php
    $nub+=$unit;
    $tmp_from_id = $from_id;
    $tmp_wh_name = $from_name;
}

if($j == 0){
    echo "<tr><td colspan=3 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
?>
    <tr bgcolor="#FFFFD9" style="font-weight:bold">
        <td colspan="3" align="right"><?php echo $tmp_wh_name; ?> | ยอดรวม <?php echo $nub; ?> รายการ</td>
    </tr>
<?php
}
?>
</table>

<div style="margin:10px 0 0 0; font-weight:bold; color:#5454A7">รายงานรถออก วันที่ <?php echo $date; ?></div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#C9C9E4" style="font-weight:bold; text-align:center">
    <td width="35%">อยู่ที่</td>
	<td width="35%">ย้ายไปที่</td>
    <td width="15%">สีรถ</td>
    <td width="15%">จำนวนคัน</td>
</tr>
<?php
$j = 0;
$k = 0;
$sum = 0;
$nub = 0;
$unit = 0;

$qry = pg_query("SELECT wh_id,wh_name,color,target_go,COUNT(color) AS unit FROM \"VCarMovement\" WHERE date_out='$date' and target_go <> '1'  GROUP BY wh_id,wh_name,target_go,color ORDER BY wh_name,target_go,color ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $k++;
    $wh_id = $res['wh_id'];
    $wh_name = $res['wh_name'];
    $color = $res['color'];
    $unit = $res['unit'];
    $target_go = $res['target_go'];
	
    if($j == 1){
		$tmp_wh_id = $wh_id;
    }

    if($wh_id != $tmp_wh_id){
    ?>
    <tr bgcolor="#FFFFD9" style="font-weight:bold">
        <td colspan="4" align="right"><?php echo $tmp_wh_name; ?> | ยอดรวม <?php echo $nub; ?> รายการ</td>
    </tr>
    <?php
        $nub = 0;
        $k = 1;
    }

    if($k%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $wh_name; ?></td>
	<td><?php echo get_Warehouses($target_go); ?></td>
    <td><?php echo getCarColor($color); ?></td>
    <td align="right"><?php echo $unit; ?></td>
</tr>
<?php
    $nub+=$unit;
    $tmp_wh_id = $wh_id;
    $tmp_wh_name = $wh_name;
}

if($j == 0){
    echo "<tr><td colspan=4 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
?>
    <tr bgcolor="#FFFFD9" style="font-weight:bold">
        <td colspan="4" align="right"><?php echo $tmp_wh_name; ?> | ยอดรวม <?php echo $nub; ?> รายการ</td>
    </tr>
<?php
}
?>
</table>

<?php
}
?>
<script>
function Save_printcar_log(car_idno,id,chkstatus){
	$('body').append('<div id="divdialogshow"></div>');
    $('#divdialogshow').load('carinout_remark_print.php?car_idno='+car_idno+'&auto_id='+id+'&chkstatus='+chkstatus);
	$('#divdialogshow').dialog({
        title: 'แสดงรายละเอียด',
        resizable: false,
        modal: true,  
        width:400,
        height: 300,
        close: function(ev, ui){
            $('#divdialogshow').remove();
        }
    });
}
</script>
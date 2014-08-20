<?php
include_once("../include/config.php");
include_once("../include/function.php");
$page_title = "Reprint สัญญารถยนต์มือสอง ";
$license_plate = pg_escape_string($_GET['license_plate']);
	if($license_plate != ""){
		$qry_list = "select * from \"Cars\" where \"license_plate\" LIKE '%$license_plate%' AND (\"car_type_id\" in (2,3,4)) AND substr(\"car_idno\",1,2) not in ('US','SC','CP')  ORDER BY \"car_idno\" ASC ";
	}else{
		// $qry_list = "select * from \"Cars\" where (\"car_type_id\" in (2,3,4)) AND substr(\"car_idno\",1,2) not in ('US','SC','CP')  ORDER BY \"car_idno\" ASC ";
	}
?>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>ลำดับที่</td>
	<td>ทะเบียนรถในสต๊อก</td>
    <td>ทะเบียนรถ</td>
	<td>รายละเอียดรถ</td>
    <td>เลขตัวถัง</td>
	<td>ปีรถ</td>
	<td>เลขที่เอกสาร</td>
    <td></td>
</tr>

<?php
$j = 0;
if($license_plate != ""){
$qry = pg_query($qry_list);

while($res = pg_fetch_array($qry)){
    $j++;
    $car_id = $res['car_id'];
    $car_num = $res['car_num'];  //เลขถัง
    $car_idno = $res['car_idno'];
	$license_plate = $res['license_plate'];
	$car_year = $res['car_year'];
	$po_con = $res['po_con'];
	$car_name = $res['car_name'];
?>	
	<tr bgcolor="#E1F0FF" style="font-weight:bold">
	<td align="center"><?php echo $j; ?></td>
	<td><?php echo $car_idno; ?></td>
    <td><?php echo $license_plate; ?></td>
    <td><?php echo $car_name; ?></td>
    	<td><?php echo $car_num; ?></td>
	<td><?php echo $car_year; ?></td>
	<td><?php echo $po_con; ?></td>
    <td align="center"><img src="../images/print.png" onclick="javascript:Save_printcon_log('<?php echo $car_idno;?>')" style="cursor:pointer;"/></td>
	</tr>
<?php	
}
}

if($j == 0){
    echo "<tr><td colspan=8 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>
<script>
function Save_printcon_log(id){
	$('body').append('<div id="divdialogshow"></div>');
    $('#divdialogshow').load('pocon_remark_print.php?car_idno='+id);
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

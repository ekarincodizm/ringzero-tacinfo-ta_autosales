<?php
include_once("../include/config.php");
include_once("../include/function.php");

$page_title = "อนุมัติการแก้ไขรายละเอียดรถ ";

$qry_list = "select * from \"CarsEditTemp\" where \"appvStatus\" = '9' order by \"doerStamp\" ASC";
?>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <th>ลำดับที่</th>
	<th>Car ID</th>
	<th>แก้ไขครั้งที่</th>
    <th>ผู้ทำรายการ</th>
    <th>วันเวลาที่ทำรายการ</th>
    <th>ทำรายการ</th>
</tr>

<?php
$j = 0;
$qry = pg_query($qry_list);

while($res = pg_fetch_array($qry))
{
    $j++;
    $editCarID = $res['editCarID'];
    $car_id = $res['car_id'];
	$editTime = $res['editTime'];
    $doerID = $res['doerID'];
	$doerStamp = $res['doerStamp'];
	$doerName = GetUserName($doerID);
    
?>
	<tr bgcolor="#E1F0FF" style="font-weight:bold">
		<td align="center"><?php echo $j; ?></td>
		<td align="center"><?php echo $car_id; ?></td>
		<td align="center"><?php echo $editTime; ?></td>
		<td align="left"><?php echo $doerName; ?></td>
		<td align="center"><?php echo $doerStamp; ?></td>
		<td align="center"><img src="../images/detail.gif" onclick="javascript:ShowDetail('<?php echo $editCarID;?>','<?php echo $car_id;?>')" style="cursor:pointer;"/></td>
	</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=6 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>
<script>
function ShowDetail(editCarID, car_id){
    $('body').append('<div id="divdialogshow"></div>');
    $('#divdialogshow').load('popup_appv_edit_car.php?editCarID='+editCarID);
    $('#divdialogshow').dialog({
        title: 'แสดงรายละเอียดการแก้ไข : '+car_id,
        resizable: false,
        modal: true,  
        width:700,
        height: 500,
        close: function(ev, ui){
            $('#divdialogshow').remove();
        }
    });
}
</script>
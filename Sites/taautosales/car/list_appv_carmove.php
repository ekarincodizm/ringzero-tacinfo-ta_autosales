<?php
include_once("../include/config.php");
include_once("../include/function.php");

$page_title = "อนุมัติส่งมอบรถ ";

	$qry_list = "select * from \"CarMoveToCus\" where status_appv = '9' order by doer_id ASC";
?>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>ลำดับที่</td>
	<td>เลขใบจองรถ</td>
    <td>รหัสรถ</td>
    <td>ผู้ทำรายการ</td>
    <td>วันเวลาที่ทำรายการ</td>
    <td>ทำรายการ</td>
</tr>

<?php
$j = 0;
$qry = pg_query($qry_list);

while($res = pg_fetch_array($qry)){
    $j++;
    $auto_id = $res['auto_id'];
    $car_id = $res['car_id'];
    $res_id = $res['res_id'];
    $doer_id = $res['doer_id'];
		$doer_name = GetUserName($doer_id);
    $doer_stamp = $res['doer_stamp'];
    
  
?>
<tr bgcolor="#E1F0FF" style="font-weight:bold">
	<td><?php echo $j; ?></td>
	<td><span id="R_id" onclick="ShowDetailres('<?php echo $res_id;?>');" style="cursor:pointer;"><font color="blue"><u><?php echo $res_id ;?></font></u></td>
    <td><?php echo $car_id; ?></td>
    <td><?php echo $doer_name; ?></td>
    <td><?php echo $doer_stamp; ?></td>
    <td align="center"><img src="../images/detail.gif" onclick="javascript:ShowDetail('<?php echo $auto_id;?>')" style="cursor:pointer;"/></td>
</tr>

<?php
}

if($j == 0){
    echo "<tr><td colspan=6 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>
<script>
function ShowDetail(id){
    $('body').append('<div id="divdialogshow"></div>');
    $('#divdialogshow').load('appv_remark_carmove.php?auto_id='+id);
    $('#divdialogshow').dialog({
        title: 'แสดงรายละเอียด',
        resizable: false,
        modal: true,  
        width: 400,
        height: 300,
        close: function(ev, ui){
            $('#divdialogshow').remove();
        }
    });
}
function ShowDetailres(id){
		$('body').append('<div id="divdetail"></div>');
		$('#divdetail').load('../report/report_reserve_api.php?cmd=showdetail&id='+id);
		$('#divdetail').dialog({
			title: 'แสดงรายละเอียด : '+id,
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

<?php
include_once("../include/config.php");
include_once("../include/function.php");
$cmd = pg_escape_string($_REQUEST['cmd']);
if($cmd == "content"){
?>
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>เลขที่สัญญา</td>
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
$qry = pg_query("SELECT * FROM v_con_wait_acc ORDER BY \"IDNO\" ASC ");
//$qry = pg_query("SELECT * FROM \"Cars\" ");

while($res = pg_fetch_array($qry)){
    $j++;
    $IDNO = $res['IDNO'];
    $res_id = $res['res_id'];
    $cus_id = $res['cus_id'];
    $receive_date = $res['receive_date'];
    $pre_name = $res['pre_name'];
    $cus_name = $res['cus_name'];
    $surname = $res['surname'];
    $car_price = $res['car_price'];
    $down_price = $res['down_price'];
    $finance_price = $res['finance_price'];
    $license_plate = $res['license_plate'];
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>

    <td><a href="javascript:show_detail('<?php echo $IDNO; ?>','<?php echo $res_id; ?>','<?php echo $cus_id; ?>')" title="รายละเอียด ออกใบเสร็จ"><u><?php echo $IDNO; ?></u></a></td>
	<td><a href="javascript:show_dialog('<?php echo $res_id; ?>')" title="รายละเอียดการจอง"><u><?php echo $res_id; ?></u></a></td>
    <td><?php echo $receive_date; ?></td>
    <td><?php echo "$pre_name $cus_name $surname"; ?></td>
    <td align="right"><?php echo number_format($car_price,2); ?></td>
    <td align="right"><?php echo number_format($down_price,2); ?></td>
    <td align="right"><?php echo number_format($finance_price,2); ?></td>
    <td><?php echo $license_plate; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=15 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>

</table>

<script>

// แสดง Modal Form เปลี่ยนแปลงการจอง
/*function show_dialog(res_id){
	$('body').append('<div id="divdialogedit"></div>');
	//$('#divdialogedit').load('../car/reserv_car_edit_api.php?cmd=edit_resv_all&cmd2=t&id='+res_id);
	$('#divdialogedit').load('../car/reserv_car_edit_dialog.php?id='+res_id);
	$('#divdialogedit').dialog({
		title: 'เปลี่ยนแปลงการจอง : '+res_id,
		resizable: false,
		modal: true,  
		width: 800,
		height: 500,
		close: function(ev, ui){
			$('#divdialogedit').remove();
		}
	});
}*/

function show_dialog(res_id){
		$('body').append('<div id="divdetail"></div>');
		$('#divdetail').load('../report/report_reserve_api.php?cmd=showdetail&id='+res_id);
		$('#divdetail').dialog({
			title: 'แสดงรายละเอียด',
			resizable: false,
			modal: true,  
			width: 800,
			height: 550,
			close: function(ev, ui){
				$('#divdetail').remove();
			}
		});
}
function show_detail(idno,resid,cusid){
    $('body').append('<div id="div_print"></div>');
	$('#div_print').load('frm_receipt_detail.php?idno='+idno+'&resid='+resid+'&cusid='+cusid);
    $('#div_print').dialog({
        title: 'รายละเอียด ออกใบเสร็จ : '+idno,
        resizable: false,
        modal: true,  
        width: 780,
        height: 620,
        close: function(ev, ui){
            $('#div_print').remove();
        }
    });
}
</script>

<?php
}

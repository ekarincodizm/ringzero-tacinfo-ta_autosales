<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$cmd = $_REQUEST['cmd'];

if($cmd == "search_car"){
    $date = $_GET['date'];
    if(empty($date)){
        $date = $nowdate;
    }
?>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Car ID</td>
    <td>ทะเบียน</td>
    <td>ยี่ห้อ/รุ่น</td>
    <td>เลขถัง</td>
    <td>เลขเครื่อง</td>
    <td>PO ID</td>
</tr>
<?php
$j = 0;
$qry = pg_query("SELECT car_id, license_plate, car_name, car_num, mar_num,po_id FROM \"Cars\" WHERE po_id LIKE 'PO%' ORDER BY license_plate ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $car_id = $res['car_id'];
    $license_plate = $res['license_plate'];
    $car_name = $res['car_name'];
    $car_num = $res['car_num'];
    $mar_num = $res['mar_num'];
    $po_id = $res['po_id'];
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $car_id; ?></td>
    <td><?php echo $license_plate; ?></td>
    <td><?php echo $car_name; ?></td>
    <td><?php echo $car_num; ?></td>
    <td><?php echo $mar_num; ?></td>
    <td align="center"><a href="javascript:ShowDetail('<?php echo $po_id; ?>')" title="ดูรายละเอียด"><u><?php echo $po_id; ?></u></a></td>
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
    $('#divdialogshow').load('report_po_api.php?cmd=divdialogshow&id='+id);
    $('#divdialogshow').dialog({
        title: 'แสดงรายละเอียด PO : '+id,
        resizable: false,
        modal: true,  
        width: 700,
        height: 400,
        close: function(ev, ui){
            $('#divdialogshow').remove();
        }
    });
}
</script>

<?php
}

elseif($cmd == "search_cheque"){
?>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Acc No</td>
    <td>เลขที่เช็ค</td>
    <td>PO ID</td>
</tr>
<?php
$j = 0;
$qry = pg_query("SELECT chq_acc_no, chque_no, vc_detail FROM account.\"VoucherDetails\" WHERE vc_detail like 'PO%' 
ORDER BY chq_acc_no, chque_no ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $chq_acc_no = $res['chq_acc_no'];
    $chque_no = $res['chque_no'];
    $vc_detail = $res['vc_detail'];
    
    $arr_vc_detail = explode("\n", $vc_detail);
    $arr_list_po = explode(",", $arr_vc_detail[0]);
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $chq_acc_no; ?></td>
    <td><?php echo $chque_no; ?></td>
    <td align="center">
<?php
foreach($arr_list_po as $v){
?>
<a href="javascript:ShowDetail('<?php echo $v; ?>')" title="ดูรายละเอียด"><u><?php echo $v; ?></u></a> 
<?php
}
?>
    </td>
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
    $('#divdialogshow').load('report_po_api.php?cmd=divdialogshow&id='+id);
    $('#divdialogshow').dialog({
        title: 'แสดงรายละเอียด PO : '+id,
        resizable: false,
        modal: true,  
        width: 700,
        height: 400,
        close: function(ev, ui){
            $('#divdialogshow').remove();
        }
    });
}
</script>

<?php
}
?>
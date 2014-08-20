<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = pg_escape_string($_REQUEST['cmd']);

if($cmd == "chequedetail"){
    $id = pg_escape_string($_GET['id']);
?>

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>no.</td>
    <td>res_id</td>
    <td>Name</td>
    <td>service_id</td>
    <td>Amt</td>
</tr>
<?php
$qry = pg_query("SELECT * FROM \"VChequeDetail\" WHERE running_no='$id' ORDER BY res_id ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $res_id = $res['res_id'];
    $full_name = $res['full_name'];
    $service_id = $res['service_id'];
    $cus_amount = $res['cus_amount'];
    
    $service_name = GetServicesName($service_id);
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><?php echo $j; ?></td>
    <td><?php echo $res_id; ?></td>
    <td><?php echo $full_name; ?></td>
    <td><?php echo $service_name; ?></td>
    <td align="right"><?php echo number_format($cus_amount,2); ?></td>
</tr>
<?php
}
?>
</table>

<?php
}
elseif($cmd == "save"){
//  แบบใหม่ บันทึกทีละรายการ
    
	$rid = $_POST['rid'];
    $cid = $_POST['cid'];
	$enterdate = $_POST['enterdate'];
	
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $in_qry="UPDATE \"Cheques\" SET date_enter_bank='$enterdate' WHERE running_no='$rid' AND cheque_no='$cid' ";
		
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "UPDATE Cheques ไม่สำเร็จ $in_qry";
        $status++;
    }
    if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
    echo json_encode($data);	
}
?>
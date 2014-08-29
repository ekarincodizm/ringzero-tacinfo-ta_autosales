<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = pg_escape_string($_REQUEST['cmd']);
$id_user = $_SESSION['ss_iduser'];
$nowDateTime = nowDateTime();

if($cmd == "divshow"){
?>
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>ลำดับ</td>
    <td>เลขที่ใบเสร็จ</td>
    <td>เลขที่ใบแจ้งหนี้</td>
    <td>มูลค่า</td>
    <td>vat</td>
    <td>ยอดรวม</td>
	<td>ประเภท</td>
    <td>ผู้ทำรายการ</td>
    <td>วันเวลาที่ทำรายการ</td>
	<td>เหตุผล</td>
	<td>อนุมัติ</td>
	<td>ไม่อนุมัติ</td>
</tr>

<?php
$j = 0;
$qry = pg_query("SELECT * FROM \"CancelReceipt_account\" WHERE \"appvStatus\" = '9' order by \"doerStamp\" ");
while($res = pg_fetch_array($qry))
{
    $j++;
    $cancelID = $res['cancelID'];
    $r_receipt = $res['r_receipt'];
    $inv_no = $res['inv_no'];
    $amount = $res['amount'];
    $vat = $res['vat'];
    $money = $res['money'];
    $return_to = $res['return_to'];
	$doerID = $res['doerID'];
	$doerStamp = $res['doerStamp'];
	$doerNote = $res['doerNote'];
	$appvStatus = $res['appvStatus'];
	
	// หาชื่อพนักงานที่ทำรายการ
	$qry_fullnameUser = pg_query("select \"fullname\" from \"fuser\" where \"id_user\" = '$doerID' ");
	$fullnameUser = pg_fetch_result($qry_fullnameUser,0);
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><?php echo $j; ?></td>
    <td align="center"><?php echo $r_receipt; ?></td>
    <td align="center"><?php echo $inv_no; ?></td>
    <td align="right"><?php echo number_format($amount, 2); ?></td>
	<td align="right"><?php echo number_format($vat, 2); ?></td>
	<td align="right"><?php echo number_format($money, 2); ?></td>
    <td align="center"><?php echo $return_to; ?></td>
    <td align="left"><?php echo $fullnameUser; ?></td>
    <td align="center"><?php echo $doerStamp; ?></td>
	<td align="left"><?php echo $doerNote; ?></td>
    <td align="center"><input type="button" value="อนุมัติ" name="btnApp" id="btnApp" onclick="javascript:Approve('<?php echo $cancelID; ?>','<?php echo $r_receipt; ?>','1')"></td>
	<td align="center"><input type="button" value="ไม่อนุมัติ" name="btnApp" id="btnApp" onclick="javascript:Approve('<?php echo $cancelID; ?>','<?php echo $r_receipt; ?>','0')"></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=\"12\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>

<script>
function Approve(cancelID, r_receipt, appvStatus)
{
	var txt_title;
	
	if(appvStatus == '1'){txt_title = 'อนุมัติ';}else{txt_title = 'ไม่อนุมัติ';}
	
    $('body').append('<div id="divdialogconfirm"></div>');
    $("#divdialogconfirm").text('ต้องการ ' + txt_title + ' ยกเลิกใบเสร็จ ' + r_receipt + ' ใช่หรือไม่ ?');
    $("#divdialogconfirm").dialog({
        title: txt_title,
        resizable: false,
        height:140,
        modal: true,
        buttons:{
            "ใช่": function(){
                $.post('cancel_receipt_approve_api.php',{
                    cmd: 'save',
                    cancelID: cancelID,
					appvStatus: appvStatus
                },
                function(data){
                    if(data.success){
                        $("#divdialogconfirm").remove();
                        alert(data.message);
                        location.reload();
                    }else{
                        alert(data.message);
                    }
                },'json');
            },
            "ไม่ใช่": function(){
                $( this ).dialog( "close" );
            }
        }
    });
}
</script>
<?php
}

// ทำรายการอนุมัติ
elseif($cmd == "save")
{
    $cancelID = pg_escape_string($_POST['cancelID']);
	$appvStatus = pg_escape_string($_POST['appvStatus']);
    
    pg_query("BEGIN WORK");
    $status = 0;
    $stat_ok = 0;
	
	// หา หาเลขที่ใบเสร็จ ใบแจ้งหนี้ และ ตรวจสอบว่ามีการทำรายการไปก่อนหน้านี้แล้วหรือยัง
	$qry_appvS= pg_query("select \"r_receipt\", \"inv_no\", \"appvStatus\" from \"CancelReceipt_account\" where \"cancelID\" = '$cancelID' ");
	$r_receipt = pg_fetch_result($qry_appvS,0);
	$inv_no = pg_fetch_result($qry_appvS,1);
	$appvStatusChk = pg_fetch_result($qry_appvS,2);
	if($appvStatusChk == "1")
	{
		$txt_error .= "เนื่องจาก อนุมัติ ยกเลิก $inv_no ไปก่อนหน้านี้แล้ว ";
		$status++;
	}
	elseif($appvStatusChk == "0")
	{
		$txt_error .= "เนื่องจาก ไม่อนุมัติ ยกเลิก $inv_no ไปก่อนหน้านี้แล้ว ";
		$status++;
	}
	
	// บันทึกการทำรายการ
	$sql_appv = "update \"CancelReceipt_account\" set \"appvStatus\" = '$appvStatus', \"appvID\" = '$id_user', \"appvStamp\" = '$nowDateTime'
				where \"cancelID\" = '$cancelID' and \"appvStatus\" = '9' ";
	$qry_appv = pg_query($sql_appv);
	if(!$qry_appv)
	{
		$txt_error .= "เกิดข้อผิดพลาด $sql_appv ";
		$status++;
	}
	
	// ถ้าอนุมัติ
	if($appvStatus == "1")
	{
		$qry="UPDATE \"Receipts\" SET \"cancel\" = true WHERE r_receipt = '$r_receipt' ";
		if(!$res=@pg_query($qry)){
			$status++;
			$txt_error .= "เกิดข้อผิดพลาด $qry ";
		}
		
		// คืนค่า ให้สามาถกลับไปตั้ง ออกใบเสร็จ  ใหม่ได้
		$up_inv = "UPDATE \"Invoices_account\" SET \"is_print\" = '0', \"tax_no\" = null WHERE \"inv_no\" = '$inv_no' ";
		if(!$res=@pg_query($up_inv)){
			$status++;
			$txt_error .= "ผิดผลาดในการคืนค่า : $up_inv ";
		}
		
		$qry="UPDATE \"Vats\" SET cancel = 'TRUE' WHERE v_receipt = '$r_receipt' ";
		if(!$res=@pg_query($qry)){
			$status++;
			$txt_error .= "ผิดผลาด : $qry ";
		}
	}
    
    if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = $txt_error;
    }

    echo json_encode($data);
}
?>
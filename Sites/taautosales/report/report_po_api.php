<?php
include_once("../include/config.php");
include_once("../include/function.php");
$cmd = pg_escape_string($_REQUEST['cmd']);
if($cmd == "changetype"){
    $type = pg_escape_string($_GET['type']);    
    if($type == 1){
?>
        <input type="text" name="txt_po_date" id="txt_po_date" style="width:80px; text-align:center" value="<?php echo $nowdate; ?>">
        <script type="text/javascript">
        $("#txt_po_date").datepicker({
            showOn: 'button',
            buttonImage: '../images/calendar.gif',
            buttonImageOnly: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });
        </script>
<?php
    }elseif($type == 2){
?>
        <select name="cb_status_po" id="cb_status_po">
            <option value="1">ACTIVE</option>
            <option value="2">CANCEL</option>
            <option value="3">WAIT AP</option>
        </select>
<?php
     }elseif($type == 3){
?>
        <select name="cb_receive_all" id="cb_receive_all">
            <option value="1">ครบ</option>
            <option value="2">ขาดส่ง</option>
        </select>
<?php
     }elseif($type == 4){
?>
        <select name="cb_pay" id="cb_pay">
            <option value="1">READY </option>
            <option value="2">HOLD</option>
        </select>
<?php
    }
}

elseif($cmd == "report"){
    $type = $_GET['type'];

    if($type == 1){
        $date = $_GET['date'];
        $qry = "SELECT * FROM \"PurchaseOrders\" WHERE po_date='$date' ORDER BY po_id ASC ";
        $pdf = "report_po_pdf.php?cmd=report&type=1&date=$date";
    }elseif($type == 2){
        $status_po = $_GET['status_po'];
        if($status_po == "1"){
            $str_status = "FALSE";
            $str_status_apv = "AND approve='TRUE'";
        }elseif($status_po == "2"){
            $str_status = "TRUE";
            $str_status_apv = "";
        }elseif($status_po == "3"){
            $str_status = "FALSE";
            $str_status_apv = "AND approve='FALSE'";
        }
        $qry = "SELECT * FROM \"PurchaseOrders\" WHERE cancel='$str_status' $str_status_apv ORDER BY po_id ASC ";
        $pdf = "report_po_pdf.php?cmd=report&type=2&status_po=$status_po";
    }elseif($type == 3){
        $receive_all = $_GET['receive_all'];
        if($receive_all == "1"){
            $str_status = "TRUE";
        }else{
            $str_status = "FALSE";
        }
        $qry = "SELECT * FROM \"PurchaseOrders\" WHERE receive_all='$str_status' ORDER BY po_id ASC ";
        $pdf = "report_po_pdf.php?cmd=report&type=3&receive_all=$receive_all";
    }elseif($type == 4){
        $pay = $_GET['pay'];
        if($pay == "1"){
            $str_status = "TRUE";
        }else{
            $str_status = "FALSE";
        }
        $qry = "SELECT * FROM \"PurchaseOrders\" WHERE pay='$str_status' ORDER BY po_id ASC ";
        $pdf = "report_po_pdf.php?cmd=report&type=4&pay=$pay";
    }elseif($type == "all"){
        $qry = "SELECT * FROM \"PurchaseOrders\" ORDER BY po_id ASC ";
        $pdf = "report_po_pdf.php?cmd=report&type=all";
    }
?>

<div style="text-align:right">
<a href="<?php echo $pdf; ?>" target="_blank">
<span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span>
</a>
</div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Po ID</td>
    <td>Po Date</td>
    <td>ผู้ขาย</td>
    <td>ยอดเงิน</td>
    <td>สถานะ PO</td>
    <td>สถานะของส่ง</td>
    <td>การชำระ</td>
</tr>
<?php
$j = 0;
$qry = pg_query($qry);
while($res = pg_fetch_array($qry)){
    $j++;
    $po_id = $res['po_id'];
    $po_date = $res['po_date'];
    $vender_id = $res['vender_id'];
    $amount = $res['amount'];
    $cancel = $res['cancel'];
    $receive_all = $res['receive_all'];
    $pay = $res['pay'];
    $approve = $res['approve'];
	$po_type_id = $res['po_type_id'];
    
	$vender_name = getCusNameFromVender($vender_id,$po_type_id);
	
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><a href="javascript:ShowDetail('<?php echo $po_id; ?>')"><u><?php echo $po_id; ?></u></a></td>
    <td align="center"><?php echo $po_date; ?></td>
    <td><?php echo $vender_name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
<?php
if($cancel == "t"){
    echo "<td style=\"text-align:center; font-weight:bold; background-color:#FF6A6A\">";
    echo "CANCEL";
}elseif($cancel == "f" AND $approve == "t"){
    echo "<td style=\"text-align:center; font-weight:bold; background-color:#F0FFF0\">";
    echo "ACTIVE";
}elseif($cancel == "f" AND $approve == "f"){
    echo "<td style=\"text-align:center; font-weight:bold; background-color:#FFFACD\">";
    echo "WAIT AP";
}
?>
    </td>
    <td align="center"><?php if($receive_all == "f"){ echo "ขาดส่ง"; }else{ echo "ครบ"; } ?></td>
    <td align="center"><?php if($pay == "f"){ echo "HOLD"; }else{ echo "READY"; } ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=\"10\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
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

elseif($cmd == "divdialogshow"){
    $id = $_GET['id'];
    
    $qry = pg_query("SELECT * FROM \"PurchaseOrders\" WHERE po_id='$id' ");
    if($res = pg_fetch_array($qry)){
        $vender_id = $res['vender_id'];
        $cancel = $res['cancel'];
        $user_id = $res['user_id'];
        $approve_by = $res['approve_by'];
        $pay = $res['pay'];
        
        if($cancel == "f"){ $str_cancel = "ACTIVE"; }else{ $str_cancel = "CANCEL"; }
        if($pay == "f"){ $str_pay = "HOLD"; }else{ $str_pay = "READY"; }
    }
?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td align="left">PO ID : <?php echo $id; ?></td>
    <td align="right">สถานะ PO : <?php echo $str_cancel; ?></td>
</tr>
<tr>
    <td align="left">ผู้ขาย : <?php echo GetVender($vender_id); ?></td>
    <td align="right">ผู้ตั้งเรื่อง : <?php echo GetUserName($user_id); ?></td>
</tr>
<tr>
    <td align="left">&nbsp;</td>
    <td align="right">ผู้อนุมัติ : <?php echo GetUserName($approve_by); ?></td>
</tr>
</table>

<div style="margin-top:5px">
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Product ID</td>
    <td>Product Name</td>
    <td>Unit</td>
    <td>Receive</td>
    <td>Amount</td>
    <td>Vat</td>
    <td>Total</td>
</tr>
<?php
$j = 0;
    $qry_dt = pg_query("SELECT auto_id,product_id,unit,product_cost,vat FROM \"PurchaseOrderDetails\" WHERE po_id='$id' ");
    while($res_dt = pg_fetch_array($qry_dt)){
	$j++;
        $auto_id = $res_dt['auto_id'];
		$product_id = $res_dt['product_id'];
		$unit = $res_dt['unit'];
        $product_cost = $res_dt['product_cost'];
        $vat = $res_dt['vat'];
    

    $sumall_amount+=$product_cost;
    $sumall_vat+=$vat;
    $sumall_all+=($product_cost+$vat);

    $count_unit=@pg_query("select count_receive_good('$auto_id')");
    $receive=@pg_fetch_result($count_unit,0);
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><a href="javascript:ShowProductDetail('<?php echo $product_id; ?>','<?php echo $id; ?>')"><u><?php echo $product_id; ?></u></a></td>
    <td><?php echo GetProductName($product_id); ?></td>
    <td align="right"><?php echo $unit; ?></td>
    <td align="right"><?php echo $receive; ?></td>
    <td align="right"><?php echo number_format($product_cost,2); ?></td>
    <td align="right"><?php echo number_format($vat,2); ?></td>
    <td align="right"><?php echo number_format($product_cost+$vat,2); ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=\"7\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{
?>
<tr style="background-color:#E1FFE1; font-weight:bold">
    <td colspan="4" align="right">รวมเงิน</td>
    <td align="right"><?php echo number_format($sumall_amount,2); ?></td>
    <td align="right"><?php echo number_format($sumall_vat,2); ?></td>
    <td align="right"><?php echo number_format($sumall_all,2); ?></td>
</tr>
<?php
}
?>
</table>
</div>

<div style="margin-top:5px">
การชำระเงิน : <?php echo $str_pay; ?><br />
ชำระโดย<br />
<?php
$qry = pg_query("SELECT * FROM account.\"VoucherDetails\" WHERE vc_detail LIKE '%$id%' ");
while($res = pg_fetch_array($qry)){
    $cash_amt = $res['cash_amt'];
    $chque_no = $res['chque_no'];

    if(!empty($chque_no)){
        $qry7 = pg_query("SELECT * FROM account.\"ChequeAccDetails\" WHERE chq_id = '$chque_no' ");
        if($res7 = pg_fetch_array($qry7)){
            $date_on_chq = $res7['date_on_chq'];
            $amount = $res7['amount'];
            $ac_id = $res7['ac_id'];
        }
        
        $qry8 = pg_query("SELECT * FROM account.\"ChequeAccs\" WHERE ac_id = '$ac_id' ");
        if($res8 = pg_fetch_array($qry8)){
            $bank_name = $res8['bank_name'];
            $bank_branch = $res8['bank_branch'];
        }
    }
    
    if($cash_amt != 0 AND !empty($chque_no)){
        //ทั้ง 2อย่าง
        echo "- เงินสด ".number_format($cash_amt,2)." บาท<br />";
        echo "- เช็ค ธนาคาร $bank_name สาขา $bank_branch เลขที่ $chque_no ลงวันที่ $date_on_chq ยอดเงิน ".number_format($amount,2)." บาท<br />";
    }elseif($cash_amt == 0 AND !empty($chque_no)){
        //จ่ายเช็ค
        echo "- เช็ค ธนาคาร $bank_name สาขา $bank_branch เลขที่ $chque_no ลงวันที่ $date_on_chq ยอดเงิน ".number_format($amount,2)." บาท<br />";
    }elseif($cash_amt != 0 AND empty($chque_no)){
        //เงินสด
        echo "- เงินสด<br />";
    }else{
        echo "- ข้อมูลผิดผลาด !<br />";
    }
}
?>
</div>

<div style="text-align:right">
<a href="report_po_pdf.php?cmd=report_po&id=<?php echo $id; ?>" target="_blank">
<span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span>
</a>
</div>

<script>
function ShowProductDetail(pdid,poid){
    $('body').append('<div id="divdialogshowproduct"></div>');
    $('#divdialogshowproduct').load('report_po_api.php?cmd=divdialogshowproduct&pdid='+pdid+'&poid='+poid);
    $('#divdialogshowproduct').dialog({
        title: 'รายละเอียดของรถ ตาม PO : '+poid+' | Product ID : '+pdid,
        resizable: false,
        modal: false,  
        width: 650,
        height: 350,
        close: function(ev, ui){
            $('#divdialogshowproduct').remove();
        }
    });
}
</script>

<?php
}

elseif($cmd == "divdialogshowproduct"){
    $pdid = $_GET['pdid'];
    $poid = $_GET['poid'];
?>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Car ID</td>
    <td>เลขถัง</td>
    <td>ทะเบียน</td>
    <td>Product ID</td>
    <td>สีรถ</td>
</tr>

<?php
$j = 0;
$qry = pg_query("SELECT * FROM \"Cars\" WHERE product_id='$pdid' AND po_id='$poid' AND cancel='false' ORDER BY car_id ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $car_id = $res['car_id'];
    $car_num = $res['car_num'];
    $license_plate = $res['license_plate'];
    $product_id = $res['product_id'];
    $color = $res['color'];

    $product_name = GetProductName($product_id);
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><a href="javascript:ShowCarsDetail('<?php echo $car_id; ?>')"><u><?php echo $car_id; ?></u></a></td>
    <td><?php echo $car_num; ?></td>
    <td><?php echo $license_plate; ?></td>
    <td><?php echo $product_name; ?></td>
    <td><?php echo $color; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=\"5\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>

<script>
function ShowCarsDetail(cid){
    $('body').append('<div id="divdialogshowcars"></div>');
    $('#divdialogshowcars').load('report_po_api.php?cmd=divdialogshowcars&cid='+cid);
    $('#divdialogshowcars').dialog({
        title: 'รายละเอียดของรถ Car ID : '+cid,
        resizable: false,
        modal: false,  
        width: 600,
        height: 300,
        close: function(ev, ui){
            $('#divdialogshowcars').remove();
        }
    });
}
</script>

<?php
}

elseif($cmd == "divdialogshowcars"){
    $id = $_GET['cid'];
    
    $qry = pg_query("SELECT * FROM \"VStockCars\" WHERE \"car_id\"='$id' ");
    if($res = pg_fetch_array($qry)){
        $license_plate = $res['license_plate'];
        $product_id = $res['product_id'];
        $product_name = GetProductName($product_id);
    }
?>

<div style="margin:5px 0 5px 0">
<b>Car ID :</b> <?php echo $id; ?><br />
<b>Product ID :</b> <?php echo "$product_id : $product_name"; ?><br />
<b>ทะเบียน :</b> <?php echo $license_plate; ?>
</div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Date In</td>
    <td>WH ID</td>
    <td>Color</td>
    <td>Date Out</td>
    <td>Target Go</td>
</tr>
<?php
$j = 0;
$qry_mv = pg_query("SELECT * FROM \"VCarMovement\" WHERE car_id='$id' ORDER BY auto_id ASC ");
while($res_mv = pg_fetch_array($qry_mv)){
    $j++;
    $auto_id = $res_mv['auto_id'];
    $date_in = $res_mv['date_in'];
    $wh_id = $res_mv['wh_id'];
    $color = $res_mv['color'];
    $date_out = $res_mv['date_out'];
    $target_go = $res_mv['target_go'];
    
    $wh_name = GetWarehousesName($wh_id);
    $target_go_name = GetWarehousesName($target_go);

    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $date_in; ?></td>
    <td><?php echo $wh_name; ?></td>
    <td><?php echo $color; ?></td>
    <td><?php echo $date_out; ?></td>
    <td><?php echo $target_go_name; ?></td>
</tr>
<?php
    $last_wh_id = $wh_id;
    $last_auto_id = $auto_id;
}

if($j == 0){
    echo "<tr><td colspan=\"5\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>

<?php
}
?>
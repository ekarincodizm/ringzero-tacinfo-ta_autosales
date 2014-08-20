<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$inv_id = $_REQUEST['inv_id'];

if(empty($inv_id) OR $inv_id == ""){
    echo "invalid param.";
    exit;
}

$qry = pg_query("SELECT * FROM \"VAccPayment\" WHERE inv_no='$inv_id' LIMIT 1 ");
if($res = pg_fetch_array($qry)){
    $res_id = $res['res_id'];
    $cus_id = $res['cus_id'];
    $o_receipt = $res['o_receipt'];
    $o_date = $res['o_date'];
    $status = $res['status'];
    
    if($status == "OCCA"){
        $status_name = "เงินสด";
    }else{
        $qry = pg_query("SELECT cheque_no FROM \"VResDetail\" WHERE res_id='$res_id' AND cheque_no IS NOT NULL ");
        if($res = pg_fetch_array($qry)){
            $cheque_no = $res['cheque_no'];
        }
        
        $status_name = "เช็ค เลขที่ $cheque_no";
    }
    
}else{
    echo "Invalid inv record. Please check VAccPayment inv id : $inv_id";
    exit;
}

$qry = pg_query("SELECT * FROM \"Customers\" WHERE cus_id='$cus_id' ");
if($res = pg_fetch_array($qry)){
    $pre_name = trim($res['pre_name']);
    $cus_name = trim($res['cus_name']);
    $surname = trim($res['surname']);
        $fullname = "$pre_name$cus_name $surname";
    $address = trim($res['address']);
    $telephone = $res['telephone'];
}

include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//set margins
$pdf->SetMargins(10, 10, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 16); //AngsanaUPC  CordiaUPC

$pdf->AddPage();

$title = "ใบเสร็จรับเงิน";

$txt = '
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="50%" align="left" style="font-weight:bold; font-size:x-large">'.$company_name.'</td>
    <td width="50%" align="right" style="font-weight:bold; font-size:x-large">'.$title.'</td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="60%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">เลขที่จอง</td><td width="">'.$res_id.'</td>
        </tr>
        <tr>
            <td>ชื่อลูกค้า</td><td>'.$fullname.'</td>
        </tr>
        <tr valign="top">
            <td>ที่อยู่</td><td>'.$address.'<br>'.$telephone.'</td>
        </tr>
        </table>

    </td>
    <td width="40%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">เลขที่ใบเสร็จ</td><td width="">'.$o_receipt.'</td>
        </tr>
        <tr>
            <td>วันที่</td><td>'.$o_date.'</td>
        </tr>
        </table>

    </td>
</tr>
</table>

<span style="font-weight:bold; font-size:large">ชำระค่า</span><br>

<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center">
    <td width="70%">รายการ</td>
    <td width="30%">จำนวนเงิน</td>
</tr>
';

$sum = 0;
$qry = pg_query("SELECT * FROM \"VAccPayment\" WHERE inv_no='$inv_id' ");
while($res = pg_fetch_array($qry)){
    $service_id = $res['service_id'];
    $amount = $res['amount'];
    $service_name = GetServicesName($service_id);
    
    $sum += $amount;
    
$txt .= '
<tr>
    <td>'.$service_name.'</td>
    <td align="right">'.number_format($amount,2).'</td>
</tr>';
    
}

$txt .= '
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold">
    <td width="60%" align="center">=== '.num2thai($sum).' ===</td>
    <td width="10%" align="right">รวม</td>
    <td width="30%" align="right">'.number_format($sum,2).'</td>
</tr>
</table>

<span style="font-weight:bold; font-size:large">ชำระโดย '.$status_name.'</span><br>

<span>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</span>';

$pdf->writeHTML($txt, true, false, true, false, '');

$title = "สำเนาใบเสร็จรับเงิน";

$txt = '
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="50%" align="left" style="font-weight:bold; font-size:x-large">'.$company_name.'</td>
    <td width="50%" align="right" style="font-weight:bold; font-size:x-large">'.$title.'</td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="60%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">เลขที่จอง</td><td width="">'.$res_id.'</td>
        </tr>
        <tr>
            <td>ชื่อลูกค้า</td><td>'.$fullname.'</td>
        </tr>
        <tr valign="top">
            <td>ที่อยู่</td><td>'.$address.'<br>'.$telephone.'</td>
        </tr>
        </table>

    </td>
    <td width="40%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">เลขที่ใบเสร็จ</td><td width="">'.$o_receipt.'</td>
        </tr>
        <tr>
            <td>วันที่</td><td>'.$o_date.'</td>
        </tr>
        </table>

    </td>
</tr>
</table>

<span style="font-weight:bold; font-size:large">ชำระค่า</span><br>

<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center">
    <td width="70%">รายการ</td>
    <td width="30%">จำนวนเงิน</td>
</tr>
';

$sum = 0;
$qry = pg_query("SELECT * FROM \"VAccPayment\" WHERE inv_no='$inv_id' ");
while($res = pg_fetch_array($qry)){
    $service_id = $res['service_id'];
    $amount = $res['amount'];
    $service_name = GetServicesName($service_id);
    
    $sum += $amount;
    
$txt .= '
<tr>
    <td>'.$service_name.'</td>
    <td align="right">'.number_format($amount,2).'</td>
</tr>';
    
}

$txt .= '
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold">
    <td width="60%" align="center">=== '.num2thai($sum).' ===</td>
    <td width="10%" align="right">รวม</td>
    <td width="30%" align="right">'.number_format($sum,2).'</td>
</tr>
</table>

<span style="font-weight:bold; font-size:large">ชำระโดย '.$status_name.'</span><br>

<span>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</span>';

$pdf->writeHTML($txt, true, false, true, false, '');

$pdf->Output('receipt_'.$id.'.pdf', 'I');
?>
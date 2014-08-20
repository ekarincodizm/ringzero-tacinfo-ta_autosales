<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$id = $_REQUEST['id'];

if(empty($id) OR $id == ""){
    echo "invalid param.";
    exit;
}

//PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 15, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->SetFont('AngsanaUPC', '', 16);
//END PDF

$pdf->AddPage();

$qry = pg_query("SELECT * FROM \"TemporaryReceipt\" WHERE tem_rec_no='$id' AND cancel='FALSE' ");
if($res = pg_fetch_array($qry)){
    $tem_id = $res['tem_id'];
    $tem_date = $res['tem_date'];
    $status = $res['status'];
    $user_id = $res['user_id'];
        $user_name = GetUserName($user_id);
    
    $qry = pg_query("SELECT * FROM \"TemporaryCustomers\" WHERE tem_id='$tem_id' ");
    if($res = pg_fetch_array($qry)){
        $cus_id = $res['cus_id'];
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
}

$txt = '
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="50%" align="left" style="font-weight:bold; font-size:x-large">'.$company_name.'</td>
    <td width="50%" align="right" style="font-weight:bold; font-size:x-large">ใบเสร็จรับเงินชั่วคราว</td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="60%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">ชื่อลูกค้า</td><td>'.$fullname.'</td>
        </tr>
        <tr valign="top">
            <td>ที่อยู่</td><td>'.$address.'<br>'.$telephone.'</td>
        </tr>
        </table>

    </td>
    <td width="40%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">เลขที่ใบเสร็จ</td><td width="">'.$id.'</td>
        </tr>
        <tr>
            <td>วันที่</td><td>'.$tem_date.'</td>
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

$prt_stock = 0;
$sum = 0;
$qry = pg_query("SELECT * FROM \"TemRecDetail\" WHERE tem_rec_no='$id' ORDER BY auto_id ASC ");
while($res = pg_fetch_array($qry)){
    $service_id = $res['service_id'];
    $service_name = GetListForSaleName($service_id);
    $amount = $res['amount'];
    $sum += $amount;
    
    if(substr($service_id, 0, 1) == "P" OR substr($service_id, 0, 1) == "M"){
        $prt_stock = 1;
    }

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

<span style="font-weight:bold; font-size:large">ชำระโดย เงินสด</span><br>

<div style="text-align:center">
ลงชื่อ ______________________________ ผู้รับเงิน<br />
( '.$user_name.' )
</div>

<span>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</span>';


$pdf->writeHTML($txt, true, false, true, false, '');

//$pdf->AddPage();

$txt = '
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="50%" align="left" style="font-weight:bold; font-size:x-large">'.$company_name.'</td>
    <td width="50%" align="right" style="font-weight:bold; font-size:x-large">สำเนาใบเสร็จรับเงินชั่วคราว</td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="60%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">ชื่อลูกค้า</td><td>'.$fullname.'</td>
        </tr>
        <tr valign="top">
            <td>ที่อยู่</td><td>'.$address.'<br>'.$telephone.'</td>
        </tr>
        </table>

    </td>
    <td width="40%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">เลขที่ใบเสร็จ</td><td width="">'.$id.'</td>
        </tr>
        <tr>
            <td>วันที่</td><td>'.$tem_date.'</td>
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
$qry = pg_query("SELECT * FROM \"TemRecDetail\" WHERE tem_rec_no='$id' ORDER BY auto_id ASC ");
while($res = pg_fetch_array($qry)){
    $service_id = $res['service_id'];
    $service_name = GetListForSaleName($service_id);
    $amount = $res['amount'];
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

<span style="font-weight:bold; font-size:large">ชำระโดย เงินสด</span><br>

<div style="text-align:center">
ลงชื่อ ______________________________ ผู้รับเงิน<br />
( '.$user_name.' )
</div>

<span>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</span>';

$pdf->writeHTML($txt, true, false, true, false, '');

if($prt_stock == 1){//ถ้าในใบเสร็จมี product_id ที่เป็น 'P%' or 'M%' ให้มีการพิมพ์ "รายงานการเบิก stock" ด้วย

$qry = pg_query("SELECT * FROM \"WithdrawalSlip\" WHERE to_depart LIKE '$id#%' ");
while($res = pg_fetch_array($qry)){
    $wd_date = $res['wd_date'];
    $wd_id = $res['wd_id'];
    $to_depart = $res['to_depart'];
    $arr_depart = explode("#", $to_depart);
    $maker_id = $res['maker_id'];
        $maker_name = GetUserName($maker_id);

$pdf->AddPage();
    
$txt = '
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="50%" align="left" style="font-weight:bold; font-size:x-large">'.$company_name.'</td>
    <td width="50%" align="right" style="font-weight:bold; font-size:x-large">รายงานการเบิก Stock</td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="60%">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="80">วันที่เบิก</td><td>'.$wd_date.'</td>
</tr>
<tr>
    <td>เลขที่ใบเบิก</td><td>'.$wd_id.'</td>
</tr>
<tr>
    <td>วัตถุประสงค์</td><td>เบิกเพื่อขาย</td>
</tr>
</table>
    </td>
    <td width="40%">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td>เลขที่ใบเสร็จรับเงิน</td><td>'.$arr_depart[0].'</td>
</tr>
<tr>
    <td>ทะเบียนรถ</td><td>'.$arr_depart[1].'</td>
</tr>
</table>
    </td>
</tr>
</table>
<br>
<span style="font-weight:bold; font-size:large">รายการที่เบิก</span><br>

<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center">
    <td width="40%">Product ID</td>
    <td width="40%">Product Name</td>
    <td width="20%">จำนวนที่เบิก</td>
</tr>
';

$qryss = pg_query("SELECT * FROM \"StockMovement\" WHERE ref_1='$wd_id' AND ref_2='$id' ");
while($resww = pg_fetch_array($qryss)){
    $product_id = $resww['product_id'];
        $service_name = GetListForSaleName($product_id);
    $amount = $resww['amount'];

$txt .= '
<tr>
    <td>'.$product_id.'</td>
    <td>'.$service_name.'</td>
    <td align="right">'.$amount.'</td>
</tr>';
    }
$txt .= '
</table>
<br />
<div style="text-align:center">
ลงชื่อ _________________________________ ผู้รับเงิน<br />
( '.$user_name.' )<br /><br />

ลงชื่อ ______________________________ ผู้เบิกสินค้า<br />
( _________________________________ )<br /><br />

ลงชื่อ ______________________________ ผู้รับสินค้า<br />
( '.$fullname.' )<br />
</div>
';




$pdf->writeHTML($txt, true, false, true, false, '');

}

}

$pdf->Output('receipt_'.$id.'.pdf', 'I');
?>
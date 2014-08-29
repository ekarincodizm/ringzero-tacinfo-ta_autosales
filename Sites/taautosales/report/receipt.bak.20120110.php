<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$rec_id = $_REQUEST['rec_id'];

if(empty($rec_id) OR $rec_id == ""){
    echo "invalid param.";
    exit;
}

$arr_recid = explode(",", $rec_id);
$name_pdf_file = implode("_", $arr_recid);

include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//set margins
$pdf->SetMargins(10, 15, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 16); //AngsanaUPC  CordiaUPC

foreach($arr_recid as $v){
    
    $sub_3 = substr($v, 2,1);
    
    if( $sub_3 == "A" OR $sub_3 == "R" ){
        if($sub_3 == "A"){
            $title = "ใบเสร็จรับเงิน / ใบกำกับภาษี"; //VReceipt
        }elseif($sub_3 == "R"){
            $title = "ใบเสร็จรับเงิน"; //VReceipt
        }
        
        $qry = pg_query("SELECT * FROM \"VReceipt\" WHERE r_receipt='$v' ");
        if($res = pg_fetch_array($qry)){
            $rec_receipt = $res['r_receipt'];
            $rec_date = $res['r_date'];
            $cus_id = $res['cus_id'];
            $car_id = $res['car_id'];
            $car_num = $res['car_num'];
            $mar_num = $res['mar_num'];
            $color = $res['color'];
            $amount = $res['amount'];
            $vat = $res['vat'];
            $service_id = $res['service_id'];
            $car_name = $res['name'];
            /*
            $qry_resv = pg_query("SELECT cus_id FROM \"Reserves\" WHERE res_id='$cus_id' ");
            if($res_resv = pg_fetch_array($qry_resv)){
                $res_cus_id= $res_resv['cus_id'];
            }
            */
            $qry = pg_query("SELECT * FROM \"Customers\" WHERE cus_id='$cus_id' ");
            if($res = pg_fetch_array($qry)){
                $pre_name = trim($res['pre_name']);
                $cus_name = trim($res['cus_name']);
                $surname = trim($res['surname']);
                    $fullname = "$pre_name $cus_name $surname";
                $address = trim($res['address']);
                $telephone = $res['telephone'];
            }
        }
    }elseif( $sub_3 == "V" ){
        $title = "ใบกำกับภาษี"; //VVat
        
        $qry = pg_query("SELECT * FROM \"VVat\" WHERE v_receipt='$v' ");
        if($res = pg_fetch_array($qry)){
            $rec_receipt = $res['v_receipt'];
            $rec_date = $res['v_date'];
            $cus_id = $res['cus_id'];
            $car_id = $res['car_id'];
            $car_num = $res['car_num'];
            $mar_num = $res['mar_num'];
            $color = $res['color'];
            $amount = $res['amount'];
            $vat = $res['vat'];
            $service_id = $res['service_id'];
            $car_name = $res['name'];
            /*
            $qry_resv = pg_query("SELECT cus_id FROM \"Reserves\" WHERE res_id='$cus_id' ");
            if($res_resv = pg_fetch_array($qry_resv)){
                $res_cus_id= $res_resv['cus_id'];
            }
            */
            $qry = pg_query("SELECT * FROM \"Customers\" WHERE cus_id='$cus_id' ");
            if($res = pg_fetch_array($qry)){
                $pre_name = trim($res['pre_name']);
                $cus_name = trim($res['cus_name']);
                $surname = trim($res['surname']);
                    $fullname = "$pre_name $cus_name $surname";
                $address = trim($res['address']);
                $telephone = $res['telephone'];
            }
        }
        
    }elseif( $sub_3 == "I" ){
        $title = "ใบแจ้งหนี้"; //VInvoiceAR
        
        $qry = pg_query("SELECT * FROM \"VInvoiceAR\" WHERE inv_no='$v' ");
        if($res = pg_fetch_array($qry)){
            $rec_receipt = $res['inv_no'];
            $rec_date = $res['inv_date'];
            $cus_id = $res['cus_id'];
            $car_id = $res['car_id'];
            $car_num = $res['car_num'];
            $mar_num = $res['mar_num'];
            $color = $res['color'];
            $amount = $res['amount'];
            $vat = $res['vat'];
            $service_id = $res['service_id'];
            $car_name = $res['name'];
            /*
            $qry_resv = pg_query("SELECT cus_id FROM \"Reserves\" WHERE res_id='$cus_id' ");
            if($res_resv = pg_fetch_array($qry_resv)){
                $res_cus_id= $res_resv['cus_id'];
            }
            */
            $qry = pg_query("SELECT * FROM \"Customers\" WHERE cus_id='$cus_id' ");
            if($res = pg_fetch_array($qry)){
                $pre_name = trim($res['pre_name']);
                $cus_name = trim($res['cus_name']);
                $surname = trim($res['surname']);
                    $fullname = "$pre_name $cus_name $surname";
                $address = trim($res['address']);
                $telephone = $res['telephone'];
            }
        }
        
    }else{
        continue;
    }

$pdf->AddPage();

$txt = '
<div style="font-weight:bold; text-align:center; font-size:x-large">'.$title.'</div>
<br>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="60%" valign="top">

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="70">ชื่อลูกค้า</td><td width="">'.$fullname.'</td>
</tr>
<tr>
    <td>ที่อยู่</td><td>'.$address.'<br>'.$telephone.'</td>
</tr>
</table>

    </td>
    <td width="40%" valign="top">

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="75">เลขที่ใบเสร็จ</td>
    <td width="">'.$rec_receipt.'</td>
</tr>
<tr>
    <td>วันที่</td><td>'.formatDate($rec_date,"/").'</td>
</tr>
<tr>
    <td><br></td><td></td>
</tr>
<tr>
    <td>ยี่ห้อรถ</td><td>'.$car_name.'</td>
</tr>
<tr>
    <td>เลขถัง</td><td>'.$car_num.'</td>
</tr>
<tr>
    <td>เลขเครื่อง</td><td>'.$mar_num.'</td>
</tr>
<tr>
    <td>สีรถ</td><td>'.$color.'</td>
</tr>
</table>

    </td>
</tr>
</table>

<br>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="60%" valign="top">

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="70"></td><td>ค่ารถยนต์</td>
</tr>
</table>

    </td>
    <td width="40%" valign="top">

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="75">มูลค่า</td><td width="">'.number_format($amount,2).'</td>
</tr>
<tr>
    <td>VAT</td><td>'.number_format($vat,2).'</td>
</tr>
<tr>
    <td><b>ยอดรวม</b></td><td><b>'.number_format($amount+$vat,2).'</b></td>
</tr>
</table>

    </td>
</tr>
</table>

<br>

<span style="font-weight:bold; font-size:large">('.num2thai($amount+$vat).')</span>
';

$pdf->writeHTML($txt, true, false, true, false, '');

}

$pdf->Output('receipt_'.$name_pdf_file.'.pdf', 'I');
?>
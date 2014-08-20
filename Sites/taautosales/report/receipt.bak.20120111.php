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
            $IDNO = $res['IDNO'];
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
            
            $qry_resv = pg_query("SELECT license_plate,car_year FROM \"Cars\" WHERE car_id='$car_id' ");
            if($res_resv = pg_fetch_array($qry_resv)){
                $license_plate= $res_resv['license_plate'];
                $car_year= $res_resv['car_year'];
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


if( $sub_3 == "A"){

//set margins
$pdf->SetMargins(0, 22, 0);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    
$border = 0;

//วันที่
$pdf->SetXY(97,20);
$pdf->MultiCell(30, 5,formatDate($rec_date,"/"), $border, 'L', 0, 0, '', '', true);

//ชื่อลูกค้า ที่อยู่
$pdf->SetXY(25,27);
$pdf->MultiCell(90, 5,"$fullname", $border, 'L', 0, 0, '', '', true);

$pdf->SetXY(25,34);
$pdf->MultiCell(90, 5,"$address
$telephone", $border, 'L', 0, 0, '', '', true);

//ข้อมูลใบเสร็จ รถ
$pdf->SetXY(165,20);
$pdf->MultiCell(30, 5,$rec_receipt, $border, 'L', 0, 0, '', '', true);

$pdf->SetXY(165,26);
$pdf->MultiCell(30, 5,$IDNO, $border, 'L', 0, 0, '', '', true);

$pdf->SetXY(155,33);
$pdf->MultiCell(55, 5,"$car_name", $border, 'L', 0, 0, '', '', true);

$pdf->SetXY(155,40);
$pdf->MultiCell(30, 5,$license_plate, $border, 'L', 0, 0, '', '', true);

$pdf->SetXY(190,40);
$pdf->MultiCell(20, 5,$car_year, $border, 'L', 0, 0, '', '', true);

$pdf->SetXY(155,47);
$pdf->MultiCell(40, 5,$car_num, $border, 'L', 0, 0, '', '', true);

//ค่ารถยนต์
$pdf->SetXY(130,60);
$pdf->MultiCell(40, 5,"ค่ารถยนต์", $border, 'L', 0, 0, '', '', true);

//ยอดเงิน
$pdf->SetXY(145,74);
$pdf->MultiCell(50, 5,number_format($amount,2), $border, 'R', 0, 0, '', '', true);

$pdf->SetXY(110,81);
$pdf->MultiCell(50, 5,"ค่าภาษีมูลค่าเพิ่ม 7%", $border, 'L', 0, 0, '', '', true);

$pdf->SetXY(145,81);
$pdf->MultiCell(50, 5,number_format($vat,2), $border, 'R', 0, 0, '', '', true);

$pdf->SetXY(145,95);
$pdf->MultiCell(50, 5,number_format($amount+$vat,2), $border, 'R', 0, 0, '', '', true);

$pdf->SetFont('AngsanaUPC', 'B', 16); //AngsanaUPC  CordiaUPC

$pdf->SetXY(105,101);
$pdf->MultiCell(90, 5,num2thai($amount+$vat), $border, 'L', 0, 0, '', '', true);

//หน้าใหม่
$pdf->AddPage();

$pdf->SetFont('AngsanaUPC', '', 16); //AngsanaUPC  CordiaUPC

$ln = 50;

//ชื่อลูกค้า ที่อยู่
$pdf->SetXY(25,$ln);
$pdf->MultiCell(90, 5,"$fullname", $border, 'L', 0, 0, '', '', true);

$ln += 7;
$pdf->SetXY(25,$ln);
$pdf->MultiCell(90, 5,"$address
$telephone", $border, 'L', 0, 0, '', '', true);

//ค่ารถยนต์
$ln += 21;
$pdf->SetXY(35,$ln);
$pdf->MultiCell(40, 5,"ค่ารถยนต์", $border, 'L', 0, 0, '', '', true);

//ข้อมูลใบกำกับ
$ln = 50;
$pdf->SetXY(155,$ln);
$pdf->MultiCell(30, 5, $rec_receipt, $border, 'L', 0, 0, '', '', true);

$ln += 7;
$pdf->SetXY(155,$ln);
$pdf->MultiCell(30, 5,formatDate($rec_date,"/"), $border, 'L', 0, 0, '', '', true);

$ln += 7;
$pdf->SetXY(155,$ln);
$pdf->MultiCell(30, 5,$IDNO, $border, 'L', 0, 0, '', '', true);

$ln += 7;
$pdf->SetXY(155,$ln);
$pdf->MultiCell(55, 5,"$car_name", $border, 'L', 0, 0, '', '', true);

$ln += 7;
$pdf->SetXY(155,$ln);
$pdf->MultiCell(30, 5,$license_plate, $border, 'L', 0, 0, '', '', true);

$pdf->SetXY(190,$ln);
$pdf->MultiCell(20, 5,$car_year, $border, 'L', 0, 0, '', '', true);

$ln += 7;
$pdf->SetXY(155,$ln);
$pdf->MultiCell(40, 5,$car_num, $border, 'L', 0, 0, '', '', true);

//ยอดเงิน
$ln += 14;
$pdf->SetXY(35,$ln);
$pdf->MultiCell(50, 5,number_format($amount,2), $border, 'R', 0, 0, '', '', true);

$pdf->SetXY(140,$ln);
$pdf->MultiCell(50, 5,number_format($amount+$vat,2), $border, 'R', 0, 0, '', '', true);

$ln += 7;

$pdf->SetXY(15,$ln);
$pdf->MultiCell(50, 5,"ค่าภาษีมูลค่าเพิ่ม 7%", $border, 'L', 0, 0, '', '', true);

$pdf->SetXY(35,$ln);
$pdf->MultiCell(50, 5,number_format($vat,2), $border, 'R', 0, 0, '', '', true);

$pdf->SetFont('AngsanaUPC', 'B', 16); //AngsanaUPC  CordiaUPC

$pdf->SetXY(90,$ln);
$pdf->MultiCell(95, 5,num2thai($vat), $border, 'L', 0, 0, '', '', true);

$pdf->SetFont('AngsanaUPC', '', 16); //AngsanaUPC  CordiaUPC

}else{ //อื่นๆ ที่ไม่ใช่ ใบเสร็จรับเงิน และ ใบกำกับภาษี
    
//set margins
$pdf->SetMargins(10, 15, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    
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

}

$pdf->Output('receipt_'.$name_pdf_file.'.pdf', 'I');
?>
<?php
include_once("../include/config.php");
include_once("../include/function.php");

$mm = $_GET['mm'];
$yy = $_GET['yy'];
$nowyear = date("Y")+543;
$nowdate = date("d/m/")."$nowyear";

$month = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฏาคม', '08'=>'สิงหาคม' ,'09'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');
$show_month = $month[$mm];

$show_yy = $yy+543;

//------------------- PDF -------------------//
require('../thaipdfclass.php');

class PDF extends ThaiPDF
{
    function Header(){
        $this->SetFont('AngsanaNew','',14);
        $this->SetXY(10,16); 
        $buss_name=iconv('UTF-8','windows-874',"หน้า ".$this->PageNo()."/tp");
        $this->MultiCell(190,4,$buss_name,0,'R',0);
    }
}


$pdf=new PDF('P' ,'mm','a4');
$pdf->SetLeftMargin(0);
$pdf->SetTopMargin(0);
$pdf->AliasNbPages( 'tp' );
$pdf->SetThaiFont();
$pdf->AddPage();

$page = $pdf->PageNo();

$border = 0;

$pdf->SetFont('AngsanaNew','B',18);
$pdf->SetXY(10,10);
$title=iconv('UTF-8','windows-874',"สมุดรายวันรับเงิน");
$pdf->MultiCell(190,4,$title,0,'C',0);

$pdf->SetXY(10,16);
$buss_name=iconv('UTF-8','windows-874',"$company_name");
$pdf->MultiCell(190,4,$buss_name,0,'C',0);

$pdf->SetFont('AngsanaNew','',14);

$pdf->SetXY(5,23);
$buss_name=iconv('UTF-8','windows-874',"ประจำเดือน $show_month ปี $show_yy");
$pdf->MultiCell(190,4,$buss_name,0,'L',0);

$pdf->SetXY(10,23);
$buss_name=iconv('UTF-8','windows-874',"วันที่พิมพ์ $nowdate");
$pdf->MultiCell(190,4,$buss_name,0,'R',0);

$pdf->SetXY(4,24); 
$buss_name=iconv('UTF-8','windows-874',"______________________________________________________________________________________________________________________");
$pdf->MultiCell(196,4,$buss_name,0,'C',0);

$pdf->SetXY(5,30); 
$buss_name=iconv('UTF-8','windows-874',"วันที่");
$pdf->MultiCell(20,4,$buss_name,$border,'C',0);

$pdf->SetXY(25,30); 
$buss_name=iconv('UTF-8','windows-874',"เลขที่ใบเสร็จ");
$pdf->MultiCell(20,4,$buss_name,$border,'C',0);

$pdf->SetXY(45,30); 
$buss_name=iconv('UTF-8','windows-874',"ชื่อผู้ซื้อ");
$pdf->MultiCell(40,4,$buss_name,$border,'C',0);

$pdf->SetXY(85,30); 
$buss_name=iconv('UTF-8','windows-874',"เลขที่สัญญา");
$pdf->MultiCell(25,4,$buss_name,$border,'C',0);

$pdf->SetXY(110,30); 
$buss_name=iconv('UTF-8','windows-874',"รายการ");
$pdf->MultiCell(30,4,$buss_name,$border,'C',0);

$pdf->SetXY(140,30); 
$buss_name=iconv('UTF-8','windows-874',"ยอดเงิน");
$pdf->MultiCell(20,4,$buss_name,$border,'C',0);

$pdf->SetXY(160,30); 
$buss_name=iconv('UTF-8','windows-874',"VAT");
$pdf->MultiCell(20,4,$buss_name,$border,'C',0);

$pdf->SetXY(180,30); 
$buss_name=iconv('UTF-8','windows-874',"สถานะ");
$pdf->MultiCell(20,4,$buss_name,$border,'C',0);

$pdf->SetXY(4,32); 
$buss_name=iconv('UTF-8','windows-874',"______________________________________________________________________________________________________________________");
$pdf->MultiCell(196,4,$buss_name,0,'C',0);

$pdf->SetFont('AngsanaNew','',14);
$cline = 37;

$i = 0;
$j = 0;
$qry_in=pg_query("SELECT * FROM \"VReceipt\" WHERE EXTRACT(MONTH FROM r_date)='$mm' AND EXTRACT(YEAR FROM r_date)='$yy' ");
while($res_in=pg_fetch_array($qry_in)){
    $j++;
    $r_date = $res_in["r_date"];
    $r_recipt = $res_in["r_receipt"];
    $pre_name = trim($res_in["pre_name"]);
    $cus_name = trim($res_in["cus_name"]);
    $surname = trim($res_in["surname"]);
        $full_name = "$pre_name $cus_name $surname";
    $IDNO = $res_in["IDNO"];
    $service_id = $res_in["service_id"];
        $service_name = GetProductServiceName($service_id);
    $amount = $res_in["amount"];
    $vat = $res_in["vat"];
    $money_type = $res_in["money_type"];
        if($money_type == "CA"){
            $money_type_name = "เงินสด";
        }elseif($money_type == "SA"){
            $money_type_name = "ธนาคาร";
        }else{
            $money_type_name = "N/A";
        }
        
    if( ($old_date != $r_date) AND $j != 1 ){
        
    if($i > 45){//ADD PAGE
        $pdf->AddPage();
        $cline = 37;
        $i = 1;
        
        $pdf->SetFont('AngsanaNew','B',18);
        $pdf->SetXY(10,10);
        $title=iconv('UTF-8','windows-874',"สมุดรายวันรับเงิน");
        $pdf->MultiCell(190,4,$title,0,'C',0);

        $pdf->SetXY(10,16);
        $buss_name=iconv('UTF-8','windows-874',"$company_name");
        $pdf->MultiCell(190,4,$buss_name,0,'C',0);

        $pdf->SetFont('AngsanaNew','',14);

        $pdf->SetXY(5,23);
        $buss_name=iconv('UTF-8','windows-874',"ประจำเดือน $show_month ปี $show_yy");
        $pdf->MultiCell(190,4,$buss_name,0,'L',0);

        $pdf->SetXY(10,23);
        $buss_name=iconv('UTF-8','windows-874',"วันที่พิมพ์ $nowdate");
        $pdf->MultiCell(190,4,$buss_name,0,'R',0);

        $pdf->SetXY(4,24); 
        $buss_name=iconv('UTF-8','windows-874',"______________________________________________________________________________________________________________________");
        $pdf->MultiCell(196,4,$buss_name,0,'C',0);

        $pdf->SetXY(5,30); 
        $buss_name=iconv('UTF-8','windows-874',"วันที่");
        $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

        $pdf->SetXY(25,30); 
        $buss_name=iconv('UTF-8','windows-874',"เลขที่ใบเสร็จ");
        $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

        $pdf->SetXY(45,30); 
        $buss_name=iconv('UTF-8','windows-874',"ชื่อผู้ซื้อ");
        $pdf->MultiCell(40,4,$buss_name,$border,'C',0);

        $pdf->SetXY(85,30); 
        $buss_name=iconv('UTF-8','windows-874',"เลขที่สัญญา");
        $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

        $pdf->SetXY(110,30); 
        $buss_name=iconv('UTF-8','windows-874',"รายการ");
        $pdf->MultiCell(30,4,$buss_name,$border,'C',0);

        $pdf->SetXY(140,30); 
        $buss_name=iconv('UTF-8','windows-874',"ยอดเงิน");
        $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

        $pdf->SetXY(160,30); 
        $buss_name=iconv('UTF-8','windows-874',"VAT");
        $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

        $pdf->SetXY(180,30); 
        $buss_name=iconv('UTF-8','windows-874',"สถานะ");
        $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

        $pdf->SetXY(4,32); 
        $buss_name=iconv('UTF-8','windows-874',"______________________________________________________________________________________________________________________");
        $pdf->MultiCell(196,4,$buss_name,0,'C',0);

        $pdf->SetFont('AngsanaNew','',14);
    }
        
        $sum_amt_amount_ca = array_sum($amt_amount_ca);
        $sum_amt_vat_ca = array_sum($amt_vat_ca);
        $sum_amt_amount_sa = array_sum($amt_amount_sa);
        $sum_amt_vat_sa = array_sum($amt_vat_sa);

        $cline+=1;
        
        $pdf->SetXY(5,$cline);
        $buss_name=iconv('UTF-8','windows-874',"สรุปรายวัน $old_date");
        $pdf->MultiCell(180,4,$buss_name,$border,'L',0);
        
        $pdf->SetXY(5,$cline);
        $buss_name=iconv('UTF-8','windows-874',"เงินสด ".number_format($sum_amt_amount_ca,2)." Vat ".number_format($sum_amt_vat_ca,2)." | ธนาคาร ".number_format($sum_amt_amount_sa,2)." Vat ".number_format($sum_amt_vat_sa,2));
        $pdf->MultiCell(194,4,$buss_name,$border,'R',0);
        
        $pdf->SetXY(4,$cline); 
        $buss_name=iconv('UTF-8','windows-874',"______________________________________________________________________________________________________________________");
        $pdf->MultiCell(196,4,$buss_name,0,'C',0);
        
        $i+=2;
        $cline+=9;

        $amt_amount_ca = array();
        $amt_vat_ca = array();
        $amt_amount_sa = array();
        $amt_vat_sa = array();
    }
    
    //for($kkk=0; $kkk<50; $kkk++){
    
    if($i > 45){//ADD PAGE
        $pdf->AddPage();
        $cline = 37;
        $i = 1;
        
        $pdf->SetFont('AngsanaNew','B',18);
        $pdf->SetXY(10,10);
        $title=iconv('UTF-8','windows-874',"สมุดรายวันรับเงิน");
        $pdf->MultiCell(190,4,$title,0,'C',0);

        $pdf->SetXY(10,16);
        $buss_name=iconv('UTF-8','windows-874',"$company_name");
        $pdf->MultiCell(190,4,$buss_name,0,'C',0);

        $pdf->SetFont('AngsanaNew','',14);

        $pdf->SetXY(5,23);
        $buss_name=iconv('UTF-8','windows-874',"ประจำเดือน $show_month ปี $show_yy");
        $pdf->MultiCell(190,4,$buss_name,0,'L',0);

        $pdf->SetXY(10,23);
        $buss_name=iconv('UTF-8','windows-874',"วันที่พิมพ์ $nowdate");
        $pdf->MultiCell(190,4,$buss_name,0,'R',0);

        $pdf->SetXY(4,24); 
        $buss_name=iconv('UTF-8','windows-874',"______________________________________________________________________________________________________________________");
        $pdf->MultiCell(196,4,$buss_name,0,'C',0);

        $pdf->SetXY(5,30); 
        $buss_name=iconv('UTF-8','windows-874',"วันที่");
        $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

        $pdf->SetXY(25,30); 
        $buss_name=iconv('UTF-8','windows-874',"เลขที่ใบเสร็จ");
        $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

        $pdf->SetXY(45,30); 
        $buss_name=iconv('UTF-8','windows-874',"ชื่อผู้ซื้อ");
        $pdf->MultiCell(40,4,$buss_name,$border,'C',0);

        $pdf->SetXY(85,30); 
        $buss_name=iconv('UTF-8','windows-874',"เลขที่สัญญา");
        $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

        $pdf->SetXY(110,30); 
        $buss_name=iconv('UTF-8','windows-874',"รายการ");
        $pdf->MultiCell(30,4,$buss_name,$border,'C',0);

        $pdf->SetXY(140,30); 
        $buss_name=iconv('UTF-8','windows-874',"ยอดเงิน");
        $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

        $pdf->SetXY(160,30); 
        $buss_name=iconv('UTF-8','windows-874',"VAT");
        $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

        $pdf->SetXY(180,30); 
        $buss_name=iconv('UTF-8','windows-874',"สถานะ");
        $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

        $pdf->SetXY(4,32); 
        $buss_name=iconv('UTF-8','windows-874',"______________________________________________________________________________________________________________________");
        $pdf->MultiCell(196,4,$buss_name,0,'C',0);

        $pdf->SetFont('AngsanaNew','',14);
    }

    
    $pdf->SetXY(5,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$r_date");
    $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

    $pdf->SetXY(25,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$r_recipt");
    $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

    $pdf->SetFont('AngsanaNew','',12);
    $pdf->SetXY(45,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$full_name");
    $pdf->MultiCell(40,4,$buss_name,$border,'L',0);

    $pdf->SetFont('AngsanaNew','',14);
    $pdf->SetXY(85,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$IDNO");
    $pdf->MultiCell(25,4,$buss_name,$border,'L',0);

    $pdf->SetFont('AngsanaNew','',12);
    $pdf->SetXY(110,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$service_name");
    $pdf->MultiCell(30,4,$buss_name,$border,'L',0);

    $pdf->SetFont('AngsanaNew','',14);
    $pdf->SetXY(140,$cline);
    $buss_name=iconv('UTF-8','windows-874',  number_format($amount, 2) );
    $pdf->MultiCell(20,4,$buss_name,$border,'R',0);

    $pdf->SetXY(160,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($vat, 2));
    $pdf->MultiCell(20,4,$buss_name,$border,'R',0);

    $pdf->SetXY(180,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$money_type_name");
    $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

    $cline+=5;
    $i++;

    if($money_type == "CA"){
        $amt_amount_ca[] = $amount;
        $amt_vat_ca[] = $vat;
    }elseif($money_type == "SA"){
        $amt_amount_sa[] = $amount;
        $amt_vat_sa[] = $vat;
    }
    
    //}//end for

    $old_date = $r_date;
}//end while

    if($i > 45){//ADD PAGE
        $pdf->AddPage();
        $cline = 37;
        $i = 1;
        
        $pdf->SetFont('AngsanaNew','B',18);
        $pdf->SetXY(10,10);
        $title=iconv('UTF-8','windows-874',"สมุดรายวันรับเงิน");
        $pdf->MultiCell(190,4,$title,0,'C',0);

        $pdf->SetXY(10,16);
        $buss_name=iconv('UTF-8','windows-874',"$company_name");
        $pdf->MultiCell(190,4,$buss_name,0,'C',0);

        $pdf->SetFont('AngsanaNew','',14);

        $pdf->SetXY(5,23);
        $buss_name=iconv('UTF-8','windows-874',"ประจำเดือน $show_month ปี $show_yy");
        $pdf->MultiCell(190,4,$buss_name,0,'L',0);

        $pdf->SetXY(10,23);
        $buss_name=iconv('UTF-8','windows-874',"วันที่พิมพ์ $nowdate");
        $pdf->MultiCell(190,4,$buss_name,0,'R',0);

        $pdf->SetXY(4,24); 
        $buss_name=iconv('UTF-8','windows-874',"______________________________________________________________________________________________________________________");
        $pdf->MultiCell(196,4,$buss_name,0,'C',0);

        $pdf->SetXY(5,30); 
        $buss_name=iconv('UTF-8','windows-874',"วันที่");
        $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

        $pdf->SetXY(25,30); 
        $buss_name=iconv('UTF-8','windows-874',"เลขที่ใบเสร็จ");
        $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

        $pdf->SetXY(45,30); 
        $buss_name=iconv('UTF-8','windows-874',"ชื่อผู้ซื้อ");
        $pdf->MultiCell(40,4,$buss_name,$border,'C',0);

        $pdf->SetXY(85,30); 
        $buss_name=iconv('UTF-8','windows-874',"เลขที่สัญญา");
        $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

        $pdf->SetXY(110,30); 
        $buss_name=iconv('UTF-8','windows-874',"รายการ");
        $pdf->MultiCell(30,4,$buss_name,$border,'C',0);

        $pdf->SetXY(140,30); 
        $buss_name=iconv('UTF-8','windows-874',"ยอดเงิน");
        $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

        $pdf->SetXY(160,30); 
        $buss_name=iconv('UTF-8','windows-874',"VAT");
        $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

        $pdf->SetXY(180,30); 
        $buss_name=iconv('UTF-8','windows-874',"สถานะ");
        $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

        $pdf->SetXY(4,32); 
        $buss_name=iconv('UTF-8','windows-874',"______________________________________________________________________________________________________________________");
        $pdf->MultiCell(196,4,$buss_name,0,'C',0);

        $pdf->SetFont('AngsanaNew','',14);
    }

$sum_amt_amount_ca = array_sum($amt_amount_ca);
$sum_amt_vat_ca = array_sum($amt_vat_ca);
$sum_amt_amount_sa = array_sum($amt_amount_sa);
$sum_amt_vat_sa = array_sum($amt_vat_sa);

$pdf->SetXY(5,$cline);
$buss_name=iconv('UTF-8','windows-874',"สรุปรายวัน $old_date");
$pdf->MultiCell(180,4,$buss_name,$border,'L',0);

$pdf->SetXY(5,$cline);
$buss_name=iconv('UTF-8','windows-874',"เงินสด ".number_format($sum_amt_amount_ca,2)." Vat ".number_format($sum_amt_vat_ca,2)." | ธนาคาร ".number_format($sum_amt_amount_sa,2)." Vat ".number_format($sum_amt_vat_sa,2));
$pdf->MultiCell(194,4,$buss_name,$border,'R',0);

$pdf->SetXY(4,$cline); 
$buss_name=iconv('UTF-8','windows-874',"______________________________________________________________________________________________________________________");
$pdf->MultiCell(196,4,$buss_name,0,'C',0);

$pdf->Output();
?>
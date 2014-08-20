<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$yy = $_GET['yy'];
$ty = $_GET['ty'];
$mm = $_GET['mm'];
$trimas = $_GET['trimas'];
$nowdate = date("Y/m/d");

$month = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฏาคม', '08'=>'สิงหาคม' ,'09'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');
$show_month = $month[$mm];

$show_yy = $yy+543;

//------------------- PDF -------------------//
require('../thaipdfclass.php');

class PDF extends ThaiPDF
{
    function Header(){
        $this->SetFont('AngsanaNew','',12);
        $this->SetXY(10,8); 
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

$pdf->SetFont('AngsanaNew','B',15);
$pdf->SetXY(10,10);
$title=iconv('UTF-8','windows-874',"กระดาษทำการ");
$pdf->MultiCell(190,4,$title,0,'C',0);

$pdf->SetFont('AngsanaNew','',12);
$pdf->SetXY(10,16);
$buss_name=iconv('UTF-8','windows-874',$company_name);
$pdf->MultiCell(190,4,$buss_name,0,'C',0);

$pdf->SetXY(5,23); 
$buss_name=iconv('UTF-8','windows-874',"เดือน $show_month ปี $show_yy");
$pdf->MultiCell(60,4,$buss_name,0,'L',0);

$pdf->SetXY(4,24); 
$buss_name=iconv('UTF-8','windows-874',"__________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(196,4,$buss_name,0,'C',0);

$pdf->SetXY(5,29); 
$buss_name=iconv('UTF-8','windows-874',"รหัสบัญชี");
$pdf->MultiCell(135,4,$buss_name,0,'L',0);

$pdf->SetXY(140,29); 
$buss_name=iconv('UTF-8','windows-874',"ยอดยกมา");
$pdf->MultiCell(30,4,$buss_name,0,'R',0);

$pdf->SetXY(170,29); 
$buss_name=iconv('UTF-8','windows-874',"BAL ($show_month)");
$pdf->MultiCell(30,4,$buss_name,0,'R',0);

$pdf->SetXY(4,30);
$buss_name=iconv('UTF-8','windows-874',"__________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(196,4,$buss_name,0,'C',0);

$cline = 35;

$qry = pg_query("SELECT * FROM account.\"AcTable\" ORDER BY \"AcID\" ASC");
while($res=pg_fetch_array($qry)){
    $AcID = $res['AcID'];
    $AcName = $res['AcName'];

    $set_mm = (int)$mm;
    for($i=1;$i<=$set_mm;$i++){

    //$qry_view = pg_query("SELECT \"type_acb\",\"AmtDr\",\"AmtCr\" FROM account.\"VAccountBook\" WHERE \"type_acb\" <> 'ZZ' AND \"AcID\"='$AcID' AND EXTRACT(MONTH FROM \"acb_date\")='$i' AND EXTRACT(YEAR FROM \"acb_date\")='$yy'");
    $qry_view = pg_query("SELECT \"acb_date\",\"type_acb\",\"AmtDr\",\"AmtCr\" FROM account.\"VAccountBook\" WHERE \"type_acb\" <> 'ZZ' AND \"AcID\"='$AcID' ORDER BY \"acb_date\" ASC ");
    while($res_view=pg_fetch_array($qry_view)){
        $acb_date = $res_view['acb_date'];
            if(strlen($i) == 1){ $i = "0".$i; }else{ $i = $i; }
            if(substr($acb_date,0,7) != "$yy-$i"){ continue; } //ตรวจสอบ หากไม่ใช่ เดือน/ปี ที่ต้องการ ให้ข้ามไป
        
        $type_acb = $res_view['type_acb'];
        $AmtDr = $res_view['AmtDr'];
        $AmtCr = $res_view['AmtCr'];
        
        if($i != "01" AND $type_acb == "AA"){ continue; }
        
        $sum += ($AmtDr-$AmtCr);
        $sum_all += ($AmtDr-$AmtCr);
        
        if($type_acb == "AA"){
            $sum_up += ($AmtDr-$AmtCr);
            $sum_up_all += ($AmtDr-$AmtCr);
        }
    }
    
    }
    
    if($nub >= 48){
        $nub = 0;
        $cline = 35;
        $pdf->AddPage();

        $pdf->SetFont('AngsanaNew','B',15);
        $pdf->SetXY(10,10);
        $title=iconv('UTF-8','windows-874',"กระดาษทำการ");
        $pdf->MultiCell(190,4,$title,0,'C',0);

        $pdf->SetFont('AngsanaNew','',12);
        $pdf->SetXY(10,16);
        $buss_name=iconv('UTF-8','windows-874',$company_name);
        $pdf->MultiCell(190,4,$buss_name,0,'C',0);

        $pdf->SetXY(5,23); 
        $buss_name=iconv('UTF-8','windows-874',"เดือน $show_month ปี $show_yy");
        $pdf->MultiCell(60,4,$buss_name,0,'L',0);

        $pdf->SetXY(4,24); 
        $buss_name=iconv('UTF-8','windows-874',"__________________________________________________________________________________________________________________________________________");
        $pdf->MultiCell(196,4,$buss_name,0,'C',0);

        $pdf->SetXY(5,29); 
        $buss_name=iconv('UTF-8','windows-874',"รหัสบัญชี");
        $pdf->MultiCell(135,4,$buss_name,0,'C',0);

        $pdf->SetXY(140,29); 
        $buss_name=iconv('UTF-8','windows-874',"ยอดยกมา");
        $pdf->MultiCell(30,4,$buss_name,0,'C',0);

        $pdf->SetXY(170,29); 
        $buss_name=iconv('UTF-8','windows-874',"BAL ($show_month)");
        $pdf->MultiCell(30,4,$buss_name,0,'C',0);

        $pdf->SetXY(4,30);
        $buss_name=iconv('UTF-8','windows-874',"__________________________________________________________________________________________________________________________________________");
        $pdf->MultiCell(196,4,$buss_name,0,'C',0);
    }

    $pdf->SetXY(5,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$AcID : $AcName");
    $pdf->MultiCell(135,4,$buss_name,0,'L',0);

    $pdf->SetXY(140,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($sum_up,2));
    $pdf->MultiCell(30,4,$buss_name,0,'R',0);

    $pdf->SetXY(169,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($sum,2));
    $pdf->MultiCell(30,4,$buss_name,0,'R',0);

    $sum = 0;
    $sum_up = 0;
    $cline+=5;
    $nub++;
}

    $pdf->SetXY(4,$cline-4);
    $buss_name=iconv('UTF-8','windows-874',"__________________________________________________________________________________________________________________________________________");
    $pdf->MultiCell(196,4,$buss_name,0,'C',0);

    $pdf->SetXY(5,$cline);
    $buss_name=iconv('UTF-8','windows-874',"ผลรวม");
    $pdf->MultiCell(130,4,$buss_name,0,'R',0);

    $pdf->SetXY(140,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($sum_up_all,2));
    $pdf->MultiCell(30,4,$buss_name,0,'R',0);

    $pdf->SetXY(169,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($sum_all,2));
    $pdf->MultiCell(30,4,$buss_name,0,'R',0);
    
    $pdf->SetXY(4,$cline+1);
    $buss_name=iconv('UTF-8','windows-874',"__________________________________________________________________________________________________________________________________________");
    $pdf->MultiCell(196,4,$buss_name,0,'C',0);

$pdf->Output();
?>
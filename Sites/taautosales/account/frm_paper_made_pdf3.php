<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$yy = $_GET['yy'];
$ty = $_GET['ty'];
//$mm = $_GET['mm'];
//$trimas = $_GET['trimas'];
$nowdate = date("Y/m/d");
$show_yy = $yy+543;
$month_shot = array('1'=>'มกราคม', '2'=>'กุมภาพันธ์', '3'=>'มีนาคม', '4'=>'เมษายน', '5'=>'พฤษภาคม', '6'=>'มิถุนายน', '7'=>'กรกฏาคม', '8'=>'สิงหาคม' ,'9'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');

//------------------- PDF -------------------//
require('../thaipdfclass.php');

class PDF extends ThaiPDF
{
    function Header(){
        $this->SetFont('AngsanaNew','',12);
        $this->SetXY(10,8); 
        $buss_name=iconv('UTF-8','windows-874',"หน้า ".$this->PageNo()."/tp");
        $this->MultiCell(280,4,$buss_name,0,'R',0);
    }
}

$pdf=new PDF('L' ,'mm','a4');
$pdf->SetLeftMargin(0);
$pdf->SetTopMargin(0);
$pdf->AliasNbPages( 'tp' );
$pdf->SetThaiFont();
$pdf->AddPage();

$page = $pdf->PageNo();

$border = 0;

$pdf->SetFont('AngsanaNew','B',15);
$pdf->SetXY(10,10);
$title=iconv('UTF-8','windows-874',"กระดาษทำการ");
$pdf->MultiCell(280,4,$title,$border,'C',0);

$pdf->SetFont('AngsanaNew','',12); 
$pdf->SetXY(4,23); 
$buss_name=iconv('UTF-8','windows-874',"วันที่พิมพ์ ".$nowdate);
$pdf->MultiCell(285,4,$buss_name,$border,'R',0);

$pdf->SetXY(10,15);
$buss_name=iconv('UTF-8','windows-874',$company_name);
$pdf->MultiCell(280,4,$buss_name,$border,'C',0);

$pdf->SetXY(5,23);
$buss_name=iconv('UTF-8','windows-874',"ปี $show_yy");
$pdf->MultiCell(100,4,$buss_name,$border,'L',0);

$pdf->SetXY(5,24);
$buss_name=iconv('UTF-8','windows-874',"___________________________________________________________________________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(290,4,$buss_name,0,'L',0);

$pdf->SetXY(5,30);
$buss_name=iconv('UTF-8','windows-874',"รหัสบัญชี");
$pdf->MultiCell(50,4,$buss_name,$border,'C',0);

$pdf->SetXY(55,30);
$buss_name=iconv('UTF-8','windows-874',"ยอดยกมา");
$pdf->MultiCell(18,4,$buss_name,$border,'C',0);

$x_line = 73;
for($i=1;$i<=12;$i++){
    $pdf->SetXY($x_line,30);
    $buss_name=iconv('UTF-8','windows-874',"BAL
$month_shot[$i]");
    $pdf->MultiCell(18,4,$buss_name,$border,'C',0);
    $x_line+=18;
}

$pdf->SetXY(5,35);
$buss_name=iconv('UTF-8','windows-874',"___________________________________________________________________________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(290,4,$buss_name,0,'L',0);

$pdf->SetFont('AngsanaNew','',11);

$cline = 40;
$nub = 0;
$qry = pg_query("SELECT \"AcID\",\"AcName\" FROM account.\"AcTable\" ORDER BY \"AcID\" ASC");
while($res=pg_fetch_array($qry)){
    $AcID = $res['AcID'];
    $AcName = $res['AcName'];
    
    if($nub>=30){
$pdf->AddPage();
$cline = 40;
$nub = 0;
$pdf->SetFont('AngsanaNew','B',15);
$pdf->SetXY(10,10);
$title=iconv('UTF-8','windows-874',"กระดาษทำการ");
$pdf->MultiCell(280,4,$title,$border,'C',0);

$pdf->SetFont('AngsanaNew','',12); 
$pdf->SetXY(4,23); 
$buss_name=iconv('UTF-8','windows-874',"วันที่พิมพ์ ".$nowdate);
$pdf->MultiCell(285,4,$buss_name,$border,'R',0);

$pdf->SetXY(10,15);
$buss_name=iconv('UTF-8','windows-874',$company_name);
$pdf->MultiCell(280,4,$buss_name,$border,'C',0);

$pdf->SetXY(5,23);
$buss_name=iconv('UTF-8','windows-874',"ปี $show_yy");
$pdf->MultiCell(100,4,$buss_name,$border,'L',0);

$pdf->SetXY(5,24);
$buss_name=iconv('UTF-8','windows-874',"___________________________________________________________________________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(290,4,$buss_name,0,'L',0);

$pdf->SetXY(5,30);
$buss_name=iconv('UTF-8','windows-874',"รหัสบัญชี");
$pdf->MultiCell(50,4,$buss_name,$border,'C',0);

$pdf->SetXY(55,30);
$buss_name=iconv('UTF-8','windows-874',"ยอดยกมา");
$pdf->MultiCell(18,4,$buss_name,$border,'C',0);

$x_line = 73;
for($i=1;$i<=12;$i++){
    $pdf->SetXY($x_line,30);
    $buss_name=iconv('UTF-8','windows-874',"BAL
$month_shot[$i]");
    $pdf->MultiCell(18,4,$buss_name,$border,'C',0);
    $x_line+=18;
}

$pdf->SetXY(5,35);
$buss_name=iconv('UTF-8','windows-874',"___________________________________________________________________________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(290,4,$buss_name,0,'L',0);

$pdf->SetFont('AngsanaNew','',11);
    }
    
    $aaa = substr("$AcID:$AcName",0,100);
    
    $pdf->SetXY(5,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$aaa");
    $pdf->MultiCell(50,4,$buss_name,$border,'L',0);
    
    $qry_view = pg_query("SELECT \"AmtDr\",\"AmtCr\" FROM account.\"VAccountBook\" WHERE \"type_acb\" = 'AA' AND \"AcID\"='$AcID' AND EXTRACT(MONTH FROM \"acb_date\")='01' AND EXTRACT(YEAR FROM \"acb_date\")='$yy' ");
    //$qry_view = pg_query("SELECT SUM(\"AmtDr\") AS ssdr,SUM(\"AmtCr\") AS sscr FROM account.\"VAccountBook\" WHERE \"type_acb\" = 'AA' AND \"AcID\"='$AcID' AND EXTRACT(YEAR FROM \"acb_date\")='$yy'");
    if($res_view=pg_fetch_array($qry_view)){
        $ssdr = $res_view['AmtDr'];
        $sscr = $res_view['AmtCr'];
        $sum_up += ($ssdr-$sscr);
        $sum_up_all += ($ssdr-$sscr);
    }
    
    $pdf->SetXY(55,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($sum_up,2));
    $pdf->MultiCell(18,4,$buss_name,$border,'R',0);
    $sum_up = 0;
    
    $x_line = 73;
    for($i=1;$i<=12;$i++){
        $qry_view = pg_query("SELECT \"acb_date\",\"type_acb\",\"AmtDr\",\"AmtCr\" FROM account.\"VAccountBook\" WHERE \"type_acb\" <> 'ZZ' AND \"AcID\"='$AcID' ");
        //$qry_view = pg_query("SELECT \"type_acb\",\"AmtDr\",\"AmtCr\" FROM account.\"VAccountBook\" WHERE \"type_acb\" <> 'ZZ' AND \"AcID\"='$AcID' AND EXTRACT(MONTH FROM \"acb_date\")='$i' AND EXTRACT(YEAR FROM \"acb_date\")='$yy'");
        while($res_view=pg_fetch_array($qry_view)){
            
            $acb_date2 = $res_view['acb_date'];
            if(strlen($i) == 1){ $i2 = "0".$i; }else{ $i2 = $i; }
            if(substr($acb_date2,0,7) != "$yy-$i2"){ continue; } //ตรวจสอบ หากไม่ใช่ เดือน/ปี ที่ต้องการ ให้ข้ามไป
            
            $type_acb = $res_view['type_acb'];
            $AmtDr = $res_view['AmtDr'];
            $AmtCr = $res_view['AmtCr'];
            
            if($i != 1 AND $type_acb == "AA"){
                continue;
            }
            
            $sum += ($AmtDr-$AmtCr);
            $sum_all[$i] += ($AmtDr-$AmtCr);
        }

        $pdf->SetXY($x_line,$cline);
        $buss_name=iconv('UTF-8','windows-874',number_format($sum,2));
        $pdf->MultiCell(18,4,$buss_name,$border,'R',0);
        $x_line+=18;
    }
    $sum = 0;
    $cline+=5;
    $nub++;
}

    $pdf->SetXY(5,$cline-3);
    $buss_name=iconv('UTF-8','windows-874',"____________________________________________________________________________________________________________________________________________________________________________________________________________________________");
    $pdf->MultiCell(290,4,$buss_name,0,'L',0);

    $pdf->SetXY(5,$cline);
    $buss_name=iconv('UTF-8','windows-874',"ผลรวม");
    $pdf->MultiCell(50,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY(55,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($sum_up_all,2));
    $pdf->MultiCell(18,4,$buss_name,$border,'R',0);
    
    $x_line = 73;
    for($i=1;$i<=12;$i++){
        $pdf->SetXY($x_line,$cline);
        $buss_name=iconv('UTF-8','windows-874',number_format($sum_all[$i],2));
        $pdf->MultiCell(18,4,$buss_name,$border,'R',0);
        $x_line+=18;
    }
    
    $pdf->SetXY(5,$cline+1);
    $buss_name=iconv('UTF-8','windows-874',"____________________________________________________________________________________________________________________________________________________________________________________________________________________________");
    $pdf->MultiCell(290,4,$buss_name,0,'L',0);
    
$pdf->Output();
?>
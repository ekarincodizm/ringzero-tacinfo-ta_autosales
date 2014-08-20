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

$border = 0;

$pdf->SetFont('AngsanaNew','B',15);
$pdf->SetXY(10,10);
$title=iconv('UTF-8','windows-874',"กระดาษทำการ");
$pdf->MultiCell(190,4,$title,$border,'C',0);

$pdf->SetFont('AngsanaNew','',12);
$pdf->SetXY(10,16);
$buss_name=iconv('UTF-8','windows-874',$company_name);
$pdf->MultiCell(190,4,$buss_name,$border,'C',0);

$pdf->SetXY(5,23); 
$buss_name=iconv('UTF-8','windows-874',"ปี $show_yy ไตรมาสที่ $trimas");
$pdf->MultiCell(60,4,$buss_name,$border,'L',0);

$pdf->SetXY(4,24); 
$buss_name=iconv('UTF-8','windows-874',"__________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(196,4,$buss_name,0,'C',0);

$pdf->SetXY(5,29); 
$buss_name=iconv('UTF-8','windows-874',"รหัสบัญชี");
$pdf->MultiCell(95,4,$buss_name,$border,'L',0);

$pdf->SetXY(100,29); 
$buss_name=iconv('UTF-8','windows-874',"ยอดยกมา");
$pdf->MultiCell(25,4,$buss_name,$border,'C',0);

if($trimas == 1){
    $pdf->SetXY(125,29); 
    $buss_name=iconv('UTF-8','windows-874',"มกราคม");
    $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

    $pdf->SetXY(150,29); 
    $buss_name=iconv('UTF-8','windows-874',"กุมภาพันธ์");
    $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

    $pdf->SetXY(175,29); 
    $buss_name=iconv('UTF-8','windows-874',"มีนาคม");
    $pdf->MultiCell(23,4,$buss_name,$border,'C',0);
}elseif($trimas == 2){
    $pdf->SetXY(125,29); 
    $buss_name=iconv('UTF-8','windows-874',"เมษายน");
    $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

    $pdf->SetXY(150,29); 
    $buss_name=iconv('UTF-8','windows-874',"พฤษภาคม");
    $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

    $pdf->SetXY(175,29); 
    $buss_name=iconv('UTF-8','windows-874',"มิถุนายน");
    $pdf->MultiCell(23,4,$buss_name,$border,'C',0);
}elseif($trimas == 3){
    $pdf->SetXY(125,29); 
    $buss_name=iconv('UTF-8','windows-874',"กรกฏาคม");
    $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

    $pdf->SetXY(150,29); 
    $buss_name=iconv('UTF-8','windows-874',"สิงหาคม");
    $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

    $pdf->SetXY(175,29); 
    $buss_name=iconv('UTF-8','windows-874',"กันยายน");
    $pdf->MultiCell(23,4,$buss_name,$border,'C',0);
}elseif($trimas == 4){
    $pdf->SetXY(125,29); 
    $buss_name=iconv('UTF-8','windows-874',"ตุลาคม");
    $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

    $pdf->SetXY(150,29); 
    $buss_name=iconv('UTF-8','windows-874',"พฤศจิกายน");
    $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

    $pdf->SetXY(175,29); 
    $buss_name=iconv('UTF-8','windows-874',"ธันวาคม");
    $pdf->MultiCell(23,4,$buss_name,$border,'C',0);
}

$pdf->SetXY(4,30);
$buss_name=iconv('UTF-8','windows-874',"__________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(196,4,$buss_name,0,'C',0);

$cline = 35;

$qry = pg_query("SELECT \"AcID\",\"AcName\" FROM account.\"AcTable\" ORDER BY \"AcID\" ASC");
while($res=pg_fetch_array($qry)){
    $AcID = $res['AcID'];
    $AcName = $res['AcName'];

    if($nub >= 48){
        $nub = 0;
        $cline = 35;
        $pdf->AddPage();
        $pdf->SetFont('AngsanaNew','B',15);
        $pdf->SetXY(10,10);
        $title=iconv('UTF-8','windows-874',"กระดาษทำการ");
        $pdf->MultiCell(190,4,$title,$border,'C',0);

        $pdf->SetFont('AngsanaNew','',12);
        $pdf->SetXY(10,16);
        $buss_name=iconv('UTF-8','windows-874',$company_name);
        $pdf->MultiCell(190,4,$buss_name,$border,'C',0);

        $pdf->SetXY(5,23); 
        $buss_name=iconv('UTF-8','windows-874',"ปี $show_yy ไตรมาสที่ $trimas");
        $pdf->MultiCell(60,4,$buss_name,$border,'L',0);

        $pdf->SetXY(4,24); 
        $buss_name=iconv('UTF-8','windows-874',"__________________________________________________________________________________________________________________________________________");
        $pdf->MultiCell(196,4,$buss_name,0,'C',0);

        $pdf->SetXY(5,29); 
        $buss_name=iconv('UTF-8','windows-874',"รหัสบัญชี");
        $pdf->MultiCell(95,4,$buss_name,$border,'L',0);

        $pdf->SetXY(100,29); 
        $buss_name=iconv('UTF-8','windows-874',"ยอดยกมา");
        $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

        if($trimas == 1){
            $pdf->SetXY(125,29); 
            $buss_name=iconv('UTF-8','windows-874',"มกราคม");
            $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

            $pdf->SetXY(150,29); 
            $buss_name=iconv('UTF-8','windows-874',"กุมภาพันธ์");
            $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

            $pdf->SetXY(175,29); 
            $buss_name=iconv('UTF-8','windows-874',"มีนาคม");
            $pdf->MultiCell(23,4,$buss_name,$border,'C',0);
        }elseif($trimas == 2){
            $pdf->SetXY(125,29); 
            $buss_name=iconv('UTF-8','windows-874',"เมษายน");
            $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

            $pdf->SetXY(150,29); 
            $buss_name=iconv('UTF-8','windows-874',"พฤษภาคม");
            $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

            $pdf->SetXY(175,29); 
            $buss_name=iconv('UTF-8','windows-874',"มิถุนายน");
            $pdf->MultiCell(23,4,$buss_name,$border,'C',0);
        }elseif($trimas == 3){
            $pdf->SetXY(125,29); 
            $buss_name=iconv('UTF-8','windows-874',"กรกฏาคม");
            $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

            $pdf->SetXY(150,29); 
            $buss_name=iconv('UTF-8','windows-874',"สิงหาคม");
            $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

            $pdf->SetXY(175,29); 
            $buss_name=iconv('UTF-8','windows-874',"กันยายน");
            $pdf->MultiCell(23,4,$buss_name,$border,'C',0);
        }elseif($trimas == 4){
            $pdf->SetXY(125,29); 
            $buss_name=iconv('UTF-8','windows-874',"ตุลาคม");
            $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

            $pdf->SetXY(150,29); 
            $buss_name=iconv('UTF-8','windows-874',"พฤศจิกายน");
            $pdf->MultiCell(25,4,$buss_name,$border,'C',0);

            $pdf->SetXY(175,29); 
            $buss_name=iconv('UTF-8','windows-874',"ธันวาคม");
            $pdf->MultiCell(23,4,$buss_name,$border,'C',0);
        }

        $pdf->SetXY(4,30);
        $buss_name=iconv('UTF-8','windows-874',"__________________________________________________________________________________________________________________________________________");
        $pdf->MultiCell(196,4,$buss_name,0,'C',0);
    }
    
    $pdf->SetXY(5,$cline); 
    $buss_name=iconv('UTF-8','windows-874',"$AcID : $AcName");
    $pdf->MultiCell(95,4,$buss_name,$border,'L',0);
    
    $qry_view = pg_query("SELECT SUM(\"AmtDr\") AS ssdr,SUM(\"AmtCr\") AS sscr FROM account.\"VAccountBook\" WHERE \"type_acb\" = 'AA' AND \"AcID\"='$AcID' AND EXTRACT(YEAR FROM \"acb_date\")='$yy'");
    if($res_view=pg_fetch_array($qry_view)){
        $ssdr = $res_view['ssdr'];
        $sscr = $res_view['sscr'];
        $sum_up += ($ssdr-$sscr);
    }

    $pdf->SetXY(100,$cline); 
    $buss_name=iconv('UTF-8','windows-874',number_format($sum_up,2));
    $pdf->MultiCell(25,4,$buss_name,$border,'R',0);

    $sum_up = 0;

    if($trimas == 1){
        $k = 1; $j = 3;
    }elseif($trimas == 2){
        $k = 4; $j = 6;
    }elseif($trimas == 3){
        $k = 7; $j = 9;
    }elseif($trimas == 4){
        $k = 10; $j = 12;
    }

    $x_line = 125;
    for($i=$k;$i<=$j;$i++){
        $qry_view = pg_query("SELECT \"type_acb\",\"AmtDr\",\"AmtCr\" FROM account.\"VAccountBook\" WHERE \"type_acb\" <> 'ZZ' AND \"AcID\"='$AcID' AND EXTRACT(MONTH FROM \"acb_date\")='$i' AND EXTRACT(YEAR FROM \"acb_date\")='$yy'");
        while($res_view=pg_fetch_array($qry_view)){
            $type_acb = $res_view['type_acb'];
            $AmtDr = $res_view['AmtDr'];
            $AmtCr = $res_view['AmtCr'];
            
            if($i != $k AND $type_acb == "AA"){
                continue;
            }
            
            $sum += ($AmtDr-$AmtCr);
        }
        $pdf->SetXY($x_line,$cline); 
        $buss_name=iconv('UTF-8','windows-874',number_format($sum,2));
        $pdf->MultiCell(25,4,$buss_name,$border,'R',0);
        $x_line += 25;
    }
    $sum = 0;
    $cline += 5;
    $nub++;
}

$pdf->Output();
?>
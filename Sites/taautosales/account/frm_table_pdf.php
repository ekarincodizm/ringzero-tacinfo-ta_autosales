<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$data = $_GET['data'];
$dataall = $_GET['dataall'];
$mm = $_GET['mm'];
$yy = $_GET['yy'];
$nowdate = date("Y/m/d");
$mlastdate = date("Y-m-t",strtotime("$yy-$mm-01"));

$qry_cash=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='CASH'");
if($res_cash=@pg_fetch_array($qry_cash)){
    $acid_cash = $res_cash["AcID"];
}

$qry_vatb=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='VATB'");
if($res_vatb=@pg_fetch_array($qry_vatb)){
    $acid_vatb = $res_vatb["AcID"];
}

$qry_vats=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='AVAT'");
if($res_vats=@pg_fetch_array($qry_vats)){
    $acid_vats = $res_vats["AcID"];
}

$qry_pngd=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='PNGD'");
if($res_pngd=@pg_fetch_array($qry_pngd)){
    $acid_pngd = $res_pngd["AcID"];
}

$month = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฏาคม', '08'=>'สิงหาคม' ,'09'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');

$show_month = $month[$mm];
$show_yy = $yy+543;


$arr_data = explode("|",$data);
$arr_data_all = explode("|",$dataall);

//------------------- PDF -------------------//
require('../thaipdfclass.php');

class PDF extends ThaiPDF
{
    function Header(){
        $this->SetFont('AngsanaNew','',12);
        $this->SetXY(10,8); 
        $buss_name=iconv('UTF-8','windows-874',"หน้า ".$this->PageNo()."/tp");
        $this->MultiCell(310,4,$buss_name,0,'R',0);
    }
}

$pdf=new PDF('L' ,'mm','f4');
$pdf->SetLeftMargin(0);
$pdf->SetTopMargin(0);
$pdf->AliasNbPages( 'tp' );
$pdf->SetThaiFont();
$pdf->AddPage();

$page = $pdf->PageNo();

$border = 0;

$pdf->SetFont('AngsanaNew','B',15);
$pdf->SetXY(10,10);
$title=iconv('UTF-8','windows-874',"สมุดบัญชีเงินสดรับจ่าย");
$pdf->MultiCell(310,4,$title,$border,'C',0);

$pdf->SetFont('AngsanaNew','',12); 
$pdf->SetXY(10,23); 
$buss_name=iconv('UTF-8','windows-874',"วันที่พิมพ์ ".$nowdate);
$pdf->MultiCell(310,4,$buss_name,$border,'R',0);

$pdf->SetXY(10,15);
$buss_name=iconv('UTF-8','windows-874',$company_name);
$pdf->MultiCell(310,4,$buss_name,$border,'C',0);

$pdf->SetXY(5,23);
$buss_name=iconv('UTF-8','windows-874',"ประจำเดือน $show_month ปี $show_yy");
$pdf->MultiCell(100,4,$buss_name,$border,'L',0);

$pdf->SetXY(5,24); 
$buss_name=iconv('UTF-8','windows-874',"________________________________________________________________________________________________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(315,4,$buss_name,0,'L',0);


$pdf->SetXY(5,29); 
$buss_name=iconv('UTF-8','windows-874',"วันที่");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(20,29); 
$buss_name=iconv('UTF-8','windows-874',"รายการ");
$pdf->MultiCell(40,4,$buss_name,$border,'C',0);

$pdf->SetXY(60,29); 
$buss_name=iconv('UTF-8','windows-874',"VAT ซื้อ");
$pdf->MultiCell(16,4,$buss_name,$border,'C',0);

$pdf->SetXY(76,29); 
$buss_name=iconv('UTF-8','windows-874',"VAT ขาย");
$pdf->MultiCell(16,4,$buss_name,$border,'C',0);

$pdf->SetXY(92,29); 
$buss_name=iconv('UTF-8','windows-874',"มูลค่า");
$pdf->MultiCell(16,4,$buss_name,$border,'C',0);

$pdf->SetXY(108,29); 
$buss_name=iconv('UTF-8','windows-874',"หักภาษี
ณ ที่จ่าย");
$pdf->MultiCell(16,4,$buss_name,$border,'C',0);

$x_line = 124;
$count_data = count($arr_data)*3;
$plus_line = 200/($count_data+3);

$pdf->SetXY($x_line,29); 
$buss_name=iconv('UTF-8','windows-874',"Debit
1000
เงินสด");
$pdf->MultiCell($plus_line,4,$buss_name,$border,'C',0);

$x_line+=$plus_line;

$pdf->SetXY($x_line,29); 
$buss_name=iconv('UTF-8','windows-874',"Credit
1000
เงินสด");
$pdf->MultiCell($plus_line,4,$buss_name,$border,'C',0);

$x_line+=$plus_line;

$pdf->SetXY($x_line,29); 
$buss_name=iconv('UTF-8','windows-874',"Bal
1000
เงินสด");
$pdf->MultiCell($plus_line,4,$buss_name,$border,'C',0);

$x_line+=$plus_line;

foreach($arr_data as $v){
    $sql = pg_query("SELECT \"AcName\" FROM account.\"AcTable\" WHERE \"AcID\"='$v'");
    if($result = pg_fetch_array($sql)){
        $AcName = $result['AcName'];
    }
    
    for($j=1; $j<=3; $j++){
        if($j==1){
            $pdf->SetXY($x_line,29); 
            $buss_name=iconv('UTF-8','windows-874',"Debit
$v");
            $pdf->MultiCell($plus_line,4,$buss_name,$border,'C',0);
        }elseif($j==2){
            $pdf->SetXY($x_line,29); 
            $buss_name=iconv('UTF-8','windows-874',"Credit
$v");
            $pdf->MultiCell($plus_line,4,$buss_name,$border,'C',0);
        }elseif($j==3){
            $pdf->SetXY($x_line,29); 
            $buss_name=iconv('UTF-8','windows-874',"Bal
$v");
            $pdf->MultiCell($plus_line,4,$buss_name,$border,'C',0);
        }
        $x_line+=$plus_line;
    }
}


$pdf->SetXY(5,38); 
$buss_name=iconv('UTF-8','windows-874',"________________________________________________________________________________________________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(315,4,$buss_name,0,'L',0);

$pdf->SetFont('AngsanaNew','',11);
$cline = 43;
$i_nub = 0;

$sql_head = pg_query("SELECT * FROM account.\"AccountBookHead\" WHERE EXTRACT(MONTH FROM \"acb_date\")='$mm' AND EXTRACT(YEAR FROM \"acb_date\")='$yy' AND \"cancel\"='FALSE' AND \"type_acb\"<>'ZZ' ORDER BY \"acb_date\",\"acb_id\" ASC ");
while($result_head = pg_fetch_array($sql_head)){
    //$auto_id = $result_head['auto_id'];
    $acb_date = $result_head['acb_date'];
    $acb_id = $result_head['acb_id'];
    $type_acb = $result_head['type_acb'];

$a_chk = 0;
$sql_detail_chk = pg_query("SELECT \"AcID\" FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id'");
while($result_detail_chk = pg_fetch_array($sql_detail_chk)){
    $chk_acid_chk = $result_detail_chk['AcID'];
    if($chk_acid_chk == $acid_cash OR in_array($chk_acid_chk,$arr_data)){
        $a_chk++;
    }
}

if($a_chk == 0){
    continue;
}


if($i_nub >= 28){
    $cline = 43;
    $i_nub = 0;
    $pdf->AddPage();
    
$pdf->SetFont('AngsanaNew','B',15);
$pdf->SetXY(10,10);
$title=iconv('UTF-8','windows-874',"สมุดบัญชีเงินสดรับจ่าย");
$pdf->MultiCell(310,4,$title,$border,'C',0);

$pdf->SetFont('AngsanaNew','',12); 
$pdf->SetXY(10,23); 
$buss_name=iconv('UTF-8','windows-874',"วันที่พิมพ์ ".$nowdate);
$pdf->MultiCell(310,4,$buss_name,$border,'R',0);

$pdf->SetXY(10,15);
$buss_name=iconv('UTF-8','windows-874',$company_name);
$pdf->MultiCell(310,4,$buss_name,$border,'C',0);

$pdf->SetXY(5,23);
$buss_name=iconv('UTF-8','windows-874',"ประจำเดือน $show_month ปี $show_yy");
$pdf->MultiCell(100,4,$buss_name,$border,'L',0);

$pdf->SetXY(5,24); 
$buss_name=iconv('UTF-8','windows-874',"________________________________________________________________________________________________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(315,4,$buss_name,0,'L',0);


$pdf->SetXY(5,29); 
$buss_name=iconv('UTF-8','windows-874',"วันที่");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(20,29); 
$buss_name=iconv('UTF-8','windows-874',"รายการ");
$pdf->MultiCell(40,4,$buss_name,$border,'C',0);

$pdf->SetXY(60,29); 
$buss_name=iconv('UTF-8','windows-874',"VAT ซื้อ");
$pdf->MultiCell(16,4,$buss_name,$border,'C',0);

$pdf->SetXY(76,29); 
$buss_name=iconv('UTF-8','windows-874',"VAT ขาย");
$pdf->MultiCell(16,4,$buss_name,$border,'C',0);

$pdf->SetXY(92,29); 
$buss_name=iconv('UTF-8','windows-874',"มูลค่า");
$pdf->MultiCell(16,4,$buss_name,$border,'C',0);

$pdf->SetXY(108,29); 
$buss_name=iconv('UTF-8','windows-874',"หักภาษี
ณ ที่จ่าย");
$pdf->MultiCell(16,4,$buss_name,$border,'C',0);

$x_line = 124;
$count_data = count($arr_data)*3;
$plus_line = 200/($count_data+3);

    $pdf->SetXY($x_line,29); 
    $buss_name=iconv('UTF-8','windows-874',"Debit
1000
เงินสด");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'C',0);

    $x_line+=$plus_line;

    $pdf->SetXY($x_line,29); 
    $buss_name=iconv('UTF-8','windows-874',"Credit
1000
เงินสด");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'C',0);

    $x_line+=$plus_line;

    $pdf->SetXY($x_line,29); 
    $buss_name=iconv('UTF-8','windows-874',"Bal
1000
เงินสด");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'C',0);

    $x_line+=$plus_line;

    foreach($arr_data as $v){
        $sql = pg_query("SELECT \"AcName\" FROM account.\"AcTable\" WHERE \"AcID\"='$v'");
        if($result = pg_fetch_array($sql)){
            $AcName = $result['AcName'];
        }
        
        for($j=1; $j<=3; $j++){
            if($j==1){
                $pdf->SetXY($x_line,29); 
                $buss_name=iconv('UTF-8','windows-874',"Debit
$v");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'C',0);
            }elseif($j==2){
                $pdf->SetXY($x_line,29); 
                $buss_name=iconv('UTF-8','windows-874',"Credit
$v");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'C',0);
            }elseif($j==3){
                $pdf->SetXY($x_line,29); 
                $buss_name=iconv('UTF-8','windows-874',"Bal
$v");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'C',0);
            }
            $x_line+=$plus_line;
        }
    }


    $pdf->SetXY(5,38); 
    $buss_name=iconv('UTF-8','windows-874',"________________________________________________________________________________________________________________________________________________________________________________________________________________________________");
    $pdf->MultiCell(315,4,$buss_name,0,'L',0);
    
    $pdf->SetFont('AngsanaNew','',11);
}


$i_nub++;

$bl_moolka = 0;
$chk_acid = "";
$chk_acid2 = "";
$sql_detail = pg_query("SELECT * FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' 
 AND \"AcID\" <> '$acid_cash' AND \"AcID\" <> '$acid_pngd' AND \"AcID\" <> '$acid_vatb' AND \"AcID\" <> '$acid_vats' ORDER BY \"acb_id\" ASC");
while($result_detail = pg_fetch_array($sql_detail)){
    $chk_acid = $result_detail['AcID'];
    if(!in_array($chk_acid,$arr_data_all)){
        $AmtDr = $result_detail['AmtDr']; $AmtDr = round($AmtDr,2);
        $AmtCr = $result_detail['AmtCr']; $AmtCr = round($AmtCr,2);
        $bl_moolka = number_format($AmtDr+$AmtCr,2);
        $chk_acid2 = $chk_acid;
    }
}

if(empty($chk_acid2)){
    $d_qry = pg_query("SELECT * FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id'");
    while($d_res = pg_fetch_array($d_qry)){
        $d_acid = $d_res['AcID'];
        $d_dr = $d_res['AmtDr'];
        $d_cr = $d_res['AmtCr'];
        if($d_acid == $acid_vats){
            $bl_moolka = "0.00";
            $chk_acid2 = $d_acid;
            break;
        }elseif($d_dr != 0 AND $d_cr == 0){
            $bl_moolka = number_format($d_dr+$d_cr,2);
            $chk_acid2 = $d_acid;
        }
    }
}

$name = "";
$sql_name = pg_query("SELECT \"AcName\" FROM account.\"AcTable\" WHERE \"AcID\"='$chk_acid2' ");
if($result_name = pg_fetch_array($sql_name)){
    $name = $result_name['AcName'];
}

$pdf->SetXY(5,$cline);
$buss_name=iconv('UTF-8','windows-874',"$acb_date");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetFont('AngsanaNew','',10);

if($type_acb == "AA"){
    $bl_moolka = "0.00";
    $pdf->SetXY(20,$cline);
    $buss_name=iconv('UTF-8','windows-874',"ยอดยกมา");
    $pdf->MultiCell(40,4,$buss_name,$border,'L',0);
}else{
    $bl_moolka = "0.00";
    $pdf->SetXY(20,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$chk_acid2 $name");
    $pdf->MultiCell(40,4,$buss_name,$border,'L',0);
}

$pdf->SetFont('AngsanaNew','',11);

$sql_detail = pg_query("SELECT SUM(\"AmtDr\") AS amtdr,SUM(\"AmtCr\") AS amtcr FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' AND \"AcID\"='$acid_vatb' ");
if($result_detail = pg_fetch_array($sql_detail)){
    $AmtDr = $result_detail['amtdr']; $AmtDr = round($AmtDr,2);
    $AmtCr = $result_detail['amtcr']; $AmtCr = round($AmtCr,2);
    $bl = number_format($AmtDr+$AmtCr,2);
}

$pdf->SetXY(60,$cline);
$buss_name=iconv('UTF-8','windows-874',"$bl");
$pdf->MultiCell(16,4,$buss_name,$border,'R',0);

$sql_detail = pg_query("SELECT SUM(\"AmtDr\") AS amtdr,SUM(\"AmtCr\") AS amtcr FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' AND \"AcID\"='$acid_vats' ");
if($result_detail = pg_fetch_array($sql_detail)){
    $AmtDr = $result_detail['amtdr']; $AmtDr = round($AmtDr,2);
    $AmtCr = $result_detail['amtcr']; $AmtCr = round($AmtCr,2);
    $bl = number_format($AmtDr+$AmtCr,2);
}

$pdf->SetXY(76,$cline);
$buss_name=iconv('UTF-8','windows-874',"$bl");
$pdf->MultiCell(16,4,$buss_name,$border,'R',0);

$pdf->SetXY(92,$cline);
$buss_name=iconv('UTF-8','windows-874',"$bl_moolka");
$pdf->MultiCell(16,4,$buss_name,$border,'R',0);


$sql_detail = pg_query("SELECT SUM(\"AmtDr\") AS amtdr,SUM(\"AmtCr\") AS amtcr FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' AND \"AcID\"='$acid_pngd' ");
if($result_detail = pg_fetch_array($sql_detail)){
    $AmtDr = $result_detail['amtdr']; $AmtDr = round($AmtDr,2);
    $AmtCr = $result_detail['amtcr']; $AmtCr = round($AmtCr,2);
    $bl = number_format($AmtDr+$AmtCr,2);
}

$pdf->SetXY(108,$cline);
$buss_name=iconv('UTF-8','windows-874',"$bl");
$pdf->MultiCell(16,4,$buss_name,$border,'R',0);


$sql_detail = pg_query("SELECT SUM(\"AmtDr\") AS amtdr,SUM(\"AmtCr\") AS amtcr FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' AND \"AcID\"='$acid_cash' ");
if($result_detail = pg_fetch_array($sql_detail)){
    $AmtDr = $result_detail['amtdr']; $AmtDr = round($AmtDr,2);
    $AmtCr = $result_detail['amtcr']; $AmtCr = round($AmtCr,2);
    if($AmtDr >= $AmtCr){
        $sum_cash += ($AmtDr-$AmtCr);
    }else{
        $sum_cash -= ($AmtCr-$AmtDr);
    }
    
    $AmtDr = number_format($AmtDr,2);
    $AmtCr = number_format($AmtCr,2);
    $sum_cash_fm = number_format($sum_cash,2);
}

$x_line = 124;
$pdf->SetXY($x_line,$cline);
$buss_name=iconv('UTF-8','windows-874',"$AmtDr");
$pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);

$x_line+=$plus_line;

$pdf->SetXY($x_line,$cline);
$buss_name=iconv('UTF-8','windows-874',"$AmtCr");
$pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);

$x_line+=$plus_line;

$pdf->SetXY($x_line,$cline);
$buss_name=iconv('UTF-8','windows-874',"$sum_cash_fm");
$pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);

$x_line+=$plus_line;

foreach($arr_data as $v){

    $AmtDr = 0;
    $AmtCr = 0;
    $sql_detail = pg_query("SELECT SUM(\"AmtDr\") AS amtdr,SUM(\"AmtCr\") AS amtcr FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' AND \"AcID\"='$v' ");
    if($result_detail = pg_fetch_array($sql_detail)){
        $AmtDr = $result_detail['amtdr']; $AmtDr = round($AmtDr,2);
        $AmtCr = $result_detail['amtcr']; $AmtCr = round($AmtCr,2);
        if($AmtDr >= $AmtCr){
            $balance[$v] += ($AmtDr-$AmtCr);
        }else{
            $balance[$v] -= ($AmtCr-$AmtDr);
        }
    }
    
    $dr = number_format($AmtDr,2);
    $cr = number_format($AmtCr,2);
    $bl = number_format($balance[$v],2);
    
    for($j=1; $j<=3; $j++){
        if($j==1){
            $pdf->SetXY($x_line,$cline); 
            $buss_name=iconv('UTF-8','windows-874',"$dr");
            $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
        }elseif($j==2){
            $pdf->SetXY($x_line,$cline); 
            $buss_name=iconv('UTF-8','windows-874',"$cr");
            $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
        }elseif($j==3){
            $pdf->SetXY($x_line,$cline); 
            $buss_name=iconv('UTF-8','windows-874',"$bl");
            $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
        }
        $x_line+=$plus_line;
    }
}

$cline+=5;
}


$aid = "";
$sql_k = pg_query("SELECT * FROM account.\"AccountBookHead\" WHERE \"acb_date\"='$mlastdate' AND \"type_acb\"='ZZ' ");
if($result_k = pg_fetch_array($sql_k)){
    $aid = $result_k['acb_id'];
}

$next_firstmonth =date("Y-m-d", strtotime("+1 day",strtotime($mlastdate)));
$sql_k = pg_query("SELECT * FROM account.\"AccountBookHead\" WHERE \"acb_date\"='$next_firstmonth' AND \"type_acb\"='AA' ");
if($result_k = pg_fetch_array($sql_k)){
    $aid2 = $result_k['acb_id'];
}

$pdf->SetXY(5,$cline);
$buss_name=iconv('UTF-8','windows-874',"$mlastdate");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(20,$cline);
$buss_name=iconv('UTF-8','windows-874',"ยอดยกไป");
$pdf->MultiCell(40,4,$buss_name,$border,'L',0);

$x_line = 124;

if($sum_cash < 0){
    
    $pdf->SetXY($x_line,$cline-3);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY($x_line,$cline+1);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY($x_line,$cline+1.5);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY($x_line,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$sum_cash");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);

    $x_line+=$plus_line;
    
    $pdf->SetXY($x_line,$cline-3);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY($x_line,$cline+1);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY($x_line,$cline+1.5);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);

    $pdf->SetXY($x_line,$cline);
    $buss_name=iconv('UTF-8','windows-874',"0.00");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);

    $x_line+=$plus_line;
    
    $pdf->SetXY($x_line,$cline-3);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY($x_line,$cline+1);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY($x_line,$cline+1.5);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);

    $pdf->SetXY($x_line,$cline);
    $buss_name=iconv('UTF-8','windows-874',"0.00");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $x_line+=$plus_line;
}else{
    
    $pdf->SetXY($x_line,$cline-3);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY($x_line,$cline+1);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY($x_line,$cline+1.5);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY($x_line,$cline);
    $buss_name=iconv('UTF-8','windows-874',"0.00");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);

    $x_line+=$plus_line;

    $pdf->SetXY($x_line,$cline-3);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY($x_line,$cline+1);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY($x_line,$cline+1.5);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY($x_line,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$sum_cash");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);

    $x_line+=$plus_line;
    
    $pdf->SetXY($x_line,$cline-3);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY($x_line,$cline+1);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY($x_line,$cline+1.5);
    $buss_name=iconv('UTF-8','windows-874',"__________");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);

    $pdf->SetXY($x_line,$cline);
    $buss_name=iconv('UTF-8','windows-874',"0.00");
    $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
    
    $x_line+=$plus_line;
}



foreach($arr_data as $v){
    $abs_balance = abs($balance[$v]);
    $fm_bl = number_format($abs_balance,2);
    
    if($abs_balance == 0){
        for($j=1; $j<=3; $j++){
            if($j==1){
                
                $pdf->SetXY($x_line,$cline-3);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1.5);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline); 
                $buss_name=iconv('UTF-8','windows-874',"0.00");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
            }elseif($j==2){
                
                $pdf->SetXY($x_line,$cline-3);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1.5);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline); 
                $buss_name=iconv('UTF-8','windows-874',"0.00");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
            }elseif($j==3){
                
                $pdf->SetXY($x_line,$cline-3);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1.5);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline); 
                $buss_name=iconv('UTF-8','windows-874',"0.00");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
            }
            $x_line+=$plus_line;
        }
        continue;
    }

    if($balance[$v] < 0){
        for($j=1; $j<=3; $j++){
            if($j==1){
                
                $pdf->SetXY($x_line,$cline-3);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1.5);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline); 
                $buss_name=iconv('UTF-8','windows-874',"$fm_bl");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
            }elseif($j==2){
                
                $pdf->SetXY($x_line,$cline-3);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1.5);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline); 
                $buss_name=iconv('UTF-8','windows-874',"0.00");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
            }elseif($j==3){
                
                $pdf->SetXY($x_line,$cline-3);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1.5);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline); 
                $buss_name=iconv('UTF-8','windows-874',"0.00");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
            }
            $x_line+=$plus_line;
        }
    }else{
        for($j=1; $j<=3; $j++){
            if($j==1){
                
                $pdf->SetXY($x_line,$cline-3);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1.5);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline); 
                $buss_name=iconv('UTF-8','windows-874',"0.00");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
            }elseif($j==2){
                
                $pdf->SetXY($x_line,$cline-3);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1.5);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline); 
                $buss_name=iconv('UTF-8','windows-874',"$fm_bl");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
            }elseif($j==3){
                
                $pdf->SetXY($x_line,$cline-3);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline+1.5);
                $buss_name=iconv('UTF-8','windows-874',"__________");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
                
                $pdf->SetXY($x_line,$cline); 
                $buss_name=iconv('UTF-8','windows-874',"0.00");
                $pdf->MultiCell($plus_line,4,$buss_name,$border,'R',0);
            }
            $x_line+=$plus_line;
        }
    }

}

$pdf->Output();
?>
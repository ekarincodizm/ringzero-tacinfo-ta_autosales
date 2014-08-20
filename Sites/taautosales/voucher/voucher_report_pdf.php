<?php
include_once("../include/config.php");
include_once("../include/function.php");

$date = $_GET['date'];
$marker_id = $_GET['maker'];

//------------------- PDF -------------------//
require('../thaipdfclass.php');

class PDF extends ThaiPDF
{
    function Header(){
        $this->SetFont('AngsanaNew','',13);
        $this->SetXY(10,16); 
        $buss_name=iconv('UTF-8','windows-874',"หน้า ".$this->PageNo()."/tp");
        $this->MultiCell(190,4,$buss_name,0,'R',0);
    }
}

$pdf=new PDF('P' ,'mm','a4');
$pdf->AliasNbPages( 'tp' );
$pdf->SetThaiFont();
$pdf->AddPage();

$page = $pdf->PageNo();

$border = 0;

$pdf->SetFont('AngsanaNew','B',17);
$pdf->SetXY(10,10);
$title=iconv('UTF-8','windows-874',"รายงานสรุป Voucher");
$pdf->MultiCell(190,4,$title,$border,'C',0);

$pdf->SetFont('AngsanaNew','',15);
$pdf->SetXY(10,16);
$buss_name=iconv('UTF-8','windows-874',"บริษัท ".$company_name);
$pdf->MultiCell(190,4,$buss_name,$border,'C',0);

$pdf->SetXY(10,25);
$buss_name=iconv('UTF-8','windows-874',"ประจำวันที่ $date");
$pdf->MultiCell(190,4,$buss_name,$border,'L',0);

$pdf->SetXY(10,25);
$buss_name=iconv('UTF-8','windows-874',"วันที่พิมพ์ $nowdate");
$pdf->MultiCell(190,4,$buss_name,$border,'R',0);

$pdf->SetXY(10,26);
$buss_name=iconv('UTF-8','windows-874',"___________________________________________________________________________________________________________");
$pdf->MultiCell(190,4,$buss_name,$border,'C',0);

$pdf->SetXY(10,32);
$buss_name=iconv('UTF-8','windows-874',"JobID");
$pdf->MultiCell(11,4,$buss_name,$border,'C',0);

$pdf->SetXY(21,32);
$buss_name=iconv('UTF-8','windows-874',"รูปแบบ");
$pdf->MultiCell(13,4,$buss_name,$border,'C',0);

$pdf->SetXY(34,32);
$buss_name=iconv('UTF-8','windows-874',"รหัส");
$pdf->MultiCell(20,4,$buss_name,$border,'C',0);

$pdf->SetXY(54,32);
$buss_name=iconv('UTF-8','windows-874',"วันที่");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(69,32);
$buss_name=iconv('UTF-8','windows-874',"รายละเอียด");
$pdf->MultiCell(50,4,$buss_name,$border,'C',0);

$pdf->SetXY(119,32);
$buss_name=iconv('UTF-8','windows-874',"abh id");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(134,32);
$buss_name=iconv('UTF-8','windows-874',"ยอดเงินสด");
$pdf->MultiCell(18,4,$buss_name,$border,'C',0);

$pdf->SetXY(152,32);
$buss_name=iconv('UTF-8','windows-874',"รับ");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(167,32);
$buss_name=iconv('UTF-8','windows-874',"จ่าย");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(182,32);
$buss_name=iconv('UTF-8','windows-874',"ยอดเช็ค");
$pdf->MultiCell(17,4,$buss_name,$border,'C',0);

$pdf->SetXY(10,33);
$buss_name=iconv('UTF-8','windows-874',"___________________________________________________________________________________________________________");
$pdf->MultiCell(190,4,$buss_name,$border,'C',0);

$cline = 38;
$nub=0;

$j = 0;
$qry=pg_query("select * from account.\"VoucherDetails\" WHERE marker_id='$marker_id' AND \"receipt_id\" is not null AND \"acb_id\" is null AND \"recp_date\"='$date' ORDER BY \"job_id\",\"vc_id\" ASC");
while($res=pg_fetch_array($qry)){
    $j++;
    $vc_id = $res["vc_id"];
    $vc_detail = $res["vc_detail"];
    $do_date = $res["do_date"];
    $job_id = $res["job_id"];
    $cash_amt = $res["cash_amt"];
    $approve_id = $res["approve_id"];
    $chq_acc_no = $res["chq_acc_no"];
    $chque_no = $res["chque_no"];
    $autoid_abh = $res["acb_id"];


if($j > 1){
    
if($nub > 35){
    $pdf->AddPage();
    $cline = 38;
    $nub=0;
    
$pdf->SetFont('AngsanaNew','B',17);
$pdf->SetXY(10,10);
$title=iconv('UTF-8','windows-874',"รายงานสรุป Voucher");
$pdf->MultiCell(190,4,$title,$border,'C',0);

$pdf->SetFont('AngsanaNew','',15);
$pdf->SetXY(10,16);
$buss_name=iconv('UTF-8','windows-874',"บริษัท ".$company_name);
$pdf->MultiCell(190,4,$buss_name,$border,'C',0);

$pdf->SetXY(10,25);
$buss_name=iconv('UTF-8','windows-874',"ประจำวันที่ $date");
$pdf->MultiCell(190,4,$buss_name,$border,'L',0);

$pdf->SetXY(10,25);
$buss_name=iconv('UTF-8','windows-874',"วันที่พิมพ์ $nowdate");
$pdf->MultiCell(190,4,$buss_name,$border,'R',0);

$pdf->SetXY(10,26);
$buss_name=iconv('UTF-8','windows-874',"___________________________________________________________________________________________________________");
$pdf->MultiCell(190,4,$buss_name,$border,'C',0);

$pdf->SetXY(10,32);
$buss_name=iconv('UTF-8','windows-874',"JobID");
$pdf->MultiCell(11,4,$buss_name,$border,'C',0);

$pdf->SetXY(21,32);
$buss_name=iconv('UTF-8','windows-874',"รูปแบบ");
$pdf->MultiCell(13,4,$buss_name,$border,'C',0);

$pdf->SetXY(34,32);
$buss_name=iconv('UTF-8','windows-874',"รหัส");
$pdf->MultiCell(20,4,$buss_name,$border,'C',0);

$pdf->SetXY(54,32);
$buss_name=iconv('UTF-8','windows-874',"วันที่");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(69,32);
$buss_name=iconv('UTF-8','windows-874',"รายละเอียด");
$pdf->MultiCell(50,4,$buss_name,$border,'C',0);

$pdf->SetXY(119,32);
$buss_name=iconv('UTF-8','windows-874',"abh id");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(134,32);
$buss_name=iconv('UTF-8','windows-874',"ยอดเงินสด");
$pdf->MultiCell(18,4,$buss_name,$border,'C',0);

$pdf->SetXY(152,32);
$buss_name=iconv('UTF-8','windows-874',"รับ");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(167,32);
$buss_name=iconv('UTF-8','windows-874',"จ่าย");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(182,32);
$buss_name=iconv('UTF-8','windows-874',"ยอดเช็ค");
$pdf->MultiCell(17,4,$buss_name,$border,'C',0);

$pdf->SetXY(10,33);
$buss_name=iconv('UTF-8','windows-874',"___________________________________________________________________________________________________________");
$pdf->MultiCell(190,4,$buss_name,$border,'C',0);
    
}
    
if($old_job == $job_id){

    $pdf->SetFont('AngsanaNew','',13);
    $pdf->SetXY(10,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_jobid");
    $pdf->MultiCell(11,4,$buss_name,$border,'C',0);

    $pdf->SetXY(21,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_chq_acc_no");
    $pdf->MultiCell(13,4,$buss_name,$border,'C',0);
    
    $pdf->SetXY(34,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_vc_id");
    $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

    $pdf->SetXY(53,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_do_date");
    $pdf->MultiCell(17,4,$buss_name,$border,'C',0);
    
    $pdf->SetXY(69,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_vc_detail");
    $pdf->MultiCell(50,4,$buss_name,$border,'L',0);
    
    $pdf->SetXY(119,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_autoid_abh");
    $pdf->MultiCell(15,4,$buss_name,$border,'C',0);
    
    $pdf->SetXY(134,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($begin_cash_amt,2));
    $pdf->MultiCell(18,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY(182,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($begin_Amount,2));
    $pdf->MultiCell(17,4,$buss_name,$border,'R',0);
    
}else{
    $sum_sum = $sum_sub1_plus+$sum_sub1_lob;
    
    $pdf->SetFont('AngsanaNew','',13);
    $pdf->SetXY(10,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_jobid");
    $pdf->MultiCell(11,4,$buss_name,$border,'C',0);

    $pdf->SetXY(21,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_chq_acc_no");
    $pdf->MultiCell(13,4,$buss_name,$border,'C',0);
    
    $pdf->SetXY(34,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_vc_id");
    $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

    $pdf->SetXY(53,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_do_date");
    $pdf->MultiCell(17,4,$buss_name,$border,'C',0);
    
    $pdf->SetXY(69,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_vc_detail");
    $pdf->MultiCell(50,4,$buss_name,$border,'L',0);
    
    $pdf->SetXY(119,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_autoid_abh");
    $pdf->MultiCell(15,4,$buss_name,$border,'C',0);

    $pdf->SetXY(134,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($begin_cash_amt,2));
    $pdf->MultiCell(18,4,$buss_name,$border,'R',0);
    
if($sum_sum >= 0){
    $sum_j += $sum_sum;
    
    $pdf->SetXY(167,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($sum_sum,2));
    $pdf->MultiCell(16,4,$buss_name,$border,'R',0);

}else{
    $sum_r += $sum_sum;
    
    $pdf->SetXY(152,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($sum_sum,2));
    $pdf->MultiCell(16,4,$buss_name,$border,'R',0);
}
    
    $pdf->SetXY(182,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($begin_Amount,2));
    $pdf->MultiCell(17,4,$buss_name,$border,'R',0);
    
    
    $arr_dt5 = explode("\n",$begin_vc_detail);
    $c_dt5 = count($arr_dt5);
    
    $pdf->SetXY(10,$cline+(6*$c_dt5)-5);
    $buss_name=iconv('UTF-8','windows-874',"___________________________________________________________________________________________________________________________");
    $pdf->MultiCell(190,4,$buss_name,$border,'C',0);
    
    $all_sum_sum += $sum_sum;

    $sum_sum = 0;
    $sum_sub1_plus = 0;
    $sum_sub1_lob = 0;
    
}

$arr_dt = explode("\n",$begin_vc_detail);
$c_dt = count($arr_dt);
$cline+=(6*$c_dt);
$nub+=$c_dt;

}

if(empty($chq_acc_no)){
    $chq_acc_no_text = "เงินสด";
}else{
    $chq_acc_no_text = "เช็ค";
}

if( empty($autoid_abh) ){
    $autoid_abh_text = "-";
}else{
    $autoid_abh_text = "$autoid_abh";
}

$Amount = 0;

if(empty($chq_acc_no)){
    if($cash_amt >= 0){
        $sum_sub1_plus+=$cash_amt;
    }else{
        $sum_sub1_lob+=$cash_amt;
    }
    $sum_all1+=$cash_amt;
}else{
    $qry_chq=pg_query("select * from account.\"ChequeAccDetails\" WHERE \"ac_id\"='$chq_acc_no' AND \"chq_id\"='$chque_no'");
    if($res_chq=pg_fetch_array($qry_chq)){
        $Amount = $res_chq["Amount"];
    }
    $sum_sub2+=$Amount;
    $sum_all2+=$Amount;
}


$begin_jobid = $job_id;
$begin_chq_acc_no = $chq_acc_no_text;
$begin_vc_id = $vc_id;
$begin_do_date = $do_date;
$begin_vc_detail = $vc_detail;
$begin_autoid_abh = $autoid_abh_text;
$begin_cash_amt = $cash_amt;
$begin_Amount = $Amount;

$old_job = $job_id;
}

if($nub > 35){
    $pdf->AddPage();
    $cline = 38;
    $nub=0;
    
$pdf->SetFont('AngsanaNew','B',17);
$pdf->SetXY(10,10);
$title=iconv('UTF-8','windows-874',"รายงานสรุป Voucher");
$pdf->MultiCell(190,4,$title,$border,'C',0);

$pdf->SetFont('AngsanaNew','',15);
$pdf->SetXY(10,16);
$buss_name=iconv('UTF-8','windows-874',"บริษัท ".$company_name);
$pdf->MultiCell(190,4,$buss_name,$border,'C',0);

$pdf->SetXY(10,25);
$buss_name=iconv('UTF-8','windows-874',"ประจำวันที่ $date");
$pdf->MultiCell(190,4,$buss_name,$border,'L',0);

$pdf->SetXY(10,25);
$buss_name=iconv('UTF-8','windows-874',"วันที่พิมพ์ $nowdate");
$pdf->MultiCell(190,4,$buss_name,$border,'R',0);

$pdf->SetXY(10,26);
$buss_name=iconv('UTF-8','windows-874',"___________________________________________________________________________________________________________");
$pdf->MultiCell(190,4,$buss_name,$border,'C',0);

$pdf->SetXY(10,32);
$buss_name=iconv('UTF-8','windows-874',"JobID");
$pdf->MultiCell(11,4,$buss_name,$border,'C',0);

$pdf->SetXY(21,32);
$buss_name=iconv('UTF-8','windows-874',"รูปแบบ");
$pdf->MultiCell(13,4,$buss_name,$border,'C',0);

$pdf->SetXY(34,32);
$buss_name=iconv('UTF-8','windows-874',"รหัส");
$pdf->MultiCell(20,4,$buss_name,$border,'C',0);

$pdf->SetXY(54,32);
$buss_name=iconv('UTF-8','windows-874',"วันที่");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(69,32);
$buss_name=iconv('UTF-8','windows-874',"รายละเอียด");
$pdf->MultiCell(50,4,$buss_name,$border,'C',0);

$pdf->SetXY(119,32);
$buss_name=iconv('UTF-8','windows-874',"abh id");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(134,32);
$buss_name=iconv('UTF-8','windows-874',"ยอดเงินสด");
$pdf->MultiCell(18,4,$buss_name,$border,'C',0);

$pdf->SetXY(152,32);
$buss_name=iconv('UTF-8','windows-874',"รับ");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(167,32);
$buss_name=iconv('UTF-8','windows-874',"จ่าย");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(182,32);
$buss_name=iconv('UTF-8','windows-874',"ยอดเช็ค");
$pdf->MultiCell(17,4,$buss_name,$border,'C',0);

$pdf->SetXY(10,33);
$buss_name=iconv('UTF-8','windows-874',"___________________________________________________________________________________________________________");
$pdf->MultiCell(190,4,$buss_name,$border,'C',0);
    
}

//แสดงรา่ยการสุดท้าย
    $sum_sum = $sum_sub1_plus+$sum_sub1_lob;
    
    $pdf->SetFont('AngsanaNew','',13);
    $pdf->SetXY(10,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_jobid");
    $pdf->MultiCell(11,4,$buss_name,$border,'C',0);

    $pdf->SetXY(21,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_chq_acc_no");
    $pdf->MultiCell(13,4,$buss_name,$border,'C',0);
    
    $pdf->SetXY(34,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_vc_id");
    $pdf->MultiCell(20,4,$buss_name,$border,'C',0);

    $pdf->SetXY(53,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_do_date");
    $pdf->MultiCell(17,4,$buss_name,$border,'C',0);
    
    $pdf->SetXY(69,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_vc_detail");
    $pdf->MultiCell(50,4,$buss_name,$border,'L',0);
    
    $pdf->SetXY(119,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$begin_autoid_abh");
    $pdf->MultiCell(15,4,$buss_name,$border,'C',0);
    
    $pdf->SetXY(134,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($begin_cash_amt,2));
    $pdf->MultiCell(18,4,$buss_name,$border,'R',0);
        
    if($sum_sum >= 0){
        $sum_j += $sum_sum;
        
        $pdf->SetXY(167,$cline);
        $buss_name=iconv('UTF-8','windows-874',number_format($sum_sum,2));
        $pdf->MultiCell(16,4,$buss_name,$border,'R',0);

    }else{
        $sum_r += $sum_sum;
        
        $pdf->SetXY(152,$cline);
        $buss_name=iconv('UTF-8','windows-874',number_format($sum_sum,2));
        $pdf->MultiCell(16,4,$buss_name,$border,'R',0);
    }
        
    $pdf->SetXY(182,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($begin_Amount,2));
    $pdf->MultiCell(17,4,$buss_name,$border,'R',0);

    $arr_dt = explode("\n",$begin_vc_detail);
    $c_dt = count($arr_dt);
    $cline+=(6*$c_dt);
    $nub+=$c_dt;
    
    $pdf->SetXY(10,$cline+(6*$c_dt)-11);
    $buss_name=iconv('UTF-8','windows-874',"___________________________________________________________________________________________________________________________");
    $pdf->MultiCell(190,4,$buss_name,$border,'C',0);    

if($nub > 35){
    $pdf->AddPage();
    $cline = 38;
    $nub=0;
    
$pdf->SetFont('AngsanaNew','B',17);
$pdf->SetXY(10,10);
$title=iconv('UTF-8','windows-874',"รายงานสรุป Voucher");
$pdf->MultiCell(190,4,$title,$border,'C',0);

$pdf->SetFont('AngsanaNew','',15);
$pdf->SetXY(10,16);
$buss_name=iconv('UTF-8','windows-874',"บริษัท ".$company_name);
$pdf->MultiCell(190,4,$buss_name,$border,'C',0);

$pdf->SetXY(10,25);
$buss_name=iconv('UTF-8','windows-874',"ประจำวันที่ $date");
$pdf->MultiCell(190,4,$buss_name,$border,'L',0);

$pdf->SetXY(10,25);
$buss_name=iconv('UTF-8','windows-874',"วันที่พิมพ์ $nowdate");
$pdf->MultiCell(190,4,$buss_name,$border,'R',0);

$pdf->SetXY(10,26);
$buss_name=iconv('UTF-8','windows-874',"___________________________________________________________________________________________________________");
$pdf->MultiCell(190,4,$buss_name,$border,'C',0);

$pdf->SetXY(10,32);
$buss_name=iconv('UTF-8','windows-874',"JobID");
$pdf->MultiCell(11,4,$buss_name,$border,'C',0);

$pdf->SetXY(21,32);
$buss_name=iconv('UTF-8','windows-874',"รูปแบบ");
$pdf->MultiCell(13,4,$buss_name,$border,'C',0);

$pdf->SetXY(34,32);
$buss_name=iconv('UTF-8','windows-874',"รหัส");
$pdf->MultiCell(20,4,$buss_name,$border,'C',0);

$pdf->SetXY(54,32);
$buss_name=iconv('UTF-8','windows-874',"วันที่");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(69,32);
$buss_name=iconv('UTF-8','windows-874',"รายละเอียด");
$pdf->MultiCell(50,4,$buss_name,$border,'C',0);

$pdf->SetXY(119,32);
$buss_name=iconv('UTF-8','windows-874',"abh id");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(134,32);
$buss_name=iconv('UTF-8','windows-874',"ยอดเงินสด");
$pdf->MultiCell(18,4,$buss_name,$border,'C',0);

$pdf->SetXY(152,32);
$buss_name=iconv('UTF-8','windows-874',"รับ");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(167,32);
$buss_name=iconv('UTF-8','windows-874',"จ่าย");
$pdf->MultiCell(15,4,$buss_name,$border,'C',0);

$pdf->SetXY(182,32);
$buss_name=iconv('UTF-8','windows-874',"ยอดเช็ค");
$pdf->MultiCell(17,4,$buss_name,$border,'C',0);

$pdf->SetXY(10,33);
$buss_name=iconv('UTF-8','windows-874',"___________________________________________________________________________________________________________");
$pdf->MultiCell(190,4,$buss_name,$border,'C',0);
    
}

    //แสดงผลรวมทั้งหมด
    $pdf->SetFont('AngsanaNew','B',13);
    
    $pdf->SetXY(10,$cline);
    $buss_name=iconv('UTF-8','windows-874',"รวมยอดเงินทั้งสิ้น ".number_format($sum_all1+$sum_all2,2));
    $pdf->MultiCell(70,4,$buss_name,$border,'L',0);
    
    $pdf->SetXY(64,$cline);
    $buss_name=iconv('UTF-8','windows-874',"ผลรวม");
    $pdf->MultiCell(67,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY(131,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($sum_all1,2));
    $pdf->MultiCell(20,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY(151,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($sum_r,2));
    $pdf->MultiCell(16,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY(166,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($sum_j,2));
    $pdf->MultiCell(16,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY(182,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($sum_all2,2));
    $pdf->MultiCell(17,4,$buss_name,$border,'R',0);
    
//
    
    $pdf->SetXY(131,$cline);
    $buss_name=iconv('UTF-8','windows-874',"___________");
    $pdf->MultiCell(20,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY(131,$cline+1);
    $buss_name=iconv('UTF-8','windows-874',"___________");
    $pdf->MultiCell(20,4,$buss_name,$border,'R',0);
        
    $pdf->SetXY(151,$cline);
    $buss_name=iconv('UTF-8','windows-874',"_________");
    $pdf->MultiCell(16,4,$buss_name,$border,'R',0);

    $pdf->SetXY(151,$cline+1);
    $buss_name=iconv('UTF-8','windows-874',"_________");
    $pdf->MultiCell(16,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY(166,$cline);
    $buss_name=iconv('UTF-8','windows-874',"_________");
    $pdf->MultiCell(16,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY(166,$cline+1);
    $buss_name=iconv('UTF-8','windows-874',"_________");
    $pdf->MultiCell(16,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY(182,$cline);
    $buss_name=iconv('UTF-8','windows-874',"_________");
    $pdf->MultiCell(17,4,$buss_name,$border,'R',0);
    
    $pdf->SetXY(182,$cline+1);
    $buss_name=iconv('UTF-8','windows-874',"_________");
    $pdf->MultiCell(17,4,$buss_name,$border,'R',0);

$pdf->Output();
?>
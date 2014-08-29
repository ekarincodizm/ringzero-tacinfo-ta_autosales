<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$mm = $_GET['mm'];
$yy = $_GET['yy'];
$nowdate = date('Y-m-d');

//------------------- PDF -------------------//
require('../thaipdfclass.php');

class PDF extends ThaiPDF
{
    function Header(){
        $this->SetFont('AngsanaNew','',12);
        $this->SetXY(5,16); 
        $buss_name=iconv('UTF-8','windows-874',"หน้า ".$this->PageNo()."/tp");
        $this->MultiCell(290,4,$buss_name,0,'R',0);
    }
}

$pdf=new PDF('P' ,'mm','a4');
$pdf->SetLeftMargin(0);
$pdf->SetTopMargin(0);
$pdf->AliasNbPages( 'tp' );
$pdf->SetThaiFont();
$pdf->AddPage();

$page = $pdf->PageNo();

$pdf->SetFont('AngsanaNew','B',18);
$pdf->SetXY(5,10);
$title=iconv('UTF-8','windows-874',$company_name);
$pdf->MultiCell(200,4,$title,0,'C',0);

$pdf->SetFont('AngsanaNew','',15);
$pdf->SetXY(5,16);
$buss_name=iconv('UTF-8','windows-874',"รายงานภาษีซื้อ");
$pdf->MultiCell(200,4,$buss_name,0,'C',0);

$pdf->SetXY(5,25);
$buss_name=iconv('UTF-8','windows-874',"ประจำเดือน $mm ปี $yy");
$pdf->MultiCell(50,4,$buss_name,0,'L',0);

$pdf->SetFont('AngsanaNew','',12);
$pdf->SetXY(5,25);
$buss_name=iconv('UTF-8','windows-874',"วันที่พิมพ์ $nowdate");
$pdf->MultiCell(200,4,$buss_name,0,'R',0);

$pdf->SetFont('AngsanaNew','',15);
$pdf->SetXY(5,26);
$buss_name=iconv('UTF-8','windows-874',"_________________________________________________________________________________________________________________");
$pdf->MultiCell(200,4,$buss_name,0,'C',0);

$pdf->SetXY(5,32);
$buss_name=iconv('UTF-8','windows-874',"วันที่");
$pdf->MultiCell(20,4,$buss_name,0,'C',0);

$pdf->SetXY(25,32);
$buss_name=iconv('UTF-8','windows-874',"เลขที่ใบสำคัญ");
$pdf->MultiCell(40,4,$buss_name,0,'C',0);

$pdf->SetXY(65,32);
$buss_name=iconv('UTF-8','windows-874',"ซื้อจาก");
$pdf->MultiCell(50,4,$buss_name,0,'C',0);

$pdf->SetXY(115,32);
$buss_name=iconv('UTF-8','windows-874',"มูลค่า");
$pdf->MultiCell(30,4,$buss_name,0,'C',0);

$pdf->SetXY(145,32);
$buss_name=iconv('UTF-8','windows-874',"VAT");
$pdf->MultiCell(30,4,$buss_name,0,'C',0);

$pdf->SetXY(175,32);
$buss_name=iconv('UTF-8','windows-874',"ยอดรวม");
$pdf->MultiCell(30,4,$buss_name,0,'C',0);

$pdf->SetXY(5,33);
$buss_name=iconv('UTF-8','windows-874',"_________________________________________________________________________________________________________________");
$pdf->MultiCell(200,4,$buss_name,0,'C',0);

//=========================//

$pdf->SetFont('AngsanaNew','',13);
$cline = 39;

$nub = 0;
$query=pg_query("SELECT \"acb_id\",\"acb_date\",\"acb_detail\" FROM account.\"AccountBookHead\" 
WHERE (EXTRACT(MONTH FROM \"acb_date\")='$mm') AND (EXTRACT(YEAR FROM \"acb_date\")='$yy') AND \"ref_id\"='VATB' AND \"cancel\"='FALSE' ORDER BY acb_date,\"acb_id\" ASC ");
while($resvc=pg_fetch_array($query)){
    $nub2++;
    $nub++;
    $acb_id= $resvc['acb_id'];
    $acb_date = $resvc['acb_date'];
    $acb_detail = $resvc['acb_detail'];
        $arr_detail = explode("\n",$acb_detail);
        
    $buy_from = "";
    $buy_receiptno = "";
    $query_bookbuy=pg_query("SELECT buy_from,buy_receiptno FROM account.\"BookBuy\" WHERE bh_id='$acb_id' ");
    if($resvc_bookbuy=pg_fetch_array($query_bookbuy)){
        $buy_from = $resvc_bookbuy['buy_from'];
        $buy_receiptno = $resvc_bookbuy['buy_receiptno'];
    }
        
    $sum_amtdr = 0;
    $sum_amtcr = 0;
    $amt_vat = 0;
    $query_detail=pg_query("SELECT \"AcID\",\"AmtDr\",\"AmtCr\" FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' ");
    while($resvc_detail=pg_fetch_array($query_detail)){
        $AcID = "";
        $AcID = $resvc_detail['AcID'];
        $AmtDr = round($resvc_detail['AmtDr'],2);
        $AmtCr = round($resvc_detail['AmtCr'],2);

        $sum_amtdr += $AmtDr;
        $sum_amtcr += $AmtCr;

        if($AcID == '1999'){
            if($AmtDr == 0 AND $AmtCr != 0){
                $type = 1;
                $amt_vat += $AmtCr;
            }else{
                $type = 2;
                $amt_vat += $AmtDr;
            }
        }
    }

    if($type == 1){
        $txt_show1 = ($sum_amtcr-$amt_vat)*-1;
        $txt_show2 = $amt_vat*-1;
        $txt_show3 = $sum_amtdr*-1;
    }elseif($type == 2){
        $txt_show1 = ($sum_amtdr-$amt_vat);
        $txt_show2 = $amt_vat;
        $txt_show3 = $sum_amtcr;
    }


    
    if($nub >= 45){
        $nub = 1;
        $cline = 39;
        $pdf->AddPage();
        
        $pdf->SetFont('AngsanaNew','B',18);
        $pdf->SetXY(5,10);
        $title=iconv('UTF-8','windows-874',$company_name);
        $pdf->MultiCell(200,4,$title,0,'C',0);

        $pdf->SetFont('AngsanaNew','',15);
        $pdf->SetXY(5,16);
        $buss_name=iconv('UTF-8','windows-874',"รายงานภาษีซื้อ");
        $pdf->MultiCell(200,4,$buss_name,0,'C',0);

        $pdf->SetXY(5,25);
        $buss_name=iconv('UTF-8','windows-874',"ประจำเดือน $mm ปี $yy");
        $pdf->MultiCell(50,4,$buss_name,0,'L',0);

        $pdf->SetFont('AngsanaNew','',12);
        $pdf->SetXY(5,25);
        $buss_name=iconv('UTF-8','windows-874',"วันที่พิมพ์ $nowdate");
        $pdf->MultiCell(200,4,$buss_name,0,'R',0);

        $pdf->SetFont('AngsanaNew','',15);
        $pdf->SetXY(5,26);
        $buss_name=iconv('UTF-8','windows-874',"_________________________________________________________________________________________________________________");
        $pdf->MultiCell(200,4,$buss_name,0,'C',0);

        $pdf->SetXY(5,32);
        $buss_name=iconv('UTF-8','windows-874',"วันที่");
        $pdf->MultiCell(20,4,$buss_name,0,'C',0);

        $pdf->SetXY(25,32);
        $buss_name=iconv('UTF-8','windows-874',"เลขที่ใบสำคัญ");
        $pdf->MultiCell(40,4,$buss_name,0,'C',0);

        $pdf->SetXY(65,32);
        $buss_name=iconv('UTF-8','windows-874',"ซื้อจาก");
        $pdf->MultiCell(50,4,$buss_name,0,'C',0);

        $pdf->SetXY(115,32);
        $buss_name=iconv('UTF-8','windows-874',"มูลค่า");
        $pdf->MultiCell(30,4,$buss_name,0,'C',0);

        $pdf->SetXY(145,32);
        $buss_name=iconv('UTF-8','windows-874',"VAT");
        $pdf->MultiCell(30,4,$buss_name,0,'C',0);

        $pdf->SetXY(175,32);
        $buss_name=iconv('UTF-8','windows-874',"ยอดรวม");
        $pdf->MultiCell(30,4,$buss_name,0,'C',0);

        $pdf->SetXY(5,33);
        $buss_name=iconv('UTF-8','windows-874',"_________________________________________________________________________________________________________________");
        $pdf->MultiCell(200,4,$buss_name,0,'C',0);

    }

    $w1 = $pdf->GetStringWidth($arr_detail[0]);
    $w2 = $pdf->GetStringWidth($arr_detail[1]);
    
    $pdf->SetFont('AngsanaNew','',13);
    
    $pdf->SetXY(5,$cline);
    $buss_name=iconv('UTF-8','windows-874',"$acb_date");
    $pdf->MultiCell(20,4,$buss_name,0,'C',0);
    
    
    $pdf->SetFont('AngsanaNew','',13);
    
    $pdf->SetXY(25,$cline);
    $buss_name=iconv('UTF-8','windows-874',$buy_receiptno);
    $pdf->MultiCell(50,4,$buss_name,0,'L',0);

    $pdf->SetXY(75,$cline);
    $buss_name=iconv('UTF-8','windows-874',$buy_from);
    $pdf->MultiCell(50,4,$buss_name,0,'L',0);

    $pdf->SetFont('AngsanaNew','',13);
    $pdf->SetXY(125,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($txt_show1,2));
    $pdf->MultiCell(25,4,$buss_name,0,'R',0);

    $pdf->SetXY(150,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($txt_show2,2));
    $pdf->MultiCell(25,4,$buss_name,0,'R',0);

    $pdf->SetXY(175,$cline);
    $buss_name=iconv('UTF-8','windows-874',number_format($txt_show3,2));
    $pdf->MultiCell(30,4,$buss_name,0,'R',0);

    $sum_1+=$txt_show1;
    $sum_2+=$txt_show2;
    $sum_3+=$txt_show3;
    
    /*if($w1 > 240 OR $w2 > 240){
        $cline+=15;
        $nub+=2;
    }elseif($w1 > 120 OR $w2 > 120){
        $cline+=10;
        $nub+=1;
    }else{
        $cline+=5;
    }*/
    
    $cline+=5;
    $nub+=1;
}


    if($nub >= 45){
        $nub = 1;
        $cline = 39;
        $pdf->AddPage();
        
        $pdf->SetFont('AngsanaNew','B',18);
        $pdf->SetXY(5,10);
        $title=iconv('UTF-8','windows-874',$company_name);
        $pdf->MultiCell(200,4,$title,0,'C',0);

        $pdf->SetFont('AngsanaNew','',15);
        $pdf->SetXY(5,16);
        $buss_name=iconv('UTF-8','windows-874',"รายงานภาษีซื้อ");
        $pdf->MultiCell(200,4,$buss_name,0,'C',0);

        $pdf->SetXY(5,25);
        $buss_name=iconv('UTF-8','windows-874',"ประจำเดือน $mm ปี $yy");
        $pdf->MultiCell(50,4,$buss_name,0,'L',0);

        $pdf->SetFont('AngsanaNew','',12);
        $pdf->SetXY(5,25);
        $buss_name=iconv('UTF-8','windows-874',"วันที่พิมพ์ $nowdate");
        $pdf->MultiCell(200,4,$buss_name,0,'R',0);

        $pdf->SetFont('AngsanaNew','',15);
        $pdf->SetXY(5,26);
        $buss_name=iconv('UTF-8','windows-874',"_________________________________________________________________________________________________________________");
        $pdf->MultiCell(200,4,$buss_name,0,'C',0);

        $pdf->SetXY(5,32);
        $buss_name=iconv('UTF-8','windows-874',"วันที่");
        $pdf->MultiCell(20,4,$buss_name,0,'C',0);

        $pdf->SetXY(25,32);
        $buss_name=iconv('UTF-8','windows-874',"เลขที่ใบสำคัญ");
        $pdf->MultiCell(40,4,$buss_name,0,'C',0);

        $pdf->SetXY(65,32);
        $buss_name=iconv('UTF-8','windows-874',"ซื้อจาก");
        $pdf->MultiCell(50,4,$buss_name,0,'C',0);

        $pdf->SetXY(115,32);
        $buss_name=iconv('UTF-8','windows-874',"มูลค่า");
        $pdf->MultiCell(30,4,$buss_name,0,'C',0);

        $pdf->SetXY(145,32);
        $buss_name=iconv('UTF-8','windows-874',"VAT");
        $pdf->MultiCell(30,4,$buss_name,0,'C',0);

        $pdf->SetXY(175,32);
        $buss_name=iconv('UTF-8','windows-874',"ยอดรวม");
        $pdf->MultiCell(30,4,$buss_name,0,'C',0);

        $pdf->SetXY(5,33);
        $buss_name=iconv('UTF-8','windows-874',"_________________________________________________________________________________________________________________");
        $pdf->MultiCell(200,4,$buss_name,0,'C',0);

    }

$pdf->SetFont('AngsanaNew','',15);
$pdf->SetXY(5,$cline-3);
$buss_name=iconv('UTF-8','windows-874',"_________________________________________________________________________________________________________________");
$pdf->MultiCell(200,4,$buss_name,0,'C',0);

$pdf->SetXY(5,$cline+3);
$buss_name=iconv('UTF-8','windows-874',"จำนวน $nub2 รายการ");
$pdf->MultiCell(200,4,$buss_name,0,'L',0);

$pdf->SetXY(55,$cline+3);
$buss_name=iconv('UTF-8','windows-874',"รวมทั้งสิ้น");
$pdf->MultiCell(70,4,$buss_name,0,'R',0);

$pdf->SetXY(125,$cline+3);
$buss_name=iconv('UTF-8','windows-874',number_format($sum_1,2));
$pdf->MultiCell(25,4,$buss_name,0,'R',0);

$pdf->SetXY(150,$cline+3);
$buss_name=iconv('UTF-8','windows-874',number_format($sum_2,2));
$pdf->MultiCell(25,4,$buss_name,0,'R',0);

$pdf->SetXY(175,$cline+3);
$buss_name=iconv('UTF-8','windows-874',number_format($sum_3,2));
$pdf->MultiCell(30,4,$buss_name,0,'R',0);

$pdf->Output();
?>
<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$mm = $_GET['mm'];
$yy = $_GET['yy'];

if( empty($mm) OR empty($yy) ){
    echo "invalid param.";
    exit;
}

$data_month = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฏาคม', '08'=>'สิงหาคม' ,'09'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');

$save_data = '<table cellpadding="3" cellspacing="0" border="0" width="100%">';
/*
$save_data .= '
<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center">
    <td width="60">วันที่</td>
    <td width="75">เลขที่ใบกำกับ</td>
    <td width="65">เลขที่สัญญา</td>
    <td width="180">ชื่อ-สกุล</td>
    <td width="100">เลขถังรถ</td>
    <td width="85">เลขเครื่อง</td>
    <td width="70">ราคามูลค่า</td>
    <td width="70">vat</td>
    <td width="80">ยอดรวม</td>
</tr>
';
*/
$j = 0;
$qry = pg_query("SELECT * FROM \"VVat\" WHERE EXTRACT(MONTH FROM \"v_date\")='$mm' AND EXTRACT(YEAR FROM \"v_date\")='$yy' ORDER BY v_receipt ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $v_date = $res['v_date'];
    $v_receipt = $res['v_receipt'];
    $IDNO = $res['IDNO'];
    $full_name = $res['pre_name']." ".$res['cus_name']." ".$res['surname'];

    $car_num = $res['car_num'];
    $mar_num = $res['mar_num'];
    $color = $res['color'];
    $amount = round($res['amount'],2);
    $vat = round($res['vat'],2);

    $sum = $amount+$vat;
    
    $all_amount += $amount;
    $all_vat += $vat;
    $all_sum += $sum;
    
$save_data .= '
<tr>
    <td width="60" align="center">'.$v_date.'</td>
    <td width="75">'.$v_receipt.'</td>
    <td width="65">'.$IDNO.'</td>
    <td width="180">'.$full_name.'</td>
    <td width="100">'.$car_num.'</td>
    <td width="85">'.$mar_num.'</td>
    <td width="70" align="right">'.number_format($amount,2).'</td>
    <td width="70" align="right">'.number_format($vat,2).'</td>
    <td width="80" align="right">'.number_format($sum,2).'</td>
</tr>';

}

if($j == 0){
    $save_data .= "<tr><td colspan=\"10\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{
    $save_data .= "<hr><tr>
    <td colspan=\"6\" align=\"right\"><b>รวม</b></td>
    <td align=\"right\"><b>".number_format($all_amount,2)."</b></td>
    <td align=\"right\"><b>".number_format($all_vat,2)."</b></td>
    <td align=\"right\"><b>".number_format($all_sum,2)."</b></td>
</tr>";
}

$save_data .= '</table>';


//START PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){
        global $data_month;
        global $mm;
        global $yy;
        $this->SetFont('AngsanaUPC', 'B', 16);// Set font
        //$this->MultiCell(150, 0, 'รายงานภาษีขาย เดือน '.$data_month[$mm].' ปี '.$yy.'', 0, 'L', 0, 0, '', '', true);
        
        $head_data = '<span>รายงานภาษีขาย เดือน '.$data_month[$mm].' ปี '.$yy.'</span><br>
        <table cellpadding="3" cellspacing="0" border="0" width="100%">
        <tr style="font-weight:bold; text-align:center">
            <td width="60">วันที่</td>
            <td width="75">เลขที่ใบกำกับ</td>
            <td width="65">เลขที่สัญญา</td>
            <td width="180">ชื่อ-สกุล</td>
            <td width="100">เลขถังรถ</td>
            <td width="85">เลขเครื่อง</td>
            <td width="70">ราคามูลค่า</td>
            <td width="70">vat</td>
            <td width="80">ยอดรวม</td>
        </tr>
        </table>
        ';
        
        $this->writeHTML($head_data, true, false, true, false, '');
        
        $this->Line(10, 12, 286, 12);
        $this->Line(10, 20, 286, 20);
        
    }

    public function Footer(){
        $this->SetFont('AngsanaUPC', '', 14);// Set font
        $this->Line(10, 286, 200, 286);
        $this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(250, 5, 'หน้า '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
//$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(true);

//set margins
$pdf->SetMargins(10, 21, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

$pdf->AddPage('L');

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('tax_sale.pdf', 'I');
?>
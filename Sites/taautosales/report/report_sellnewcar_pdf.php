<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$mm = $_REQUEST['mm'];
$yy = $_REQUEST['yy'];

if(empty($mm) OR $mm == "" OR empty($yy) OR $yy == ""){
    echo "invalid param.";
    exit;
}

$month = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฏาคม', '08'=>'สิงหาคม' ,'09'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');

$save_data = '<div style="font-weight:bold; font-size:large">รายงานขายรถใหม่ประจำเดือน ('.$month[$mm].' '.$yy.')</div>';
$save_data .= '
<table cellpadding="1" cellspacing="0" border="1" width="820" bgcolor="#F0F0F0">
<tr bgcolor="#C0C0C0" style="font-weight:bold; text-align:center">
    <td width="30">ลำดับ</td>
    <td width="100">ผู้ซื้อ</td>
    <td width="100">ผู้ขาย</td>
    <td width="40">สต๊อก</td>
    <td width="60">เลขที่สัญญา</td>
    <td width="50">สี</td>
    <td width="90">ชนิด</td>
    <td width="100">ศูนย์</td>
    <td width="90">เลขถัง</td>
    <td width="70">เลขเครื่อง</td>
    <td width="60">ชำระเงิน</td>
</tr>';

$count_blue=0;
$count_yellow=0; 
$count_greenyellow=0;
$count_other=0; 

$nub = 0;
$query=pg_query("SELECT * FROM \"VSaleNewCar\" 
WHERE (EXTRACT(MONTH FROM receive_date)='$mm') AND (EXTRACT(YEAR FROM receive_date)='$yy') ORDER BY car_id ASC ");
while($resvc=pg_fetch_array($query)){
    $nub++;
    $car_id = $resvc['car_id'];
    $car_num = $resvc['car_num'];
    $mar_num = $resvc['mar_num'];
    $color = $resvc['color'];
    $license_plate = $resvc['license_plate'];
    $product_id = $resvc['product_id'];
    $po_id = $resvc['po_id'];
    $vender_id = $resvc['vender_id'];
    $receive_date = $resvc['receive_date'];
    $IDNO = $resvc['IDNO'];
    $cus_id = $resvc['cus_id'];
    $user_id = $resvc['user_id'];
            
    $car_name = "";
    $query_bookbuy=pg_query("SELECT * FROM \"Cars\" WHERE car_id='$car_id' ");
    if($resvc_bookbuy=pg_fetch_array($query_bookbuy)){
        $car_name = $resvc_bookbuy['car_name'];
    }
    
    $buy_from = "";
    $query_bookbuy=pg_query("SELECT * FROM account.\"BookBuy\" WHERE to_hp_id='$car_id' ");
    if($resvc_bookbuy=pg_fetch_array($query_bookbuy)){
        $buy_from = $resvc_bookbuy['buy_from'];
    }
    
    if($color == "ฟ้า"){
       $count_blue++; 
    }elseif($color == "เหลือง"){
        $count_yellow++;
    }elseif($color == "เขียวเหลือง"){
        $count_greenyellow++; 
    }else{
        $count_other++; 
    }

$save_data .= '
<tr bgcolor="#FFFFFF">
    <td align="center">'.$nub.'</td>
    <td>'.GetCusName($cus_id).'</td>
    <td>'.GetUserName($user_id).'</td>
    <td>'.$license_plate.'</td>
    <td>'.$IDNO.'</td>
    <td align="center">'.$color.'</td>
    <td style="font-size:small">'.$car_name.'</td>
    <td>'.$buy_from.'</td>
    <td>'.$car_num.'</td>
    <td>'.$mar_num.'</td>
    <td align="center">'.$receive_date.'</td>
</tr>';

}

if($nub > 0){
    $save_data .= "<tr style=\"font-weight:bold\" bgcolor=\"#C0C0C0\"><td colspan=\"11\">สรุปจำนวน : สีฟ้า $count_blue รายการ | สีเหลือง $count_yellow รายการ | สีเขียวเหลือง $count_greenyellow รายการ | สีอื่นๆ $count_other รายการ</td></tr>";
}

$save_data .= '</table>';

//START PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){

    }

    public function Footer(){
        $this->SetFont('AngsanaUPC', '', 14);// Set font
        $this->Line(10, 200, 290, 200);
        $this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(250, 0, 'หน้า '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(true);

//set margins
$pdf->SetMargins(10, 10, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

$pdf->AddPage('L');

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('sellnewcar_'.$mm.'_'.$yy.'.pdf', 'I');
?>
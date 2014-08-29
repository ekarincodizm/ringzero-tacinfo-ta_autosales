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

$save_data = '<div style="font-weight:bold; font-size:large">รายงานซื้อรถใหม่ประจำเดือน ('.$month[$mm].' '.$yy.')</div>';
$save_data .= '
<table cellpadding="1" cellspacing="0" border="1" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#C0C0C0" style="font-weight:bold; text-align:center">
    <td width="40">ลำดับ</td>
    <td width="50">สต๊อก</td>
    <td width="60">วัน</td>
    <td width="120">ศูนย์</td>
    <td width="100">ชนิด</td>
    <td width="100">เลขถัง</td>
    <td width="70">เลขเครื่อง</td>
</tr>';

$nub = 0;
$query=pg_query("SELECT * FROM account.\"AccountBookHead\" 
WHERE (EXTRACT(MONTH FROM \"acb_date\")='$mm') AND (EXTRACT(YEAR FROM \"acb_date\")='$yy') AND ref_id = 'VATB' AND \"type_acb\"='AP' AND \"cancel\"='FALSE' ORDER BY acb_date,acb_id ASC ");
while($resvc=pg_fetch_array($query)){
    $nub++;
    $acb_id = trim($resvc['acb_id']);
    $acb_date = $resvc['acb_date'];
    $acb_detail = $resvc['acb_detail'];
    
    $buy_from = "";
    
    $car_num = "";
    $mar_num = "";
    $car_name = "";
            
    $query_bookbuy=pg_query("SELECT * FROM account.\"BookBuy\" WHERE bh_id='$acb_id' ");
    if($resvc_bookbuy=pg_fetch_array($query_bookbuy)){
        $buy_from = $resvc_bookbuy['buy_from'];
    }/*else{
        $buy_from = "not found - buy_from";
    }*/
    
    $mr = "";
    if(strpos($acb_detail, 'M') === false){
        $mr = "";
    }else{
        $pos = strpos($acb_detail, 'M');
        $mr = trim(substr($acb_detail, $pos, 17));
        
        $query_cars=pg_query("SELECT * FROM \"Cars\" WHERE car_num='$mr' ");
        if($resvc_cars=pg_fetch_array($query_cars)){
            $car_num = $resvc_cars['car_num'];
            $mar_num = $resvc_cars['mar_num'];
            $car_name = $resvc_cars['car_name'];
            $license_plate = $resvc_cars['license_plate'];
        }/*else{
            $car_num = "not found - car_num";
            $mar_num = "not found - mar_num";
            $car_name = "not found - car_name";
        }*/
    }

$save_data .= '
<tr bgcolor="#FFFFFF">
    <td align="center">'.$nub.'</td>
    <td align="center">'.$license_plate.'</td>
    <td align="center">'.$acb_date.'</td>
    <td style="font-size:small">'.$buy_from.'</td>
    <td>'.$car_name.'</td>
    <td>'.$car_num.'</td>
    <td>'.$mar_num.'</td>
</tr>';

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
        $this->Line(10, 286, 202, 286);
        $this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(165, 5, 'หน้า '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
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

$pdf->AddPage();

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('sellnewcar_'.$mm.'_'.$yy.'.pdf', 'I');
?>
<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$wh = pg_escape_string($_GET['wh']);

if(empty($wh) OR $wh == ""){
    echo "invalid param.";
    exit;
}

$save_data = "<span style=\"font-weight:bold; font-size:large\">รายงาน Stock รถในบริษัท (แสดงรายละเอียด)</span>";

if($wh == "all"){

$qry_wh = pg_query("SELECT * FROM \"Warehouses\" WHERE cancel='FALSE' ORDER BY wh_name ASC ");
while($res_wh = pg_fetch_array($qry_wh)){
    $wh_id = $res_wh['wh_id'];
    $wh_name = $res_wh['wh_name'];

$qry_num = 0;
$qry = pg_query("SELECT c.car_idno,m.car_id,c.license_plate,c.product_id,c.car_num,m.date_in,
(select v1.\"color\" from \"VStockCars\" v1 where v1.car_id = m.car_id limit 1) as color
FROM \"CarMove\" m LEFT JOIN \"Cars\" c ON m.car_id=c.car_id AND c.cancel='FALSE' 
WHERE m.date_out IS NULL AND m.wh_id='$wh_id' AND c.cancel='FALSE' 
ORDER BY m.car_id ASC ");
$qry_num = pg_num_rows($qry);

$count_all += $qry_num;

$save_data .= '
<div style="font-weight:bold">สถานที่ : '.$wh_name.' ทั้งหมด '.$qry_num.' รายการ</div>

<table cellpadding="2" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center" bgcolor="#F0F0F0">
    <td>ทะเบียนรถในสต๊อก</td>
    <td>ทะเบียน</td>
    <td>สีรถ</td>
    <td>Product</td>
    <td>เลขถัง</td>
    <td>Date In</td>
</tr>';

$j = 0;
while($res = pg_fetch_array($qry)){
    $j++;
    $car_id = $res['car_id'];
	$car_idno = $res['car_idno'];
    $license_plate = $res['license_plate'];
    $color = $res['color'];
    $product_id = $res['product_id'];
    $car_num = $res['car_num'];
    $date_in = $res['date_in'];
    
    $product_name = GetProductName($product_id);

$save_data .= '
<tr>
    <td>'.$car_idno.'</td>
    <td>'.$license_plate.'</td>
    <td>'.getCarColor($color).'</td>
    <td>'.$product_name.'</td>
    <td>'.$car_num.'</td>
    <td>'.$date_in.'</td>
</tr>';
}

if($j == 0){
    $save_data .= "<tr><td colspan=\"6\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}

$save_data .= '</table>';
}

}else{
    
$qry = pg_query("SELECT c.car_idno,m.car_id,c.license_plate,c.product_id,c.car_num,m.date_in ,
(select v1.\"color\" from \"VStockCars\" v1 where v1.car_id = m.car_id limit 1) as color
FROM \"CarMove\" m LEFT JOIN \"Cars\" c ON m.car_id=c.car_id 
WHERE m.date_out IS NULL AND wh_id='$wh' AND c.cancel='FALSE' 
ORDER BY m.car_id ASC ");
$qry_num = pg_num_rows($qry);

$warehouse_name = GetWarehousesName($wh);

$save_data .= '
<div style="font-weight:bold">สถานที่ : '.$warehouse_name.' ทั้งหมด '.$qry_num.' รายการ</div>

<table cellpadding="2" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center" bgcolor="#F0F0F0">
    <td>ทะเบียนรถในสต๊อก</td>
    <td>ทะเบียน</td>
    <td>สีรถ</td>
    <td>Product</td>
    <td>เลขถัง</td>
    <td>Date In</td>
</tr>';

$j = 0;
while($res = pg_fetch_array($qry)){
    $j++;
    $car_id = $res['car_id'];
	$car_idno = $res['car_idno'];
    $license_plate = $res['license_plate'];
    $color = $res['color'];
    $product_id = $res['product_id'];
    $car_num = $res['car_num'];
    $date_in = $res['date_in'];
    
    $product_name = GetProductName($product_id);
    
$save_data .= '    
<tr>
    <td>'.$car_idno.'</td>
    <td>'.$license_plate.'</td>
     <td>'.getCarColor($color).'</td>
    <td>'.$product_name.'</td>
    <td>'.$car_num.'</td>
    <td>'.$date_in.'</td>
</tr>';

}

if($j == 0){
    $save_data .= "<tr><td colspan=\"6\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}

$save_data .= '</table>';
    
}

//START PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){

    }

    public function Footer(){
        $this->SetFont('AngsanaUPC', '', 14);// Set font
        $this->Line(10, 199, 285, 199);
        $this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d H:i:s'), 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(247, 5, 'หน้า '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
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
$pdf->SetAutoPageBreak(TRUE, 15);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

$pdf->AddPage('L');

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('report_warehouse.pdf', 'I');
?>
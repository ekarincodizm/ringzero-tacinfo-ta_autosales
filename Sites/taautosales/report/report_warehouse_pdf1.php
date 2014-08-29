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

$save_data = "<span style=\"font-weight:bold; font-size:large\">รายงาน Stock รถในบริษัท (แบบสรุป)</span>";

if($wh == "all"){

$qry_wh = pg_query("SELECT * FROM \"Warehouses\" WHERE cancel='FALSE' ORDER BY wh_name ASC ");
while($res_wh = pg_fetch_array($qry_wh)){
    $wh_id = $res_wh['wh_id'];
    $wh_name = $res_wh['wh_name'];

$qry_num = 0;
$qry = pg_query("SELECT color, COUNT(car_id) AS count_car
FROM
(
	SELECT m.car_id, (select v1.\"color\" from \"VStockCars\" v1 where v1.car_id = m.car_id limit 1) as color
	FROM \"CarMove\" m LEFT JOIN \"Cars\" c ON m.car_id=c.car_id AND c.cancel='FALSE' 
	WHERE m.date_out IS NULL AND m.wh_id='$wh_id' AND c.cancel='FALSE'
) as tabletemp
GROUP BY color
ORDER BY color::integer ASC ");
$qry_num = pg_num_rows($qry);

$count_all += $qry_num;

$save_data .= '
<div style="font-weight:bold">สถานที่ : '.$wh_name.' ทั้งหมด '.$qry_num.' รายการ</div>

<table cellpadding="2" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center" bgcolor="#F0F0F0">
    <td>สถานที่</td>
    <td>สีรถ</td>
    <td>จำนวนคัน</td>
</tr>';

$sum_car = 0;
$j = 0;
while($res = pg_fetch_array($qry)){
    $j++;
    $count_car = 0;
    $color = $res['color'];
    $count_car = $res['count_car'];
    
    $sum_car += $count_car;

$save_data .= '
<tr>
    <td>'.$wh_name.'</td>
    <td>'.getCarColor($color).'</td>
    <td align="right">'.$count_car.'</td>
</tr>';
}

if($j == 0){
    $save_data .= "<tr><td colspan=\"3\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{
    $save_data .= "<tr><td colspan=\"2\" align=\"right\"><b>รวม</b></td><td align=\"right\"><b>$sum_car</b></td></tr>";
}

$save_data .= '</table>';
}

}else{

$qry = pg_query("SELECT color, COUNT(car_id) AS count_car
FROM
(
	SELECT m.car_id, (select v1.\"color\" from \"VStockCars\" v1 where v1.car_id = m.car_id limit 1) as color
	FROM \"CarMove\" m LEFT JOIN \"Cars\" c ON m.car_id=c.car_id AND c.cancel='FALSE' 
	WHERE m.date_out IS NULL AND m.wh_id='$wh' AND c.cancel='FALSE'
) as tabletemp
GROUP BY color
ORDER BY color::integer ASC ");

$qry_num = pg_num_rows($qry);

$warehouse_name = GetWarehousesName($wh);

$save_data .= '
<div style="font-weight:bold">สถานที่ : '.$warehouse_name.' ทั้งหมด '.$qry_num.' รายการ</div>

<table cellpadding="2" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center" bgcolor="#F0F0F0">
    <td>สถานที่</td>
    <td>สีรถ</td>
    <td>จำนวนคัน</td>
</tr>';

$sum_car = 0;
$j = 0;
while($res = pg_fetch_array($qry)){
    $j++;
    $color = $res['color'];
    $count_car = $res['count_car'];
    
    $sum_car+=$count_car;
    
$save_data .= '    
<tr>
    <td>'.$warehouse_name.'</td>
    <td>'.getCarColor($color).'</td>
    <td align="right">'.$count_car.'</td>
</tr>';

}

if($j == 0){
    $save_data .= "<tr><td colspan=\"3\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{
    $save_data .= "<tr><td colspan=\"2\" align=\"right\"><b>รวม</b></td><td align=\"right\"><b>$sum_car</b></td></tr>";
}

$save_data .= '</table>';
    
}

//START PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){
        $this->SetFont('AngsanaUPC', '', 14);// Set font
        $this->MultiCell(190, 0, 'วันที่พิมพ์ '.date('Y-m-d H:i:s'), 0, 'R', 0, 0, '', '', true);
    }

    public function Footer(){
        $this->SetFont('AngsanaUPC', '', 14);// Set font
        //$this->Line(10, 286, 200, 286);
        //$this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(210, 0, 'หน้า '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
//$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(false);

//set margins
$pdf->SetMargins(10, 15, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

$pdf->AddPage();

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('report_warehouse.pdf', 'I');
?>
<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$cmd = pg_escape_string($_REQUEST['cmd']);
$date = pg_escape_string($_REQUEST['date']);

if(empty($cmd) OR $cmd == "" OR empty($date) OR $date == ""){
    echo "invalid param.";
    exit;
}

$save_data = "";

if($cmd == "1"){

$save_data .= '<div style="margin:10px 0 0 0; font-weight:bold">รายงานรถเข้า วันที่ '.$date.'</div>';

$save_data .= '
<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center" bgcolor="#F0F0F0">
     <td width="50%">มาจาก</td>
    <td width="30%">สีรถ</td>
    <td width="20%">จำนวนคัน</td>
</tr>';

$j = 0;
$k = 0;
$sum = 0;
$nub = 0;
$unit = 0;

$qry = pg_query("SELECT \"from_id\", \"from_name\", \"color\", count(*) as \"unit\"
				FROM \"VCarMovement\"
				WHERE (\"date_in\" = '$date' and \"wh_id\" = '1') or (\"date_out\" = '$date' and \"target_go\" = '1')
				GROUP BY \"from_id\", \"from_name\", \"color\" ORDER BY \"from_name\", \"color\" ");
while($res = pg_fetch_array($qry)){
    $j++;
    $k++;
    $from_id = $res['from_id'];
    $from_name = $res['from_name'];
    $color = $res['color'];
    $unit = $res['unit'];
	
	if($from_name == ""){$from_name = "รับรถเข้าสต๊อก";}
	
    if($j == 1){
		$tmp_from_id = $from_id;
    }

    if($from_id != $tmp_from_id){
	
	$save_data .= '
    <tr style="font-weight:bold">
        <td colspan="3" align="right">'.$tmp_from_name.' | ยอดรวม '.$nub.' รายการ</td>
    </tr>';
    
        $nub = 0;
        $k = 1;
    }
$save_data .= '
<tr>
    <td>'.$from_name.'</td>
    <td>'.getCarColor($color).'</td>
    <td align="right">'.$unit.'</td>
</tr>
';
    $nub+=$unit;
    $tmp_from_id = $from_id;
    $tmp_from_name = $from_name;
}

if($j == 0){
    $save_data .= "<tr><td colspan=3 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
$save_data .= '
    <tr style="font-weight:bold">
        <td colspan="3" align="right">'.$tmp_from_name.' | ยอดรวม '.$nub.' รายการ</td>
    </tr>';

}

$save_data .= '</table>';


$save_data .= '<div style="margin:10px 0 0 0; font-weight:bold">รายงานรถออก วันที่ '.$date.'</div>';

$save_data .= '
<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center" bgcolor="#F0F0F0">
      <td width="35%">สถานที่</td>
	<td width="35%">ย้ายไปที่</td>
    <td width="15%">สีรถ</td>
    <td width="15%">จำนวนคัน</td>
</tr>';

$j = 0;
$k = 0;
$sum = 0;
$nub = 0;
$unit = 0;

$qry = pg_query("SELECT wh_id,wh_name,color,target_go,COUNT(color) AS unit FROM \"VCarMovement\" WHERE date_out='$date' and target_go <> '1'  GROUP BY wh_id,wh_name,target_go,color ORDER BY wh_name,target_go,color ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $k++;
    $wh_id = $res['wh_id'];
    $wh_name = $res['wh_name'];
    $color = $res['color'];
    $unit = $res['unit'];
    $target_go = $res['target_go'];
	
    if($j == 1){
		$tmp_wh_id = $wh_id;
    }

    if($wh_id != $tmp_wh_id){
	$save_data .= '
    <tr style="font-weight:bold">
        <td colspan="4" align="right">'.$tmp_wh_name.' | ยอดรวม '.$nub.' รายการ</td>
    </tr>';

        $nub = 0;
        $k = 1;
    }

$save_data .= '
<tr>
    <td>'.$wh_name.'</td>
	 <td>'.get_Warehouses($target_go).'</td>
    <td>'.getCarColor($color).'</td>
    <td align="right">'.$unit.'</td>
</tr>';

    $nub+=$unit;
    $tmp_wh_id = $wh_id;
    $tmp_wh_name = $wh_name;
}

if($j == 0){
    $save_data .= "<tr><td colspan=4 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
$save_data .= '
    <tr style="font-weight:bold">
        <td colspan="4" align="right">'.$tmp_wh_name.' | ยอดรวม '.$nub.' รายการ</td>
    </tr>';
}

$save_data .= '</table>';

}

elseif($cmd == "2"){

$save_data .= '<div style="margin:10px 0 0 0; font-weight:bold">รายงานรถเข้า วันที่ '.$date.'</div>';

$save_data .= '
<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center" bgcolor="#F0F0F0">
    <td width="35%">มาจาก</td>
    <td width="15%">ทะเบียน</td>
    <td width="35%">รุ่นรถ</td>
    <td width="15%">สีรถ</td>
</tr>';

$j = 0;
$k = 0;
$sum = 0;
$nub = 0;
$qry = pg_query("SELECT *
				FROM \"VCarMovement\"
				WHERE (\"date_in\" = '$date' and \"wh_id\" = '1') or (\"date_out\" = '$date' and \"target_go\" = '1')
				ORDER BY \"from_name\", \"license_plate\" ");
while($res = pg_fetch_array($qry)){
    $j++;
    $k++;
    $from_id = $res['from_id']; if($j == 1){ $tmp_from_id = $from_id; }
    $from_name = $res['from_name'];
    $license_plate = $res['license_plate'];
    $name = $res['name'];
    $color = $res['color'];
	
	if($from_name == ""){$from_name = "รับรถเข้าสต๊อก";}
	
    if($from_id != $tmp_from_id){
$save_data .= '
    <tr style="font-weight:bold">
        <td colspan="4" align="right">'.$tmp_from_name.' | รวม '.$nub.' รายการ</td>
    </tr>';

        $nub = 0;
        $k = 1;
    }

$save_data .= '
<tr>
    <td>'.$from_name.'</td>
    <td>'.$license_plate.'</td>
    <td>'.$name.'</td>
    <td>'.getCarColor($color).'</td>
</tr>';

    $nub++;
    $tmp_from_id = $from_id;
    $tmp_from_name = $from_name;
}

if($j == 0){
	$save_data .= "<tr><td colspan=4 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
$save_data .= '
    <tr style="font-weight:bold">
        <td colspan="4" align="right">'.$tmp_from_name.' | รวม '.$nub.' รายการ</td>
    </tr>';
}

$save_data .= '</table>';

$save_data .= '<div style="margin:10px 0 0 0; font-weight:bold">รายงานรถออก วันที่ '.$date.'</div>';

$save_data .= '
<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center" bgcolor="#F0F0F0">
    <td width="25%">สถานที่</td>
	<td width="25%">ย้ายไปที่</td>
    <td width="10%">ทะเบียน</td>
    <td width="25%">รุ่นรถ</td>
    <td width="15%">สีรถ</td>
</tr>';

$j = 0;
$k = 0;
$sum = 0;
$nub = 0;
$qry = pg_query("SELECT * FROM \"VCarMovement\" WHERE date_out='$date' and target_go <> '1' ORDER BY wh_name, target_name, license_plate ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $k++;
    $wh_id = $res['wh_id']; if($j == 1){ $tmp_wh_id = $wh_id; }
    $wh_name = $res['wh_name'];
    $license_plate = $res['license_plate'];
    $name = $res['name'];
    $color = $res['color'];
	$target_go = $res['target_go'];
	
    if($wh_id != $tmp_wh_id){
$save_data .= '
    <tr  style="font-weight:bold">
        <td colspan="5" align="right">'.$tmp_wh_name.' | รวม '.$nub.' รายการ</td>
    </tr>';

        $nub = 0;
        $k = 1;
    }

$save_data .= '
<tr>
    <td>'.$wh_name.'</td>
	<td>'.get_Warehouses($target_go).'</td>
    <td>'.$license_plate.'</td>
    <td>'.$name.'</td>
    <td>'.getCarColor($color).'</td>
</tr>';

    $nub++;
    $tmp_wh_id = $wh_id;
    $tmp_wh_name = $wh_name;
}

if($j == 0){
    $save_data .= "<tr><td colspan=5 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
$save_data .= '
    <tr style="font-weight:bold">
        <td colspan="5" align="right">'.$tmp_wh_name.' | รวม '.$nub.' รายการ</td>
    </tr>';
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
        $this->Line(10, 286, 200, 286);
        $this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(160, 5, 'หน้า '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
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

$pdf->AddPage();

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('carmovement_'.$cmd.'_'.$date.'.pdf', 'I');
?>
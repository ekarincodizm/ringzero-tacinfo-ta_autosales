<?php
include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$mm = pg_escape_string($_GET['mm']);
$yy = pg_escape_string($_GET['yy']);


$txtdate = get_thai_month($mm)." ".$yy;

$save_data = "";
$save_data .= '
<table cellpadding="2" cellspacing="0" border="0" width="100%" style="font-size:36px;" >
	<tr style="font-weight:bold;text-align:center" >
		<td colspan="4" align="center"><b>รายงานการติดตั้งแก๊ส</b></td>
	</tr>	
	<tr>
		<td colspan="4" align="center"><b>ประจำเดือน '.$txtdate.'</b></td>
	</tr>
</table>
<br>
<table cellpadding="2" cellspacing="0" border="1" width="100%" style="font-size:36px;">
<tr style="font-weight:bold;text-align:center" bgcolor="#F0F0F0">
	<th>ลำดับ</th>
    <th>วันที่ใบแจ้งหนี้</th>
	<th>เลขที่ใบแจ้งหนี้</th>
	<th width="72">ทะเบียนรถในสต๊อก</th>
	<th>รุ่น/สี</th>
	<th>บ.ที่ติดตั้ง</th>
	<th>ประเภทที่ติดตั้ง</th>
	<th>เลขที่ใบวิศวะ</th>
	<th>เลขที่ใบจอง</th>
	<th>ชื่อลูกค้า</th>
	<th>วันที่ลูกค้ารับรถ</th>
	<th>ค่าติดตั้งแก๊ส</th>
	<th>ค่าน้ำมัน</th>
	<th>วันที่จ่ายเงิน</th>
</tr>';

$nub = 0;
$query=pg_query("SELECT a.*, b.\"gasInvoiceDate\", b.\"payDate\", b.\"Vender\"
				FROM \"installGasDetail\" a, \"installGas\" b
				WHERE a.\"gasInvoiceNo\" = b.\"gasInvoiceNo\"
				AND (EXTRACT(MONTH FROM b.\"gasInvoiceDate\")='$mm') AND (EXTRACT(YEAR FROM b.\"gasInvoiceDate\")='$yy')
				ORDER BY b.\"gasInvoiceDate\", a.\"gasInvoiceNo\" ");
while($resvc=pg_fetch_array($query))
{
	$nub++;
	$auto_id_CarMove = $resvc['auto_id_CarMove']; // รหัสรายการใน CarMove
	$gasInvoiceDate = $resvc['gasInvoiceDate']; // วันที่ใบแจ้งหนี้
	$gasInvoiceNo = $resvc['gasInvoiceNo']; // เลขที่ใบแจ้งหนี้
	$car_idno = $resvc['car_idno']; // ทะเบียนรถในสต๊อก
	$car_name = $resvc['car_name']; // รุ่น
	$Vender = $resvc['Vender']; // บ.ที่ติดตั้ง
	$eng_cert = $resvc['eng_cert']; // เลขที่ใบวิศวะ
	$res_id = $resvc['res_id']; // เลขที่ใบจอง 
	$received_date = $resvc['received_date']; // วันที่ลูกค้ารับรถ
	$install_amount = $resvc['install_amount']; // ค่าติดตั้งแก๊ส
	$oil_amount = $resvc['oil_amount']; // ค่าน้ำมัน
	$payDate = $resvc['payDate']; // วันที่จ่ายเงิน
	
	// หารหัสรถ ใน CarMove
	$qry_car = pg_query("select \"car_id\", \"color\" from \"CarMove\" where \"auto_id\" = '$auto_id_CarMove' ");
	$car_id = pg_fetch_result($qry_car,0);
	$color_id = pg_fetch_result($qry_car,1);
	
	// หาชื่อสี
	$qry_color = pg_query("select \"color_name\" from \"CarColor\" where \"color_id\" = '$color_id' ");
	$color_name = pg_fetch_result($qry_color,0);
	
	// หารหัสประเภทติดตั้ง
	$qry_GasTypeID = pg_query("select a.\"GasTypeID\" from \"CarMove\" a
								where a.\"auto_id\" = (select max(b.\"auto_id\") from \"CarMove\" b where b.\"car_id\" = '$car_id' and b.\"auto_id\" < '$auto_id_CarMove' and b.\"GasTypeID\" is not null)");
	$GasTypeID = pg_fetch_result($qry_GasTypeID,0);

	// หาชื่อประเภทติดตั้ง
	$qry_GasTypeName = pg_query("select \"GasTypeName\" from \"GasType\" where \"GasTypeID\" = '$GasTypeID' ");
	$GasTypeName = pg_fetch_result($qry_GasTypeName,0);
	
	// หารหัสลูกค้า
	if($res_id != "")
	{
		$qry_cus_id = pg_query("select \"cus_id\" from \"Reserves\" where \"res_id\" = '$res_id' ");
		$cus_id = pg_fetch_result($qry_cus_id,0);
	}
	else
	{
		$cus_id = "";
	}
	
	if($cus_id == "")
	{
		$qry_cus_id = pg_query("select \"cus_id\" from \"Reserves\" where \"car_id\" = '$car_id' and \"reserve_status\" != '0' ");
		$cus_id = pg_fetch_result($qry_cus_id,0);
	}
	
	// หาชื่อลูกค้า
	if($cus_id != "")
	{
		$qry_reg_customer = pg_query("select \"reg_customer\" from \"Customers\" where \"cus_id\" = '$cus_id' ");
		$reg_customer = pg_fetch_result($qry_reg_customer,0);
	}
	else
	{
		$reg_customer = "";
	}
	
	$sum_install_amount += $install_amount;
	$sum_oil_amount += $oil_amount;
    
	$save_data .= '
	<tr style="font-size:36px">
		<td align="center">'.$nub.'</td>
		<td align="center">'.$gasInvoiceDate.'</td>
		<td align="center">'.$gasInvoiceNo.'</td>
		<td align="center">'.$car_idno.'</td>
		<td align="center">'."$car_name / $color_name".'</td>
		<td align="center">'.$Vender.'</td>
		<td align="center">'.$GasTypeName.'</td>
		<td align="center">'.$eng_cert.'</td>
		<td align="center">'.$res_id.'</td>
		<td align="center">'.$reg_customer.'</td>
		<td align="center">'.$received_date.'</td>
		<td align="right">'.number_format($install_amount,2).'</td>
		<td align="right">'.number_format($oil_amount,2).'</td>
		<td align="center">'.$payDate.'</td>
	</tr>';
}

if($nub == 0){
    $save_data .= "<tr><td colspan=\"13\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{
    $save_data .= '<tr style=\"font-size:36px\">
		<td colspan="11" align="right"><b>รวมทั้งสิ้น</b></td>
		<td align="right"><b>'.number_format($sum_install_amount,2).'</b></td>
		<td align="right"><b>'.number_format($sum_oil_amount,2).'</b></td>
		<td></td>
    </tr>';
}

$save_data .= '</table>';
//START PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdfLegal.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){

    }

    public function Footer(){
		$this->SetFont('AngsanaUPC', '', 12);// Set font
        //$this->Line(10, 200, 340, 200);
        $this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(280, 5, 'หน้า '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
    }
}

//$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);    // A4
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LEGAL', true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(true);

//set margins
$pdf->SetMargins(10, 5, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

$pdf->AddPage('L');

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('tax_buy.pdf', 'I');
?>
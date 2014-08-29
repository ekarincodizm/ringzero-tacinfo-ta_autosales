<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$car_type_id = $_GET['cartype'];


//เช็คเมื่อเลือกค้นหาด้วยตัวรถ
if($car_type_id == ""){
    echo "invalid param.";
    exit;
} 

if($car_type_id == ""){
		$where = "";
	}else if($car_type_id == "2"){
		$where = "where car_type_id in ('2','3','4') and substr(car_idno,3,2) != 'SE' ";
	}else if($car_type_id == "6"){
		$where = "where substr(car_idno,3,2) = 'SE' ";
	}else if($car_type_id == "7"){
		$where = "where car_status = 'R' ";
	}else if($car_type_id == "8"){
		$where = "where car_status = 'A' ";
	}else if($car_type_id == "9"){
		$where = "where car_status = 'S' ";
	}else if($car_type_id != "2"){
		$where = "where car_type_id = '$car_type_id' ";
	}

 //$str_where .= " car_type_id='$car_type_id';

 
$qry_cartypeid = pg_query("select * from \"CarType2\" where car_type_id = $car_type_id");
$res = pg_fetch_array($qry_cartypeid);
$car_type_name = $res['car_type_name'];
$str_title = "ประเภทรถ  $car_type_name". "  ( สถานะ (S) --> A-ว่าง รอขาย , R-จอง, Y-รถยึดฝากจอด , S-ขายแล้ว , P-รถซ่อมฝากจอด )";
						
 
/* if(!empty($str_where)){
    $where = " WHERE ".$str_where;
} */

$save_data = "";

$save_data .= '
<span><span style="font-weight:bold; font-size:large">รายงาน Stock รถในบริษัท</span> '.$str_title.'</span><br>

<table cellpadding="2" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center" bgcolor="#F0F0F0">
    <td width="30" >No.</td>
    <td width="80">ทะเบียนรถในสต๊อก</td>
    <td width="50">ทะเบียน</td>
    <td width="120">Product</td>
    <td width="100">เลขถัง</td>
    <td width="80">เลขเครื่อง</td>
    <td width="40">ปีรถ</td>
    <td width="60">สีรถ</td>
	<td width="60">เลขสัญญา</td>
    <td width="60">เลขจอง</td>
	<td width="80">หมายเหตุ</td>
	<td width="15">S</td>
</tr>';

$j = 0;
//$jnpage = 0 ;
$qry = pg_query("SELECT * FROM \"VStockCars\"$where ORDER BY car_id ASC");

while($res = pg_fetch_array($qry)){
    $j++;
	//$jnpage = ($j%22);
    $car_id = $res['car_id'];
	$car_idno = $res['car_idno'];
    $product_id = $res['product_id'];
    $car_num = $res['car_num'];
    $mar_num = $res['mar_num'];
    $car_year = $res['car_year'];
    $color = $res['color'];
    $license_plate = $res['license_plate'];
    $IDNO = $res['IDNO'];
    $res_id = $res['res_id'];
    $product_name = GetProductName($product_id);
	$car_status = $res['car_status'];
	$comment = $res['comment'];
	
	$save_data .= '
 <tr>
    <td align="right">'.$j.'</td>
    <td>'.$car_idno.'</td>
    <td>'.$license_plate.'</td>
    <td>'.$product_name.'</td>
    <td>'.$car_num.'</td>
    <td>'.$mar_num.'</td>
    <td>'.$car_year.'</td>
    <td>'.getCarColor($color).'</td>
	<td>'.$IDNO.'</td>
	<td>'.$res_id.'</td>
	<td>'.$comment.'</td>
	<td>'.$car_status.'</td>
</tr>';

}

if($j == 0){
    $save_data .= "<tr><td colspan=9 align=center>- ไม่พบข้อมูล -</td></tr>";
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
        $this->Line(10, 199, 285, 199);
        $this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
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
$pdf->SetFont('AngsanaUPC', '', 12); //AngsanaUPC  CordiaUPC

$pdf->AddPage('L');

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('report_warehouse.pdf', 'I');
?>
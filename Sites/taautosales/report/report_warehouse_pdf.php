<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$radio_type = $_GET['radio_type'];
$chk_color = $_GET['chk_color'];
$cb_color = $_GET['cb_color'];
$chk_car_type = $_GET['chk_car_type'];
$cb_car_type = $_GET['cb_car_type'];


//เช็คเมื่อเลือกค้นหาด้วยตัวรถ
/* if(empty($radio_type) OR $radio_type == ""){
    echo "invalid param.";
    exit;
} */

$str_title = "รูปแบบ ";
$str_where = "";

if($radio_type == 1){
    $str_title .= "ดูทั้งหมด";
}elseif($radio_type == 2){
    $str_title .= "ดูเฉพาะที่ยังไม่ขาย";
    $str_where .= "res_id IS NULL";
}elseif($radio_type == 3){
    $str_title .= "ดูเฉพาะรถที่จองอยู่";
    $str_where .= "res_id IS NOT NULL AND \"IDNO\" IS NULL";
}elseif($radio_type == 4){
    $str_title .= "ดูรถที่ลูกค้ารับแล้ว";
    $str_where .= "\"IDNO\" IS NOT NULL";
}

if($chk_color == "on"){
    if($radio_type == 1){
        if($cb_color == "ฟ้า"){
            $str_title .= " สีรถ ฟ้า";
            $str_where .= " color='ฟ้า'";
        }elseif($cb_color == "เหลือง"){
            $str_title .= " สีรถ เหลือง";
            $str_where .= " color='เหลือง'";
        }elseif($cb_color == "เขียวเหลือง"){
            $str_title .= " สีรถ เขียวเหลือง";
            $str_where .= " color='เขียวเหลือง'";
        }else{
            $str_title .= " สีรถ อื่นๆ";
            $str_where .= " color<>'ฟ้า' AND color<>'เหลือง' AND color<>'เขียวเหลือง'";
        }
    }else{
        if($cb_color == "ฟ้า"){
            $str_title .= " สีรถ ฟ้า";
            $str_where .= " AND color='ฟ้า'";
        }elseif($cb_color == "เหลือง"){
            $str_title .= " สีรถ เหลือง";
            $str_where .= " AND color='เหลือง'";
        }elseif($cb_color == "เขียวเหลือง"){
            $str_title .= " สีรถ เขียวเหลือง";
            $str_where .= " AND color='เขียวเหลือง'";
        }else{
            $str_title .= " สีรถ อื่นๆ";
            $str_where .= " AND color<>'ฟ้า' AND color<>'เหลือง' AND color<>'เขียวเหลือง'";
        }
    }
}

if($chk_car_type == "on"){
    if(!empty($str_where)){
        $str_where .= " AND product_id='$cb_car_type'";
    }else{
        $str_where .= " product_id='$cb_car_type'";
    }
    $str_title .= " รุ่นรถ $cb_car_type";
}

if(!empty($str_where)){
    $where = " WHERE ".$str_where;
}

$save_data = "";

$save_data .= '
<span><span style="font-weight:bold; font-size:large">รายงาน Stock รถในบริษัท</span> '.$str_title.'</span><br>

<table cellpadding="2" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center" bgcolor="#F0F0F0">
    <td width="30" >No.</td>
    <td width="120">ทะเบียนรถในสต๊อก</td>
    <td width="50">ทะเบียน</td>
    <td width="150">Product</td>
    <td width="120">เลขถัง</td>
    <td width="100">เลขเครื่อง</td>
    <td width="40">ปีรถ</td>
    <td width="60">สีรถ</td>
    <td width="60">เลขที่สัญญา</td>
    <td width="60">เลขจอง</td>
</tr>';

$j = 0;
//$jnpage = 0 ;
$qry = pg_query("SELECT * FROM \"VStockCars\"$where ORDER BY license_plate ASC");

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
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

$pdf->AddPage('L');

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('report_warehouse.pdf', 'I');
?>
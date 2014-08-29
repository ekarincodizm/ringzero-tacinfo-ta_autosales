<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

    $type = $_GET['type'];
    
  $condition = pg_escape_string($_GET['condition']);
$car_plate = pg_escape_string($_GET['car_plate']);

	if($condition == "all"){
		$where = "";
		$param_pdf = "condition=all";
		$type_txt = "รายการทั้งหมด";
	}else if($condition == "carplate"){
		$where = "where new_plate='$car_plate'";
		$param_pdf = "condition=carplate&car_plate=$car_plate";
		$type_txt  = "";
	}
	$qry_list = "SELECT * FROM \"P_NewCarPlate\" $where ORDER BY new_plate ASC";
	
$save_data = "";

$save_data .= '
<span><b>รายงานป้ายเหล็ก </b> รูปแบบ '.$type_txt.'</span><br>
<br>
<table cellpadding="3" cellspacing="1" border="0" width="100%" >
<tr style="font-weight:bold; text-align:center">
    <td width="10%">เลขป้ายแดง</td>
    <td width="10%">วันที่รับป้ายไป</td>
    <td width="14%">ทะเบียนรถในสต๊อก</td>
    <td width="10%">เลขที่สัญญา SI</td>
    <td width="10%">วันที่คืน</td>
	<td>รถขาย/ใช้ภายใน</td>
	<td width="30%">หมายเหตุใช้ภายใน</td>
</tr>';

$j = 0;
$qry = pg_query($qry_list);
while($res = pg_fetch_array($qry)){
    $j++;
    $new_plate = $res['new_plate'];
    $car_idno = $res['car_idno'];
    $for_sale = $res['for_sale'];
	$date_in = $res['date_in'];
    $date_out = $res['date_out'];
	$date_return = $res['date_return'];
    $memo_use_inhouse = $res['memo_use_inhouse'];
    
	if($car_idno != ""){
		$qry_car = pg_query("select car_id from \"Cars\" where car_idno = '$car_idno' ");
		$car_id = pg_fetch_result($qry_car,0);
	}else{
		$car_id = "";
	}
	
	if($car_id != ""){
		$qry_reserve = pg_query("select res_id,\"IDNO\" from \"Reserves\" where car_id='$car_id' ");
		$res_id = pg_fetch_result($qry_reserve,0);
		$IDNO = pg_fetch_result($qry_reserve,1);
		
		$txt_res_id = "<span id=\"R_id\" onclick=\"ShowDetailres('$res_id');\" style=\"cursor:pointer;\"><font color=\"blue\"><u>".$res_id."</u></font></span>";
	}else{
		$IDNO = "";
		$res_id = "";
	}
        if($for_sale == 'f' OR $for_sale == 'false'){
            $for_sale_txt = "ใช้ภายใน";
        }elseif($for_sale == 't' OR $for_sale == 'true'){
            $for_sale_txt = "รถขาย";
        }else{
            $for_sale_txt = "N/A";
        }
	
$save_data .= '
<tr style="font-size:32px">
    <td align="center">'.$new_plate.'</td>
    <td align="center">'.$date_out.'</td>
    <td align="center">'.$car_idno.'</td>
    <td align="center">'.$IDNO.'</td>
	<td align="center">'.$date_return.'</td>
    <td align="center">'.$for_sale_txt.'</td>
	<td align="center">'.$memo_use_inhouse.'</td>
</tr>';
}

if($j == 0){
    $save_data .= "<tr><td colspan=\"7\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
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

$pdf->Output('report_reserve_'.$type.'.pdf', 'I');
?>
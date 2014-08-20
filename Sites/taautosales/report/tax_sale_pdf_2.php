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
<tr>
	<td colspan="4" align="center"><b>รายงานภาษีขาย</b></td>
</tr>	
<tr>
	<td colspan="4" align="center"><b>ประจำเดือน '.$txtdate.'</b></td>
</tr>
<tr>
	<td align="left" width="12%">ชื่อผู้ประกอบการ : </td>
	<td align="left">บริษัท ที.เอ.โอโตเซลส์ จำกัด</td>
	<td  align="right" width="38%"></td>
	<td align="left" colspan="2">เลขประจำตัวผู้เสียภาษี 0105546153597</td>
</tr>
<tr>
	<td align="left" width="12%">ชื่อสถานประกอบการ : </td>
	<td align="left">บริษัท ที.เอ.โอโตเซลส์ จำกัด</td>
	<td  align="right"></td>
	<td align="left">สำนักงานใหญ่</td>
	
</tr>
<tr>
	<td align="left"  width="12%">ที่อยู่ : </td>
	<td colspan="2" align="left">555 ถนนนวมินทร์ แขวงคลองกุ่ม เขตบึงกุ่ม กรุงเทพมหานคร 10240 โทรศัพท์ 0-2744-2222 โทรสาร 0-2379-1111</td>
</tr>
</table>
<br>
<table cellpadding="2" cellspacing="0" border="1" width="100%" style="font-size:36px;">
<tr style="font-weight:bold;text-align:center" bgcolor="#F0F0F0">
	<td width="3%" rowspan="2">ลำดับที่</td>
	<td colspan="2" width="11%" >ใบกำกับภาษี</td>
	<td width="6%" rowspan="2">เลขที่สัญญา</td>
	<td width="4%" rowspan="2">รหัสลูกค้า</td>
	<td width="13%" rowspan="2">ชื่อผู้ซื้อสินค้า/ผู้รับบริการ</td>
	<td width="14%" rowspan="2">รายละเอียดสินค้า</td>
	<td width="11%" rowspan="2">เลขประจำตัวผู้เสียภาษีอากร<br>ของผู้ซื้อสินค้า</td>
	<td colspan="2" width="11%">สถานประกอบการ</td>
	<td rowspan="2" width="5%">มูลค่าสินค้า<br>หรือบริการ</td>
	<td width="6%" rowspan="2">ภาษีมูลค่าเพิ่ม</td>
	<td width="6%" rowspan="2">รวมทั้งสิ้น</td>
	<td colspan="2" width="10%" >ใบเสร็จรับเงิน</td>
</tr>
<tr style="font-weight:bold;text-align:center" bgcolor="#F0F0F0">
	<td>วันที่</td>
	<td>เลขที่ใบกำกับ</td>
	<td>สำนักงานใหญ่</td>
	<td>สาขาที่</td>
	<td>วันที่</td>
	<td>เลขที่ใบเสร็จ</td>
</tr>';


$j = 0;
$qry = pg_query("SELECT * FROM v_vat WHERE EXTRACT(MONTH FROM \"v_date\")='$mm' AND EXTRACT(YEAR FROM \"v_date\")='$yy' ORDER BY v_receipt ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $v_date = $res['v_date'];
    $v_receipt = $res['v_receipt'];
	$v_date = $res['v_date'];
	$inv_no = $res['inv_no'];
	$inv_date = $res['inv_date'];
    $IDNO = $res['IDNO'];
	$cus_id = $res['cus_id'];
    //$full_name = $res['pre_name']." ".$res['cus_name']." ".$res['surname'];
	$full_name = $res['reg_customer'];
	$product_name = $res['name'];

    $car_num = $res['car_num'];
    $mar_num = $res['mar_num'];
    $color = $res['color'];
    $amount = round($res['amount'],2);
    $vat = round($res['vat'],2);

    $sum = $amount+$vat;
    
    $all_amount += $amount;
    $all_vat += $vat;
    $all_sum += $sum;
		
	
	/*if($vender_id != ""){
		if(preg_match('/CUS/',$vender_id)){
			$cus_id = $vender_id;
		}else{
			$qry_cus = pg_query("select cus_id from \"Venders\" where vender_id='$vender_id'");
			$cus_id = pg_fetch_result($qry_cus,0);
		}
		
		$qry_brance = pg_query("select branch_id,card_id from \"Customers\" where cus_id='$cus_id' ");
		$branch_id = pg_fetch_result($qry_brance,0);
		$card_id = pg_fetch_result($qry_brance,1);
		
		
	}*/
	
	if( !empty($cus_id) ){
		$qry_cus = pg_query(" SELECT cus_type,branch_id, card_type, card_id
							FROM \"Customers\" WHERE cus_id='$cus_id' ");
		if($res_cus = pg_fetch_array($qry_cus)){
			$cus_type = $res_cus['cus_type'];
			$branch_id = $res_cus['branch_id'];
			$card_id = $res_cus['card_id'];
		}
		
		if($cus_type == 2){
			if($branch_id == ""){
				$main_brance = "";
				$brance = "";
			}else if($branch_id == 0){
				$main_brance = "X";
				$brance = "";
			}else{
				$main_brance = "";
				$brance = $branch_id;
			}
		}else{
			$main_brance = "";
			$brance = "";
		}
	}else{
		$main_brance = "";
		$brance = "";
	}
$save_data .= '
<tr style="font-size:34px">
	<td align="center">'.$j.'</td>
	<td align="center">'.date_dmy($inv_date).'</td>
	<td align="center">'.$inv_no.'</td>
	<td align="center">'.$IDNO.'</td>
	<td align="center">'.$cus_id.'</td>
	<td align="left">'.$full_name.'</td>
	<td align="left">'.$product_name.'</td>
	<td align="center">'.$card_id.'</td>
	<td align="center">'.$main_brance.'</td>
	<td align="center">'.$brance.'</td>
	<td align="right">'.number_format($amount,2).'</td>
	<td align="right">'.number_format($vat,2).'</td>
	<td align="right">'.number_format($sum,2).'</td>
	<td align="center">'.date_dmy($v_date).'</td>
	<td align="center">'.$v_receipt.'</td>
</tr>';

}

if($j == 0){
	$save_data .= "<tr><td colspan=\"11\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{
	$save_data .= "<tr style=\"font-size:34px\">
	<td colspan=\"2\" align=\"center\"><b>".$j." รายการ</b></td>
	<td colspan=\"8\" align=\"right\"><b>รวม</b></td>
	<td align=\"right\"><b>".number_format($all_amount,2)."</b></td>
	<td align=\"right\"><b>".number_format($all_vat,2)."</b></td>
	<td align=\"right\"><b>".number_format($all_sum,2)."</b></td>
	<td colspan=\"2\"></td>
	</tr>";
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
        $this->MultiCell(310, 5, 'หน้า '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LEGAL', true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);

//set margins
$pdf->SetMargins(4, 4, 4);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

$pdf->AddPage('L');

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('tax_sale.pdf', 'I');
?>
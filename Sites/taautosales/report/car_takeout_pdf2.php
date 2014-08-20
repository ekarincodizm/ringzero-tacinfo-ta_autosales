<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$idno = $_REQUEST['idno'];
$company = "บริษัท ที.เอ.โอโตเซลส์ จำกัด";	

if(empty($idno )){
    echo "invalid param.";
    exit;
}

$qry = pg_query("SELECT * FROM v_reserve WHERE idno = '$idno' ");
if($res = pg_fetch_array($qry)){
$res_id = $res['res_id'];
	
	$product_id = $res['product_id'];
	
	if($res['num_install'] == '0'){
		$str_title = "หนังสือสัญญาซื้อขายรถยนต์ ";
		$wording_type_buy = "ผู้ซื้อ";
		$wording_type_sale = "ผู้ขาย";
		$sale_type_title = "ซื้อสด";
		$str_sale_type = "สด"; 
		$str_total_down ="รวมรับชำระเงินค่ารถยนต์";
	}else{ $sale_type_title = "ซื้อผ่อน"; $str_sale_type = "จัดผ่อน";
		$str_title = "หนังสือสัญญาจะซื้อจะขายรถยนต์";
		$wording_type_buy = "ผู้จะซื้อ";
		$wording_type_sale = "ผู้จะขาย";
		$str_total_down ="รวมชำระเงินดาวน์";
	}
	
	if($res['use_radio'] == "t"){$str_use_radio = "ติดตั้ง";}else{$str_use_radio = "ไม่ติดตั้ง";}
    $finance_cus_id = $res['finance_cus_id'];
	$witness = $res['witness'];
	$sale = $res['user_id'];
}

$qry_product = pg_query(" SELECT 
						  \"Products\".name, 
						  \"Products\".product_id
						FROM 
						  public.\"Products\"
						WHERE \"Products\".product_id = '$product_id' ");
						
	if($res_product = pg_fetch_array($qry_product)){}
	
	if(empty($res['car_id'])){
		$car_type_name = "ป้ายแดง";
		$car_name = $res_product['name'];
	}else{
		$car_type_name = $res['car_type_name'];
		$car_name = $res['car_name'];
	}
		

$qry_finance_name = pg_query("SELECT  cus_id,finance_name 
								 FROM v_finances
								WHERE cus_id = '$finance_cus_id' ");
if($res_finance_name = pg_fetch_array($qry_finance_name)){}

$qry_reserve_amount = pg_query(" SELECT * FROM v_cashier 
								WHERE (service_id = 'S002' or service_id = 'S003' or service_id = 'S004') 
								AND res_id = '$res_id'");					
							
$tmp_reserve_amount = 0;					
while($res_reserve_amount = pg_fetch_array($qry_reserve_amount)){
	$tmp_reserve_amount += $res_reserve_amount['amount'];
	}
for($m=0;$m<=3;$m++){
	if($m != 0){
		$pageName = "(สำเนา)";
	} else {
		$pageName = "ต้นฉบับ(สำหรับลูกค้า)";
	}

	$qry_sale = pg_query("SELECT  fullname FROM v_users WHERE id_user = '$sale' ");
	if($res_sale = pg_fetch_array($qry_sale)){};
	
	$qry_witness = pg_query("SELECT  fullname FROM v_users WHERE id_user = '$witness' ");
		if($res_witness = pg_fetch_array($qry_witness)){}
	
	$qry_payment = pg_query("SELECT receipt_date,receipt_no,(cash_amount + chq_amount + discount_amount)as amount,cash_amount,chq_amount,discount_amount
								FROM receipt_tmp 
								WHERE res_id = '$res_id' 
								AND status <> '0' ");						   
	$a=0;
	$total_payment = 0;
	$num_rows = pg_num_rows($qry_payment);
	if($num_rows == 0){
		$save_data[$m] .= '<tr><td colspan="6"></td></tr>';
	}
	while($res_payment = pg_fetch_array($qry_payment)){
		$a++;
		$total_payment += $res_payment['amount'];

	}
	
$save_data[$m] .='

<table cellpadding="1" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="10%"><img src="../images/logo.jpg" border="0" width="50" height="50" ></td>
		<td width="90%" style="font-size:smaller; text-align:left">บริษัท ที.เอ.โอโตเซลส์  จำกัด <br>
			T.A. AUTOSALES CO.,LTD.
			<hr/><br>
			สำนักงานใหญ่ 555 ถนนนวมินทร์ แขวงคลองกุ่ม เขตบึงกุ่ม กรุงเทพมหานคร 10240 โทรศัพท์ 0-2744-2222 โทรสาร 0-2379-1111 <br>
			เลขประจำตัวผู้เสียภาษี 0105546153597
		</td>
	</tr>
</table>

<br>
<br>
<br>
<span style="font-weight:bold; font-size:larger; text-align:center"><b>'.$str_title.' - '.$pageName.'</b></span>
<hr color= "red" size="10">


<table cellpadding="3" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="65%"><b>เลขที่สัญญา:</b> '.$idno.'</td>
		<td width="35%"><b>ทำที่:</b> '.$company.'</td>
	</tr>
	<tr>
		<td width="65%"></td>
		<td width="35%"><b>วันที่ :</b>  '.formatDate($nowdate,"/").'</td>
	</tr>
	<tr>
	</tr>
	<tr>
	</tr>
	<tr>
	</tr>
</table>



<span style="font-weight:normal; font-size:medium; text-align:left"><p><font color="white">................</font>
ข้าพเจ้า.........<b>'.$res['cus_name'].'</b>..........('.$wording_type_buy.') ได้ตกลงซื้อรถยนต์ยี่ห้อ <b>'.$car_name.'<br>
</b> หมายเลขทะเบียน  <b> '.$res['license_plate'].'</b>&nbsp;&nbsp; ปีรถ <b> '.$res['car_year'].'</b>
ในราคา  <b>'.number_format($res['car_price'],2).' </b> บาท (ตามสภาพ) กับ ..... <b>'.$res_sale['fullname'].'('.$wording_type_sale.') </b>..... <br>
ในวันนี้  '.$wording_type_buy.'ได้ชำระเงินจำนวน <b>'.number_format($total_payment,2).'  </b> บาท  พร้อมรับรถยนต์ตามสภาพที่พอใจไปเรียบร้อยแล้วในวันนี้
</span>
<br>


<br>';
$save_data[$m] .='
<table style="font-size:medium"  width="100%" boder="1" >
	<tr>
		<td width="7%"></td><td width="93%">เพื่อเป็นหลักฐานจึงได้ทำเป็นหนังสือไว้ต่อหน้าพยาน</td>
	</tr>';
	
		
$save_data[$m] .='
	<tr><td width="100%"></td></tr>
</table>';

$save_data[$m] .= '
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td>
			<table cellpadding="5" cellspacing="5" border="0" width="100%">
				<tr align="center">
					<td width="800" colspan="2">ลงชื่อ _____________________ '.$wording_type_buy.'</td>
				</tr>
				<tr align="center">
					<td width="800" colspan="2">(___'.$res['cus_name'].'___)</td>
				</tr>
				<tr align="center">
					<td width="800" colspan="2">ลงชื่อ _____________________ '.$wording_type_sale.'</td>
				</tr>
				<tr align="center">
					<td width="800" colspan="2">(___'.$res_sale['fullname'].'___)</td>
				</tr>
				<tr align="center">
					<td width="800" colspan="2">เจ้าหน้าที่บริษัท</td>
				</tr>
				<tr align="center">
					<td width="800" colspan="2">ลงชื่อ ..................................... พยาน</td>
				</tr>
				<tr align="center">
					<td width="800" colspan="2">(____'.$res_witness['fullname'].'____)</td>
				</tr>
				<tr align="center">
					<td width="800" colspan="2">ลงชื่อ ..................................... ผู้รับเงิน</td>
				</tr>
				<tr align="center">
					<td width="800" colspan="2">(________________________)</td>
				</tr>
				<tr align="center">
					<td width="800" colspan="2">ลงชื่อ ..................................... ผู้อนุมัติ</td>
				</tr>
				<tr align="center">
					<td width="800" colspan="2">(________________________)</td>
				</tr>
			</table>
		</td>
	</tr>
</table>';

$save_data[$m] .= '
<table width="100%">
	<tr>
		<td><b>หมายเหตุ </b>&nbsp;&nbsp;'.$res['remark'].'</td>
	</tr>
</table>';

}
//START PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){

    }

    public function Footer(){
	 $this->SetFont('AngsanaUPC', '', 10);// Set font
		
		$style = array(
			'border' => true,
			'vpadding' => 'auto',
			'hpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255)
			'module_width' => 1, // width of a single module in points
			'module_height' => 1 // height of a single module in points
		);
		$str_pages = $this->getAliasNbPages();
		
        $this->Line(10, 286, 200, 286);
        $this->MultiCell(55, 0,'วันที่พิมพ์ : '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
		$this->MultiCell(55, 0, 'ชื่อผู้พิมพ์ : '.$_SESSION["ss_username"], 0, 'R', 0, 0, '', '', true);
		$this->MultiCell(80, 0, 'หน้าที่'.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
	
		
		$res_id = $_REQUEST['res_id'];
		$this->write2DBarcode($res_id, 'QRCODE,H', 190, 275, 10, 10, $style, 'N');
	}
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);

//set margins
$pdf->SetMargins(10, 10, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC


/*$pdf->AddPage();
$pdf->writeHTML($save_data, true, false, true, false, '');*/

for($k=0;$k<=3;$k++){
$pdf->AddPage();
$pdf->writeHTML($save_data[$k], true, false, true, false, '');
}

$pdf->Output('car_takeout_'.$res_id.'.pdf', 'I');

?>
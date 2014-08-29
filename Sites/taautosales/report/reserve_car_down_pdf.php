<?php
/*

*/

include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$res_id = $_REQUEST['res_id'];
$inv_no_list = $_REQUEST['inv_id'];

$explode_inv_no = explode(",",$inv_no_list);
$str_inv_no = " '" .implode("','",$explode_inv_no)."' ";


if(empty($res_id )){
    echo "invalid param.";
    exit;
}

$qry = pg_query("SELECT * FROM v_reserve WHERE res_id='$res_id' ");
if($res = pg_fetch_array($qry)){
	
	if($res['num_install'] == '0'){$sale_type_title = "ซื้อสด"; $str_sale_type = "สด";}else{ $sale_type_title = "ซื้อผ่อน"; $str_sale_type = "จัดผ่อน";}  
	if($res['use_radio'] == "t"){$str_use_radio = "ติดตั้ง";}else{$str_use_radio = "ไม่ติดตั้ง";}
    $finance_cus_id = $res['finance_cus_id'];
	$witness = $res['witness'];
	$sale = $res['user_id'];
}

$qry_finance_name = pg_query("SELECT  cus_id,finance_name 
								 FROM v_finances
								WHERE cus_id = '$finance_cus_id' ");
if($res_finance_name = pg_fetch_array($qry_finance_name)){}

								
$qry_reserve_amount = pg_query("SELECT sum(reserve_amount) as receive_amount 
								 FROM v_invoice
								WHERE service_id = 'S002'
								AND status = 'OCCA'
								AND res_id = '$res_id' ");
/*$qry_reserve_amount = pg_query("SELECT sum(receive_amount) as receive_amount 
									FROM
										(  SELECT receive.inv_no, 
											receive.amount AS receive_amount,
											receive.service_id,
											services.name, 
											receive.pay_method
										   FROM ( SELECT \"OtherpayDtl\".auto_id, 
															\"OtherpayDtl\".inv_no, 
															\"OtherpayDtl\".service_id, 
															\"OtherpayDtl\".amount, 
															\"OtherpayDtl\".status AS pay_method
														   FROM \"OtherpayDtl\"
												UNION 
														 SELECT \"ChequeDetails\".auto_id, 
															\"ChequeDetails\".inv_no, 
															\"ChequeDetails\".service_id, 
															\"ChequeDetails\".cus_amount, 
															\"ChequeDetails\".pay_method
														   FROM \"ChequeDetails\" ) receive
										   LEFT JOIN \"Services\" services ON receive.service_id::text = services.service_id::text ) total_s002
								WHERE total_s002.inv_no in ('$str_inv_no') ");*/
					
 $tmp_reserve_amount = 0;					
while($res_reserve_amount = pg_fetch_array($qry_reserve_amount)){
	$tmp_reserve_amount = $res_reserve_amount['receive_amount'];
	}


$save_data = "";

$save_data .= '

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

<span style="font-weight:bold; font-size:larger; text-align:left"><b>ใบจองรถยนต์ ('.$sale_type_title.') </b></span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<span style="font-weight:bold; font-size:larger; text-align:right"><b>***ในวันรับรถชำระเป็น แคชเชียร์เช็ค เท่านั้น***  </b></span>
<hr color= "red" size="10">
<br><p>
<table  border="0" width="100%">
	<tr>
		<td style="font-weight:bold; font-size:medium; text-align:center"><b>เลขที่จอง  &nbsp;&nbsp;'.$res['res_id'].'</b></td>
		<td style="font-weight:bold; font-size:medium; text-align:center"><b>วันที่จอง  &nbsp;&nbsp;'.$res['reserve_date'].'</b></td>
	</tr>
</table>
<br><br>
<table cellpadding="3" cellspacing="0" border="1" width="100%" style="font-size:smaller;" >
	<tr>
		<td width="15%">ชื่อผู้จอง </td>
		<td width="35%">'.$res['cus_name'].'</td>
		<td width="15%">วันที่ต้องการออกรถ </td>
		<td width="35%">'.$res['reserve_date'].'</td>
	</tr>
	<tr>
		<td>ที่อยู่ตามบัตรประชาชน</td>
		<td>'.$res['address'].'<br>&nbsp;'.$res['add_post'].'</td>
		<td>ที่อยู่ที่ติดต่อได้</td>
		<td>'.$res['contract_add'].'&nbsp;'.$res['contract_post'].'  </td>
	</tr>
	<tr>
		<td>ประเภทบัตร</td>
		<td>'.$res['card_type'].' &nbsp;&nbsp; เลขที่ '.$res['card_id'].'</td>
		<td>โทรศัพท์</td>
		<td>'.$res['telephone'].'</td>
	</tr>
	<tr>
		<td>แบบรถ</td>
		<td>'.$res['car_name'].'</td>
		<td>สีรถ  &nbsp;&nbsp;'.$res['reserve_color'].'</td>
		<td>ประเภทรถ &nbsp;&nbsp;  '.$res['car_type_name'].'</td>
	</tr>
	<tr>
		<td>เลขเครื่อง</td>
		<td>'.$res['car_num'].'</td>
		<td>เลขตัวถัง </td>
		<td>'.$res['mar_num'].'</td>
	</tr>
	<tr>
		<td>ทะเบียนรถ</td>
		<td>'.$res['license_plate'].'&nbsp;&nbsp; ปีรถ '.$res['car_year'].'</td>
		<td>ทะเบียน STOCK</td>
		<td>'.$res['car_idno'].'</td>
	</tr>
	<tr>
		<td>ขายแบบ</td>
		<td>'.$str_sale_type.'</td>
		<td>ประกันภัย  </td>
		<td>ประเภทที่   '.$res['type_insure'].'</td>
	</tr>
	<tr>
		<td>บริษัทไฟแนนซ์</td>
		<td>'.$res_finance_name['finance_name'].'</td>
		<td>ติดตั้งวิทยุ </td>
		<td>'.$str_use_radio.'</td>
	</tr>
	<tr>
		<td>ราคาขายรถ</td>
		<td>'.number_format($res['car_price'],2).'</td>
		<td>ยอดจัด</td>
		<td>'.number_format($res['finance_price'],2).'</td>
	</tr>
	<tr>
		<td>เงินดาวน์</td>
		<td>'.number_format($res['down_price'],2).'</td>
		<td>จำนวนเงินจอง</td>
		<td>'.number_format($tmp_reserve_amount,2).'</td>
	</tr>';
	
	$down_balance = ($res['down_price']-$res_reserve_amount['receive_amount']);
	$save_data .='
	<tr>
		<td><b>เงินดาวน์คงค้าง </b></td>
		<td><b>'.number_format($down_balance,2).'</b></td>
		<td><b>จำนวนงวด  </b>'.$res['num_install'].'</td>
		<td><b>ค่าผ่อนต่องวด  </b>'.number_format($res['installment'],2).'</td>
	</tr>
	<tr>
		<td colspan="2"><b> นัดชำระเงินดาวน์คงค้างภายในวันนัดส่งมอบรถวันที่ </b></td>
		<td colspan="2">&nbsp;<b>'.$res['receive_date'].'</b></td>
  </tr>
</table>
<table cellpadding="3" cellspacing="0" border="1" width="100%" style="font-size:smaller;">
	<tr  bgcolor="#CCCCCC">
		<td width="10%"><b>ลำดับที่</b></td>
		<td width="70%"><b>ค่าใช้จ่ายอื่นๆ (ชำระภายในวันรับรถ)</b></td>
		<td width="20%"><b>จำนวนเงิน</b></td>
	</tr>';
	
	$qry_other_pay = pg_query("SELECT name,(amount+vat) as amount 
								FROM v_print_other_pay
							   WHERE res_id = '$res_id' ");
	$j=0;
	$total_amount = 0;
	while($res_other_pay = pg_fetch_array($qry_other_pay)){
		$j++;
		$total_amount += $res_other_pay['amount'];
	$save_data .='
	<tr>
		<td>'.$j.'</td>
		<td>'.$res_other_pay['name'].'</td>
		<td align="right">'.number_format($res_other_pay['amount'],2).'</td>
	</tr>';
	}
	$save_data .='
	<tr>
		<td colspan="2" align="right"><b>รวมค่าใช้จ่ายอื่นๆ</b></td>
		<td align="right"><b>'.number_format($total_amount,2).'</b></td>
	</tr>';
	
	$save_data .='
</table>
<table cellpadding="3" cellspacing="0" border="1" width="100%" style="font-size:smaller;">
	<tr  bgcolor="#CCCCCC">
		<td width="40%"><b>รายละเอียดการรับชำระเงินวันจองรถ</b></td>
		<td width="20%"><b>เลขที่ใบเสร็จ</b></td>
		<td width="20%"><b>จำนวนเงิน</b></td>
		<td width="20%"><b>ชำระเป็น</b></td>
	</tr>';
	
	$qry_pay_invoice = pg_query("SELECT o_receipt,pay_method,name as service_name,receive_amount,inv_no 
								 FROM v_print_reserve_pay
								WHERE inv_no in ('$inv_no_list') ");
	
	while($res_pay_invoice = pg_fetch_array($qry_pay_invoice)){
	
	if($res_pay_invoice['pay_method'] == 'CA'){$str_pay_method = "เงินสด" ;}else{$str_pay_method ="เช็ค" ;}
	
	$save_data .= '
    <tr>
        <td align="left">'.$res_pay_invoice['service_name'].'</td>
		<td align="left">'.$res_pay_invoice['o_receipt'].'</td>
        <td align="right">'.number_format($res_pay_invoice['receive_amount'],2).'</td>
        <td align="center">'.$str_pay_method.'</td>
    </tr>';
	}
	
	if(!empty($str_pay_method)){
	$qry_chq = pg_query("SELECT cheque_no,bank_name,bank_branch,date_on_cheque 
								 FROM \"VChequeDetail\"
								WHERE inv_no in ('$str_inv_no') ");
	if($res_chq = pg_fetch_array($qry_chq)){}

	$save_data .='
	
		<tr>
			<td width="15%"><b>เลขที่เช็ค</b></td>
			<td width="10%">'.$res_chq['cheque_no'].'</td>
			<td width="10%"><b>ธนาคาร</b></td>
			<td width="20%">'.$res_chq['bank_name'].'</td>
			<td width="10%"><b>สาขา</b></td>
			<td width="15%">'.$res_chq['bank_branch'].'</td>
			<td width="10%"><b>วันที่เช็ค</b></td>
			<td width="10%">'.$res_chq['date_on_cheque'].'</td>
		</tr>';
	}else{}
	
$save_data .= '	
</table>
<table cellpadding="3" cellspacing="0" border="1" width="100%" style="font-size:smaller;">
	<tr  bgcolor="#CCCCCC">
		<td width="10%"><b>ลำดับที่</b></td>
		<td width="80%"><b>รายการของแถม</b></td>
		<td width="10%"><b>จำนวนเงิน</b></td>
	</tr>';
	
	$qry_gif_detail = pg_query("SELECT 
								  gif_detail.res_id, 
								  gif_detail.product_id, 
								  gif_detail.amount, 
								  \"Products\".name as product_name
								FROM 
								  public.gif_detail, 
								  public.\"Products\"
								WHERE 
								  gif_detail.product_id = \"Products\".product_id
								AND res_id = '$res_id' ");
	$row_gif = 0;							
	while($res_gif_detail = pg_fetch_array($qry_gif_detail)){
		$row_gif++;
		
	$save_data .='
	<tr>
		<td>'.$row_gif.'</td>
		<td>'.$res_gif_detail['product_name'].'</td>
		<td align="right">'.$res_gif_detail['amount'].'</td>
	</tr>';
	}
	$save_data .='
</table>
<br><br><br>
<table width="100%">
	<tr>
		<td><b>หมายเหตุ </b>      </td>
	</tr>
</table>

<table cellpadding="1" cellspacing="0" border="1" width="100%" style="font-size:smaller;">
	<tr>
		<td align="center">
			<table cellpadding="1" cellspacing="0" border="0" width="250px" style="font-size:smaller;" align="center">
				<tr align="center">
					<td width="120px" colspan="2">ลงชื่อ _____________________ ผู้จอง</td>
				</tr>
				<tr align="center">
					<td width="120px" colspan="2">(___'.$res['cus_name'].'___)</td>
				</tr>
				<tr align="center">
					<td width="120px" colspan="2">วันที่_____________________</td>
				</tr>
			</table>
		</td>
		<td>';
		$qry_sale = pg_query("SELECT  fullname FROM v_users WHERE id_user = '$sale' ");
		if($res_sale = pg_fetch_array($qry_sale)){}
		
		$save_data .='
			<table cellpadding="1" cellspacing="0" border="0" width="250px" style="font-size:smaller;" align="center">
				<tr align="center">
					<td width="120px" colspan="2">ลงชื่อ _________________ พนง.ขาย</td>
				</tr>
				<tr align="center">
					<td width="120px" colspan="2">(___'.$res_sale['fullname'].'___)</td>
				</tr>
				<tr align="center">
					<td width="120px" colspan="2">วันที่_____________________</td>
				</tr>
			</table>
		</td>
		<td>';
		
		$qry_witness = pg_query("SELECT  fullname FROM v_users WHERE id_user = '$witness' ");
		if($res_witness = pg_fetch_array($qry_witness)){}

	$save_data .='
			<table cellpadding="1" cellspacing="0" border="0" width="250px" style="font-size:smaller;" align="center">
				<tr align="center">
					<td width="120px" colspan="2">ลงชื่อ _____________________ พยาน</td>
				</tr>
				<tr align="center">
					<td width="120px" colspan="2">(____'.$res_witness['fullname'].'____)</td>
				</tr>
				<tr align="center">
					<td width="120px" colspan="2">วันที่_____________________</td>
				</tr>
			</table>
		</td>
		<td>
			<table cellpadding="1" cellspacing="0" border="0" width="250px" style="font-size:smaller;" align="center">
				<tr align="center">
					<td width="120px" colspan="2">ลงชื่อ ___________________ ผู้รับเงิน</td>
				</tr>
				<tr align="center">
					<td width="120px" colspan="2">(__'.$_SESSION["ss_username"].'__)</td>
				</tr>
				<tr align="center">
					<td width="120px" colspan="2">วันที่_____________________</td>
				</tr>
			</table>
		</td>
	</tr>
</table>';



$save_data .='
<table style="font-size:smaller"  width="100%" boder="1" >
	<tr>
		<td width="100%"></td>
	</tr>
';
	
	if($res['reserve_color'] == "เขียวเหลือง"){
$save_data .='
	<tr>
		<td width="100%"><br>อนึ่งหากผู้จะซื้อต้องการจดทะเบียนเป็นแท็กซี่่ส่วนบุคคล (เขียวเหลือง) ผู้จะซื้อมีหน้าที่นำใบประกอบการเพื่อขอจดทะเบียนแท็กซี่มิเตอร์มามอบให้กับผู้จะขาย <br>
			เพื่อผู้จะขายจะได้นำใบประกอบการไปจดทะเบียนแท็กซี่มิเตอร์ส่วนบุคคลต่อไป
		</td>
	</tr>';
	} 
	if($res['use_radio'] == "t"){	
$save_data .= '	
	<tr><td width="100%"></td></tr>
	<tr>
		<td width="100%"><br>ผู้เช่าซื้อมีหน้าที่ชำระค่าวิทยุสื่อสาร เดือนละ 342.40 บาท ทุกๆ เดือน เริ่มชำระวันออกรถ และผู้จะซื้อมีหน้าที่ชำระพร้อมนำรถยนต์ไปตรวจมิเตอร์ทุกๆ 6 เดือนพร้อมต่อภาษีประจำปี 
		</td>
	</tr>';
	}
	
$save_data .='
	<tr><td width="100%"></td></tr>
	<tr>
		<td width="100%"><b>เงื่อนไขและการสงวนสิทธิ์ของการขาย </b><br>
			&nbsp;&nbsp;1. การซื้อรถยนต์ จะมีผลต่อเมื่อผู้ขายได้รับการชำระเงินเรียบร้อยแล้ว <br>
			&nbsp;&nbsp;2. ราคารถยนต์อาจมีการเปลี่ยนแปลงได้  โดยทางผู้ขายจะแจ้งให้ทราบก่อนวันออกรถ <br>
			&nbsp;&nbsp;3. ถ้าผู้จองมีความประสงค์ยกเลิกการสั่งซื้อ  ผู้จองไม่สามารถเรียกร้องเงินคืนได้ <br>
			&nbsp;&nbsp;4. ถ้าผู้จองรถยนต์ไม่มารับรถภายในกำหนด  หลังจากได้รับใบแจ้งทางผู้ขาย  ถือว่าผู้จองสละสิทธิ์ไม่รับรถ  และจะเรียกร้องเงินจองที่ชำระไว้คืนมิได้ <br>
			&nbsp;&nbsp;5. ในกรณีที่ผู้ขายไม่สามารถจัดหารถเพื่อส่งมอบให้ลูกค้าได้ ทางผู้ขาย ยินดีคืนเงินจองให้แก่ลูกค้า 
		</td>
	</tr>
</table>';

 $in_qry="INSERT INTO reserve_print_status (res_id,print_status) VALUES('$res_id','1')";
 if($in_qry){ $obj_print_status = pg_query($in_qry);}
 
 
//START PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){

    }

    public function Footer(){/*
        $this->SetFont('AngsanaUPC', '', 14);// Set font
        $this->Line(10, 286, 200, 286);
        $this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(160, 5, 'หน้า '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
    */}
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



/*$print_tmp_receive = include_once("reserve_tmp_receive_pdf.php");
$pdf->writeHTML($print_tmp_receive, true, false, true, false, '');*/

$pdf->Output('reserve_car_'.$res_id.'.pdf', 'I');
?>
<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$idno = pg_escape_string($_REQUEST['idno']);


if(empty($idno )){
    echo "invalid param.";
    exit;
}

$qry = pg_query("SELECT * FROM v_reserve WHERE idno = '$idno' ");
if($res = pg_fetch_array($qry)){
	$res_id = $res['res_id'];
	
	$product_id = $res['product_id'];
	
	if($res['num_install'] == '0'){
		$str_title = "หนังสือสัญญาซื้อขายรถยนต์";
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
	// หาชื่อรุ่นรถ กรณี ยังไม่ เจาะจงรถ ให้ เลือก จาก  table product ก่อน
	if(empty($res['car_id'])){
		$car_type_name = "รถป้ายแดง";
		$car_name = $res_product['name'];
	}else{
		$car_type_name = $res['car_type_name'];
		// $car_type_name = "มือสองตามสภาพ";
		$car_name = $res['car_name'];
	}
	
	//Check that Are there ป้ายแดง ??? If yes, change it to มือสองตามสภาพ
	if(!(strpos($car_type_name, "ป้ายแดง") !== false)){
		$car_type_name = "มือสองตามสภาพ";
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
<table>
	<tr border="1">
		<td width="60%" style="font-weight:bold; font-size:larger; text-align:left; ">
			<b>'.$str_title.' - '.$pageName.'</b>
		</td>
		<td width="40%" style="font-weight:bold; font-size:larger; text-align:right; ">
			<b>เลขที่สัญญา&nbsp;&nbsp;'.$res['idno'].'</b>
		</td>
	</tr>
</table>
<hr color= "red" size="10">
<span style="font-weight:normal; font-size:small; text-align:left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
ข้าพเจ้า...'.$res['cus_name'].'...('.$wording_type_buy.') ได้ตกลงทำสัญญาซื้อขายรถยนต์ไว้กับ บริษัท ที.เอ.โอโตเซลส์ จำกัด ('.$wording_type_sale.') โดยมีรายละเอียดดังต่อไปนี้
</span>
<br>
<table  cellpadding="2" cellspacing="0" border="1" width="100%" style="font-size:12.5;" >
	<tr bgcolor="#CCCCCC">
		<td width="18%">วันที่จอง &nbsp;&nbsp;'.$res['reserve_date'].'</td>
		<td width="32%">เลขที่จอง'.'&nbsp;'.$res['res_id'].'</td>
		<td width="22%">วันที่ส่งมอบ&nbsp;&nbsp; '.$res['receive_date'].'</td>
		<td width="28%">เลขที่สัญญา&nbsp;&nbsp;'.$res['idno'].'</td>
	</tr>
	<tr>
		<td width="13%">สถานที่ส่งมอบ  </td>
		<td width="37%">บริษัท ที.เอ. โอโตเซลส์ จำกัด</td>
		<td width="12%">ชื่อ'.$wording_type_buy.'</td>
		<td width="38%">'.$res['cus_name'].'</td>
	</tr>
	<tr>
		<td>ชื่อจดทะเบียน </td>
		<td colspan ="3">'.$res['reg_customer'].'</td>
	</tr>
	<tr>
		<td width="13%">ที่อยู่จดทะเบียน</td>
		<td width="37%">'.$res['reg_address'].' '.$res['reg_post'].'</td>
		<td width="12%">ที่อยู่ที่ติดต่อได้</td>
		<td width="38%">'.$res['contract_add'].' '.$res['contract_post'].' โทรศัพท์ '.$res['telephone'].'</td>
	</tr>
</table>
<table cellpadding="2" cellspacing="0" border="1" width="100%" style="font-size:12.5;" >
	<tr bgcolor="#CCCCCC">
		<td width="15%">ประเภทบัตร</td>';
		if($res['card_type'] == 'บัตรผู้เสียภาษีอากร'){$card_type = 'บัตรประจำตัวผู้เสียภาษีอากร';}else{$card_type = $res['card_type'];}
		$save_data[$m] .='
		<td width="35%">'.$card_type.' &nbsp;&nbsp; เลขที่ '.$res['card_id'].'</td>
		<td width="17%">วันที่ต้องการออกรถ</td>
		<td width="33%">'.$res['receive_date'].'</td>
	</tr>
	<tr bgcolor="#CCCCCC">
		<td>แบบรถ</td>
		<td>'.$car_name.'</td>
		<td>สีรถ  &nbsp;&nbsp;'.$res['reserve_color'].'</td>
		<td>ประเภทรถ &nbsp;&nbsp;  '.$car_type_name.'</td>
	</tr>
	<tr>
		<td>เลขเครื่อง</td>
		<td>'.$res['mar_num'].'</td>
		<td>เลขตัวถัง </td>
		<td>'.$res['car_num'].'</td>
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
		<td>ติดตั้งวิทยุสื่อสาร</td>
		<td>'.$str_use_radio.'</td>
	</tr>
	<tr>
		<td width="30%">วันที่ไฟแนนซ์อนุมัติ </td>
		<td width="20%">'.$res['finance_date'].'</td>
		<td width="17%"><b>ราคาขายรถ </b></td>
		<td width="33%">'.number_format($res['car_price'],2).'</td>
	</tr>
	<tr>
		<td>ส่วนที่จัดทำสัญญากับไฟแนนซ์(ยอดจัด)</td>
		<td>'.number_format($res['finance_price'],2).'</td>
		<td><b>เงินดาวน์</b></td>
		<td>'.number_format($res['down_price'],2).'</td>
	</tr>';
	
	
	$down_balance = ( $res['down_price'] - $tmp_reserve_amount );
	$save_data[$m] .='
	<tr>
		<td><b>สรุปเงินคงค้างทั้งสิ้น </b></td>
		<td><b>'.number_format($down_balance,2).'</b></td>
		<td><b>จำนวนงวด  </b>'.$res['num_install'].'</td>
		<td><b>ค่าผ่อนต่องวด  </b>'.number_format($res['installment'],2).'</td>
	</tr>
	<tr>
		<td width="23%">นัดชำระค่างวดทุกวันที่</td>
		<td width="12%" align="right">'.$res['due_date'].'</td>
		<td width="25%">นัดชำระค่างวด งวดแรกวันที่&nbsp;&nbsp;</td>
		<td width="40%" style="font-weight:bold;font-color:red;" >'.$res['rpay_date'].'</td>
  </tr>
	<tr>
		<td width="23%"><b>นัดชำระเงินคงค้างภายในวันที่</b></td>
		<td width="12%" align="right">'.$res['fpay_date'].'</td>
		<td colspan="2" style="font-weight:bold;font-color:red; font-size:11" width="65%" ><b>หาก'.$wording_type_buy.'ไม่ชำระเงินส่วนที่ค้างตามกำหนดเวลาทางบริษัทฯ จะไม่ดำเนินการจดทะเบียนใดๆ ทั้งสิ้น </b></td>
  </tr>
</table>
<table cellpadding="2" cellspacing="0" border="1" width="100%" style="font-size:12.5;">
	<tr bgcolor="#CCCCCC">
		<td colspan="6" align="left">รายละเอียดการรับชำระเงิน</td>
	</tr>
	<tr  bgcolor="#CCCCCC">
		<td width="5%" align="center"><b>#</b></td>
		<td width="13%" align="center"><b>วันที่ใบเสร็จ</b></td>
		<td width="12%" align="center"><b>เลขที่ใบเสร็จ</b></td>
		<td width="19%" align="center"><b>จำนวนเงินรวม</b></td>
		<td width="17%" align="center"><b>ชำระเป็นเงินสด</b></td>
		<td width="17%" align="center"><b>ชำระเป็นเช็ค</b></td>
		<td width="17%" align="center"><b>ชำระเป็นส่วนลด</b></td>	
	</tr>';
	
	/*
	select inv_no,inv_date
(SELECT a.inv_no,a.inv_date,B.name,a.amount,a.status FROM "VOtherpay2" A 
								LEFT JOIN "Services" B on A.service_id = B.service_id 
								WHERE A.o_receipt = '14N0106005' 
								AND A.o_receipt IS NOT NULL 
								AND B.constant_var IS NOT NULL 
								ORDER BY inv_no ASC)

union

(SELECT inv_no,inv_date,service_name,cus_amount,pay_method 
							FROM v_chq
							WHERE receipt_no = '14N0106005'
							AND accept = 'TRUE' AND is_pass = 'FALSE'
							GROUP BY inv_no,inv_date,service_name,cus_amount,pay_method
							ORDER BY inv_no ASC)*/
							
							
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
	$save_data[$m] .='
	<tr>
		<td>'.$a.'</td>
		<td>'.$res_payment['receipt_date'].'</td>
		<td>'.$res_payment['receipt_no'].'</td>
		<td align="right">'.number_format($res_payment['amount'],2).'</td>
		<td align="right">'.replace_empty_currency(number_format($res_payment['cash_amount'],2)).'</td>
		<td align="right">'.replace_empty_currency(number_format($res_payment['chq_amount'],2)).'</td>
		<td align="right">'.replace_empty_currency(number_format($res_payment['discount_amount'],2)).'</td>
	</tr>';
	}
	$save_data[$m] .= '
	</table>';

$save_data[$m] .='
<table cellpadding="2" cellspacing="0" border="1" width="100%" style="font-size:12.5;">
<tr  bgcolor="#CCCCCC">
		<td width="5%" align="center">#</td>
		<td width="13%" align="center"><b>เลขที่เช็ค</b></td>
		<td width="12%" align="center"><b>วันที่เช็ค</b></td>
		<td width="30%" align="center"><b>ธนาคาร</b></td>
		<td width="20%" align="center"><b>สาขา</b></td>
		<td width="20%" align="center"><b>จำนวนเงิน</b></td>
		
	</tr>';
						
//*************************************	ดึงข้อมูลเช็ค ********************************************************//												
	$qry_chq = pg_query(" SELECT * FROM v_cashier_pay_chq 
						  WHERE res_id ='$res_id' AND accept = 'TRUE' AND is_pass = 'FALSE' 
						  ORDER BY receipt_no ASC ");
	$rows=0;
	$num_rows = pg_num_rows($qry_chq);
	if($num_rows == 0){
		$save_data[$m] .= '<tr><td colspan="6"></td></tr>';
	}
	while($res_chq = pg_fetch_array($qry_chq)){
		$rows++;
	$save_data[$m] .='
		<tr>
			<td>'.$rows.'</td>
			<td>'.$res_chq['cheque_no'].'</td>
			<td>'.$res_chq['date_on_cheque'].'</td>
			<td>'.$res_chq['bank_name'].'</td>
			<td>'.$res_chq['bank_branch'].'</td>
			<td align="right">'.number_format($res_chq['cus_amount'],2).'</td>
		</tr>';
	}
	
$save_data[$m] .= '	
</table>';
$save_data[$m] .='
<table style="font-size:11.5; "  width="100%" boder="1" >
	<tr>
		<td width="100%"><b>ทั้งนี้ '.$wording_type_buy.'รถ ได้รับรถยนต์ตามสภาพที่พอใจเรียบร้อยแล้ว ในวันนี้ </b></td>
	</tr>';
	
	if($res['reserve_color'] == "เขียวเหลือง"){
$save_data[$m] .='
	<tr>
		<td width="100%">&nbsp;&nbsp;&nbsp;&nbsp;อนึ่งหาก'.$wording_type_buy.'ต้องการจดทะเบียนเป็นแท็กซี่่ส่วนบุคคล (เขียวเหลือง) '.$wording_type_buy.'มีหน้าที่ นำใบประกอบการ เพื่อขอจดทะเบียน แท็กซี่มิเตอร์มามอบให้กับ'.$wording_type_buy.' เพื่อ'.$wording_type_buy.'จะได้นำใบประกอบการไปจดทะเบียนแท็กซี่มิเตอร์ส่วนบุคคลต่อไป
		</td>
	</tr>';
	}
	
	//(strpos($res['car_type_name'], "ป้ายแดง") !== false)
	
	if($res['use_radio'] == "t"){
		
		if((strpos($res['car_type_name'], "ป้ายแดง") !== false)){
			
		
			$save_data[$m] .= '	
				<tr>
					<td width="100%"><br>&nbsp;&nbsp;&nbsp;&nbsp; ';
			if($str_sale_type == "สด"){
				$save_data[$m] .= 'โดยผู้ซื้อจะได้รับสิทธิพิเศษฟรีค่าบริการวิทยุตลอดอายุสัญญาผ่อนชำระ เว้นแต่ผู้ซื้อทำการโอนสิทธิ์ ให้ถือสิทธิพิเศษดังกล่าวถือเป็นสิ้นสุด';
			}
			elseif($str_sale_type == "จัดผ่อน"){
				$save_data[$m] .= 'โดยผู้จะซื้อจะได้รับสิทธิพิเศษฟรีค่าบริการวิทยุตลอดอายุสัญญาผ่อนชำระ เว้นแต่ผู้จะซื้อทำการโอนสิทธิ์ ให้ถือสิทธิพิเศษดังกล่าวถือเป็นสิ้นสุด';
			}
			$save_data[$m] .= '	
					</td>
				</tr>';
		}
		else{
			$save_data[$m] .= '	
				<tr>
					<td width="100%">
			';
			
			if($str_sale_type == "สด"){
				$save_data[$m] .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;ผู้ซื้อมีหน้าที่ชำระค่าวิทยุสื่อสาร เดือนละ 342.40 บาท ทุกๆ เดือน เริ่มชำระวันออกรถ และผู้ซื้อมีหน้าที่ชำระพร้อมนำรถยนต์ไป
ตรวจมิเตอร์ทุกๆ 6 เดือนพร้อมต่อภาษีประจำปี';
			}
			elseif($str_sale_type == "จัดผ่อน"){
				$save_data[$m] .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;ผู้จะซื้อมีหน้าที่ชำระค่าวิทยุสื่อสาร เดือนละ 342.40 บาท ทุกๆ เดือน เริ่มชำระวันออกรถ และผู้จะซื้อมีหน้าที่ชำระพร้อมนำรถยนต์ไป
ตรวจมิเตอร์ทุกๆ 6 เดือนพร้อมต่อภาษีประจำปี';
			}
			$save_data[$m] .= '	
					</td>
				</tr>';
		}
		
	}
	
$save_data[$m] .='
	
</table>';

$save_data[$m] .= '
<table width="100%" style="font-size:11.5; " >
	<tr>
		<td><b>หมายเหตุ </b>&nbsp;&nbsp;'.$res['remark'].'</td>
	</tr>
	<tr>
		<td>เพื่อเป็นหลักฐานจึงได้ทำเป็นหนังสือสัญญาไว้ต่อหน้าพยาน</td>
	</tr>
</table>
';

$save_data[$m] .= '
<table cellpadding="1" cellspacing="0" border="1" width="100%" style="font-size:smaller;">
	<tr>
		<td align="center" width="22%">
			<table cellpadding="1" cellspacing="0" border="0" width="250px" style="font-size:smaller;" align="center">
				<tr><td></td></tr>
				<tr align="center">
					<td width="120px" colspan="2">ลงชื่อ _____________________ '.$wording_type_buy.'</td>
				</tr>
				<tr align="center">
					<td width="120px" colspan="2">(___'.$res['cus_name'].'___)</td>
				</tr>
				<tr align="center">
					<td width="120px" colspan="2">วันที่_____________________</td>
				</tr>
			</table>
		</td>
		<td align="center" width="19%">';
		$qry_sale = pg_query("SELECT  fullname FROM v_users WHERE id_user = '$sale' ");
		if($res_sale = pg_fetch_array($qry_sale)){}
		
		$save_data[$m] .='
			<table cellpadding="1" cellspacing="0" border="0" width="250px" style="font-size:smaller;" align="center">
				<tr><td></td></tr>
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
		<td align="center" width="19%">';
		
			$save_data[$m] .='
			<table cellpadding="1" cellspacing="0" border="0" width="250px" style="font-size:smaller;" align="center">
				<tr><td></td></tr>
				<tr align="center">
					<td width="120px" colspan="2">ลงชื่อ _________________ ผู้รับเงิน</td>
				</tr>
				<tr align="center">
					<td width="120px" colspan="2">(______________________)</td>
				</tr>
				<tr align="center">
					<td width="120px" colspan="2">วันที่_____________________</td>
				</tr>
			</table>
		</td>
		<td align="center" width="20%">';
		
		$qry_witness = pg_query("SELECT  fullname FROM v_users WHERE id_user = '$witness' ");
		if($res_witness = pg_fetch_array($qry_witness)){}

	$save_data[$m] .='
			<table cellpadding="1" cellspacing="0" border="0" width="250px" style="font-size:smaller;" align="center">
				<tr><td></td></tr>
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
		<td align="center" width="20%">
			<table cellpadding="1" cellspacing="0" border="0" width="250px" style="font-size:smaller;" align="center">
				<tr><td></td></tr>
				<tr align="center">
					<td width="120px" colspan="2">ลงชื่อ ___________________ ผู้อนุมัติ</td>
				</tr>
				<tr align="center">
					<td width="120px" colspan="2">(________________________)</td>
				</tr>
				<tr align="center">
					<td width="120px" colspan="2">วันที่_____________________</td>
				</tr>
			</table>
		</td>
	</tr>
</table>';
}

$footer_text = '
	<table style="font-size:13" border="1">
		<tr>
			<td width="323">
				*** เอกสารฉบับนี้ไม่ใช่ใบเสร็จรับเงิน กรุณาเรียกใบรับเงินชั่วคราวจากพนักงานทุกครั้ง *** 
			</td>
		</tr>
	</table>
';

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
	
		//$res_id = $_REQUEST['res_id'];
		$idno = pg_escape_string($_REQUEST['idno']);

		
		$this->write2DBarcode($idno, 'QRCODE,H', 160, 275, 10, 10, $style, 'N');
		$this->write2DBarcode($res['res_id'], 'QRCODE,H', 175, 275, 10, 10, $style, 'N');
		
		
		
		
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
$pdf->SetFont('CordiaUPC', '', 14); //AngsanaUPC  CordiaUPC


/*$pdf->AddPage();
$pdf->writeHTML($save_data, true, false, true, false, '');*/

for($k=0;$k<=3;$k++){
	$pdf->AddPage();
	$pdf->writeHTML($save_data[$k], true, false, true, false, '');
	
	// For make the header.
	$pdf->writeHTMLCell(0, 0, 80, 10, $footer_text);
	
	// For make the Footer.
	// $pdf->writeHTMLCell(0, 0, 40, 278, $footer_text);
}

$pdf->Output('car_takeout_'.$res_id.'.pdf', 'I');

?>
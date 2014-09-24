<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$res_id = $_REQUEST['res_id'];

if(empty($res_id )){
    echo "invalid param.";
    exit;
}
$qry = pg_query("SELECT * FROM v_reserve WHERE res_id='$res_id' ");
if($res = pg_fetch_array($qry)){
	
	$product_id = $res['product_id'];
	
	if($res['num_install'] == '0'){
		$sale_type_title = "ซื้อสด"; $str_sale_type = "สด"; $receive_car = "นัดชำระเงินค่ารถยนต์คงค้างพร้อมรับรถยนต์ในวันที่"; $str_down_remain = "ค่ารถยนต์คงค้าง";
	}else{ $sale_type_title = "ซื้อผ่อน"; $str_sale_type = "จัดผ่อน"; $receive_car = "นัดชำระเงินดาวน์คงค้างพร้อมรับรถยนต์ในวันที่"; $str_down_remain = "เงินดาวน์คงค้าง";
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
	/*$qry_reserve_amount = pg_query(" SELECT res_id,service_id,name as service_name,o_receipt,pay_amount
								FROM
								(SELECT  
								  \"Invoices\".res_id, 
								  \"InvoiceDetails\".amount, 
								  \"InvoiceDetails\".vat, 
								  \"InvoiceDetails\".service_id, 
								  \"OtherpayDtl\".inv_no, 
								  \"OtherpayDtl\".amount as pay_amount, 
								  \"OtherpayDtl\".o_receipt, 
								  \"Services\".name
								FROM 
								  public.\"InvoiceDetails\", 
								  public.\"Invoices\", 
								  public.\"OtherpayDtl\", 
								  public.\"Otherpays\", 
								  public.\"Services\"
								WHERE 
								  \"InvoiceDetails\".inv_no = \"Invoices\".inv_no AND
								  \"OtherpayDtl\".o_receipt = \"Otherpays\".o_receipt AND
								  \"OtherpayDtl\".inv_no = \"InvoiceDetails\".inv_no AND
								  \"OtherpayDtl\".o_receipt = \"Otherpays\".o_receipt AND
								  \"Services\".service_id = \"InvoiceDetails\".service_id

								  union

								SELECT  
								  \"Invoices\".res_id, 
								  \"InvoiceDetails\".amount, 
								  \"InvoiceDetails\".vat, 
								  \"InvoiceDetails\".service_id, 
								  \"ChequeDetails\".inv_no, 
								  \"ChequeDetails\".cus_amount as pay_amount, 
								  \"ChequeDetails\".receipt_no,
								   \"Services\".name
								FROM 
								  public.\"InvoiceDetails\", 
								  public.\"Invoices\", 
								  public.\"ChequeDetails\", 
								  public.\"Services\"
								WHERE 
								  \"InvoiceDetails\".inv_no = \"Invoices\".inv_no AND
								  \"Invoices\".inv_no = \"ChequeDetails\".inv_no AND
								  \"Services\".service_id = \"InvoiceDetails\".service_id)payment
							WHERE  (service_id = 'S002' or service_id = 'S003' or service_id = 'S004') AND res_id = '$res_id'
							 ");		*/				
							
 $tmp_reserve_amount = 0;					
while($res_reserve_amount = pg_fetch_array($qry_reserve_amount)){
	$tmp_reserve_amount += $res_reserve_amount['amount'];
}
for($m=0;$m<=2;$m++){
	if($m != 0){
		$pageName = "(สำเนา)";
	} else {
		$pageName = "ต้นฉบับ(สำหรับลูกค้า)";
	}
	
$save_data[$m] .= '
<table cellpadding="1" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="10%"><img src="../images/logo.jpg" border="0" width="50" height="50" ></td>
		<td width="90%" style="font-size:smaller; text-align:left">
			บริษัท ที.เอ.โอโตเซลส์  จำกัด 
			<br/>
			<table >
				<tr>
					<td width="50%">
						T.A. AUTOSALES CO.,LTD.
					</td>
					<td width="50%" valign="top" style="font-size:smaller; text-align:right">
					
						<b>'.$pageName.'</b>
						
					</td>
				</tr>
			</table>
			สำนักงานใหญ่ 555 ถนนนวมินทร์ แขวงคลองกุ่ม เขตบึงกุ่ม กรุงเทพมหานคร 10240 โทรศัพท์ 0-2744-2222 โทรสาร 0-2379-1111 <br />เลขประจำตัวผู้เสียภาษี 0105546153597
		</td>
	</tr>
</table>
<span style="font-weight:bold; font-size:medium; text-align:left"><b>ใบจองรถยนต์ ('.$sale_type_title.')</b></span>&nbsp;
<span style="font-weight:bold; font-size:medium; text-align:right"><b>***วันรับรถชำระเป็น แคชเชียร์เช็ค สั่งจ่ายในนาม บริษัท ที.เอ.โอโตเซลส์ จำกัด เท่านั้น*** </b></span>
<hr color= "red" size="10">
<br><p>
<table  border="0" width="100%">
	<tr>
		<td style="font-weight:bold; font-size:medium; text-align:left"><b>เลขที่จอง  &nbsp;&nbsp;'.$res['res_id'].'</b></td>
		<td style="font-weight:bold; font-size:medium; text-align:right"><b>วันที่จอง  &nbsp;&nbsp;'.$res['reserve_date'].'</b></td>
	</tr>
</table>
<br><br>
<table cellpadding="2" cellspacing="0" border="1" width="100%" style="font-size:smaller;" >
	<tr>
		<td width="15%">ชื่อผู้จอง </td>
		<td width="35%">'.$res['cus_name'].'&nbsp;&nbsp;';

		if($res['branch_id'] == '0'){$str_branch = 'สำนักงานใหญ่';}else{$str_branch = 'สาขาที่  &nbsp;&nbsp;'.$res['branch_id'];}
		if($res['cus_type'] == 2){$save_data[$m] .= $str_branch.'</td>';
		}else{$save_data[$m] .='</td>';}
		
		$save_data[$m] .='
		<td width="15%">ชื่อผู้จดทะเบียน </td>
		<td width="35%">'.$res['reg_customer'].'</td> 
	</tr>
	<tr>';
	if( $res['cus_type'] == 2 ){$str_address = 'ภ.พ. 20';}else{$str_address = 'บัตรประชาขน';}
	$save_data[$m] .='
		<td>ที่อยู่ตาม<br>'.$str_address.'</td>
		<td>'.$res['address'].'<br>&nbsp;'.$res['add_post'].'</td>
		<td>ที่อยู่ที่ติดต่อได้</td>
		<td>'.$res['contract_add'].'&nbsp;'.$res['contract_post'].'  </td>
	</tr>
	<tr>
		<td>ประเภทบัตร</td>';
		if($res['card_type'] == 'บัตรผู้เสียภาษีอากร'){$card_type = 'บัตรประจำตัวผู้เสียภาษีอากร';}else{$card_type = $res['card_type'];}
		$save_data[$m] .='
		<td>'.$card_type.' &nbsp;&nbsp; เลขที่ '.$res['card_id'].'</td>
		<td>โทรศัพท์</td>
		<td>'.$res['telephone'].'</td>
	</tr>
	<tr>
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
		<td>'.replace_empty_txt($res_finance_name['finance_name']).'</td>
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
		<td colspan="2"><b>เงินดาวน์/เงินจองที่รับชำระ</b>      '.number_format($tmp_reserve_amount,2).'</td>
	</tr>';
	
	$down_balance = ($res['down_price'] - $tmp_reserve_amount );
	$save_data[$m] .='
	<tr>
		<td><b>'.$str_down_remain.' </b></td>
		<td><b>'.number_format($down_balance,2).'</b></td>
		<td  colspan="2" ><b>จำนวน    </b>'.$res['num_install'].'<b>    งวด      ค่าผ่อนต่องวด  </b>'.replace_empty_txt(number_format($res['installment'],2)).'</td>
	</tr>
	<tr>
		<td colspan="2"><b>'.$receive_car.'</b></td>
		<td colspan="2">&nbsp;<b>'.$res['receive_date'].'</b></td>
  </tr>
</table>';

// ตั้งไว้แต่ยังไม่มีการรับชำระ								
	$qry_othery_pay = pg_query("SELECT 
								  inv.res_id, 
								  inv.inv_no, 
								  inv.cancel, 
								  s.name as service_name, 
								 (inv_detail.amount + inv_detail.vat)as amount
								FROM public.\"InvoiceDetails\" inv_detail
								LEFT JOIN public.\"Invoices\" inv ON inv.inv_no = inv_detail.inv_no
								LEFT JOIN public.\"Services\" s ON s.service_id = inv_detail.service_id
								WHERE inv.cancel= 'FALSE'
								AND inv_detail.service_id NOT IN ('S002','S003','S004')
								AND substr(inv.inv_no,3,1) = 'I'
								AND inv.res_id = '$res_id'
								ORDER BY inv.inv_no ASC"); 
								
	$j=0;
	$total_amount = 0;
	$num_rows = pg_num_rows($qry_othery_pay);
	if($num_rows != 0){
		$save_data[$m] .='
		<table cellpadding="2" cellspacing="0" border="1" width="100%" style="font-size:smaller;">
			<tr  bgcolor="#CCCCCC">
				<td width="10%"><b>ลำดับที่</b></td>
				<td width="70%"><b>ค่าใช้จ่ายอื่นๆ (ชำระภายในวันรับรถ)</b></td>
				<td width="20%"><b>จำนวนเงิน</b></td>
			</tr>';				
			
			while($res_other_pay = pg_fetch_array($qry_othery_pay)){
				$j++;
				$total_amount += $res_other_pay['amount'];
				
			$save_data[$m] .='
			<tr>
				<td align="center">'.$j.'</td>
				<td align="left">'.$res_other_pay['service_name'].'</td>
				<td align="right">'.number_format($res_other_pay['amount'],2).'</td>
			</tr>';
			}
			$save_data[$m] .='
			<tr>
				<td colspan="2" align="right"><b>รวมค่าใช้จ่ายอื่นๆ</b></td>
				<td align="right"><b>'.number_format($total_amount,2).'</b></td>
			</tr></table>';
	}
	
	$save_data[$m] .='
	<table cellpadding="2" cellspacing="0" border="1" width="100%" style="font-size:smaller;">
	<tr bgcolor="#CCCCCC">
		<td colspan="6" align="left">รายละเอียดการรับชำระเงินวันจองรถ</td>
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
	$qry_payment = pg_query("SELECT receipt_date,receipt_no,(cash_amount + chq_amount + discount_amount)as amount,cash_amount,chq_amount,discount_amount
								FROM receipt_tmp 
								WHERE res_id = '$res_id' 
								AND status <> '0' ");						   
	$a=0;
	$total_payment = 0;
	$num_rows = pg_num_rows($qry_payment);
	if($num_rows == 0){
	$save_data[$m] .='<tr><td colspan = "6"></td></tr>';
	}
	while($res_payment = pg_fetch_array($qry_payment)){
		$a++;
		$total_payment += $res_payment['amount'];
	$save_data[$m] .='
	<tr>
		<td>'.$a.'</td>
		<td>'.$res_payment['receipt_date'].'</td>
		<td>'.$res_payment['receipt_no'].'</td>
		<td align="right">'.replace_empty_currency(number_format($res_payment['amount'],2)).'</td>
		<td align="right">'.replace_empty_currency(number_format($res_payment['cash_amount'],2)).'</td>
		<td align="right">'.replace_empty_currency(number_format($res_payment['chq_amount'],2)).'</td>
		<td align="right">'.replace_empty_currency(number_format($res_payment['discount_amount'],2)).'</td>
	</tr>';
	}
	$save_data[$m] .= '
	</table>';
		
	//*************************************	ดึงข้อมูลเช็ค ********************************************************//							
	$qry_chq = pg_query("SELECT distinct 
							\"Cheques\".bank_name, 
							\"Cheques\".bank_branch, 
							\"Cheques\".amt_on_cheque, 
							\"Cheques\".cheque_no, 
							\"Cheques\".date_on_cheque							
						 FROM 
							public.\"ChequeDetails\", 
							public.\"Cheques\"
						WHERE 
							\"ChequeDetails\".running_no = \"Cheques\".running_no AND
							\"ChequeDetails\".cheque_no = \"Cheques\".cheque_no
						AND \"ChequeDetails\".res_id = '$res_id' ");
	
	$rows_chq = 0;	
	$num_rows = pg_num_rows($qry_chq);	
	
	if($num_rows != 0){	
		$save_data[$m] .='
		<table cellpadding="2" cellspacing="0" border="1" width="100%" style="font-size:30px;">
			<tr bgcolor="#CCCCCC">
				<td width="10%" align="center"><b>#</b></td>
				<td width="15%" align="center"><b>เลขที่เช็ค</b></td>
				<td width="15%" align="center"><b>วันที่เช็ค</b></td>
				<td width="25%" align="center"><b>ธนาคาร</b></td>
				<td width="25%" align="center"><b>สาขา</b></td>
				<td width="10%" align="center"><b>จำนวนเงิน</b></td>
			</tr>';
		
		while($res_chq = pg_fetch_array($qry_chq)){
			$rows_chq++;
		$save_data[$m] .='
			<tr>
				<td align = "center">'.$rows_chq.'</td>
				<td align = "center">'.$res_chq['cheque_no'].'</td>
				<td align = "center">'.$res_chq['date_on_cheque'].'</td>
				<td>'.$res_chq['bank_name'].'</td>
				<td>'.$res_chq['bank_branch'].'</td>
				<td align = "right">'.number_format($res_chq['amt_on_cheque'],2).'</td>
			</tr>';
		}

		$save_data[$m] .= '</table>';
}

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
	$num_rows = pg_num_rows($qry_gif_detail);
	if($num_rows != 0){
		$save_data[$m] .='	
		<table cellpadding="2" cellspacing="0" border="1" width="100%" style="font-size:smaller;">
			<tr  bgcolor="#CCCCCC">
				<td width="10%"><b>ลำดับที่</b></td>
				<td width="80%"><b>รายการของแถม</b></td>
				<td width="10%"><b>จำนวน</b></td>
			</tr>';
			
			
			while($res_gif_detail = pg_fetch_array($qry_gif_detail)){
				$row_gif++;
				
			$save_data[$m] .='
			<tr>
				<td>'.$row_gif.'</td>
				<td>'.$res_gif_detail['product_name'].'</td>
				<td align="right">'.$res_gif_detail['amount'].'</td>
			</tr>';
			}
			$save_data[$m] .='
		</table>';
	}

$save_data[$m] .='
<table style="font-size:22px"  width="100%" boder="1" >
	<tr>
		<td width="100%"><b>เงื่อนไขและการสงวนสิทธิ์ของการขาย </b><br>
			&nbsp;&nbsp;1.) การซื้อรถยนต์ จะมีผลต่อเมื่อผู้ขายได้รับการชำระเงินเรียบร้อยแล้ว 
			&nbsp;&nbsp;2.) ราคารถยนต์อาจมีการเปลี่ยนแปลงได้  โดยทางผู้ขายจะแจ้งให้ทราบก่อนวันออกรถ 
			&nbsp;&nbsp;3.) ถ้าผู้จองมีความประสงค์ยกเลิกการสั่งซื้อ  ผู้จองไม่สามารถเรียกร้องเงินคืนได้ <br>
			&nbsp;&nbsp;4.) ถ้าผู้จองรถยนต์ไม่มารับรถภายในกำหนด  หลังจากได้รับใบแจ้งทางผู้ขาย  ถือว่าผู้จองสละสิทธิ์ไม่รับรถ  และจะเรียกร้องเงินจองที่ชำระไว้คืนมิได้ และให้ผู้จะขายสามารถขายรถยนต์ดังกล่าวต่อบุคคลภายนอกได้ทันที
			<br />&nbsp;&nbsp;5.) ในกรณีที่ผู้ขายไม่สามารถจัดหารถเพื่อส่งมอบให้ลูกค้าได้ ทางผู้ขาย ยินดีคืนเงินจองให้แก่ลูกค้า 
		</td>
	</tr>
	';
	if($res['reserve_color'] == "เขียวเหลือง"){
$save_data[$m] .='
	<tr>
		<td width="100%"><br>* อนึ่งหากผู้จะซื้อต้องการจดทะเบียนเป็นแท็กซี่่ส่วนบุคคล (เขียวเหลือง) ผู้จะซื้อมีหน้าที่นำใบประกอบการเพื่อขอจดทะเบียนแท็กซี่มิเตอร์มามอบให้กับผู้จะขาย
			เพื่อผู้จะขายจะได้นำใบประกอบการไปจดทะเบียนแท็กซี่มิเตอร์ส่วนบุคคลต่อไป
		</td>
	</tr>';
	} 
	if($res['use_radio'] == "t"){
		if(strpos($car_type_name, "ป้ายแดง") !== false){
			$save_data[$m] .= '	
				<tr>
					<td width="100%"><br>* โดยผู้จะซื้อจะได้รับสิทธิพิเศษฟรีค่าบริการวิทยุตลอดอายุสัญญาผ่อนชำระ เว้นแต่ผู้จะซื้อทำการโอนสิทธิ์ ให้ถือสิทธิพิเศษด้งกล่าวถือเป็นสิ้นสุด 
					</td>
				</tr>';
		}
		else{
			$save_data[$m] .= '	
				<tr>
					<td width="100%"><br>* ผู้เช่าซื้อมีหน้าที่ชำระค่าวิทยุสื่อสาร เดือนละ 342.40 บาท ทุกๆ เดือน เริ่มชำระวันออกรถ และผู้จะซื้อมีหน้าที่ชำระพร้อมนำรถยนต์ไปตรวจมิเตอร์ทุกๆ 6 เดือนพร้อมต่อภาษีประจำปี 
					</td>
				</tr>';
		}	
	}
	
$save_data[$m] .='
	
</table>';

$save_data[$m] .='
<br><br><br>
<table width="100%" style="font-size:smaller; margin-top: 50px;">
	<tr>
		<td><b>หมายเหตุ </b>&nbsp;&nbsp;'.$res['remark'].'</td>
	</tr>
</table>
<span style="font-size:smaller;" >เพื่อเป็นหลักฐานจึงได้ทำเป็นหนังสือสัญญาไว้ต่อหน้าพยาน</span>
<br />
<table cellpadding="1" cellspacing="0" border="1" width="100%" style="font-size:smaller;">
	<tr>
		<td width="25%" align="center">
			<table cellpadding="1" cellspacing="0" border="0" width="100%" style="font-size:smaller;" align="center">
				<tr align="center">
					<td width="100%" colspan="2">ลงชื่อ _________________________ ผู้จอง</td>
				</tr>
				<tr align="center">
					<td width="100%" colspan="2">('.$res['cus_name'].')</td>
				</tr>
				<tr align="center">
					<td width="100%" colspan="2">วันที่___________________________</td>
				</tr>
			</table>
		</td>
		<td width="25%">';
		$qry_sale = pg_query("SELECT  fullname FROM v_users WHERE id_user = '$sale' ");
		if($res_sale = pg_fetch_array($qry_sale)){}
		
		$save_data[$m] .='
			<table cellpadding="1" cellspacing="0" border="0" width="100%" style="font-size:smaller;" align="center">
				<tr align="center">
					<td width="100%" colspan="2">ลงชื่อ _____________________ พนง.ขาย</td>
				</tr>
				<tr align="center">
					<td width="100%" colspan="2">('.$res_sale['fullname'].')</td>
				</tr>
				<tr align="center">
					<td width="100%" colspan="2">วันที่___________________________</td>
				</tr>
			</table>
		</td>
		<td width="25%">';
		
		$qry_witness = pg_query("SELECT  fullname FROM v_users WHERE id_user = '$witness' ");
		if($res_witness = pg_fetch_array($qry_witness)){}

	$save_data[$m] .='
			<table cellpadding="1" cellspacing="0" border="0" width="100%" style="font-size:smaller;" align="center">
				<tr align="center">
					<td width="100%" colspan="2">ลงชื่อ _________________________ พยาน</td>
				</tr>
				<tr align="center">
					<td width="100%" colspan="2">(_________________'.$res_witness['fullname'].'________________)</td>
				</tr>
				<tr align="center">
					<td width="100%" colspan="2">วันที่___________________________</td>
				</tr>
			</table>
		</td>
		<td width="25%">
			<table cellpadding="1" cellspacing="0" border="0" width="100%" style="font-size:smaller;" align="center">
				<tr align="center">
					<td width="100%" colspan="2">ลงชื่อ _______________________ ผู้อนุมัติ</td>
				</tr>
				<tr align="center">
					<!-- <td width="120px" colspan="2">('.$_SESSION["ss_username"].')</td> -->
					<td width="100%" colspan="2">(_________________________________)</td>
				</tr>
				<tr align="center">
					<td width="100%" colspan="2">วันที่___________________________</td>
				</tr>
			</table>
		</td>
	</tr>
</table>


';
}


$header_text = '
<table>
	<tr>
		<td width="60px">
			
		</td>
		<td width="320px" border="1">
			*** เอกสารฉบับนี้ไม่ใช่ใบเสร็จรับเงิน กรุณาเรียกใบรับเงินชั่วคราวจากพนักงานทุกครั้ง ***
		</td>
	</tr>
</table>
';


$footer_text = '
	<table style="font-size:13" border="1">
		<tr>
			<td width="323">
				*** เอกสารฉบับนี้ไม่ใช่ใบเสร็จรับเงิน กรุณาเรียกใบรับเงินชั่วคราวจากพนักงานทุกครั้ง *** 
			</td>
		</tr>
	</table>
';

	$print_count = print_count('$res_id','1');
	
	//บันทึกประวัติการพิมพ์
	$in_doc_print_logs = "INSERT INTO doc_print_logs (doc_no,doc_type,print_count,id_user,print_date)
									 VALUES('$res_id','1','$print_count','$_SESSION[ss_iduser]','$nowDateTime')";
	if(!$res=@pg_query($in_doc_print_logs)){
       $txt_error[] = "INSERT doc_print_logs ไม่สำเร็จ $in_doc_print_logs";
       $status++;
    }
	
	$_SESSION['ss_print_count'] = $print_count;
		
//START PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
	public function Header(){
		/*$image_file = K_PATH_IMAGES.'logo.jpg';
		$this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		$this->SetFont('AngsanaUPC', '', 14);
		$txt_company_name = 'บริษัท ที.เอ.โอโตเซลส์  จำกัด  ';
		$txt_company_name_eng = 'T.A. AUTOSALES CO.,LTD.';
		$txt_address = 'สำนักงานใหญ่ 555 ถนนนวมินทร์ แขวงคลองกุ่ม เขตบึงกุ่ม กรุงเทพมหานคร 10240 โทรศัพท์ 0-2744-2222 โทรสาร 0-2379-1111 <br>
			เลขประจำตัวผู้เสียภาษี 0105546153597';
		
		$this->MultiCell(0, 50, $txt_company_name, 0, 'L', 0, 0, '', '', true);
		$this->MultiCell(0, 50, $txt_company_name_eng, 0, 'L', 0, 0, '', '', true);*/
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
        $this->MultiCell(55, 0,'ครั้งที่พิมพ์ : '.$_SESSION['ss_print_count'].'         วันที่พิมพ์ : '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);

        // $this->MultiCell(55, 0,'ครั้งที่พิมพ์ : '.$_SESSION['ss_print_count'].'         วันที่พิมพ์ : '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
		$this->MultiCell(55, 0, 'ชื่อผู้พิมพ์ : '.$_SESSION["ss_username"], 0, 'R', 0, 0, '', '', true);
		$this->MultiCell(80, 0, 'หน้าที่'.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
		
		$res_id = $_REQUEST['res_id'];
		$this->write2DBarcode($res_id, 'QRCODE,H', 190, 275, 10, 10, $style, 'N');
		
		
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(true);// false => not used
$pdf->setPrintFooter(true);

//set margins
$pdf->SetMargins(10, 10, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 20); //default 10

// set font
$pdf->SetFont('AngsanaUPC', '', 16); //AngsanaUPC  CordiaUPC

for($k=0;$k<=2;$k++){
	
	$pdf->AddPage();
	$pdf->writeHTML($save_data[$k], true, false, true, false, '');
	
	$pdf->writeHTMLCell(0, 0, 75, 10, $footer_text);
	
	// For make the Footer.
	$pdf->writeHTMLCell(0, 0, 50, 270, $footer_text);
	
}

$pdf->Output('reserve_car_'.$res_id.'.pdf', 'I');
?>
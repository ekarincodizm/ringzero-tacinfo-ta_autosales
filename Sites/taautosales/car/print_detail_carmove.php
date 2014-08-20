<?php
include_once("../include/config.php");
include_once("../include/function.php");

$auto_id = pg_escape_string($_GET['auto_id']);
$idno = pg_escape_string($_GET['idno']);

$qry = pg_query("select * from \"CarMoveToCus\" where auto_id='$auto_id' ");
if($res = pg_fetch_array($qry)){
	$res_id = $res['res_id'];
}

$qry = pg_query("SELECT * FROM v_reserve WHERE res_id='$res_id' ");
if($res_v = pg_fetch_array($qry)){
	
	$num_install = $res_v['num_install'];
	$down_price = $res_v['down_price'];	
}

$qry_reserv = pg_query("select * from \"Reserves\" where res_id='$res_id' ");
if($res_r = pg_fetch_array($qry_reserv)){
	$remark = $res_r['remark'];
}

$sum_remain = 0;
$qry_reserve_amount = pg_query("SELECT pay_amount FROM v_down_balance WHERE res_id = '$res_id'");						
							
$tmp_reserve_amount = 0;					
while($res_reserve_amount = pg_fetch_array($qry_reserve_amount)){
	$tmp_reserve_amount += $res_reserve_amount['pay_amount'];
	}
	if($down_price==0){
		$sum_remain= 0;
	}else{
		$sum_remain=( $down_price - $tmp_reserve_amount );
	}
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <title><?php echo $company_name; ?> - <?php echo $page_title; ?></title>
    <LINK href="../images/styles.css" type=text/css rel=stylesheet>

    <link type="text/css" href="../images/jqueryui/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="../images/jqueryui/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="../images/jqueryui/js/jquery-ui-1.8.16.custom.min.js"></script>

</head>
<body>
	<input type="hidden" name="install" id="install" value="<?php if($num_install != 0) echo "Y" ?>" />
	<div>
		<table cellpadding="1" cellspacing="10" align="center">
		<?php if($num_install!= 0) { ?>
			<tr >
				<td><b>วันที่ไฟแนนซ์อนุมัติ :</b></td>
				<td><input type="text" name="financeDate" id="financeDate" /></td>
			</tr>
            
            <tr >
				<td><b>จำนวนวันที่นัดชำระงวดแรก  :</b></td>
				<td><input name="numDate" type="text" id="numDate"  onChange="calcu();" onKeyUp="calcu();" value="45" /> วัน</td>
			</tr>
            
			<tr>
				<td><b>วันที่นัดชำระงวดแรก</b></td>
				<td><input type="text" name="FpaymentDate" id="FpaymentDate" /></td>
			</tr>
			<tr>
				<td><b>นัดชำระค่างวดทุกวันที่</b></td>
				<td>
					<select name="first_due" id="first_due">
						<option value="">เลือกวันที่</option>
						<?php
							for($i=1;$i<=31;$i++){
								echo "<option value='$i'>$i</option>";
							}
						?>
					</select>
				</td>
			</tr>
		<?php } ?>
			<tr >
				<td><b>สรุปเงินคงค้างทั้งสิ้น :</b></td>
				<td><?php echo number_format($sum_remain,2); ?></td>
			</tr>
			<tr>
				<td><b>นัดชำระเงินคงค้างวันที่ :</b></td>
				<td><input type="text" name="paymentDate" id="paymentDate" <?php if($sum_remain == 0){ echo "disabled";}?>/></td>
			</tr>
			<tr >
				<td><b>หมายเหตุ :</b></td>
				<td><textarea name="remark" id="remark"><?php echo $remark;?></textarea></td>
			</tr>
			<!-- คอมเม้นไว้ยังไม่ได้ใช้ เพราะยังไม่มี process รองรับ
			<tr>
				<td colspan="2">
					<table>
						<tr>
							<td colspan="2"><b>รายการอุปกรณ์แถม :</b></td>
						</tr>
						<?php
						/*	$qry = pg_query("select * from gif_detail where res_id='$res_id' order by auto_run ");
							while($res = pg_fetch_array($qry)){
								$auto_run = $res['auto_run'];
								$product_id = $res['product_id'];
								$amount = $res['amount'];
								$flag = $res['flag'];
								
								$product_name = GetProductName($product_id);
								echo "<tr>";
									echo "<td><input type=\"checkbox\" name=\"equipt_chk[]\" id=\"equipt_chk\" value=\"$auto_run\" \></td>";
									echo "<td>$product_name</td>";
								echo "</tr>";
							} */
						?>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table>
						<tr>
							<td colspan="2"><b>รายการอุปกรณ์มาตรฐาน :</b></td>
						</tr>
						<?php
						/*	$qry = pg_query("select * from reserve_std_proc where res_id='$res_id' order by std_proc_id");
							while($res = pg_fetch_array($qry)){
								$auto_run = $res['auto_run'];
								$product_id = $res['std_proc_id'];
								$amount = $res['amount'];
								$flag = $res['flag'];
								
								$product_name = GetProductStdName($product_id);
								echo "<tr>";
									echo "<td><input type=\"checkbox\" name=\"std_chk[]\" id=\"std_chk\" value=\"$auto_run\" \></td>";
									echo "<td>$product_name</td>";
								echo "</tr>";
							} */
						?>
					</table>
				</td>
			</tr>
			-->
			<tr align="center">
				<td colspan="2" ><input type="button" name="print" id="print" value="พิมพ์ใบส่งมอบ"></td>
			</tr>
			
		</table>
	</div>
	
<script>
function popU(U,N,T) {
    newWindow = window.open(U, N, T);
}

function calcu(){
	 var startdate = $('#financeDate').val();
	
	 var numDate = parseInt($('#numDate').val());
	 
	
	var date2 = $('#financeDate').datepicker('getDate');
	var nextDayDate = new Date();
	nextDayDate.setDate(nextDayDate.getDate() + numDate); // นับจาก วันปัจจุบัน
	//nextDayDate.setDate(date2.getDate() + numDate); // นับจาก วันที่ไฟแนนซ์อนุมัติ
	
	 	var dd = nextDayDate.getDate();
 		 var mm = nextDayDate.getMonth()+1; //January is 0! 
  		 var yyyy = nextDayDate.getFullYear();
    if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm}
	// var nextDayDate = dd+'/'+mm+'/'+yyyy;
	  var nextDayDate = yyyy+'-'+mm+'-'+dd;
	

	    $('#FpaymentDate').val(nextDayDate);
		
	
			//alert(nextDayDate);
}


 $("#financeDate").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
		
	
		
});
 $("#FpaymentDate").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
});
 $("#paymentDate").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
});
$('#print').click(function(){
	/* คอมเม้นไว้ยังไม่ได้ใช้
	var chk_eq = new Array();
	$('#equipt_chk:checked').each(function(i){
          chk_eq[i] = $(this).val();
    });
	var chk_std = new Array();
	$('#std_chk:checked').each(function(i){
          chk_std[i] = $(this).val();
    }); */
	
	var fDate = $('#financeDate').val();
	var pDate = $('#paymentDate').val();
	var fpDate = $('#FpaymentDate').val();
	var first_due = $('#first_due').val();
	var sum_remain = '<?php echo $sum_remain; ?>'; // สรุปเงินคงค้างทั้งสิ้น
	
	if($('#install').val() == 'Y'){
		if(fDate != ""){
			if(chkDate(fDate) == 0){
			alert('ระบุวันที่ไม่ถูกต้อง');
			return false;
			}
		}

		if(fpDate != ""){
			if(chkDate(fpDate) == 0){
			alert('ระบุวันที่ไม่ถูกต้อง');
			return false;
			}
		}else{
			alert('กรุณาระบุวันที่นัดชำระงวดแรก');
			return false;
		}
	
		if(first_due == ""){
			alert('กรุณาเลือกชำระทุกวันที่');
			return false;
		}
		
		// ถ้า สรุปเงินคงค้างทั้งสิ้น มากกว่า 0 จะต้องระบุ นัดชำระเงินคงค้างวันที่ ด้วย
		if(parseFloat(sum_remain) > 0)
		{
			if(pDate != ""){
				if(chkDate(pDate) == 0){
					alert('ระบุวันที่ไม่ถูกต้อง');
					return false;
				}
			}else{
				alert('กรุณาระบุ นัดชำระเงินคงค้างวันที่');
				return false;
			}
		}
	}
	if(pDate != ""){
		if(chkDate(pDate) == 0){
			alert('ระบุวันที่ไม่ถูกต้อง');
			return false;
		}
	}

	var r = confirm("ยืนยันบันทึกข้อมูล!");
	
	if(r == true){
	$.post('process_print_carmove.php',{
		cmd : 'save',
		FDate : fDate,
		PDate : pDate,
		FPDate : fpDate,
		FirstDue : first_due,
		//equipt_chk : chk_eq,
		//std_chk : chk_std,
		remark : $('#remark').val(),
		auto_id : '<?php echo $auto_id;?>',
		idno: '<?php echo $idno; ?>'
	},function(data){
		if(data == 1){
			alert('บันทึกข้อมูลเรียบร้อยแล้ว');
			popU('../report/car_takeout_pdf.php?idno=<?php echo $idno; ?>','','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=980,height=550');
			location.reload();
		}else{
			alert(data);
			location.reload();
		}
	});
	}else{
		return false;
	}
});
function chkDate(datetxt){
	var str = datetxt;
	var Date_split = str.split("-");
	var chk = 0;
	if(Date_split.length!= 3){
		chk++;
	}else{
	
		var dtYear = parseInt(Date_split[0]);  
		var dtMonth = parseInt(Date_split[1]);
		var dtDay = parseInt(Date_split[2]);
		
		if(isNaN(dtYear) == true){
			chk++;
		}
		if(isNaN(dtMonth) == true){
			chk++;
		}
		if(isNaN(dtDay) == true){
			chk++;
		}
			
		if (dtMonth < 1 || dtMonth > 12){
			chk++;
		}else if (dtDay < 1 || dtDay> 31) {
			chk++;
		}else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31) {
			chk++;
		} else if (dtMonth == 2) {
			var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
			if (dtDay> 29 || (dtDay ==29 && !isleap)) 
            chk++;
		}
	}

	if(chk>0){
		return 0;
	}else{
		return 1;
	}
}

// คำนวณ วันที่นัดชำระงวดแรก เบื่องต้นไว้ให้ก่อนเลย
calcu();
</script>
<body>
</html>
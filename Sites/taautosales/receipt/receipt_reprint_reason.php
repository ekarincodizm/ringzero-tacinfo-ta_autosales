<?php
include_once("../include/config.php");
$res_id = pg_escape_string($_GET['res_id']);
$receipt_no = pg_escape_string($_GET['receipt_no']);
	
	//ช่องทางการชำระเงิน
	$qry_money_type = pg_query(" SELECT money_type
							FROM \"Otherpays\"
							WHERE o_receipt = '$receipt_no' ");
	
	if($res_money_type = pg_fetch_array($qry_money_type)){
		$money_type = $res_money_type['money_type'];
	}
	
/*	if($money_type == 'CA'){
		$qry = pg_query(" SELECT inv_no FROM \"OtherpayDtl\" WHERE o_receipt = '$receipt_no' ");
	}else{
		$qry = pg_query(" SELECT inv_no FROM \"ChequeDetails\" WHERE receipt_no = '$receipt_no' ");
	}   */
	
	$qry = pg_query("SELECT i.inv_no , i.res_id , o.o_receipt FROM \"Invoices\" i,\"OtherpayDtl\" o
	WHERE o.inv_no = i.inv_no and o_receipt = '$receipt_no'	UNION
	SELECT i.inv_no , i.res_id , c.receipt_no FROM  \"Invoices\" i, \"ChequeDetails\" c
	WHERE c.inv_no = i.inv_no and c.receipt_no = '$receipt_no'");
  	
	$arr_inv_no = array();					
	while($res = pg_fetch_array($qry)){
		$arr_inv_no[] = $res['inv_no'];
	}
	$tmp_inv_no = implode(",", $arr_inv_no);
?>
<table>
	<tr>
		<td><label>เหตุผล :</label></td>
		<td><textarea name="txt_reason" id="txt_reason" rows="6" cols="40"></textarea></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="button" name="btn_save" id="btn_save" value="บันทึก"></td>	
	</tr>
</table>
<script>
$(document).ready(function(){
});

$('#btn_save').click(function(){

	if( $('#txt_reason').val()  == "" ){
		alert('กรุณาระบุ  เหตุผลในการขอพิมพ์สำเนาใบเสร็จรับเงิน');
		return false;
	}
    $.post('save_receipt_reprint_api.php',{
        cmd: 'save_tax_rec_reprint_reason',
		doc_no: '<?php echo $receipt_no; ?>',
		reason: $('#txt_reason').val()
    },
    function(data){
        if(data.success){
			var txt_res_id = '<?php echo $res_id; ?>';
			var txtchkresid = txt_res_id.substring(0,2);			
            alert(data.message);
			$('#div_dialog').remove();			
			if(txtchkresid=='RP'){
				print_other_pay('<?php echo $tmp_inv_no; ?>','<?php echo $res_id; ?>','<?php echo $receipt_no; ?>');
			}
			else
			{
				print_receipt('<?php echo $tmp_inv_no; ?>','<?php echo $res_id; ?>','<?php echo $receipt_no; ?>');
								
			}		
        }else{
            alert(data.message);
        }
    },'json');
});

//==================== พิมพ์ใบเสร็จรับเงินชั่วคราว ========================//
function print_receipt(inv_no,res_id,receipt_no){
    $('body').append('<div id="div_prt"></div>');
    $('#div_prt').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br/><br/> <input type=\"button\" name=\"btn_print_receive\" id=\"btn_print_receive\" value=\"พิมพ์ใบเสร็จรับเงินชั่วคราว\" onclick=\"javascript:window.open('../report/reserve_tmp_receipt_pdf.php?inv_id="+inv_no+"&res_id="+res_id+"&receipt_no="+receipt_no+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:CloseDialogChq();\">  </div>");
    $('#div_prt').dialog({
        title: 'พิมพ์เอกสาร  ',
        resizable: false,
        modal: true,  
        width: 300,
        height: 150,
        close: function(ev, ui){
            $('#div_prt').remove();
			location.reload();
        }
    });
}

//==================== พิมพ์ใบเสร็จรับเงิน (ค่าใช้จ่ายอื่นๆ) ========================//
function print_other_pay(inv_no,res_id,receipt_no){
    $('body').append('<div id="div_prt"></div>');
	//$('#div_prt').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br/><br/> <input type=\"button\" name=\"btn_print_receive\" id=\"btn_print_receive\" value=\"พิมพ์ใบเสร็จรับเงิน\" onclick=\"javascript:window.open('../report/reserve_tmp_receipt_pdf2.php?inv_id=<?php echo $prt; ?>&res_id="+res_id+"&receipt_no="+receipt_no+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:CloseDialogChq();\">  </div>");
    $('#div_prt').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br/><br/> <input type=\"button\" name=\"btn_print_receive\" id=\"btn_print_receive\" value=\"พิมพ์ใบเสร็จรับเงิน\" onclick=\"javascript:window.open('../report/reserve_tmp_receipt_pdf2.php?inv_id="+inv_no+"&res_id="+res_id+"&receipt_no="+receipt_no+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:CloseDialogChq();\">  </div>");
	$('#div_prt').dialog({
        title: 'พิมพ์เอกสาร  ',
        resizable: false,
        modal: true,  
        width: 300,
        height: 150,
        close: function(ev, ui){
            $('#div_prt').remove();
			location.reload();
        }
    });
}


function CloseDialogChq(){
	$('#div_prt').remove();
    location.reload();
}
</script>

<?php
//include_once("../include/config.php");
$doc_no = pg_escape_string($_GET['doc_no']);
$c1 = pg_escape_string($_GET['c1']);
?>
<table>
	<tr>
		<td><label>เหตุผล :</label></td>
		<td><textarea name="txt_reason" id="txt_reason" rows="7" cols="50"></textarea></td>
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
		alert('กรุณาระบุ  เหตุผลในการขอพิมพ์สำเนาเอกสาร');
		return false;
	}
	
    $.post('save_reprint_api.php',{
        cmd: 'save_reprint_reason',
		doc_no: '<?php echo $doc_no; ?>',
		reason: $('#txt_reason').val()
    },
    function(data){
        if(data.success){
            alert(data.message);
			$('#div_dialog').remove();
			print_doc('<?php echo $doc_no; ?>');
        }else{
            alert(data.message);
        }
    },'json');
});

//พิมพ์สำเนาเอกสาร
function print_doc(doc_no){
	var str_filename = "";
	var	str_si = "";
	var str_doc_type = doc_no.substring(2,3);
	var c1 = '<?php echo $c1; ?>';
	//alert(c1);
	if(str_doc_type == 'R'){
		str_filename = 'receipt_pdf.php?receipt_no=';
	}else if(str_doc_type == 'V'){
		str_filename = 'tax_invoice_pdf.php?invoice_no=';
	}else{
		//str_si = doc_no.substring(0,2);
		if(c1 == '1'){
				str_filename = 'car_takeout_pdf.php?idno=';
			}else if(c1 == '2'){
				str_filename = 'car_takeout_pdf2.php?idno=';
			}else {
				str_filename = 'reserve_car_down_pdf_1.php?res_id=';				
			}			
		
	}
    $('body').append('<div id="div_prt"></div>');
	$('#div_prt').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br/><br/> <input type=\"button\" name=\"btn_print\" id=\"btn_print\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/"+str_filename+""+doc_no+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:CloseDialogChq();\">  </div>");
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

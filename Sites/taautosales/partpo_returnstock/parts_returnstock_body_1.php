<?php
$po_withdrawal_page = 1;
include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}
include_once("parts_returnstock_webservice.php");
$function = new Return_stock_body1();
?>
<div style="width:870px; overflow-y: hidden; overflow-x: auto; ">

	<table cellpadding="3" cellspacing="1" border="0" width="870" bgcolor="#F0F0F0">
		<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
			<td width="80"><b>เลขที่ขอคืน</b></td>
			<td width="50"><b>วันที่ขอคืน</b></td>
			<td width="50"><b>ผู้ขอเคืน</b></td>
			<td width="50"><b>ผู้ทำรายการ</b></td>
			<td width="50"><b>ยกเลิก</b></td>
			<td width="50"><b>ส่งรออนุมัติ</b></td>
		</tr>
<?php
		$ReturnParts = $function->get_ReturnParts();
		if($ReturnParts["numrow"] != 0){
			foreach ($ReturnParts["result"] as $withdrawalParts_result) {
	?>
				<tr>
					<td><a href="#" onclick="javascript:EditWithdrawal('parts_returnstock_view.php', '<?php echo $withdrawalParts_result["return_code"]; ?>', 'return'); " ><?php echo $withdrawalParts_result["return_code"]; ?></a></td>
					<td><a href="#" onclick="javascript:EditWithdrawal('parts_returnstock_view.php', '<?php echo $withdrawalParts_result["return_code"]; ?>', 'return'); " ><?php echo $withdrawalParts_result["date"]; ?></a></td>
					<td><a href="#" onclick="javascript:EditWithdrawal('parts_returnstock_view.php', '<?php echo $withdrawalParts_result["return_code"]; ?>', 'return'); " ><?php echo get_fuser_fullname($withdrawalParts_result["return_user_id"]); ?></a></td>
					<td><a href="#" onclick="javascript:EditWithdrawal('parts_returnstock_view.php', '<?php echo $withdrawalParts_result["return_code"]; ?>', 'return'); " ><?php echo get_fuser_fullname($withdrawalParts_result["user_id"]); ?></a></td>
					<td align="center"><img src="../images/close_button.png" border="0" class="btn_cancel" data-withdrawal_code="<?php echo $withdrawalParts_result["code"]; ?>" alt="ยกเลิก" title="ยกเลิก" style="cursor: pointer; "></td>
					<td align="center"><a href="#" onclick="javascript:ConfirmWithdrawal('po_withdrawal_body_1_confirm.php', '<?php echo $withdrawalParts_result["code"]; ?>'); " ><img src="../images/icon-edit.png" border="0" alt="ส่งรออนุมัติ" title="ส่งรออนุมัติ" style="cursor: pointer; " /></a></td>
				</tr>
	<?php
			}
		}
		if($ReturnParts["numrow"] == 0){
?>
			<tr>
				<td align="center" colspan="6">ไม่พบข้อมุล</td>
			</tr>
<?php
		}
?>
	</table>
</div>
<script>
	$(".btn_cancel").click(function(){
		var _withdrawal_code = $(this).data("withdrawal_code");
		
		if(confirm('คุณต้องการที่จะยืนยันการยกเลิกการเบิกหรือไม่') == false){
			return false;
		}
		else{
			$.post(
				'po_withdrawal_body_save.php',
				{
					withdrawal_code : _withdrawal_code,
					set_status: 0,
				},
				function(data){
					if(data.success){
						console.log("data.success = " + data.success);
						console.log("data.message = " + data.message);
						ShowSuccess();
						//location.reload();
					}else{
						alert(data.message);
						console.log("data.success = " + data.success);
						console.log("data.message = " + data.message);
					}
				},
				'json'
			);
		}
	});
	
	function EditWithdrawal(url, code, return_type){
	    $('body').append('<div id="divdialogadd"></div>');
	    $('#divdialogadd').load(url+'?return_type='+return_type+'&code='+code);
	    $('#divdialogadd').dialog({
	        title: 'แก้ไขใบเบิก code : ' + code,
	        resizable: false,
	        modal: true,  
	        width: 1000,
	        height: 600,
	        close: function(ev, ui){
	            $('#divdialogadd').remove();
	        }
	    });
	}
	
	function ConfirmWithdrawal(url, code){
	    $('body').append('<div id="divdialogadd"></div>');
	    $('#divdialogadd').load(url+'?code='+code);
	    $('#divdialogadd').dialog({
	        title: 'ยึนยันใบเบิก code : ' + code,
	        resizable: false,
	        modal: true,  
	        width: 1000,
	        height: 600,
	        close: function(ev, ui){
	            $('#divdialogadd').remove();
	        }
	    });
	}
	
	function ShowSuccess(){
		$('body').append('<div id="divdialogprint"></div>');
		$('#divdialogprint').html("<br/><div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"ตกลง\" onclick=\"javascript:location.reload();\"></div>");
		$('#divdialogprint').dialog({
			title: 'บันทึกเรียบร้อยแล้ว',
			resizable: false,
			modal: true,
			width: 300,
			height: 150,
			close: function(ev, ui){
				for( i=1; i<=counter; i++){
					$('#combo_product'+ i).val("");
					$('#txt_unit'+ i).val("");
					$('#txt_cost'+ i).val("");
					$('#span_price'+ i).text("0");
					$('#txt_vat'+ i).val("");
					$('#span_sum'+ i).text("0");
				}
				$('#span_sum_all_price').text("0");
				$('#span_sum_all_vat').text("0");
				$('#span_sum_all_all').text("0");
				$('#divdialogprint').remove();
			}
		});
	}
	
</script>
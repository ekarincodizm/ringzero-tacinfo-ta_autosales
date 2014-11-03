<?php
$po_withdrawal_page = 3;
include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}
// ##################### Model ########################

function get_fuser_fullname($id_user){
	$fuser_strQuery = "
		SELECT 
			fullname
		FROM 
			fuser
		WHERE 
			id_user = '".$id_user."'
		ORDER BY fullname
		;
	";
	$fuser_query = @pg_query($fuser_strQuery);
	while ($fuser_result = @pg_fetch_array($fuser_query)) {
		return $fuser_result["fullname"];
	}
}
// ################### END Model ######################
?>
<div style="width:870px; overflow-y: hidden; overflow-x: auto; ">

	<table cellpadding="3" cellspacing="1" border="0" width="870" bgcolor="#F0F0F0">
		<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
			<td width="80"><b>เลขที่ขอเบิก</b></td>
			<td width="50"><b>วันที่ขอเบิก</b></td>
			<td width="50"><b>ผู้ขอเบิก</b></td>
			<td width="50"><b>ผู้ทำรายการ</b></td>
			<td width="50"><b>ทำรายการจ่าย</b></td>
		</tr>
<?php
		$withdrawalParts_strQuery = "
			SELECT 
				code, 
				type, 
				user_id, 
				withdraw_user_id, 
				date, 
				usedate, 
				status
			FROM 
				\"WithdrawalParts\"
			WHERE
				status = 3 ;
		";
		$withdrawalParts_query = @pg_query($withdrawalParts_strQuery);
		$withdrawalParts_numrow = @pg_num_rows($withdrawalParts_query);
		while ($withdrawalParts_result = @pg_fetch_array($withdrawalParts_query)) {
?>
			<tr data-withdrawal_code="<?php echo $withdrawalParts_result["code"]; ?>">
				<td><a href="#" onclick="javascript:ViewWithdrawal('po_withdrawal_view.php', '<?php echo $withdrawalParts_result["code"]; ?>'); " ><?php echo $withdrawalParts_result["code"]; ?></a></td>
				<td><a href="#" onclick="javascript:ViewWithdrawal('po_withdrawal_view.php', '<?php echo $withdrawalParts_result["code"]; ?>'); " ><?php echo $withdrawalParts_result["date"]; ?></a></td>
				<td><a href="#" onclick="javascript:ViewWithdrawal('po_withdrawal_view.php', '<?php echo $withdrawalParts_result["code"]; ?>'); " ><?php echo get_fuser_fullname($withdrawalParts_result["withdraw_user_id"]); ?></a></td>
				<td><a href="#" onclick="javascript:ViewWithdrawal('po_withdrawal_view.php', '<?php echo $withdrawalParts_result["code"]; ?>'); " ><?php echo get_fuser_fullname($withdrawalParts_result["user_id"]); ?></a></td>
				<td align="center">
					<a href="po_withdrawal_mat_pdf.php?withdrawal_parts_code=<?php echo $withdrawalParts_result["code"]; ?>" alt="ดูรายละเอียด" title="ดูรายละเอียด" target="_blank"><img src="..\images\print.png" class="btn_view" /></a>
					<img src="../images/icon-edit.png" border="0" alt="ส่งจ่าย" title="ส่งจ่าย" style="cursor: pointer; " onclick="javascript:SendWithdrawal('po_withdrawal_send.php', '<?php echo $withdrawalParts_result["code"]; ?>'); " />
				</td>
			</tr>
<?php
		}
		if($withdrawalParts_numrow == 0){
?>
			<tr>
				<td align="center" colspan="5">ไม่พบข้อมุล</td>
			</tr>
<?php
		}
?>
	</table>
</div>
<script>
	function SendWithdrawal(url, code){
	    $('body').append('<div id="divdialogadd"></div>');
	    $('#divdialogadd').load(url+'?code='+code);
	    $('#divdialogadd').dialog({
	        title: 'ส่งใบเบิก code : ' + code,
	        resizable: false,
	        modal: true,  
	        width: 900,
	        height: 600,
	        close: function(ev, ui){
	            $('#divdialogadd').remove();
	        }
	    });
	}
	
	function ViewWithdrawal(url, code){
	    $('body').append('<div id="divdialogadd"></div>');
	    $('#divdialogadd').load(url+'?status=3&po_withdrawal_page=<?php echo $po_withdrawal_page; ?>&code='+code);
	    $('#divdialogadd').dialog({
	        title: 'ดูรายละเอียดใบเบิก code : ' + code,
	        resizable: false,
	        modal: true,  
	        width: 900,
	        height: 600,
	        close: function(ev, ui){
	            $('#divdialogadd').remove();
	        }
	    });
	}
</script>
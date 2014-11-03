<?php
include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}
?>
<div style="width:870px; overflow-y: hidden; overflow-x: auto; ">

	<table cellpadding="3" cellspacing="1" border="0" width="870" bgcolor="#F0F0F0">
		<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
			<td width="80"><b>เลขที่ขอเบิก</b></td>
			<td width="50"><b>วันที่ขอเบิก</b></td>
			<td width="50"><b>ผู้ขอเบิก</b></td>
			<td width="50"><b>ผู้ทำรายการ</b></td>
			<!-- <td width="50"><b>ยกเลิก</b></td> -->
			<td width="50"><b>ทำรายการอนุมัติ</b></td>
		</tr>
<?php
		// find user id
		function find_username($userid){
			$user_StrQuery = "
				SELECT fullname
				FROM fuser
				WHERE id_user = '".$userid."';
			";
			$user_query = @pg_query($user_StrQuery);
			while ($user_result = @pg_fetch_array($user_query)) {
				return $user_result["fullname"];
			}
		}
		
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
				status = 2 ;
		";
		$withdrawalParts_query = @pg_query($withdrawalParts_strQuery);
		$withdrawalParts_numrow = @pg_num_rows($withdrawalParts_query);
		while ($withdrawalParts_result = pg_fetch_array($withdrawalParts_query)) {
?>
			<tr>
				<td><a href="#" onclick="javascript:ViewWithdrawal('po_withdrawal_view.php', '<?php echo $withdrawalParts_result["code"]; ?>'); " ><?php echo $withdrawalParts_result["code"]; ?></a></td>
				<td><a href="#" onclick="javascript:ViewWithdrawal('po_withdrawal_view.php', '<?php echo $withdrawalParts_result["code"]; ?>'); " ><?php echo $withdrawalParts_result["date"]; ?></a></td>
				<td><a href="#" onclick="javascript:ViewWithdrawal('po_withdrawal_view.php', '<?php echo $withdrawalParts_result["code"]; ?>'); " ><?php echo find_username($withdrawalParts_result["withdraw_user_id"]); ?></a></td>
				<td><a href="#" onclick="javascript:ViewWithdrawal('po_withdrawal_view.php', '<?php echo $withdrawalParts_result["code"]; ?>'); " ><?php echo find_username($withdrawalParts_result["user_id"]); ?></a></td>
				<td align="center"><a href="#" onclick="javascript:ApprWithdrawal('po_withdrawal_admin_view.php', '<?php echo $withdrawalParts_result["code"]; ?>'); " ><img src="../images/icon-edit.png" border="0" alt="ทำรายการอนุมัติ" title="ทำรายการอนุมัติ" style="cursor: pointer; " /></a></td>
			</tr>
<?php
		}
		if($withdrawalParts_numrow == 0){
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
	function ViewWithdrawal(url, code){
	    $('body').append('<div id="divdialogadd"></div>');
	    $('#divdialogadd').load(url+'?status=2&code='+code);
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
	
	function ApprWithdrawal(url, code){
	    $('body').append('<div id="divdialogadd"></div>');
	    $('#divdialogadd').load(url+'?status=2&code='+code);
	    $('#divdialogadd').dialog({
	        title: 'ทำรายการอนุมัติละเอียดใบเบิก code : ' + code,
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
<?php
//include_once("../include/config.php");
$res_id = pg_escape_string($_GET['res_id']);
$doc_no = pg_escape_string($_GET['doc_no']);
$car_id = pg_escape_string($_GET['car_id']);
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
		alert('กรุณาระบุ  เหตุผลในการขอยกเลิกการส่งมอบรถ');
		return false;
	}
	
    $.post('save_cancel_api.php',{
		car_id: '<?php echo $car_id; ?>',
		res_id: '<?php echo $res_id; ?>',
        doc_no: '<?php echo $doc_no; ?>',
		reason: $('#txt_reason').val()
    },
    function(data){
        if(data.success){
            alert(data.message);
			$('#div_dialog').remove();
			location.reload();
        }else{
            alert(data.message);
        }
    },'json');
});

function CloseDialogChq(){
	$('#div_prt').remove();
    location.reload();
}
</script>

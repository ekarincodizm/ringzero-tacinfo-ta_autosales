<?php
if($id != "") {

   $qry = pg_query("SELECT * FROM \"PurchaseOrderDetails\" WHERE auto_id='$id' AND cancel='FALSE' ");
    if($res = pg_fetch_array($qry)){
        $auto_id = $res['auto_id'];
        $po_id = $res['po_id'];
        $product_id = $res['product_id'];
       //$product_name = GetRawMaterialName($product_id);
	   $product_name = GetProductName($product_id);
    }
}

?>
	<div>
		<table cellpadding="5" cellspacing="0" border="0" width="100%">
			<tr>
				<td width="30%">รหัสวัสดุอุปกรณ์ :</td>
				<td width="70%"><?php echo $product_id; ?></td> 
			</tr>
			<tr>
				<td width="30%">ชื่อ :</td>
				<td width="70%"><input type="text" name="txt_name" id="txt_name" size="30px" onkeypress="check_num(event);" value="<?php echo $product_name; ?>">
				</td>    
			</tr>
			<tr>
				<td>สถานที่รับสินค้า :</td>
				<td colspan="3">
					<select name="combo_mat_wh" id="combo_mat_wh">
				<?php
					$qry_wh = pg_query("SELECT * FROM \"Warehouses\" WHERE cancel='FALSE' ORDER BY wh_name ASC");
					while( $res_wh = pg_fetch_array($qry_wh) ){
						$wh_id = $res_wh['wh_id'];
						$wh_name = $res_wh['wh_name'];
				?>
						<option value="<?php echo $wh_id; ?>" <?php if($wh_id == 1){ echo "selected"; } ?>><?php echo $wh_name; ?></option>
				<?php
					}
				?>
					</select> (ยังไม่ได้ใช้)
				</td>
			</tr>
			<tr>
				<td>จำนวน :</td>
				<td><input type="text" name="txt_mat_num" id="txt_mat_num" size="10" onkeypress="check_num(event);"></td>
			</tr>
		</table>

		<div style="text-align:right; margin-top:10px">
			<input type="button" name="btnSubmit_mat" id="btnSubmit_mat" value="บันทึก">
		</div>
	</div>
	
<script>
 $('#txt_mat_num').attr('style','background:#EEE9E9;text-align:right');
 $('#txt_name').prop('disabled',true);
 
function check_num(e){ 
    var key;
    if(window.event)
	{
        key = window.event.keyCode; // IE
		if(key <= 57 && key != 33 && key != 34 && key != 35 && key != 36 && key != 37 && key != 38 && key != 39 && key != 40 && key != 41 && key != 42
			&& key != 43 && key != 44 && key != 45 && key != 47)
		{
			
		}
		else{
			window.event.returnValue = false;
		}
    }
	else
	{
        key = e.which; // Firefox       
		if(key <= 57 && key != 33 && key != 34 && key != 35 && key != 36 && key != 37 && key != 38 && key != 39 && key != 40 && key != 41 && key != 42
			&& key != 43 && key != 44 && key != 45 && key != 47)
		{
			
		}
		else
		{
			key = e.preventDefault();
		}
	}
}

$('#btnSubmit_mat').click(function(){
    $.post('po_receive_api.php',{
        cmd: 'mat_save',
        product_id: '<?php echo $product_id; ?>',
        po_id: '<?php echo $po_id; ?>',
        po_auto_id: '<?php echo $auto_id; ?>',
        txt_mat_num: $('#txt_mat_num').val(),
        combo_mat_wh: $('#combo_mat_wh').val()
    },
    function(data){
        if(data == "success"){
			alert('บันทึกเรียบร้อยแล้ว');
            $('#divdialogadd').remove();
			location.reload();
			$('#list_receive').load('po_list_receive.php',{
				condition: '<?php echo $condition; ?>',
				keyword: '<?php echo $keyword; ?>'
			});
        }else{
           alert('ไม่สามารถบันทึกได้ '+ data);
        }
    });
});
</script>
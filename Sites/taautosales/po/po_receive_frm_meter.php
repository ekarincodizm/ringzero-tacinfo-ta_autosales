<?php
if($id != "") {

	$qry = pg_query("SELECT * FROM \"PurchaseOrderDetails\" WHERE auto_id='$id' AND cancel='FALSE' ");
    if($res = pg_fetch_array($qry)){
        $auto_id = $res['auto_id'];
        $po_id = $res['po_id'];
        $unit = $res['unit'];
        $product_id = $res['product_id'];
        $product_name = GetRawMaterialProductName($product_id);
        
        $GetAmountPO_Last = GetAmountPO("I",$po_id,'');
        $unit -= $GetAmountPO_Last;
    }
}
?>
	<div>
		<table cellpadding="5" cellspacing="0" border="0" width="100%">
			<tr>
				<td width="20%"><b>PO ID :</b></td><td width="30%"><?php echo $po_id; ?></td>
				<td width="20%"><b>Product Name :</b></td><td width="30%"><?php echo $product_name; ?></td>    
			</tr>
			<tr>
				<td>จำนวน :</td><td><input type="text" name="txt_num" id="txt_num" style="width:100px" value="<?php echo $unit; ?>"></td>
				<td colspan="2"></td>
			</tr>
		<?php
			for($i=1; $i<=$unit; $i++){
		?>
			<tr>
				<td>รหัส Meter (<?php echo $i; ?>) :</td><td><input type="text" name="txt_mt_id" id="txt_mt_id_<?php echo $u; ?>" style="width:100px"></td>
				<td colspan="2"></td>
			</tr>
		<?php
			}
		?>
		</table>

		<div style="text-align:right; margin-top:10px">
			<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
		</div>
	</div>
	
<script>
$('#btnSubmit').click(function(){
    
    var val_chkbox = $("input[name=txt_mt_id]").map(function(){
        return this.value;
    }).get().join(",");
    
    $.post('po_receive_api.php',{
        cmd: 'P_Meter_Save',
        po_id: '<?php echo $po_id; ?>',
        product_id: '<?php echo $product_id; ?>',
        unit : '<?php echo $unit; ?>',
        txt_num: $('#txt_num').val(),
        val_chkbox: val_chkbox
    },
    function(data){
        if(data == "success"){
            alert('บันทึกเรียบร้อยแล้ว');
            $('#divdialogadd').remove();
			$('#list_receive').load('po_list_receive.php',{
				condition: '<?php echo $condition; ?>',
				keyword: '<?php echo $keyword; ?>'
			});
        }else{
           alert('ไม่สามารถบันทึกได้ '+ data);
        }
    },'json');
});
</script>
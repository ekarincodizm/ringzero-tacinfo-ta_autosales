<?php
if($id != "") {

	$qry = pg_query("SELECT * FROM \"PurchaseOrderDetails\" WHERE auto_id='$id' AND cancel='FALSE' ");
	if($res = pg_fetch_array($qry)){
		$auto_id = $res['auto_id'];
		$po_id = $res['po_id'];
		$product_id = $res['product_id'];
		$product_cost = $res['product_cost'];
		$vat = $res['vat'];
		$unit = $res['unit'];
    
		$cost_val = round($product_cost/$unit,2);
		$cost_vat = round($vat/$unit,2);
    
		$qry_name = pg_query("SELECT name,link_table FROM \"Products\" WHERE product_id='$product_id' ");
		if($res_name = pg_fetch_array($qry_name)){
			$product_name = $res_name['name'];
			$link_table = $res_name['link_table'];
		}
		
		$qry_po = pg_query("select vender_id from \"PurchaseOrders\" where po_id='$po_id' ");
		$vender_id_q = pg_fetch_result($qry_po,0);
		
	}
}
?>
	<div>
		<table cellpadding="3" cellspacing="0" border="0" width="100%">
			<tr>
				<td width="18%"><b>ผู้ขาย :</b></td>
				<td colspan="3">
					<select name="combo_warehouse" id="combo_warehouse">
					<?php
					$qry = pg_query("SELECT * FROM \"VVenders\" WHERE type_ven = 'P' or type_ven='B' ORDER BY pre_name,cus_name ASC");
					while( $res = pg_fetch_array($qry) ){
						$vender_id = $res['vender_id'];
						$pre_name = trim($res['pre_name']);
						$cus_name = trim($res['cus_name']);
						$surname = trim($res['surname']);
						$branch_id = trim($res['branch_id']);
						$alphas  = trim($res['alphas']);
					?> 
					<option value="<?php echo $vender_id; ?>" <?php if($vender_id == $vender_id_q){ echo "selected"; } ?>><?php echo "$pre_name $cus_name $surname"; if($alphas != ""){echo " : $alphas ";} if($branch_id != ""){ if($branch_id == '0'){ echo "( สนญ)"; }else{ echo  "(สาขาที่ $branch_id)"; } } ?></option>
					<?php
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td width="10%">รหัสสินค้า :</td>
				<td width="40%"><?php echo $product_id; ?></td>
				<td width="10%">ชื่อสินค้า :</td>
				<td width="40%"><?php echo $product_name; ?></td>    
			</tr>
			<tr>
				<td width="15%">เลขตัวถัง :</td>
				<td width="35%"><input type="text" name="txt_carnum" id="txt_carnum" style="width:200px" onkeyup="check_car_mar_num();" onkeypress="check_car_mar_num();" ></td>
				<td width="15%">เลขเครื่อง :</td>
				<td width="35%"><input type="text" name="txt_marnum" id="txt_marnum" style="width:200px" onkeyup="check_car_mar_num();" onkeypress="check_car_mar_num();" ></td>
			</tr>
			<tr>
				<td>สีรถ :</td>
				<td colspan="3">
					<select name="txt_color" id="txt_color">
						<option value="">กรุณาเลือกสี</option>
						<?php 
							$qry_color = pg_query("select * from \"CarColor\" order by color_name");
							while($res = pg_fetch_array($qry_color)){
							
								$color_id = $res['color_id'];
								$color_name = $res['color_name'];
								
								echo "<option value=\"$color_id\">$color_name</option>";
							}
						?>
					</select>
				</td>
				
			</tr>
			<tr>
				<td width="18%">สถานที่รับสินค้า :</td>
				<td colspan="3">
					<select name="combo_wh" id="combo_wh">
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
					</select>
				</td>
			</tr>
		</table>
		<br>
		<div style="text-align:right; margin-top:10px">
			<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
			<input type="hidden" name="chk_car_mar" id="chk_car_mar" >
		</div>
	</div>
<script>
$('#btnSubmit').click(function(){
	
	var chk = 0;
	var Error = "-- Error! -- \n";
	
	
	
	if($('#txt_carnum').val() == ""){
		Error += 'กรุณาระบุเลขตัวถัง \n';
		chk++;
	} 
	
	if($('#txt_marnum').val() == ""){
		Error += 'กรุณาระบุเลขเลขเครื่อง \n';
		chk++;
	}
	
	if($('#txt_color').val() == ""){
		Error += 'กรุณาระบุสีรถ \n';
		chk++;
	}
	
	if($('#chk_car_mar').val() == '0'){
		Error += 'กรุณาตรวจสอบเลขเครื่องหรือเลขตัวถังซ้ำ\n';
		chk++;
	}
	
	
	if(chk>0){
		alert(Error);
	}else {
    $.post('po_receive_api.php',{
        cmd: 'save',
        product_id: '<?php echo $product_id; ?>',
		product_name: '<?php echo $product_name; ?>',
        po_id: '<?php echo $po_id; ?>',
        po_auto_id: '<?php echo $auto_id; ?>',
        txt_carnum: $('#txt_carnum').val(),
        txt_marnum: $('#txt_marnum').val(),
        txt_color: $('#txt_color').val(),
        combo_wh: $('#combo_wh').val(),
        cost_val: '<?php echo $cost_val; ?>',
        cost_vat: '<?php echo $cost_vat; ?>',
		combo_warehouse: $('#combo_warehouse').val()
    },
    function(data){
	   if(data.length == 20 || data.length == 19){
			alert('บันทึกเรียบร้อยแล้ว');
			location.reload();
			/*$('#divdialogadd').empty();
			$('#list_receive').load('po_list_receive.php',{
				condition: '<?php echo $condition; ?>',
				keyword: '<?php echo $keyword; ?>'
			});*/
			
        }else{
			alert('ไม่สามารถบันทึกได้ '+ data);
        }
    });
	}
});

function CloseDialogChq(){
    $('#divdialogadd').remove();
    location.reload();
}
function check_car_mar_num(){
	$.post('chkdata.php',{
		txtcarnum: $('#txt_carnum').val(),
		txtmarnum: $('#txt_marnum').val(),
		cmd:'newcar'
	},function(data){
		if(data == 't'){
			$('#chk_car_mar').val('1');
		}else if(data == 'f'){
			$('#chk_car_mar').val('0');
		}
	});
}
</script>
<?php
include_once("../include/config.php");
include_once("../include/function.php");
include_once("parts_project_api_service.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "div_add"){
?>
<table cellpadding="1" cellspacing="1" border="0" width="100%">
<tr>
    <td width="10%"><b>ชื่อ Project :</b></td>
    <td width="40%"><input type="text" name="add_project_name" id="add_project_name" style="width:300px"></td>
    <td width="40%"><b>รหัสสินค้าที่ผูกกับโปรเจค :</b> <input type="text" name="project_add_exist_parts" id="project_add_exist_parts" style="width:200px"></td>
    <td width="10%"><input type="button" name="project_add_new_parts" id="project_add_new_parts" value="เพิ่มสินค้า" onclick="javascript:project_add_new_Parts('parts_project_add_product.php')" ></td>
</tr>
</table>

<div style="margin-top:10px; float:left; width:12%">
	<b>เพิ่ม/ลบ รายการ</b>
	<br>
	<input type="button" name="btn_list_add" id="btn_list_add" class="add" value="+ เพิ่ม"><input type="button" name="btn_list_del" id="btn_list_del" class="add" value="- ลบ">
</div>

<div style="margin-top:10px; float:right; width:88%">
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:left" bgcolor="#D0D0D0">
    <td width="5%">no.</td>
    <td width="30%">รหัสสินค้า</td>
    <td width="20%">ชื่อสินค้า</td>
    <td width="35%">รายละเอียด</td>
    <td width="10%">จำนวน</td>
</tr>
<tr bgcolor="#FFFFFF">
    <td>1.</td>
    <td>
		<select name="combo_mat_add1" id="combo_mat_add1" class="combo_mat_add chosen-select" data-code_id="1" style="width: 100%">
    		<option value="">เลือก</option>
<?php
			$parts = get_Parts_type_0();
?>
    		<script>
    			var parts_add = <?php echo json_encode($parts); ?>;
    		</script>
<?php
			foreach ($parts as $key => $value){
?>
				<option value="<?php echo $value["code"]; ?>"><?php echo $value["code"]."#".$value["name"]."#".$value["details"]."#".$value["barcode"]; ?></option>
<?php 
			}
?>
		</select>
    </td>
    <td>
    	<span class="parts_name_add" id="parts_name_add1">ชื่อสินค้า</span>
    </td>
    <td>
    	<span class="parts_detail_add" id="parts_detail_add1">รายละเอียด</span>
    </td>
    <td>
    	<input type="text" name="txt_unit_add1" id="txt_unit_add1" class="txt_unit_add" style="width:70px; text-align:right">
    </td>
</tr>
</table>

<div class="linedotted" style="margin:0px"></div>
    
<div id="TextBoxesGroup"></div>

</div>

<div style="clear:both"></div>

<div style="margin-top:8px; text-align:right">
<input type="button" name="btnSave" id="btnSave" value="บันทึก">
</div>

<script>
var counter = 1;
$('#btn_list_add.add').live("click", function(){
    counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);

    table = '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
    + ' <tr>'
    + ' <td width="5%">'+counter+'.</td>'
    + ' <td width="30%">'
    + ' <select id="combo_mat_add' + counter + '" name="combo_mat_add' + counter + '" class="combo_mat_add chosen-select" data-code_id="'+counter+'" style="width: 100%">'
    + '		<option value="">เลือก</option>';
    for(var i=0; i < parts_add.length; i++){
    	table += 
    	'	<option value="'+parts_add[i]["code"]+'">'+parts_add[i]["code"]+'#'+parts_add[i]["name"]+'#'+parts_add[i]["details"]+'#'+parts_add[i]["barcode"]+'</option>';
    }
    table += 
	  ' </select>'
    + ' </td>'
    
    + ' <td width="20%">'
    + '		<span class="parts_name_add" id="parts_name_add'+counter+'">ชื่อสินค้า</span>'
    + '	</td>'
    + '	<td width="35%">'
    + '		<span class="parts_detail_add" id="parts_detail_add'+counter+'">รายละเอียด</span>'
    + '	</td>'
    
    + '<td width="10%"><input type="text" name="txt_unit_add'+ counter +'" id="txt_unit_add'+ counter +'" class="txt_unit_add" style="width:70px; text-align:right"></td>'
    + ' </tr>'
    + ' </table><div class="linedotted" style="margin:0px"></div>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#TextBoxesGroup");
    
    // For make the jQuery Chosen for all Option
	$(".chosen-select").chosen({
		no_results_text: "ไม่มีข้อมูล"
	}); 
});

$("#btn_list_del.add").click(function(){
    if(counter==1){
        return false;
    }
    $("#TextBoxDiv" + counter).remove();
    counter--;
});

$(".combo_mat_add").live("change", function(){
	
	var this_id = $(this).data("code_id");
	var parts_code = $(this).val();
	
	var i = 0;
	var parts_name_value = "";
	var parts_detail_value = "";
	
	for(i = 0; i < parts_add.length; i++){
		if(parts_code == parts_add[i].code){
			parts_name_value = parts_add[i].name;
			parts_detail_value = parts_add[i].details;
			break;
		}
	}
	
	console.log("parts_name = " + parts_name_value);
	console.log("parts_detail_value = " + parts_detail_value);
	
	if(parts_code != "" ){
		$(".parts_name_add#parts_name_add"+this_id).html(parts_name_value);
		$(".parts_detail_add#parts_detail_add"+this_id).html(parts_detail_value);
	}
	else{
		$(this).val("");
		$(".parts_name_add#parts_name_add"+this_id).html("");
		$(".parts_detail_add#parts_detai_add"+this_id).html("");
	}
	$(".chosen-container#combo_mat_add"+this_id+"_chosen .chosen-single span").html(parts_code);
});

$(function() {
	// For make the jQuery Chosen for all Option
	$(".chosen-select").chosen({
		no_results_text: "ไม่มีข้อมูล"
	});
	
	$(".txt_unit_add").live("keydown", function(e){
		// Allow: backspace, delete, tab, escape, enter and .
		if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
		     // Allow: Ctrl+A
		    (e.keyCode == 65 && e.ctrlKey === true) || 
		     // Allow: home, end, left, right
		    (e.keyCode >= 35 && e.keyCode <= 39)) {
		         // let it happen, don't do anything
		         return;
		}
		// Ensure that it is a number and stop the keypress
		if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
		    e.preventDefault();
		}
	});
});

var parts_code_autocomplete = <?php echo json_encode(get_Parts_autocomplete_type_1()); ?>;
$("#project_add_exist_parts").live("focus", function() {
	$(this).autocomplete({
		source: parts_code_autocomplete,
		minLength:1,
		select: function(event, ui) {
			if(ui.item.value == 'ไม่พบข้อมูลเก่า'){
				
			}else{
			   
			}
		}
	});
});

$('#btnSave').click(function(){
	var new_project_check = 0;
	
	var _add_project_name = $('#add_project_name').val();
	if(_add_project_name == ""){
		alert('กรุณากรอก ชื่อ Project');
		new_project_check++;
        return false;
	}
	
	var _project_add_exist_parts = $("#project_add_exist_parts").val();
	if(project_add_exist_parts == ""){
		alert('กรุณากรอก รหัสสินค้าที่ผูกกับโปรเจค ');
		new_project_check++;
        return false;
	}
	
    var arradd = [];
    for( i=1; i<=counter; i++ ){
        var cc = $('#combo_mat_add'+ i).val();
        var uu = $('#txt_unit_add'+ i).val();
        
        if(cc == ""){
            alert('กรุณาเลือก Material (รายการที่ '+i+')');
            new_project_check++;
            return false;
        }
        if(uu == "" || uu == 0){
            alert('กรุณากรอกจำนวน (รายการที่ '+i+')');
            new_project_check++;
            return false;
        }
        arradd[i] =  { mat:cc, unit:uu };
    }
    if(new_project_check == 0){
    	
		if(!confirm('ต้องการทำการบันทึกหรือไม่?')){
			return false;
		}
		else{
			
		    $.post('parts_project_api.php',{
		        cmd: 'add_save',
		        txt_name: $('#add_project_name').val(),
		        project_add_exist_parts : _project_add_exist_parts,
		        arradd: JSON.stringify(arradd)
		    },
		    function(data){
		        if(data.success){
		            var pj_name = encodeURIComponent($('#txt_name').val());
		            $('#div_add').remove();
		            alert(data.message);
		            console.log(data.message);
		            // ShowAddProduct(data.pj_id,pj_name);            
		            location.reload();
		        }else{
		            console.log(data.message);
		            alert(data.message);
		        }
		    },'json');
    		
			
		}
    }
});

function popU(U, N, T) {
	newWindow = window.open(U, N, T);
	if (!newWindow.opener)
		newWindow.opener = self;
}

function project_add_new_Parts(url){
	javascript:popU(url,'','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=940,height=390');
}

function ShowAddProduct(pj_id,pj_name){
    $('body').append('<div id="divdialogadd"></div>');
    $('#divdialogadd').load('parts_project_api.php?cmd=add_product&pj_id='+pj_id+'&pj_name='+pj_name);
    $('#divdialogadd').dialog({
        title: 'บันทึก Product',
        resizable: false,
        modal: true,  
        width: 500,
        height: 350,
        close: function(ev, ui){
            $('#divdialogadd').remove();
        }
    });
}
</script>
<?php
}
elseif($cmd == "add_save"){
    $txt_name = pg_escape_string($_POST['txt_name']);
	$project_add_exist_parts = pg_escape_string($_POST["project_add_exist_parts"]);
    $arradd = json_decode(stripcslashes($_POST["arradd"]));
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();

    $qry = pg_query("SELECT COUNT(project_id) AS countid FROM \"Projects\"");
    $res = pg_fetch_array($qry);
    $res_count=$res['countid'];
    if($res_count == 0){
        $pj_id = 1;
    }else{
        $pj_id = $res_count+1;
    }

    $qry = "INSERT INTO \"Projects\" (project_id, name, product_id) VALUES ('$pj_id','$txt_name', '$project_add_exist_parts')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT Projects ไม่สำเร็จ $qry";
        $status++;
    }
    
    foreach($arradd as $key => $value){
        $mat = $value->mat;
        $unit = $value->unit;
        
        if(empty($mat) or empty($unit) ){
            continue;
        }
        
        $qry = "INSERT INTO \"ProjectDetails\" (project_id, material_id, use_unit) VALUES ('$pj_id','$mat','$unit'); ";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT ProjectDetails ไม่สำเร็จ $qry";
            $status++;
        }
    }

    if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
        $data['pj_id'] = $pj_id;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! เนื่องจาก $txt_error[0]";
    }
    echo json_encode($data);
}

elseif($cmd == "add_product"){
    $pj_id = $_GET['pj_id'];
    $pj_name = $_GET['pj_name'];
?>
<table width="100%" border="0" cellpadding="2">
<tr >
    <td width="30%" style="text-align:right;">Product Name</td>
    <td width="70%"><input type="text" name="p_name" id="p_name" style="width:200px;" value="<?php echo $pj_name; ?>"></td>
</tr>
<tr>
    <td style="text-align:right;">cost price</td>
    <td><input type="text"  name="p_costprice" id="p_costprice"></td>
</tr>
<tr>
    <td style="text-align:right;">sale price</td>
    <td><input type="text"  name="p_saleprice" id="p_saleprice"></td>
</tr>
<tr>
    <td style="text-align:right;">use vat</td>
    <td><input type="radio" name="usevat" id="usevat" value="TRUE">YES <input type="radio" name="usevat" id="usevat" value="FALSE">NO</td>
</tr>
<tr>
    <td style="text-align:right;">type rec</td>
    <td><input type="radio" name="type_rec" id="type_rec" value="N"> N
    <input type="radio" name="type_rec"  id="type_rec"  value="R"> R
    <input type="radio" name="type_rec"   id="type_rec"  value="A"> A
    </td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="button" name="btnSaveAddPD" id="btnSaveAddPD" value="SAVE"></td>
</tr>
</table>

<script>
$('#btnSaveAddPD').click(function(){
    $.post('parts_project_api.php',{
        cmd : 'add_product_save',
        pj_id : '<?php echo $pj_id; ?>',
        p_name: $('#p_name').val(),
        p_costprice: $('#p_costprice').val(),
        p_saleprice: $('#p_saleprice').val(),
        usevat: $('input[id=usevat]:checked').val(),
        type_rec: $('input[id=type_rec]:checked').val()
    },
    function(data){
        if(data.success){
            alert(data.message);
            location.reload();
        }else{
            alert(data.message);
        }
    },'json');	  
});
</script>
<?php
}

elseif($cmd == "add_product_save"){
    $pj_id = $_POST['pj_id'];
    $p_name=$_POST['p_name'];
    $p_costprice=$_POST['p_costprice'];
    $p_saleprice=$_POST['p_saleprice'];
    $usevat=$_POST['usevat'];
    $type_rec=$_POST['type_rec'];
    
    if( empty($pj_id) OR empty($p_name) OR $p_costprice=="" OR $p_saleprice=="" OR empty($usevat) OR empty($type_rec) ){
        $data['success'] = false;
        $data['message'] = "กรุณากรอกข้อมลให้ครบถ้วน !";
        echo json_encode($data);
        exit;
    }
    
    $qry_pro=pg_query("select count(*) AS num from \"Products\" ");
    $res_pro=pg_fetch_array($qry_pro);
    $num_count=$res_pro["num"];

    if($num_count==0){
        $res_sn=1;
    }else{
        $res_sn=$num_count+1;
    }

    $product_sn="P".insertZero($res_sn , 3); // products code

    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();

    $in_qry="INSERT INTO \"Products\" (product_id,\"name\",cost_price,sale_price,use_vat,type_rec) values 
    ('$product_sn','$p_name','$p_costprice','$p_saleprice','$usevat','$type_rec')";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "บันทึก Products ไม่สำเร็จ $in_qry";
        $status++;
    }
    
    $in_qry="UPDATE \"Projects\" SET product_id='$product_sn' WHERE project_id='$pj_id' ";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "บันทึก Projects ไม่สำเร็จ $in_qry";
        $status++;
    }

    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
    
    echo json_encode($data);
}

elseif($cmd == "div_edit"){
    $id = $_GET['id'];
	
	$edit_project_strQuery = "
		SELECT
			product_id
		FROM 
			\"Projects\" 
		WHERE 
			project_id='$id' 
			AND
			cancel='FALSE' 
		ORDER BY project_id ASC;
	";
	$edit_project_query = @pg_query($edit_project_strQuery);
	
	while (
		$edit_project_result = @pg_fetch_array($edit_project_query)
	){
?>
		<table cellpadding="1" cellspacing="1" border="0" width="100%">
		<tr>
		    <td width="10%"></td>
		    <td width="40%"></td>
		    <td width="40%"><b>รหัสสินค้าที่ผูกกับโปรเจค :</b> <input type="text" name="project_edit_exist_parts" id="project_edit_exist_parts" style="width:200px" value="<?php echo $edit_project_result["product_id"]; ?>"></td>
		    <td width="10%"><input type="button" name="project_add_new_parts" id="project_add_new_parts" value="เพิ่มสินค้า" onclick="javascript:project_add_new_Parts('parts_project_add_product.php'); " ></td>
		</tr>
		</table>

		<div style="margin-top:10px; float:left; width:12%">
			<b>เพิ่ม/ลบ รายการ</b>
			<br>
			<input type="button" name="btn_list_add" id="btn_list_add" class="edit" value="+ เพิ่ม"><input type="button" name="btn_list_del" id="btn_list_del" class="edit" value="- ลบ">
		</div>

		<div style="margin-top:10px; float:right; width:88%">
		
			<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
			<tr style="font-weight:bold; text-align:center" bgcolor="#D0D0D0">
			    <td width="5%">no.</td>
			    <td width="30%">รหัสสินค้า</td>
			    <td width="20%">ชื่อสินค้า</td>
			    <td width="35%">รายละเอียด</td>
			    <td width="10%">จำนวน</td>
			</tr>
<?php
			$j = 0;
			$projectDetail_strQuery = "
				SELECT * 
				FROM 
					\"ProjectDetails\" 
				WHERE 
					project_id='$id' 
					AND
					cancel='FALSE' 
				ORDER BY material_id ASC
			";
			$qry_pj_dt = pg_query($projectDetail_strQuery);
			$qry_numrow_pj_dt = @pg_num_rows($qry_pj_dt);
			while( $res_pj_dt = pg_fetch_array($qry_pj_dt) ){
			    $j++;
			    $dt_material_id = $res_pj_dt['material_id'];
			    $dt_use_unit = $res_pj_dt['use_unit'];
				
				if($j == 1){
?>
					<tr bgcolor="#FFFFFF">
					    <td align="center"><?php echo $j; ?>.</td>
					    <td>
							<select name="combo_mat_edit1" id="combo_mat_edit1" class="combo_mat_edit chosen-select" data-code_id="1" style="width: 100%">
					    		<option value="">เลือก</option>
<?php
								$parts = get_Parts_type_0();
?>
					    		<script>
					    			var parts_edit = <?php echo json_encode($parts); ?>;
					    		</script>
<?php
								foreach ($parts as $key => $value){
?>
									<option value="<?php echo $value["code"]; ?>" <?php 
										if($value["code"] == $dt_material_id){
											?>selected='selected'<?php
										}
									?>><?php echo $value["code"]."#".$value["name"]."#".$value["details"]."#".$value["barcode"]; ?></option>
<?php 
								}
?>
							</select>
					    </td>
					    <td>
					    	<span class="parts_name_edit" id="parts_name_edit1"><?php echo get_Parts_with_field($dt_material_id, "name"); ?></span>
					    </td>
					    <td>
					    	<span class="parts_detail_edit" id="parts_detail_edit1"><?php echo get_Parts_with_field($dt_material_id, "details"); ?></span>
					    </td>
					    
					    <td>
					    	<input type="text" name="txt_unit_edit1" id="txt_unit_edit1" class="txt_unit_edit" style="width:70px; text-align:right" value="<?php echo $dt_use_unit; ?>">
					    </td>
					</tr>
				</table>
<?php
				}
				else{
					if($j == 2){
?>
						<div id="TextBoxesGroup">
<?php
					}
?>
					<div id="TextBoxDiv<?php echo $j; ?>">
						<table width="100%" cellpadding="5" cellspacing="0" border="0">
							<tr>
								<td align="center" width="5%"><?php echo $j; ?>.</td>
								<td width="30%">
									<select id="combo_mat_edit<?php echo $j; ?>" name="combo_mat_edit<?php echo $j; ?>" class="combo_mat_edit chosen-select" data-code_id="<?php echo $j; ?>" style="width: 100%">
										<option value="">เลือก</option>
<?php
										foreach ($parts as $key => $value){
?>
											<option value="<?php echo $value["code"]; ?>" <?php 
												if($value["code"] == $dt_material_id){
													?>selected='selected'<?php
												}
											?>><?php echo $value["code"]."#".$value["name"]."#".$value["details"]."#".$value["barcode"]; ?></option>
<?php 
										}
?>
									</select>
								</td>
								<td width="20%">
									<span class="parts_name_edit" id="parts_name_edit<?php echo $j; ?>"><?php echo get_Parts_with_field($dt_material_id, "name"); ?></span>
								</td>
								<td width="35%">
									<span class="parts_detail_edit" id="parts_detail_edit<?php echo $j; ?>"><?php echo get_Parts_with_field($dt_material_id, "details"); ?></span>
								</td>
								<td width="10%">
									<input type="text" name="txt_unit_edit<?php echo $j; ?>" id="txt_unit_edit<?php echo $j; ?>" class="txt_unit_edit" value="<?php echo $dt_use_unit; ?>" style="width:70px; text-align:right">
								</td>
							</tr>
						</table>
						<div class="linedotted" style="margin:0px"></div>
					</div>
<?php
					if($j == $qry_numrow_pj_dt){
?>
						</div>
<?php
					}
				}
				
				if($qry_numrow_pj_dt == 1){
?>
					<div id="TextBoxesGroup"></div><?php
				}
			}
?>
		</div>
		<div style="clear: both; "></div>
<?php 
		if($j > 0){
?>
			<div style="margin-top:8px; text-align:right">
			<input type="button" name="btnSaveMat" id="btnSaveMat" value="บันทึก">
			</div>
<?php 
		} 
	}
?>

<script>
var counter = <?php echo $j; ?>;
$('#btn_list_add.edit').live("click", function(){
counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);

    table = '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
    + ' <tr>'
    + ' <td align="center" width="5%">'+counter+'.</td>'
    + ' <td width="30%">'
    + ' <select id="combo_mat_edit' + counter + '" name="combo_mat_edit' + counter + '" class="combo_mat_edit chosen-select" data-code_id="'+counter+'" style="width: 100%">'
    + '		<option value="">เลือก</option>';
    for(var i=0; i < parts_edit.length; i++){
    	table += 
    	'	<option value="'+parts_edit[i]["code"]+'">'+parts_edit[i]["code"]+'#'+parts_edit[i]["name"]+'#'+parts_edit[i]["details"]+'#'+parts_edit[i]["barcode"]+'</option>';
    }
    table += 
	  ' </select>'
    + ' </td>'
    
    + ' <td width="20%">'
    + '		<span class="parts_name_edit" id="parts_name_edit'+counter+'">ชื่อสินค้า</span>'
    + '	</td>'
    + '	<td width="35%">'
    + '		<span class="parts_detail_edit" id="parts_detail_edit'+counter+'">รายละเอียด</span>'
    + '	</td>'
    
    + '<td width="10%"><input type="text" name="txt_unit_edit'+ counter +'" id="txt_unit_edit'+ counter +'" class="txt_unit_edit" style="width:70px; text-align:right"></td>'
    + ' </tr>'
    + ' </table><div class="linedotted" style="margin:0px"></div>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#TextBoxesGroup");
    
    // For make the jQuery Chosen for all Option
	$(".chosen-select").chosen({
		no_results_text: "ไม่มีข้อมูล"
	}); 
});

$("#btn_list_del.edit").click(function(){
    if(counter==1){
        return false;
    }
    $("#TextBoxDiv" + counter).remove();
    counter--;
});

$(".combo_mat_edit").live("change", function(){
	
	var this_id = $(this).data("code_id");
	var parts_code = $(this).val();
	
	var i = 0;
	var parts_name_value = "";
	var parts_detail_value = "";
	
	for(i = 0; i < parts_edit.length; i++){
		if(parts_code == parts_edit[i].code){
			parts_name_value = parts_edit[i].name;
			parts_detail_value = parts_edit[i].details;
			break;
		}
	}
	
	console.log("parts_name = " + parts_name_value);
	console.log("parts_detail_value = " + parts_detail_value);
	
	if(parts_code != "" ){
		$(".parts_name_edit#parts_name_edit"+this_id).html(parts_name_value);
		$(".parts_detail_edit#parts_detail_edit"+this_id).html(parts_detail_value);
	}
	else{
		$(this).val("");
		$(".parts_name_edit#parts_name_edit"+this_id).html("");
		$(".parts_detail_edit#parts_detai_edit"+this_id).html("");
	}
	$(".chosen-container#combo_mat_edit"+this_id+"_chosen .chosen-single span").html(parts_code);
});

function jquery_chosen(){
	$(".chosen-select").chosen({
		no_results_text: "ไม่มีข้อมูล"
	});
}

function callback_jquery_chosen(counter){
	for(var i = 1; i <= counter; i++){
		var parts_code = $("#combo_mat_edit"+i).val();
		$(".chosen-container#combo_mat_edit"+i+"_chosen .chosen-single span").html(parts_code);
	}
}

$(function(){
	// For make the jQuery Chosen for all Option
	jquery_chosen();
	
	setTimeout(function(){
		callback_jquery_chosen(counter)
	},0)
	
	
	$(".txt_unit_edit").live("keydown", function(e){
		// Allow: backspace, delete, tab, escape, enter and .
		if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
		     // Allow: Ctrl+A
		    (e.keyCode == 65 && e.ctrlKey === true) || 
		     // Allow: home, end, left, right
		    (e.keyCode >= 35 && e.keyCode <= 39)) {
		         // let it happen, don't do anything
		         return;
		}
		// Ensure that it is a number and stop the keypress
		if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
		    e.preventDefault();
		}
	});
});


$('#combo_mat_edit1').trigger('chosen:updated');

var parts_code_autocomplete = <?php echo json_encode(get_Parts_autocomplete_type_1()); ?>;
$("#project_edit_exist_parts").live("focus", function() {
	$(this).autocomplete({
		source: parts_code_autocomplete,
		minLength:1,
		select: function(event, ui) {
			if(ui.item.value == 'ไม่พบข้อมูลเก่า'){
				
			}else{
			   
			}
		}
	});
});

$('#btnEditAddMat').click(function(){
    $('body').append('<div id="DivEditAddMat" style="margin:5px; padding:0; font-size:12px"></div>');
    $('#DivEditAddMat').load('parts_project_api.php?cmd=div_edit_add_mat&pid=<?php echo $id; ?>');
    $('#DivEditAddMat').dialog({
        title: 'เพิ่ม Material',
        resizable: false,
        modal: true,  
        width: 900,
        height: 400,
        close: function(ev, ui){
            $('#DivEditAddMat').remove();
        }
    });
});

$('#btnSaveMat').click(function(){
	var new_project_check = 0;
	var _project_edit_exist_parts = $("#project_edit_exist_parts").val();
	if(_project_add_exist_parts == ""){
		new_project_check++;
		alert('กรุณากรอก รหัสสินค้าที่ผูกกับโปรเจค ');
        return false;
	}
	
    var arradd = new Array();
    for( i=1; i<=counter; i++ ){
        var cc = $('#combo_mat_edit'+ i).val();
        var uu = $('#txt_unit_edit'+ i).val();
        if(uu == "" || uu == 0){
            new_project_check++;
            alert('กรุณากรอกจำนวน (รายการที่ '+i+')');
            return false;
        }
        arradd[i-1] =  { mat:cc, unit:uu };
    }
    
    if(new_project_check == 0){
	    if(!confirm('ต้องการทำการบันทึกหรือไม่?')){
			return false;
		}
		else{
		    $.post('parts_project_api.php',{
		        cmd: 'edit_edit_mat_save',
		        project_edit_exist_parts: _project_edit_exist_parts,
		        pid: '<?php echo $id; ?>',
		        arradd: JSON.stringify(arradd)
		    },
		    function(data){
		        if(data.success){
		            alert(data.message);
		            console.log(data.message);
		            location.reload();
		        }else{
		            alert(data.message);
		            console.log(data.message);
		        }
		    },'json');
	
		}
	}
});

function popU(U, N, T) {
	newWindow = window.open(U, N, T);
	if (!newWindow.opener)
		newWindow.opener = self;
}

function project_add_new_Parts(url){
	javascript:popU(url,'','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=940,height=390');
}
    
function DelMatList(mid){
	
	if(confirm('คุณต้องการที่จะลบ Material หรือไม่') == false){
		return false;
	}
	else{
	    $.post('parts_project_api.php',{
	        cmd: 'del_mat_list',
	        pid: '<?php echo $id; ?>',
	        mid: mid
	    },
	    function(data){
	        if(data.success){
	            alert(data.message);
	            $('#div_edit').empty();
	            location.reload();
	            $('#div_edit').load('parts_project_api.php?cmd=div_edit&id=<?php echo $id; ?>');
	        }else{
	            alert(data.message);
	        }
	    },'json');
    }
}
</script>
<?php
}

elseif($cmd == "del_mat_list"){
    $pid = $_POST['pid'];
    $mid = $_POST['mid'];
    
    $in_qry="UPDATE \"ProjectDetails\" SET cancel='TRUE' WHERE project_id='$pid' AND material_id='$mid' ";
    if($result=@pg_query($in_qry)){
        $data['success'] = true;
        $data['message'] = "ลบรายการเรียบร้อยแล้ว";
    }else{
        $data['success'] = false;
        $data['message'] = "ไม่สามารถลบได้! เนื่องจาก $in_qry";
    }
    echo json_encode($data);
}

elseif($cmd == "div_edit_add_mat"){
    $pid = $_GET['pid'];
?>
<div style="margin-top:2px">
<b>เพิ่ม/ลบ รายการ</b> <input type="button" name="btn_list_add" id="btn_list_add" class="edit" value="+ เพิ่ม"><input type="button" name="btn_list_del" id="btn_list_del" class="edit" value="- ลบ">
</div>

<div style="margin-top:5px" >
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:left" bgcolor="#D0D0D0">
    <td width="5%">no.</td>
    <td width="30%">รหัสสินค้า</td>
    <td width="20%">ชื่อสินค้า</td>
    <td width="35%">รายละเอียด</td>
    <td width="10%">จำนวน</td>
</tr>
<tr bgcolor="#FFFFFF">
    <td>1.</td>
    <td>
		<select name="combo_mat_edit1" id="combo_mat_edit1" class="combo_mat_edit chosen-select" data-code_id="1">
		    <option value="">เลือก</option>
<?php
			$parts = get_Parts();
?>
		    <script>
		    	var parts_edit = <?php echo json_encode($parts); ?>;
		    </script>
<?php
			foreach ($parts as $key => $value){
?>
				<option value="<?php echo $value["code"]; ?>"><?php echo $value["code"]."#".$value["name"]."#".$value["details"]."#".$value["barcode"]; ?></option>
<?php 
			}
?>
		</select>
    </td>
    <td>
    	<span class="parts_name_edit" id="parts_name_edit1">ชื่อสินค้า</span>
    </td>
    <td>
    	<span class="parts_detail_edit" id="parts_detail_edit1" class="add">รายละเอียด</span>
    </td>
    <td>
    	<input type="text" name="txt_unit_edit1" id="txt_unit_edit1" class="txt_unit_edit div_edit_add_mat" style="width:70px; text-align:right" />
    </td>
</tr>
</table>

<div class="linedotted" style="margin:0px"></div>
    
<div id="TextBoxesGroup"></div>

</div>

<div style="clear:both"></div>

<div style="margin-top:8px; text-align:right; margin-bottom: 20px;">
<input type="button" name="btnSaveEdit_add_mat" id="btnSaveEdit_add_mat" value="บันทึก">
</div>

<script>
var counter = 1;

// For make the jQuery Chosen for all Option
$(".chosen-select").chosen({
	no_results_text: "ไม่มีข้อมูล"
}); 

$('#btn_list_add.edit').click(function(){
    counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);

    table = '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
    + ' <tr>'
    + ' <td width="5%">'+counter+'.</td>'
    + ' <td width="30%">'
    + ' <select name="combo_mat' + counter + '" id="combo_mat' + counter + '" class="combo_mat_edit chosen-select" data-code_id="' + counter + '" style="width: 100%; ">'
    + '		<option value="">เลือก</option>';
    for(var i=0; i < parts_edit.length; i++){
    	table += 
    	'	<option value="'+parts_edit[i]["code"]+'">'+parts_edit[i]["code"]+'#'+parts_edit[i]["code"]+'#'+parts_edit[i]["code"]+'</option>';
    }
    table += 
    + ' </select>'
    + ' </td>'
    + ' <td width="20%">'
    + '		<span class="parts_name_edit" id="parts_name_edit'+counter+'">ชื่อสินค้า</span>'
    + '	</td>'
    + '	<td width="35%">'
    + '		<span class="parts_detail_edit" id="parts_detail_edit'+counter+'">รายละเอียด</span>'
    + '	</td>'
    + '	<td width="10%"><input type="text" name="txt_unit_edit'+ counter +'" id="txt_unit_edit'+ counter +'" class="txt_unit_edit" style="width:70px; text-align:right"></td>'
    + ' </tr>'
    + ' </table><div class="linedotted" style="margin:0px"></div>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#TextBoxesGroup");
    
    // For make the jQuery Chosen for all Option
	$(".chosen-select").chosen({
		no_results_text: "ไม่มีข้อมูล"
	}); 
});

$("#btn_list_del.edit").click(function(){
    if(counter==1){
        return false;
    }
    $("#TextBoxDiv" + counter).remove();
    counter--;
});

$(".combo_mat_edit").live("change", function(){
	
	var this_id = $(this).data("code_id");
	var parts_code = $(this).val();
	
	var i = 0;
	var parts_name_value = "";
	var parts_detail_value = "";
	
	for(i = 0; i < parts_edit.length; i++){
		if(parts_code == parts_edit[i].code){
			parts_name_value = parts_edit[i].name;
			parts_detail_value = parts_edit[i].details;
			break;
		}
	}
	
	console.log("parts_name = " + parts_name_value);
	console.log("parts_detail_value = " + parts_detail_value);
	
	if(parts_code != "" ){
		$(".parts_name_edit#parts_name_edit"+this_id).html(parts_name_value);
		$(".parts_detail_edit#parts_detail_edit"+this_id).html(parts_detail_value);
	}
	else{
		$(this).val("");
		$(".parts_name_edit#parts_name_edit"+this_id).html("");
		$(".parts_detail_edit#parts_detail_edit"+this_id).html("");
	}
	
	$(".chosen-container#combo_mat_edit"+this_id+"_chosen .chosen-single span").html(parts_code);
});

$('#btnSaveEdit_add_mat').click(function(){
    var arradd = [];
    for( i=1; i<=counter; i++ ){
        var cc = $('#combo_mat_edit'+ i).val();
        var uu = $('.div_edit_add_mat.txt_unit_edit#txt_unit_edit'+ i).val();
        
        if(cc == ""){
            alert('กรุณาเลือก Material (รายการที่ '+i+')');
            return false;
        }
        if(uu == "" || uu == 0){
            alert('กรุณากรอกจำนวน (รายการที่ '+i+')');
            return false;
        }
        arradd[i] =  { mat:cc, unit:uu };
    }

    $.post('parts_project_api.php',{
        cmd: 'edit_add_mat_save',
        pid: '<?php echo $pid; ?>',
        txt_name: $('#txt_name').val(),
        arradd: JSON.stringify(arradd)
    },
    function(data){
        if(data.success){
            $('#DivEditAddMat').remove();
            $('#div_edit').empty();
            $('#div_edit').load('parts_project_api.php?cmd=div_edit&id=<?php echo $pid; ?>');
            alert(data.message);
            location.reload();
        }else{
            alert(data.message);
            console.log(data.message);
        }
    },'json');
});
</script>
<?php
}

elseif($cmd == "edit_add_mat_save"){
    $pid = $_POST['pid'];
    $arradd = json_decode(stripcslashes($_POST["arradd"]));
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    foreach($arradd as $key => $value){
        $mat = $value->mat;
        $unit = $value->unit;
        
        if(empty($mat) or empty($unit) ){
            continue;
        }
        
		$qry = "INSERT INTO \"ProjectDetails\" (project_id, material_id, use_unit) VALUES ('$pid','$mat','$unit')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT ProjectDetails ไม่สำเร็จ $qry";
            $status++;
        }
    }

    if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "เพิ่มรายการเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถเพิ่มรายการได้! เนื่องจาก $txt_error[0]";
    }
    echo json_encode($data);
}

elseif($cmd == "edit_edit_mat_save"){
    $pid = $_POST['pid'];
	$project_edit_exist_parts = pg_escape_string($_POST["project_edit_exist_parts"]);
    $arradd = json_decode(stripcslashes(pg_escape_string($_POST["arradd"])));
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
	
	// Set Old Data to Cancel Status
	$qry="
		UPDATE 
			\"ProjectDetails\" 
		SET 
			cancel = true
		WHERE 
			project_id='$pid' 
	";
	if(!$res=@pg_query($qry)){
	    $txt_error[] = "UPDATE ProjectDetails ไม่สำเร็จ $qry";
	    $status++;
	}
	
	//Update and Change the Project_id
	
	
	// project_edit_exist_parts
    
    foreach($arradd as $key => $value){
        $mat = $value->mat;
        $unit = $value->unit;
        
        if(empty($mat) or empty($unit) ){
            continue;
        }
        
        $qry="
        	INSERT INTO
        		\"ProjectDetails\"
        			(use_unit, project_id, material_id, cancel)
        		VALUES
        			('$unit', '$pid', '$mat', FALSE); 
        ";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "UPDATE ProjectDetails ไม่สำเร็จ $qry";
            $status++;
        }
    }

    if($status == 0){
        pg_query("COMMIT");
		// pg_query("ROLLBACK");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! เนื่องจาก $txt_error[0]";
    }
    echo json_encode($data);
}

elseif($cmd == "div_del_project"){
    $pid = $_GET['id'];
?>
<div style="font-weight:bold; text-align: center">การลบ Project ต้องได้รับการยืนยันจาก Admin</div>

<table width="230" cellspacing="0" cellpadding="3" border="0" align="center">
<tr>
    <td><B>User Admin</B></td>
    <td><input type="text" name="username" id="username" style="width:130px"></td>
</tr>
<tr>
    <td><B>Password</B></td>
    <td><input type="password" name="password" id="password" style="width:130px"></td>
</tr>
</table>

<div class="linedotted"></div>

<div style="margin-top:8px; text-align:right">
<input type="button" name="btnCFDel" id="btnCFDel" value="ยืนยัน">
</div>

<script>
$('#btnCFDel').click(function(){
    $.post('parts_project_api.php',{
        cmd: 'del_project_save',
        pid: '<?php echo $pid; ?>',
        username: $('#username').val(),
        password: $('#password').val()
    },
    function(data){
        if(data.success){
            alert(data.message);
            location.reload();
        }else{
            alert(data.message);
        }
    },'json');
});
</script>
<?php
}

elseif($cmd == "del_project_save"){
    $pid = $_POST['pid'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if(empty($password) OR empty($password)){
        $data['success'] = false;
        $data['message'] = "กรุณากรอกข้อมูลให้ครบถ้วน !";
    }else{
        $password = md5($password);

        // $qry = @pg_query("SELECT * FROM fuser WHERE username='$username' AND password='$password' AND status_user='TRUE' AND user_group='AD' ");
		$qry = @pg_query("SELECT * FROM fuser WHERE username='$username' AND password='$password' AND status_user='TRUE' AND user_group=3 ");
        if($res = @pg_fetch_array($qry)){
            $qry="UPDATE \"Projects\" SET cancel='TRUE' WHERE project_id='$pid' ";
            if($res=@pg_query($qry)){
                $data['success'] = true;
                $data['message'] = "ลบ Project เรียบร้อยแล้ว";
            }else{
                $data['success'] = false;
                $data['message'] = "Username และ Password ถูกต้อง แต่ไม่สามารถลบ Project ได้!";
            }
        }else{
            $data['success'] = false;
            $data['message'] = "Username หรือ Password ไม่ถูกต้อง !";
        }
    }
    echo json_encode($data);
}
?>
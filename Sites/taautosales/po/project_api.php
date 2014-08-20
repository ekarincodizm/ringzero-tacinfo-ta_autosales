<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "div_add"){
?>
<table cellpadding="1" cellspacing="1" border="0" width="100%">
<tr>
    <td width="20%"><b>ชื่อ Project :</b></td>
    <td width="80%"><input type="text" name="txt_name" id="txt_name" style="width:300px"></td>
</tr>
</table>

<div style="margin-top:10px; float:left; width:20%">
<b>เพิ่ม/ลบ รายการ</b><br><input type="button" name="btn_list_add" id="btn_list_add" value="+ เพิ่ม"><input type="button" name="btn_list_del" id="btn_list_del" value="- ลบ">
</div>

<div style="margin-top:10px; float:right; width:80%">
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:left" bgcolor="#D0D0D0">
    <td width="10%">no.</td>
    <td width="70%">Material</td>
    <td width="20%">จำนวน</td>
</tr>
<tr bgcolor="#FFFFFF">
    <td>1.</td>
    <td>
<select name="combo_mat1" id="combo_mat1">
    <option value="">เลือก</option>
<?php
$qry_list = pg_query("SELECT * FROM \"RawMaterialProduct\" ORDER BY product_id ASC");
while( $res_list = pg_fetch_array($qry_list) ){
    $material_id = $res_list['product_id'];
    $name = $res_list['name'];
?>
    <option value="<?php echo $material_id; ?>"><?php echo "$material_id : $name"; ?></option>
<?php
}
?>
</select>
    </td>
    <td><input type="text" name="txt_unit1" id="txt_unit1" style="width:70px; text-align:right"></td>
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

$('#btn_list_add').click(function(){
    counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);

    table = '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
    + ' <tr>'
    + ' <td width="10%">'+counter+'.</td>'
    + ' <td width="70%">'
    + ' <select id="combo_mat' + counter + '" name="combo_mat' + counter + '">'
    + ' <?php
        echo "<option value=\"\">เลือก</option>";
    $qry = pg_query("SELECT * FROM \"RawMaterialProduct\" ORDER BY product_id ASC");
    while( $res = pg_fetch_array($qry) ){
        $material_id = $res['product_id'];
        $name = $res['name'];
            $name = str_replace("'", "", $name);
        echo "<option value=\"$material_id\">$material_id : $name</option>";
    }
    ?>'
    + ' </select>'
    + ' </td>'
    + '<td width="20%"><input type="text" name="txt_unit'+ counter +'" id="txt_unit'+ counter +'" style="width:70px; text-align:right"></td>'
    + ' </tr>'
    + ' </table><div class="linedotted" style="margin:0px"></div>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#TextBoxesGroup");
});

$("#btn_list_del").click(function(){
    if(counter==1){
        return false;
    }
    $("#TextBoxDiv" + counter).remove();
    counter--;
});

$('#btnSave').click(function(){
    var arradd = [];
    for( i=1; i<=counter; i++ ){
        var cc = $('#combo_mat'+ i).val();
        var uu = $('#txt_unit'+ i).val();
        
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

    $.post('project_api.php',{
        cmd: 'add_save',
        txt_name: $('#txt_name').val(),
        arradd: JSON.stringify(arradd)
    },
    function(data){
        if(data.success){
            var pj_name = encodeURIComponent($('#txt_name').val());
            $('#div_add').remove();
            alert(data.message);
            ShowAddProduct(data.pj_id,pj_name);            
            //location.reload();
        }else{
            alert(data.message);
        }
    },'json');
});

function ShowAddProduct(pj_id,pj_name){
    $('body').append('<div id="divdialogadd"></div>');
    $('#divdialogadd').load('project_api.php?cmd=add_product&pj_id='+pj_id+'&pj_name='+pj_name);
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
    $txt_name = $_POST['txt_name'];
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

    $qry = "INSERT INTO \"Projects\" (project_id, name) VALUES ('$pj_id','$txt_name')";
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
        
        $qry = "INSERT INTO \"ProjectDetails\" (project_id, material_id, use_unit) VALUES ('$pj_id','$mat','$unit')";
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
    $.post('project_api.php',{
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
?>

<div style="text-align:right"><input type="button" name="btnEditAddMat" id="btnEditAddMat" value="+ เพิ่ม"></div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:center" bgcolor="#D0D0D0">
    <td width="10%">no.</td>
    <td width="60%">Material</td>
    <td width="20%">จำนวน</td>
    <td width="10%">ลบ</td>
</tr>
<?php
$j =0;
$qry_pj_dt = pg_query("SELECT * FROM \"ProjectDetails\" WHERE project_id='$id' AND cancel='FALSE' ORDER BY material_id ASC");
while( $res_pj_dt = pg_fetch_array($qry_pj_dt) ){
    $j++;
    $dt_material_id = $res_pj_dt['material_id'];
    $dt_use_unit = $res_pj_dt['use_unit'];
    
    if( substr($dt_material_id, 0,1) == "P" ){
        $dt_material_name = GetProductName($dt_material_id);
    }elseif( substr($dt_material_id, 0,1) == "M" ){
        $dt_material_name = GetRawMaterialName($dt_material_id);
    }
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $j; ?>.</td>
    <td><?php echo $dt_material_name; ?></td>
    <td>
        <input type="hidden" name="txt_mat_<?php echo $j; ?>" id="txt_mat_<?php echo $j; ?>" value="<?php echo $dt_material_id; ?>">
        <input type="text" name="txt_unit<?php echo $j; ?>" id="txt_unit<?php echo $j; ?>" style="width:95%; text-align:right" value="<?php echo $dt_use_unit; ?>"></td>
    <td align="center"><input type="button" name="btnDel" id="btnDel" value="X" onclick="javascript:DelMatList('<?php echo $dt_material_id; ?>')"></td>
</tr>
<?php
}
if($j == 0){
    echo "<tr><td colspan=\"4\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>
<div class="linedotted"></div>
<?php if($j > 0){ ?>
<div style="margin-top:8px; text-align:right">
<input type="button" name="btnSaveMat" id="btnSaveMat" value="บันทึก">
</div>
<?php } ?>

<script>
$('#btnEditAddMat').click(function(){
    $('body').append('<div id="DivEditAddMat" style="margin:5px; padding:0; font-size:12px"></div>');
    $('#DivEditAddMat').load('project_api.php?cmd=div_edit_add_mat&pid=<?php echo $id; ?>');
    $('#DivEditAddMat').dialog({
        title: 'เพิ่ม Material',
        resizable: false,
        modal: true,  
        width: 400,
        height: 300,
        close: function(ev, ui){
            $('#DivEditAddMat').remove();
        }
    });
});

$('#btnSaveMat').click(function(){
    var arradd = [];
    for( i=1; i<=<?php echo $j; ?>; i++ ){
        var cc = $('#txt_mat_'+ i).val();
        var uu = $('#txt_unit'+ i).val();

        if(uu == "" || uu == 0){
            alert('กรุณากรอกจำนวน (รายการที่ '+i+')');
            return false;
        }
        arradd[i] =  { mat:cc, unit:uu };
    }

    $.post('project_api.php',{
        cmd: 'edit_edit_mat_save',
        pid: '<?php echo $id; ?>',
        arradd: JSON.stringify(arradd)
    },
    function(data){
        if(data.success){
            $('#div_edit').empty();
            $('#div_edit').load('project_api.php?cmd=div_edit&id=<?php echo $id; ?>');
            alert(data.message);
        }else{
            alert(data.message);
        }
    },'json');
});
    
function DelMatList(mid){
    $.post('project_api.php',{
        cmd: 'del_mat_list',
        pid: '<?php echo $id; ?>',
        mid: mid
    },
    function(data){
        if(data.success){
            $('#div_edit').empty();
            $('#div_edit').load('project_api.php?cmd=div_edit&id=<?php echo $id; ?>');
            alert(data.message);
        }else{
            alert(data.message);
        }
    },'json');
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
<b>เพิ่ม/ลบ รายการ</b> <input type="button" name="btn_list_add" id="btn_list_add" value="+ เพิ่ม"><input type="button" name="btn_list_del" id="btn_list_del" value="- ลบ">
</div>

<div style="margin-top:5px">
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:left" bgcolor="#D0D0D0">
    <td width="10%">no.</td>
    <td width="70%">Material</td>
    <td width="20%">จำนวน</td>
</tr>
<tr bgcolor="#FFFFFF">
    <td>1.</td>
    <td>
<select name="combo_mat1" id="combo_mat1">
    <option value="">เลือก</option>
<?php
$qry_list = pg_query("SELECT * FROM \"RawMaterialProduct\" ORDER BY product_id ASC");
while( $res_list = pg_fetch_array($qry_list) ){
    $material_id = $res_list['product_id'];
    $name = $res_list['name'];
?>
    <option value="<?php echo $material_id; ?>"><?php echo "$material_id : $name"; ?></option>
<?php
}
?>
</select>
    </td>
    <td><input type="text" name="txt_unit1" id="txt_unit1" style="width:70px; text-align:right"></td>
</tr>
</table>

<div class="linedotted" style="margin:0px"></div>
    
<div id="TextBoxesGroup"></div>

</div>

<div style="clear:both"></div>

<div style="margin-top:8px; text-align:right">
<input type="button" name="btnSaveEdit_add_mat" id="btnSaveEdit_add_mat" value="บันทึก">
</div>

<script>
var counter = 1;

$('#btn_list_add').click(function(){
    counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);

    table = '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
    + ' <tr>'
    + ' <td width="10%">'+counter+'.</td>'
    + ' <td width="70%">'
    + ' <select id="combo_mat' + counter + '" name="combo_mat' + counter + '">'
    + ' <?php
        echo "<option value=\"\">เลือก</option>";
    $qry = pg_query("SELECT * FROM \"RawMaterialProduct\" ORDER BY product_id ASC");
    while( $res = pg_fetch_array($qry) ){
        $material_id = $res['product_id'];
        $name = $res['name'];
            $name = str_replace("'", "", $name);
        echo "<option value=\"$material_id\">$material_id : $name</option>";
    }
    ?>'
    + ' </select>'
    + ' </td>'
    + '<td width="20%"><input type="text" name="txt_unit'+ counter +'" id="txt_unit'+ counter +'" style="width:70px; text-align:right"></td>'
    + ' </tr>'
    + ' </table><div class="linedotted" style="margin:0px"></div>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#TextBoxesGroup");
});

$("#btn_list_del").click(function(){
    if(counter==1){
        return false;
    }
    $("#TextBoxDiv" + counter).remove();
    counter--;
});

$('#btnSaveEdit_add_mat').click(function(){
    var arradd = [];
    for( i=1; i<=counter; i++ ){
        var cc = $('#combo_mat'+ i).val();
        var uu = $('#txt_unit'+ i).val();
        
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

    $.post('project_api.php',{
        cmd: 'edit_add_mat_save',
        pid: '<?php echo $pid; ?>',
        txt_name: $('#txt_name').val(),
        arradd: JSON.stringify(arradd)
    },
    function(data){
        if(data.success){
            $('#DivEditAddMat').remove();
            $('#div_edit').empty();
            $('#div_edit').load('project_api.php?cmd=div_edit&id=<?php echo $pid; ?>');
            alert(data.message);
        }else{
            alert(data.message);
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
        
        $qry="UPDATE \"ProjectDetails\" SET use_unit='$unit' WHERE project_id='$pid' AND material_id='$mat' ";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "UPDATE ProjectDetails ไม่สำเร็จ $qry";
            $status++;
        }
    }

    if($status == 0){
        pg_query("COMMIT");
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
    $.post('project_api.php',{
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

        $qry = @pg_query("SELECT * FROM fuser WHERE username='$username' AND password='$password' AND status_user='TRUE' AND user_group='AD' ");
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
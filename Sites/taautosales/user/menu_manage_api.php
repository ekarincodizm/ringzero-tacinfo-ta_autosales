<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$cmd = $_REQUEST['cmd'];

if($cmd == "divadd"){
?>

<table cellpadding="5" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">ID :</td>
    <td><input type="text" name="txt_id" id="txt_id" style="width:60px"></td>
</tr>
<tr>
    <td>ชื่อเมนู :</td>
    <td><input type="text" name="txt_namemenu" id="txt_namemenu" style="width:200px"></td>
</tr>
<tr>
    <td>Path Menu :</td>
    <td><input type="text" name="txt_path" id="txt_path" style="width:280px"></td>
</tr>
<tr>
    <td>สถานะ :</td>
    <td>
<input type="radio" name="radio_status" id="radio_status" value="1" checked> ใช้งาน
<input type="radio" name="radio_status" id="radio_status" value="2"> ปิดใช้งาน
    </td>
</tr>
</table>

<div style="text-align:right">
<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
</div>

<script>
$('#btnSubmit').click(function(){
    $.post('menu_manage_api.php',{
        cmd: 'add',
        txt_id: $('#txt_id').val(),
        txt_namemenu: $('#txt_namemenu').val(),
        txt_path: $('#txt_path').val(),
        radio_status: $('input[id=radio_status]:checked').val(),
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

elseif($cmd == "add"){
    $txt_id = $_POST['txt_id'];
    $txt_namemenu = $_POST['txt_namemenu'];
    $txt_path = $_POST['txt_path'];
    $radio_status = $_POST['radio_status'];

    if( empty($txt_id) OR empty($txt_namemenu) OR empty($txt_path) OR empty($radio_status) ){
        $data['success'] = false;
        $data['message'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
        echo json_encode($data);
        exit;
    }
    
    if($radio_status == 2){ $radio_status = 0; }
    
    $qry = "INSERT INTO f_menu (\"id_menu\",\"name_menu\",\"status_menu\",\"path_menu\") VALUES ('$txt_id','$txt_namemenu','$radio_status','$txt_path')";
    if($res=@pg_query($qry)){
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $qry";
    }
    echo json_encode($data);
}

elseif($cmd == "divedit"){
    $id = $_GET['id'];
    
    $qry = pg_query("SELECT * FROM f_menu WHERE id_menu='$id' ");
    if($res = pg_fetch_array($qry)){
        $name_menu=$res['name_menu'];
        $status_menu=$res['status_menu'];
        $path_menu=$res['path_menu'];
    }
?>

<table cellpadding="5" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">ID :</td>
    <td><?php echo $id; ?></td>
</tr>
<tr>
    <td>ชื่อเมนู :</td>
    <td><input type="text" name="txt_namemenu" id="txt_namemenu" value="<?php echo $name_menu; ?>" style="width:200px"></td>
</tr>
<tr>
    <td>Path Menu :</td>
    <td><input type="text" name="txt_path" id="txt_path" value="<?php echo $path_menu; ?>" style="width:280px"></td>
</tr>
<tr>
    <td>สถานะ :</td>
    <td>
<input type="radio" name="radio_status" id="radio_status" value="1" <?php if($status_menu == 1){ echo "checked"; } ?>> ใช้งาน
<input type="radio" name="radio_status" id="radio_status" value="2" <?php if($status_menu != 1){ echo "checked"; } ?>> ปิดใช้งาน
    </td>
</tr>
</table>

<div style="text-align:right">
<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
</div>

<script>
$('#btnSubmit').click(function(){
    $.post('menu_manage_api.php',{
        cmd: 'edit',
        id: '<?php echo $id; ?>',
        txt_namemenu: $('#txt_namemenu').val(),
        txt_path: $('#txt_path').val(),
        radio_status: $('input[id=radio_status]:checked').val()
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

elseif($cmd == "edit"){
    $id = $_POST['id'];
    $txt_namemenu = $_POST['txt_namemenu'];
    $txt_path = $_POST['txt_path'];
    $radio_status = $_POST['radio_status'];
    
    if( empty($id) OR empty($txt_namemenu) OR empty($txt_path) OR empty($radio_status) ){
        $data['success'] = false;
        $data['message'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
        echo json_encode($data);
        exit;
    }
    
    if($radio_status == 2){ $radio_status = 0; }
    
    $qry = "UPDATE f_menu SET name_menu='$txt_namemenu',status_menu='$radio_status',path_menu='$txt_path' WHERE id_menu='$id' ";
    if($res=@pg_query($qry)){
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $qry";
    }
    echo json_encode($data);
}

?>
<?php
include_once("include/config.php");
include_once("include/function.php");

$seed = $_SESSION["session_company_seed"];
if(!CheckAuth()){
    exit();
}

$cmd = $_REQUEST['cmd'];

if($cmd == "showdiv"){
?>

<table cellpadding="5" cellspacing="0" border="0" width="100%" align="center">
<tr> 
    <td width="130">รหัสผ่านเดิม</td><td><input type="password" name="txt_pass_old" id="txt_pass_old"></td>
</tr>
<tr>
    <td>รหัสผ่านใหม่</td><td><input type="password" name="txt_pass_new1" id="txt_pass_new1"></td>
</tr>
<tr>
    <td>รหัสผ่านใหม่ อีกครั้ง</td><td><input type="password" name="txt_pass_new2" id="txt_pass_new2"></td>
</tr>
<tr>
    <td>&nbsp;</td><td><input type="button" name="btnSave" id="btnSave" value="บันทึก"></td>
</tr>
</table>

<script>
$('#btnSave').click(function(){
    if( $('#txt_pass_old').val() == "" ){
        alert('กรุณากรอก รหัสผ่านเดิม !');
        $('#txt_pass_old').focus();
        return false;
    }else if( $('#txt_pass_new1').val() == "" ){
        alert('กรุณากรอก รหัสผ่านใหม่ !');
        $('#txt_pass_new1').focus();
        return false;
    }else if( $('#txt_pass_new2').val() == "" ){
        alert('กรุณากรอก รหัสผ่านใหม่ อีกครั้ง !');
        $('#txt_pass_new2').focus();
        return false;
    }else if( $('#txt_pass_new1').val() != $('#txt_pass_new2').val() ){
        alert('รหัสผ่านใหม่ 2ครั้ง ไม่เหมือนกัน !');
        $('#txt_pass_new1').focus();
        return false;
    }
    
    $.post('change_pass.php',{
        cmd: 'save',
        txt_pass_old : $('#txt_pass_old').val(),
        txt_pass_new1 : $('#txt_pass_new1').val(),
        txt_pass_new2 : $('#txt_pass_new2').val()
    },
    function(data){
        if(data.success){
            $('#DivChangePass').remove();
            alert(data.message);
        }else{
            alert(data.message);
        }
    },'json');
    
});
</script>

<?php
}

elseif($cmd == "save"){

    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $txt_pass_old = $_POST['txt_pass_old'];
    $txt_pass_new1 = $_POST['txt_pass_new1'];

    $qry = pg_query("SELECT password FROM fuser WHERE id_user='$_SESSION[ss_iduser]' ");
    if($res = pg_fetch_array($qry)){
       $password = $res["password"];
    }
    
    if($password != md5($txt_pass_old) AND $password != md5(md5($txt_pass_old).$seed)){
        $txt_error[] = "รหัสผ่านเดิม ไม่ถูกต้อง !";
		
        $status++;
    }else{
		$seed = $_SESSION["session_company_seed"];
        $txt_pass_new1 = md5(md5($txt_pass_new1).$seed);
        $qry = "UPDATE fuser SET password='$txt_pass_new1' WHERE id_user='$_SESSION[ss_iduser]' ";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "UPDATE fuser password ไม่สำเร็จ $qry";
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
        $data['message'] = "$txt_error[0]";
    }
    
    echo json_encode($data);
    
}
?>
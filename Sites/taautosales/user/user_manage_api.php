<?php
include_once("../include/config.php");
include_once("../include/function.php");
$seed = $_SESSION["session_company_seed"];
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$cmd = $_REQUEST['cmd'];

if($cmd == "divadd"){
?>

<table cellpadding="5" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">Username :</td>
    <td><input type="text" name="txt_username" id="txt_username"></td>
</tr>
<tr>
    <td>Password :</td>
    <td><input type="password" name="txt_password" id="txt_password"></td>
</tr>
<tr>
    <td>ชื่อ/สกุล :</td>
    <td><input type="text" name="txt_name" id="txt_name"></td>
</tr>
<tr>
    <td>กลุ่มผู้ใช้ :</td>
    <td>
<select name="combo_group" id="combo_group">
<?php
$qry = pg_query("SELECT * FROM f_groupuser ORDER BY name_group ASC");
while($res = pg_fetch_array($qry)){
    $id_qroup = $res['id_qroup'];
    $name_group = $res['name_group'];
    echo "<option value=\"$id_qroup\">$name_group</option>";
}
?>
</select>
    </td>
</tr>
<tr>
    <td>สาขา :</td>
    <td>
<select name="combo_office" id="combo_office">
    <?php 
		$qry = pg_query("select * from branch order by branch_id");
		while($branch = pg_fetch_array($qry)){
			$branch_id = $branch['branch_id'];
			$branch_name = $branch['branch_name'];
			
			echo "<option value=\"$branch_id\">$branch_name</option>";
		}
	?>
</select>
    </td>
</tr>
</table>

<div style="text-align:right">
<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
</div>

<script>
$('#btnSubmit').click(function(){
    $.post('user_manage_api.php',{
        cmd: 'add',
        txt_username: $('#txt_username').val(),
        txt_password: $('#txt_password').val(),
        txt_name: $('#txt_name').val(),
        combo_group: $('#combo_group').val(),
        combo_office: $('#combo_office').val()
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
    $txt_username = $_POST['txt_username'];
    $txt_password = $_POST['txt_password'];
    $txt_name = $_POST['txt_name'];
    $combo_group = $_POST['combo_group'];
    $combo_office = $_POST['combo_office'];
    
    if( empty($txt_username) OR empty($txt_password) OR empty($txt_name) OR empty($combo_group) OR empty($combo_office) ){
        $data['success'] = false;
        $data['message'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
        echo json_encode($data);
        exit;
    }
    
    $iduser = GetUserID();
	
    $txt_password = md5(md5($txt_password).$seed);
    
    $qry = "INSERT INTO fuser (\"id_user\",\"fullname\",\"username\",\"password\",\"office_id\",\"user_group\",\"status_user\",\"last_log\",\"last_datepassword\") VALUES 
    ('$iduser','$txt_name','$txt_username','$txt_password','$combo_office','$combo_group','TRUE',DEFAULT,DEFAULT)";
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

    $qry = pg_query("SELECT * FROM fuser WHERE id_user='$id' ");
    if($res = pg_fetch_array($qry)){
        $fullname=$res['fullname'];
        $username=$res['username'];
        $password=$res['password'];
        $office_id=$res['office_id'];
        $user_group=$res['user_group'];
        $status_user=$res['status_user'];
    }
?>

<div style="float:left; width:220px; border-right: 1px solid #E0E0E0;">

<table cellpadding="5" cellspacing="0" border="0" width="100%">
<tr>
    <td width="80">ID :</td>
    <td><?php echo $id; ?></td>
</tr>
<tr>
    <td>Username :</td>
    <td><input type="text" name="txt_username" id="txt_username" value="<?php echo $username; ?>" style="width:100px; background-color:#CCCCCC;" readOnly></td>
</tr>
<tr>
    <td>Password :</td>
    <td><input type="password" name="txt_password" id="txt_password" style="width:100px; background-color:#CCCCCC;" readOnly></td>
</tr>
<tr>
    <td>ชื่อ/สกุล :</td>
    <td><input type="text" name="txt_name" id="txt_name" value="<?php echo $fullname; ?>" style="width:100px; background-color:#CCCCCC;" readOnly></td>
</tr>
<tr>
    <td>กลุ่มผู้ใช้  :</td>
    <td>
<select name="combo_group" id="combo_group">
<?php
$qry = pg_query("SELECT * FROM f_groupuser ORDER BY name_group ASC");
while($res = pg_fetch_array($qry)){
    $id_qroup = $res['id_qroup'];
    $name_group = $res['name_group'];
    if($user_group == $id_qroup){
        echo "<option value=\"$id_qroup\" selected>$name_group</option>";
    }else{
        echo "<option value=\"$id_qroup\">$name_group</option>";
    }
}
?>
</select>
    </td>
</tr>
<tr>
    <td>สาขา :</td>
    <td>
<select name="combo_office" id="combo_office">
	 <?php 
		$qry = pg_query("select * from branch order by branch_id");
		while($branch = pg_fetch_array($qry)){
			$branch_id = $branch['branch_id'];
			$branch_name = $branch['branch_name'];
			if($office_id == $branch_id){
				echo "<option value=\"$branch_id\" selected >$branch_name</option>";
			}else{
				echo "<option value=\"$branch_id\">$branch_name</option>";
			}
		}
	?>
</select>
    </td>
</tr>
<tr>
    <td valign="top">สถานะ :</td>
    <td>
<input type="radio" name="radio_status" id="radio_status" value="1" <?php if($status_user == 't'){ echo "checked"; } ?>> ใช้งานได้<br />
<input type="radio" name="radio_status" id="radio_status" value="2" <?php if($status_user != 't'){ echo "checked"; } ?>> ระงับใช้งาน
    </td>
</tr>
</table>

<div>
<input type="button" name="btn_SaveUser" id="btn_SaveUser" value="บันทึก">
</div>

</div>
<div style="float:right; width:380px">

<div style="float:left"><b>จัดการเมนู</b></div>
<div style="float:right"><input type="button" name="btnAddRow" id="btnAddRow" value="เพิ่ม"><input type="button" name="btnDelRow" id="btnDelRow" value="ลบ"></div>
<div style="clear:both"></div>

<div id="divmenuuser" style="height:300px; overflow:auto">
<table cellpadding="3" cellspacing="0" border="0" width="100%">
<?php
$j = 0;
$arr_menu = array();
$qry = pg_query("SELECT * FROM f_usermenu WHERE id_user='$id' ORDER BY id_menu ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $id_menu=$res['id_menu'];
    $id_user=$res['id_user'];
    $status=$res['status'];
    
    $arr_menu[] = $id_menu;
    
    $qry_name = pg_query("SELECT name_menu FROM f_menu WHERE id_menu='$id_menu' ");
    if($res_name = pg_fetch_array($qry_name)){
        $name_menu=$res_name['name_menu'];
    }
?>
<tr>
    <td><?php echo "$j."; ?></td>
    <td><?php echo $name_menu; ?></td>
    <td>
<input type="hidden" name="txt_hid_mid<?php echo $j; ?>" id="txt_hid_mid<?php echo $j; ?>" value="<?php echo $id_menu; ?>">
<input type="radio" name="radio_open<?php echo $j; ?>" id="radio_open<?php echo $j; ?>" value="1" <?php if($status == 't'){ echo "checked"; } ?>> เปิด
<input type="radio" name="radio_open<?php echo $j; ?>" id="radio_open<?php echo $j; ?>" value="2" <?php if($status == 'f'){ echo "checked"; } ?>> ปิด</td>
</tr>
<?php
}
?>
</table>

<div id="divmsgerror" style="text-align:center">
<?php
if($j == 0){ echo "- ยังไม่มีรายการเมนู -<br />คลิกปุ่มเพิ่ม เพื่อเพิ่มรายการ"; }
?>
</div>

<div id="TextBoxesGroup"></div>

</div>

<div style="text-align:right">
<input type="button" name="btn_SaveMenu" id="btn_SaveMenu" value="บันทึกเมนู">
</div>

</div>

<script>
$('#btn_SaveUser').click(function(){
    $.post('user_manage_api.php',{
        cmd: 'edit_save',
        id: '<?php echo $id; ?>',
        txt_username: $('#txt_username').val(),
        txt_password: $('#txt_password').val(),
        txt_name: $('#txt_name').val(),
        combo_group: $('#combo_group').val(),
        combo_office: $('#combo_office').val(),
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

var counter = 0;

$('#btnAddRow').click(function(){
    $('#divmsgerror').hide();
    counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);

    table = '<table width="100%" cellpadding="3" cellspacing="0" border="0">'
    + ' <tr>'
    + ' <td width="28">'+ counter +'.</td>'
    + ' <td>'
    + ' <select id="cb_menu' + counter + '" name="cb_menu' + counter + '">'
    + ' <?php
    echo "<option value=\"\">เลือก</option>";
    $qry_menu = pg_query("SELECT * FROM f_menu WHERE status_menu='1' ORDER BY id_menu ASC");
    while($res_menu = pg_fetch_array($qry_menu) ){
        $id_menu = $res_menu['id_menu'];
        $name_menu = $res_menu['name_menu'];
        
        if(!in_array($id_menu,$arr_menu)){
            echo "<option value=\"$id_menu\">$name_menu</option>";
        }
    }
    ?>'
    + ' </select>'
    + ' </td>'
    + ' </tr>'
    + ' </table>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#TextBoxesGroup");
    
});

$("#btnDelRow").click(function(){
    if(counter==0){
        return false;
    }
    $("#TextBoxDiv" + counter).remove();
    counter--;
});

$('#btn_SaveMenu').click(function(){
    var arrnewmenu = [];
    for( i=1; i<=counter; i++ ){
        var c1 = $('#cb_menu'+ i).val();
        if(c1 != ''){
            arrnewmenu[i] =  { mid : c1 };
        }
    }
    
    var arroldmenu = [];
    for( i=1; i<=<?php echo $j; ?>; i++ ){
        var c1 = $('input[id=radio_open'+ i +']:checked').val();
        arroldmenu[i] =  { hid : $('#txt_hid_mid'+i).val() , stat : c1 };
    }
    
    $.post('user_manage_api.php',{
        cmd: 'edit_save_menu',
        id: '<?php echo $id; ?>',
        arrnewmenu: JSON.stringify(arrnewmenu),
        arroldmenu: JSON.stringify(arroldmenu)
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

elseif($cmd == "edit_save_menu"){
    $id = $_POST['id'];
    $arrnewmenu = json_decode(stripcslashes($_POST["arrnewmenu"]));
    $arroldmenu = json_decode(stripcslashes($_POST["arroldmenu"]));
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();

    foreach($arrnewmenu as $key => $value){
        $mid = $value->mid;
        if($mid != ""){
            $in_qry="insert into \"f_usermenu\" (\"id_menu\",\"id_user\") values ('$mid','$id')";
            if(!$result=@pg_query($in_qry)){
                $txt_error[] = "บันทึก f_usermenu ไม่สำเร็จ $in_qry";
                $status++;
            }
        }
    }

    foreach($arroldmenu as $key => $value){
        $stat = $value->stat;
        $hid = $value->hid;
        
        if($stat == 1){ $str_stat = "TRUE"; }else{ $str_stat = "FALSE"; }

        $in_qry="UPDATE \"f_usermenu\" SET status='$str_stat' WHERE id_menu='$hid' AND id_user='$id' ";
        if(!$result=@pg_query($in_qry)){
            $txt_error[] = "บันทึก update f_usermenu ไม่สำเร็จ $in_qry";
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
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
    echo json_encode($data);

}

elseif($cmd == "edit_save"){
    $id = $_POST['id'];
    $txt_username = $_POST['txt_username'];
    $txt_password = $_POST['txt_password'];
    $txt_name = $_POST['txt_name'];
    $combo_group = $_POST['combo_group'];
    $combo_office = $_POST['combo_office'];
    $radio_status = $_POST['radio_status'];
    
    if( empty($id) OR empty($txt_username) OR empty($txt_name) OR empty($combo_group) OR empty($combo_office) OR empty($radio_status) ){
        $data['success'] = false;
        $data['message'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
        echo json_encode($data);
        exit;
    }
    
    if($radio_status == 1){ $str_status = "TRUE"; }else{ $str_status = "FALSE"; }
    
    if(empty($txt_password)){
        $qry = "UPDATE fuser SET fullname='$txt_name',username='$txt_username',office_id='$combo_office',user_group='$combo_group',status_user='$str_status' WHERE id_user='$id' ";
    }else{
        $txt_password = md5(md5($txt_password).$seed);
        $qry = "UPDATE fuser SET fullname='$txt_name',username='$txt_username',password='$txt_password',office_id='$combo_office',user_group='$combo_group',status_user='$str_status' WHERE id_user='$id' ";
    }

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
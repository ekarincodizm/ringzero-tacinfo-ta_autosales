<?php
include_once("include/config.php");
?>
<meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
<?php
//header ('Content-type: text/html; charset=utf-8 without BOM');
$username = pg_escape_string($_POST['username']);
// $password = md5($_POST['password']);
$seed = $_SESSION["session_company_seed"];
$passSend = pg_escape_string($_POST['password']); // pass ในการเช็คค่าเปลี่ยนบริษัท
$passwordbemd = md5($_POST['password']);
$passwordafmd = md5(md5($_POST['password']).$seed);
$qry = pg_query("SELECT * FROM fuser WHERE username='$username' AND status_user='TRUE' AND (password='$passwordbemd' OR password='$passwordafmd')");
if($res = pg_fetch_array($qry)){
    $_SESSION["ss_iduser"] = $res['id_user'];
    $_SESSION["ss_username"] = $res['fullname'];
	//$_SESSION["ss_fullname"] = $res['fullname'];
    $_SESSION["ss_office_id"] = $res['office_id'];
    $_SESSION["ss_user_group"] = $res['user_group'];
    $_SESSION["ss_last_log"] = $res['last_log'];
	
	$_SESSION['user_login'] = $username;
	$_SESSION['pass_login'] = $passSend;
	
	//echo "<script>alert('$_SESSION[ss_iduser]');</script>";
    
	$upd_sql="UPDATE fuser SET \"last_log\"=NOW() WHERE id_user='$res[id_user]' ";
    if($result=pg_query($upd_sql)){
        header("Location: main.php");   
    }else{
	    echo '<script language="Javascript">alert ("ไม่สามารถบันทึกเวลาการเข้าใช้งานระบบได้ !");</script>';
        header("Refresh: 0; url=logout.php");
    }
}else{
	echo '<script language="Javascript">alert ("ชื่อผู้ใช้หรือรหัสผ่าน ไม่ถูกต้อง !");</script>';
    header("Refresh: 0; url=index.php");
}
?>
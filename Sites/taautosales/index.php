<?php
include_once("include/config.php");
include_once("include/function.php");

$passlog = pg_escape_string($_GET['passlog']);

if($passlog == "1")
{
	$user_login = $_SESSION['user_login'];
	$pass_login = $_SESSION['pass_login'];
}
else
{
	$user_login = "";
	$pass_login = "";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <title><?php echo $company_name; ?> - เข้าสู่ระบบ</title>
    <LINK href="images/styles.css" type=text/css rel=stylesheet>
    
    <link type="text/css" href="images/jqueryui/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="images/jqueryui/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="images/jqueryui/js/jquery-ui-1.8.16.custom.min.js"></script>
    
</head>
<body>
<?php if($_SESSION["ss_iduser"] == ""){ ?>
<div class="roundedcornr_box" style="width:430px">
   <div class="roundedcornr_top"><div></div></div>
      <div class="roundedcornr_content">

<div style="float:left"><img src="images/logo.jpg" border="0" width="100" height="100" alt="<?php echo $company_name; ?>"></div>
<div style="float:right">

<form method="post" action="login.php">
<table width="230" cellspacing="0" cellpadding="3" border="0" align="center">
<tr>
    <td><B>ชื่อผู้ใช้</B></td>
    <td><input type="text" name="username" id="username" style="width:150px" value="<?php echo $user_login; ?>"></td>
</tr>
<tr>
    <td><B>รหัสผ่าน</B></td>
    <td><input type="password" name="password" id="password" style="width:150px" value="<?php echo $pass_login; ?>"></td>
</tr>
<tr>
    <td>&nbsp;</TD>
    <td><input type="submit" value="เข้าสู่ระบบ" name="btnLogin" id="btnLogin"></td>
</tr>
</table>
</form>

</div>
<div style="clear:both"></div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $('#username').focus();
});

var passlog = '<?php echo $passlog; ?>';
if(passlog == '1')
{
	document.getElementById("btnLogin").click();
}
</script>
</body>
<?php 
}
else{
	header("Location: main.php");
}
?>
</html>
<?php
include_once("include/config.php");
include_once("include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=index.php");
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <title><?php echo $company_name; ?> - หน้าหลัก</title>
    <LINK href="images/styles.css" type=text/css rel=stylesheet>

    <link type="text/css" href="images/jqueryui/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="images/jqueryui/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="images/jqueryui/js/jquery-ui-1.8.16.custom.min.js"></script>

<script type="text/javascript">
var wnd = new Array();
function popU(U,N,T){
    wnd[N] = window.open(U, N, T);
}
function closeAll(){
    for (i in wnd){
        wnd[i].close();
    }
}

$(function(){
    $(window).bind("beforeunload",function(event){
        var msg = "คุณกำลังปิดหน้าต่างหลัก\nหน้าต่างโปรแกรมที่เกี่ยวข้อง จะปิดตัวลงทั้งหมด ?";
        $(window).bind("unload",function(event){
            event.stopImmediatePropagation();
            closeAll();
        });
        return msg;
    });
    
    var auto_refresh = setInterval(function(){
        $.get('session_expire.php', function(data){
            if(data == 0){
                $(window).unbind('beforeunload');
                closeAll();
                $('#div_panel_main').empty();
                $('#div_panel_main').html('<div style="text-align:center"><img src="images/icon_session_timeout.png" border="0" width="256" height="256"><br>เนื่องจากท่านไม่มีการทำงานต่อเนื่องในระยะเวลาที่กำหนด ระบบจึงปิดตัวเองเพื่อความปลอดภัย<br>หากท่านต้องการทำงาน กรุณา LOGIN เข้าระบบใหม่ <input type="button" value="Login" onclick="window.location=\'index.php\'"></div>');
            }else{
                $('#load_session_expire').text(data);
            }
        });
    }, 1000); // refresh every 1 seconds
});

function menulog(name){
	$.post("menu_log.php",{
			id : name			
		}
	)
}
$(function(){
    $('#div_user_menu').load('list_admin_menu.php');
});
var refreshId2 = setInterval(function(){
    $('#div_user_menu').load('list_admin_menu.php');
}, 30000);
</script>

</head>
<body onLoad="begintimer()">

<div class="roundedcornr_box" style="width:1000px">
   <div class="roundedcornr_top"><div></div></div>
      <div class="roundedcornr_content">

<div id="div_panel_main">
<?php include_once("include/header.php"); ?>
<br>
<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
<tr>
	<td colspan="4">
		<div id="div_user_menu"></div>	<!-- ที่แสดง admin menu -->
	</td>	
</tr>
<tr><td colspan="4"><br></td></tr>
<tr>
<?php
$admin_array = $_session['menu_admin'];
$j = 0;
$result=pg_query("SELECT A.*,B.* FROM f_usermenu A INNER JOIN f_menu B on A.id_menu=B.id_menu WHERE (A.id_user='$_SESSION[ss_iduser]') AND (B.status_menu='1') AND (A.status=true) ORDER BY A.id_menu ASC");
while($arr_menu = pg_fetch_array($result)){
    $menu_id = $arr_menu["id_menu"];
    $menu_name = $arr_menu["name_menu"];
    $menu_path = $arr_menu["path_menu"];
	
	if(!in_array($menu_id,$admin_array)){
            $arr['user'][$menu_id]['name'] = "$menu_name";
            $arr['user'][$menu_id]['path'] = "$menu_path";
			$arr['user'][$menu_id]['idmenu_log'] = "$menu_id";
        }
    
   
}
if( count($arr['user']) > 0 ){ 

	foreach($arr['user'] as $k => $v){
		$j++;	
?>
			<td width="24%" align="center" style="font-weight:bold; height:80px">
				<a href="javascript:popU('<?php echo $v['path']; ?>?ss_iduser=<?php echo $_SESSION["ss_iduser"]; ?>','<?php echo $v['idmenu_log']; ?>','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=950,height=670'),menulog('<?php echo $v['idmenu_log']; ?>')">
					<img src="images/menu/<?php echo strtolower($v['idmenu_log']); ?>.gif" border="0" width="80" height="80"><br /><?php echo $v['name']; ?>
				</a>
			</td>
<?php

		if($j == 4){
			$j = 0;
			echo "</tr><tr>";
		}
	}

}

//echo "<script>alert('$_SESSION[ss_iduser] bbb');</script>";
?>

</tr>
</table>

</div>
    
      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

</body>
</html>
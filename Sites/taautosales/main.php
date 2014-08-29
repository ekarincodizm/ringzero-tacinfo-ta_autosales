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
var searchText = ''; // เมนูที่จะค้นหา
var searchTextSend = ''; // ค่าที่จะส่งไปหาเมนู

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
    $('#div_admin_menu').load('list_admin_menu.php');
	 $('#div_user_menu').load('list_user_menu.php');
});

// alert admin menu ที่จะ refresh ทุก 30 วินาที
var refreshId2 = setInterval(function(){
    $('#div_admin_menu').load('list_admin_menu.php?searchText='+searchTextSend);
}, 30000);

// alert admin menu ที่จะ refresh ทุก 10 นาที
var refreshId3 = setInterval(function(){
    $('#div_user_menu').load('list_user_menu.php?searchText='+searchTextSend);
}, 600000);

$(document).ready(function(){
	$("#searchText").autocomplete({
		source: "list_menu_search.php",
		minLength:1
	});
});

function searchLoad() // ค้นหาเมนู
{
	searchText = document.getElementById("searchText").value;
	
	searchTextSend = searchText.replace(" ","TspaceT","g");
	
	$('#div_admin_menu').load('list_admin_menu.php?searchText='+searchTextSend);
	$('#div_user_menu').load('list_user_menu.php?searchText='+searchTextSend);
	
	if(searchText != '')
	{
		document.getElementById("searchSpan").innerHTML = 'ค้นหาเมนูด้วยคำว่า "'+searchText+'"';
	}
	else
	{
		document.getElementById("searchSpan").innerHTML = '';
	}
}
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
		<div>
			<br>
			ค้นหาเมนู : 
			<input type="textbox" id="searchText" size="60">
			<input type="button" value="ค้นหาเมนู" onClick="searchLoad();">
			<input type="button" value="แสดงเมนูทั้งหมดของฉัน" onClick="document.getElementById('searchText').value = ''; searchLoad();">
		</div>

		<div>
			<br><font size="3"><span id="searchSpan"></span></font>
		</div>
		<br/>
		<div id="div_admin_menu"></div>	<!-- ที่แสดง admin menu -->
	</td>	
</tr>
<tr><td colspan="4"><br></td></tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
	<tr>
		<td colspan="4">
			<div id="div_user_menu"></div>	<!-- ที่แสดง user menu -->
		</td>
	</tr>
</table>

</div>
    
      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

</body>
</html>
<!--Date and time show on titlebar-->
<?php 
$thai_w=array("อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์");
$thai_n=array("มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
$w=$thai_w[date("w")];
$d=date("d");
$n=$thai_n[date("n") -1];
$y=date("Y") +543;
$timenow = date('H:i:s'); 
?>
<script type="text/javascript">

var limit="<?php echo $timenow ?>"
var daythai = "<?php echo $w ?>"
var daynum = "<?php echo $d ?>"
var month = "<?php echo $n ?>"
var year = "<?php echo $y ?>"

if (document.images){
	var parselimit=limit.split(":")
	parselimit=parselimit[0]*60*60+parselimit[1]*60+parselimit[2]*1
}
function begintimer(){

parselimit+=1
curhour=Math.floor(parselimit/3600)%24
curmin=Math.floor(parselimit/60)%60
cursec=parselimit%60

curtime="<center>วัน<font color=red> "+daythai+" </font>ที่<font color=red> "+daynum+" </font><font color=red> "+month+" </font> <font color=red> "+year+" </font> เวลา : <font color=red> "+curhour+" </font>นาฬิกา <font color=red> "+curmin+" </font>นาที <font color=red>"+cursec+" </font>วินาที </center>"
document.getElementById('dplay').innerHTML = curtime;
setTimeout("begintimer()",1000)
}
</script>
<!-- finish date and time show on titlebar-->

<script>
function ChangePass(){
    $('body').append('<div id="DivChangePass"></div>');
    $('#DivChangePass').load('change_pass.php?cmd=showdiv');
    $('#DivChangePass').dialog({
        title: 'แก้ไขรหัสผ่าน',
        resizable: false,
        modal: true,  
        width: 350,
        height: 250,
        close: function(ev, ui){
            $('#DivChangePass').remove();
        }
    });
}
</script>
	
	
<div style="float:left"><img src="images/logo.jpg" border="0" width="80" height="80" alt="<?php echo $company_name; ?>"></div>
<!-- <div style="float:right; position:relative; height:50px; bottom:-50px; text-align:right">เข้าสู่ระบบโดย: <?php echo $_SESSION['ss_username']; ?> | 
<a href="javascript:ChangePass();">เปลี่ยนรหัสผ่าน</a> | 
<a href="logout.php">ออกจากระบบ</a>
<br />เข้าสู่ระบบครั้งล่าสุดเมื่อ: <?php //echo date( "d-m-Y H:i:s", strtotime( $_SESSION['ss_last_log']) ); ?> (<span id="load_session_expire">~</span>)</div>
-->
<div style="float:right;padding-top:65px;" id="dplay">  </div>
<div style="clear:both"></div>
<hr/>



<?php
	$user_login = $_SESSION['ss_iduser'];
	$login_date_time = date('Y-m-d H:i:s');
	$ip_login = $_SERVER['REMOTE_ADDR'];
	
	pg_query("BEGIN WORK");
	$status = 0;
	
	$qry_ins="insert into public.\"fuser_log_access\"(\"username\",\"login_datetime\",\"IP_Address\") values ('$user_login','$login_date_time','$ip_login')";
	
	if($resultS=pg_query($qry_ins)){
	}else{
		$status++;
	}
	
	if($status == 0)
	{
		pg_query("COMMIT");
	}
	else
	{
		pg_query("ROLLBACK");
		echo "<div style=\"float:left\">ไม่สามารถบันทึกข้อมูลการเข้าใช้งานได้!!</div>";
	}

    echo "<div style=\"float:left\">เข้าสู่ระบบโดย :<b>$_SESSION[ss_username]</b><br />เข้าสู่ระบบครั้งล่าสุดเมื่อ :<b>". date( "d-m-Y H:i:s", strtotime($_SESSION['ss_last_log']) ) . "(<span id=\"load_session_expire\">~</span>)</b><br />ไอพีของท่าน  :<b>". $_SERVER['REMOTE_ADDR'] . "</b></div>";
    //echo "<div style=\"float:right\"><a href=\"javascript:ChangePass();\"><font color=\"#ff0000\"><b>เปลี่ยนรหัสผ่าน</b></font></a> | <a href=\"logout.php\"><font color=\"#ff0000\"><b>ออกจากระบบ</b></font></a></div>";
	echo "<div style=\"float:right\"><a href=\"../../xlease-nw/xlease/index.php?passlog=1\"><font color=\"#0000FF\"><b>เข้าสู่ระบบ XLEASE</b></font></a> | <a href=\"../../xlease-nw/xlease/logout.php\"><font color=\"#ff0000\"><b>ออกจากระบบ</b></font></a></div>";
    echo "<div style=\"clear:both\"></div>";
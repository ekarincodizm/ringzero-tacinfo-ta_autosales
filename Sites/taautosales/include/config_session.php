<?php
if(!session_start()){
    session_start();
} 

function setSessionTime($_timeSecond){
    if(!isset($_SESSION['ses_time_life'])){
        $_SESSION['ses_time_life'] = time();
    }
    if(isset($_SESSION['ses_time_life']) && time()-$_SESSION['ses_time_life']>$_timeSecond){
        if(count($_SESSION)>0){
            foreach($_SESSION as $key=>$value){
                unset($$key);
                unset($_SESSION[$key]);
            }
        }
    }else{
        $_SESSION['ses_time_life'] = time();
    }
}
$set_expire_time = 10*60; //ตั้งเวลาปิดตัวเองหากไม่ทำงาน 10นาที คือ 10*60 (เป็น วินาที)
setSessionTime($set_expire_time); //ตั้งค่า 2 ที่คือ ไฟล์นี้ และ ไฟล์ session_expire.php
//seed
$company[0]['seed']='xxx';
foreach($company as $v){
	$_SESSION["session_company_seed"]=$v['seed'];
	break;
}	
//Alert Admin menu
$_session['menu_admin'] = array('PO02','C041','RC03','CUS02','TA01','TA02','TA03','TA04','TA05');

?>

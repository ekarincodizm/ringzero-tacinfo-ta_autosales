<?php ob_start();
session_start();
?>
<?php
function sec2hms($sec, $padHours = false){
    $hms = "";
    $hours = intval(intval($sec) / 3600); 
    $hms .= ($padHours) 
    ? str_pad($hours, 2, "0", STR_PAD_LEFT). ":"
    : $hours. ":";
    $minutes = intval(($sec / 60) % 60); 
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";
    $seconds = intval($sec % 60); 
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
    return $hms;
}

$set_expire_time = 10*60; //ตั้งเวลาปิดตัวเองหากไม่ทำงาน 10นาที คือ 10*60 (เป็น วินาที)

$session_duration = time()-$_SESSION['ses_time_life'];
if( $session_duration > $set_expire_time ){
    session_unset();
    session_destroy();
    echo 0;
}else{
    $total_expire_time = $set_expire_time-$session_duration;
    echo sec2hms($total_expire_time);
}
?>
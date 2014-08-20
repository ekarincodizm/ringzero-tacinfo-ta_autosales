<?php 
$date = date('Y-m-d H:i:s');
$id_user = $_SESSION["av_iduser"];
$menuname = $_POST['id'];
$status = 0;
pg_query('BEGIN');
$sql = "INSERT INTO fuser_log_menu(
           \"menuID\", id_user, menu_date)
    VALUES ('$menuname','$id_user', '$date')";	
$query = pg_query($sql);
if($query){}else{$status++;}
if($status==0){ pg_query('COMMIT');  }else{ pg_query('ROLLBACK');}
?>
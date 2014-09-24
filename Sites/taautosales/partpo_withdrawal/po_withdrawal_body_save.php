<?php
	include_once("../include/config.php");
	include_once("../include/function.php");
	
	//Load Initial HTTP Post Variables
	$id_user = $_SESSION["ss_iduser"];
	$withdrawal_code= pg_escape_string($_POST["withdrawal_code"]);
	$set_status = pg_escape_string($_POST["set_status"]);
		
	pg_query("BEGIN WORK");
	$status = 0;
	$txt_error = array();
	
	//Query PartsReceived
	$withdrawalParts_strQuery = "
		UPDATE 
			\"WithdrawalParts\"
		SET 
			status = ".$set_status."
		WHERE code = '".$withdrawal_code."';
	";
	
	if(!$result=@pg_query($withdrawalParts_strQuery)){
        $txt_error[] = "INSERT withdrawalParts ไม่สำเร็จ $withdrawalParts_strQuery";
        $status++;
    }
	
	// Check Is Query or Not?
	if($status == 0){
        // pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = pass;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
	
	echo json_encode($data);
?>
<?php 
include_once("../include/config.php");
include_once("../include/function.php");


$cmd_save_cus = $_REQUEST['cmd_save_cus'];
$tab = $_REQUEST['tab'];

if($cmd_save_cus == "save_customer"){
	$cus_type = pg_escape_string($_POST['cus_type']);
    $txt_pre_name = checknull( pg_escape_string($_POST['txt_pre_name']) );
    $txt_firstname = checknull( pg_escape_string($_POST['txt_firstname']) );
    $txt_lastname = checknull( pg_escape_string($_POST['txt_lastname']) );
    $txt_address = checknull( pg_escape_string($_POST['txt_address']));
    $txt_post =  checknull( pg_escape_string($_POST['txt_post']));
    $chkContact = pg_escape_string($_POST['chkContact']);
    $txt_contact = checknull(pg_escape_string($_POST['txt_contact']));
	$txt_post_contract = checknull(pg_escape_string($_POST['txt_post_contract']));
    $txt_phone = checknull(pg_escape_string($_POST['txt_phone']));

    $txt_reg = checknull( pg_escape_string($_POST['txt_reg']) );
    $txt_barthdate = pg_escape_string($_POST['txt_barthdate']);
    $combo_cardtype = pg_escape_string($_POST['combo_cardtype']);
    $txt_cardother = pg_escape_string($_POST['txt_cardother']);
    $txt_cardno = checknull( pg_escape_string($_POST['txt_cardno']) );
    $txt_carddate = checknull(pg_escape_string($_POST['txt_carddate']));
    $txt_cardby = checknull( pg_escape_string($_POST['txt_cardby']) );
    $txt_job =  checknull( pg_escape_string($_POST['txt_job']) );
	
	$rdo_reg_address = pg_escape_string($_POST['rdo_reg_address']);
	$txt_name_reg =  checknull(pg_escape_string($_POST['txt_name_reg']));
	$txt_address_reg = checknull(pg_escape_string($_POST['txt_address_reg']));
	$txt_post_reg = checknull(pg_escape_string($_POST['txt_post_reg']));

    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $cus_id = GetCusID();

    if($chkContact == 1){ 
		$str_contact = $txt_address;
		$srt_contract_post =   $txt_post;
		}
	else if($chkContact == 2){
		$str_contact = $txt_contact;
		$srt_contract_post = $txt_post;
	}else if($chkContact == 3){
		$str_contact = $txt_address_reg;
		$srt_contract_post =$txt_post_reg;
	}
	if($rdo_reg_address == 2){
		$str_reg_address = $txt_address_reg;
		$str_reg_post = $txt_post_reg;
	}else{
		$str_reg_address = $txt_address;
		$str_reg_post = $txt_post;
	}
    $in_qry="INSERT INTO \"Customers\" (cus_id,pre_name,cus_name,surname,address,add_post,nationality,birth_date,
										card_type,card_id,card_do_date,card_do_by,job,contract_add,contract_post ,
										telephone,reg_customer,reg_address,reg_post,cus_type) 
								VALUES ('$cus_id',$txt_pre_name,$txt_firstname,$txt_lastname,$txt_address,$txt_post,$txt_reg,'$txt_barthdate',
										'$combo_cardtype',$txt_cardno,$txt_carddate,$txt_cardby,$txt_job,$str_contact,$srt_contract_post,
										$txt_phone,$txt_name_reg,$str_reg_address,$str_reg_post,'$cus_type')";
    
	if(!$res=@pg_query($in_qry)){
        $txt_error[] = "INSERT Customers ไม่สำเร็จ $in_qry";
        $status++;
    }
    
    if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
		$data['tab'] = "1";
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! ";
    }
    echo json_encode($data);
}
?>
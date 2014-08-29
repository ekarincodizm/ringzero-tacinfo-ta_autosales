<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "divdialog"){
    $id = pg_escape_string($_GET['id']);
    $t = pg_escape_string($_GET['t']);
	$potype = pg_escape_string($_GET['potype']);
	$condition = pg_escape_string($_GET["condition"]);
	$keyword =  pg_escape_string($_GET["keyword"]);
	
	if($t == "Cars"){
	
		switch($potype){
			case 'PONW':
				$file = "po_receive_frm_nw.php";
				break;
			case 'PORT':
				$file = "po_receive_frm_used.php";
				break;
			case 'POUS':
				$file = "po_receive_frm_used.php";
				break;
			case 'POSC':
				$file = "po_receive_frm_used.php";
				break;
			default :
				echo "ไม่พบข้อมูล";
				break;
		}
		
	} elseif($t == "MAT"){
	
		$file = "po_receive_frm_mat.php";
		
    } elseif($t == "P_CouponGas"){
	
		$file = "po_receive_frm_coupongas.php";
		
    } elseif($t == "P_NewCarPlate"){
	
		$file = "po_receive_frm_newcarplate.php";
		
    } elseif($t == "P_Shirt"){
	
		$file = "po_receive_frm_shirt.php";
		
    } elseif($t == "P_SignFrame"){
	
		$file = "po_receive_frm_signframe.php";
		
    } elseif($t == "P_WrapSeat"){
	
		$file = "po_receive_frm_wrapseat.php";
		
    } elseif($t == "P_Meter"){
	
		$file = "po_receive_frm_meter.php";
		
    } elseif($t == "P_LighterRoof"){
	
        echo "ผิดผลาด P_LighterRoof ไม่สามารถทำรายการได้ !!!";
		
    }   
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <title><?php echo $company_name; ?> - <?php echo $page_title; ?></title>
    <LINK href="../images/styles.css" type=text/css rel=stylesheet>

    <link type="text/css" href="../images/jqueryui/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="../images/jqueryui/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="../images/jqueryui/js/jquery-ui-1.8.16.custom.min.js"></script>

</head>
<body>
	<div><?php include($file);?></div>
<body>
</html>
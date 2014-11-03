<?php
include_once("../include/config.php");
include_once("../include/function.php");
include_once("parts_returnstock_webservice.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}
$page_title = "คืนสินค้า (อะไหล่และอุปกรณ์)";
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
<body class="body">
<div class="roundedcornr_box" style="width:1000px">
	<div class="roundedcornr_top">
		<div></div>
	</div>
	<div class="roundedcornr_content">
<?php
		include_once("../include/header_popup.php");
		// include_once("po_receive_body.php"); 
?>
		<div style="text-align: right; ">
			<input type="button" value="เพิ่มใบคืนสินค้าที่คืน" onclick="javascript:AddNewWithdrawal('parts_returnstock_new.php')" style="cursor:pointer;" alt="เพิ่มใบคืนสินค้าที่คืน" title="เพิ่มใบคืนสินค้าที่คืน" />
		</div>
		<div id="maintabs">
		    <ul>
		        <li><a href="parts_returnstock_body_1.php">คืนของเข้าสต๊อก</a></li>
		        <li><a href="parts_returnstock_body_2.php">คืนเป็นของเสีย</a></li>
				<!-- <li><a href="parts_returnstock_body_3.php">รอคืน</a></li> -->
		    </ul>
		</div>
		
		      </div>
		   <div class="roundedcornr_bottom"><div></div></div>
		</div>
		
		<script>
			$(function(){
			    $( "#maintabs" ).tabs({
			        select: function(e, ui) {
			            $('#ui-tabs-1').empty();
			            $('#ui-tabs-2').empty();
			        },
			        ajaxOptions: {
			            error: function( xhr, status, index, anchor ) {
			                $( anchor.hash ).html("ไม่สามารถโหลดเนื้อหาได้");
			            }
			        }
			    });
			});
			
			function AddNewWithdrawal(url){
			    $('body').append('<div id="divdialogadd"></div>');
			    $('#divdialogadd').load(url);
			    $('#divdialogadd').dialog({
			        title: 'เพิ่มใบคืนสินค้าที่คืน',
			        resizable: false,
			        modal: true,  
			        width: 1000,
			        height: 600,
			        close: function(ev, ui){
			            $('#divdialogadd').remove();
			        }
			    });
			}
		</script>
	</div>
</div>

</body>
</html>
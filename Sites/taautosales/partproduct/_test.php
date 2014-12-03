<?php
include_once("../include/config.php");
include_once("../include/function.php");
include_once("parts_project_api_service.php");
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
	
	<!-- Jquery Chosen -->
	<link type="text/css" href="../include/Javascript/jquery_chosen/chosen.min.css" rel="stylesheet" />
	<style>
		.chosen-drop{
			margin-bottom: 20px;
		}
	</style>
	<script type="text/javascript" src="../include/Javascript/jquery_chosen/chosen.jquery.min.js"></script>
</head>
<body>

							<select name="combo_mat_edit1" id="combo_mat_edit1" class="combo_mat_edit chosen-select" data-code_id="1" style="width: 100%">
					    		<option value="">เลือก</option>
<?php
								$parts = get_Parts_type_0();
?>
					    		<script>
					    			var parts_edit = <?php echo json_encode($parts); ?>;
					    		</script>
<?php
								foreach ($parts as $key => $value){
?>
									<option value="<?php echo $value["code"]; ?>" <?php 
										if($value["code"] == $dt_material_id){
											?>selected='selected'<?php
										}
									?>><?php echo $value["code"]."#".$value["name"]."#".$value["details"]."#".$value["barcode"]; ?></option>
<?php 
								}
?>
							</select>

<select name="combo_mat_edit31" id="combo_mat_edit31" class="combo_mat_edit chosen-select" data-code_id="1" style="width: 100%">
	</select>
	
</body>
</html>

<script>
	$(".chosen-select").chosen({
		placeholder_text_single: "เลือก",
		search_contains: true,
		no_results_text: "ไม่มีข้อมูล"
	}); 
</script>
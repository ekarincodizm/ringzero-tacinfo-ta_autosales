<?php
include_once("../include/config.php");
include_once("../include/function.php");
/*
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}
*/
$page_title = "Vender";
?>

    

  
<script type="text/javascript">
$(document).ready(function(){
	
	
	
	
    $("#t_name").autocomplete({
        source: "vender_api.php?cmd=find",
        minLength:1
    });
	
	$('#btn_find').click(function(){
        $("#panel").load("edit_vender.php?cusid="+ $("#t_name").val());
		
    });
	
	
});	
</script>
</head>
<body>



<?php
//include_once("../include/header_popup.php");
?>

<div style="text-align:left;">&nbsp;&nbsp;</div>

<div>
  <div style="float:left"><b>Edit Vender </b></div><div style="float:right; "></div><br />
  <div>ตรวจสอบข้อมูล vender
    <input type="text" name="t_name" id="t_name" size="50"><input type="button" id="btn_find" name="btn_find" value="ค้นหา"  />

  </div>
 
<div></div>

<div id="panel" style="padding-top: 10px;"></div>

</div>
  
</div>
   
</div>


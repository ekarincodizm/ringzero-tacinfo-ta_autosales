<?php ob_start();
session_start();
?>
<html>
<title><?php echo $_SESSION["session_company_name"]; ?></title>
<meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
<body>
<style type="text/css">
<!--
#Content {
    overflow: hidden;
    width: 200px;
    margin-right: auto;
    margin-left: auto;
    margin-top: 0px;
    color: #777;
}
#Content .LeftC {
    float: left;
    width: 200px;
    margin-right: 25px;
}
#Content .LeftC ul {
    margin: 0px;
    padding: 0px 0px 3px;
    background: url(smNavBttm.gif) no-repeat left bottom;
    _background: url(smNavBttm.gif) no-repeat left bottom;
}
#Content .LeftC ul li {
    list-style: none;
    border-right: 1px solid #ececec;
    border-left: 1px solid #ececec;
    margin: 0px;
    padding: 7px 10px 8px 15px;
    border-top: 1px dotted #ececec;
    display: block;
    font-size: 0.7em;
    color: #999;
}
* html #Content .LeftC ul li{
    height:100%;
    width:200px;
    padding: 10px 10px 11px 15px;
}
#Content .LeftC ul li:hover {
    background: #f7f7f7;
}

#Content .LeftC ul li a, #Content .LeftC ul li:visited {
    color: #999;
    text-decoration: none;
}
#Content .LeftC ul li a:hover , #Content .LeftC ul li.Selected {
    color: #006cb7;
    text-decoration: none;
}

#Content .LeftC ul li.Head {
    border-width: 0px;
    margin: 0px;
    padding: 0px;
    height: 34px;
}
#Content .LeftC ul li.SS {
    margin: 0px;
    padding: 0px;
    background: url(ssBG.gif) repeat-x top;
    width: 198px;
    height: 28px;
}
* html #Content .LeftC ul li{
    margin-top:-5px;
}
* html #Content .LeftC ul li.SS, * html #Content .LeftC ul li.Head {
    padding-bottom:0px;
    margin-top:-5px;
    margin-bottom:0px;
    border-bottom:none;
    width:172px;
    height:100%;
}
* html #Content .LeftC ul li.SS a img{
    position: fixed;
    height:100%;
    width:100%;
}
#Content .LeftC ul li.SSS {
    padding-left: 30px;
}
-->
</style>

<script language=javascript>
<!--
function popU(U,N,T) {
    newWindow = window.open(U, N, T);
}
//-->
</script>

<div id="Content">
    <div class="LeftC">
<ul>
<li style="background-color:#0080C0; color:#ffffff;"><b>เลขที่บัญชี</b></li> 
<li >- <a href="ac_table_add.php" target=frm_r>สร้างเลขที่บัญชี</a></li>
<li >- <a href="ac_table_show.php" target=frm_r>แสดงเลขที่บัญชี/แก้ไข</a></li>

</ul>
    </div>
</div>
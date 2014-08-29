<?php
include_once("include/config.php");
$term = $_GET['term'];
$iduser = $_SESSION['uid'];

$sql = "SELECT distinct b.name_menu FROM f_usermenu a, f_menu b
		WHERE a.id_menu = b.id_menu AND a.id_user = '$iduser' AND b.status_menu = '1' AND a.status = true AND b.name_menu like '%$term%'
		ORDER BY b.name_menu";

$results=pg_query($sql);						 
$nrows=pg_num_rows($results);

while($row = pg_fetch_array( $results ))
{
	$name_menu = $row["name_menu"];
    
	$dt['value'] = $name_menu;
	$dt['label'] = $name_menu;
	
    $matches[] = $dt;
}
if($nrows==0){
    $matches[] = "ไม่พบข้อมูล";
}
$matches = array_slice($matches, 0, 100);
print json_encode($matches);
?>
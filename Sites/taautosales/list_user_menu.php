<?php
include_once("include/config.php");

// เมนูที่จะค้นหา
$searchText = pg_escape_string($_GET["searchText"]);
$searchText = str_replace("TspaceT"," ",$searchText);
if($searchText != ""){$searchText = "and b.\"name_menu\" like '%$searchText%'";}
?>

<table width="93%" cellpadding="0" cellspacing="0" border="0" align="center">
	<tr>
<?php
		$admin_array = $_session['menu_admin'];
		$j = 0;
		$result=pg_query("SELECT A.*,B.* FROM f_usermenu A INNER JOIN f_menu B on A.id_menu=B.id_menu WHERE (A.id_user='$_SESSION[ss_iduser]') AND (B.status_menu='1') AND (A.status=true) $searchText ORDER BY A.id_menu ASC");
		while($arr_menu = pg_fetch_array($result))
		{
			$menu_id = $arr_menu["id_menu"];
			$menu_name = $arr_menu["name_menu"];
			$menu_path = $arr_menu["path_menu"];
			
			if(!in_array($menu_id,$admin_array))
			{
				$arr['user'][$menu_id]['name'] = "$menu_name";
				$arr['user'][$menu_id]['path'] = "$menu_path";
				$arr['user'][$menu_id]['idmenu_log'] = "$menu_id";
			}
		}
		if( count($arr['user']) > 0 )
		{ 
			foreach($arr['user'] as $k => $v)
			{
				$j++;
?>
				<td width="24%" align="center" style="font-weight:bold; height:80px">
					<a href="javascript:popU('<?php echo $v['path']; ?>?ss_iduser=<?php echo $_SESSION["ss_iduser"]; ?>','<?php echo $v['idmenu_log']; ?>','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=950,height=670'),menulog('<?php echo $v['idmenu_log']; ?>')">
						<img src="images/menu/<?php echo strtolower($v['idmenu_log']); ?>.gif" border="0" width="80" height="80"><br /><?php echo $v['name']; ?>
					</a>
				</td>
<?php
			if($j == 4)
			{
				$j = 0;
				echo "</tr><tr>";
			}
		}
	}
?>
	</tr>
</table>
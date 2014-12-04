<?php
/**
 * 
 */
class Project {
	
	function __construct($argument = '') {
		
	}
	
	function get_project_detail($project_id = '')
	{
		$strQuery = "
			SELECT 
				\"ProjectDetails\".material_id AS parts_code,
				\"ProjectDetails\".use_unit
			FROM
				\"Projects\"
			LEFT JOIN
				\"ProjectDetails\"
				ON
				\"ProjectDetails\".project_id = \"Projects\".project_id
			WHERE
				\"Projects\".project_id = '".$project_id."'
				AND
				\"Projects\".cancel = FALSE
				AND
				\"ProjectDetails\".cancel = FALSE
		";
		$query = pg_query($strQuery);
		$return = pg_fetch_all($query);
		
		return $return;		
	}
}

?>
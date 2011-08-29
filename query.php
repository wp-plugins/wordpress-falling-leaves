<?php
	require_once 'config.php';
	$query = "SELECT * FROM ".$table_leaves."";
	$result = mysql_query($query);
	$strippedResults = '';
	while($row = mysql_fetch_array($result))
	{
				$strippedResults []= array( 
					'id' => stripcslashes($row['id']),
					'url' => stripcslashes($row['url']),
					'height' => stripcslashes($row['height']),
					'width' => stripcslashes($row['width']),
					'rotationType' => stripcslashes($row['rotationType']),
					'number_leaves' => stripcslashes($row['number_leaves']),
					'id_leaves' => stripcslashes($row['id_leaves'])				
					);
			
			

	}
	print_r(json_encode($strippedResults)).'<br>';
?>
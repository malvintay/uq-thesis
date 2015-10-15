<?php

require '../db.php';

set_time_limit(0);

$files = scandir('input');

foreach ($files as $f) {
	if (strpos($f, '.') === 0 || ! preg_match("/^\d.*?\.csv$/", $f)) {continue;}
	$fp = fopen('input/'.$f, 'r');
	
	if (! $fp) {
		echo "Error while opening file $f, skipping<br>\n";
		continue;
	}
	
	$module = substr($f, 0, -4);
	
	$heading = TRUE;
	while (($row = fgetcsv($fp)) != FALSE) {
		if ($heading) {
			$heading = FALSE;
			continue;
		}
		
		$row[1] = (int)$row[1];
		
		$date = date('Y-m-d H:i:s', strtotime($row[3]));
		
		$duration = 0;
		$d_parts = explode(':', $row[5]);
		
		$duration += $d_parts[0] * 3600;
		$duration += $d_parts[1] * 60;
		$duration += (int)$d_parts[2];
		
		mysql_query("INSERT INTO quizz_weekly VALUES('$module', '{$row[0]}', '{$row[1]}', '{$row[2]}', '$date', '{$row[4]}', $duration)") or die(mysql_error());
	}
	
	
}

?>

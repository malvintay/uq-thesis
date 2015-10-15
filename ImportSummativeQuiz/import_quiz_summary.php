<?php

set_time_limit(0);

ini_set('display_errors', 'on');

require 'db.php';

$fp = fopen('quiz_summary.csv', 'r');

while (($row = fgetcsv($fp)) !== FALSE) {

	for ($i = 4; $i < 16; $i++) {
		if (! is_numeric($row[$i])) {
			$row[$i] = 0;
		}
	}
	
	mysql_query("INSERT INTO quiz_summary(user_id, week1, week1_max, week2, week2_max, week3, week3_max, week4, week4_max, week5, week5_max, week6, week6_max)
	VALUES('{$row[0]}', {$row[4]}, {$row[5]}, {$row[6]}, {$row[7]}, {$row[8]}, {$row[9]}, {$row[10]}, {$row[11]}, {$row[12]}, {$row[13]}, {$row[14]}, {$row[15]})") or die(mysql_error($db));
}

?>

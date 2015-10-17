<?php

set_time_limit(0);

ini_set('display_errors', 'on');

require '../db.php';

$fp = fopen('mid_semester.csv', 'r');

while (($row = fgetcsv($fp)) !== FALSE) {
	

	if (is_numeric($row[4])) {
		mysql_query("INSERT INTO mid_semester(student_id, MCQ, part_b, part_c, total) VALUES('{$row[0]}', ".floatval($row[1]).", ".floatval($row[2]).", ".floatval($row[3]).", ".floatval($row[4]).")") or die(mysql_error($db));	
	} else {
		if ($row[4] == 'Deferred') {
			mysql_query("INSERT INTO mid_semester(student_id, deferred) VALUES('{$row[0]}', 1)") or die(mysql_error($db));
		}
		
		if ($row[4] == 'Withdrawn') {
			mysql_query("INSERT INTO mid_semester(student_id, withdrawn) VALUES('{$row[0]}', 1)") or die(mysql_error($db));
		}
	}
	
}

?>

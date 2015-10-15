<?php

set_time_limit(0);

ini_set('display_errors', 'on');

require 'db.php';

$fp = fopen('data.csv', 'r');

while (($row = fgetcsv($fp)) !== FALSE) {
	mysql_query("INSERT INTO clicks_data(anonID, `timestamp`, `action`, item, parentitem)
	VALUES('{$row[0]}', '{$row[1]}', '".mysql_real_escape_string($row[2], $db)."', '".mysql_real_escape_string($row[3], $db)."', '".mysql_real_escape_string($row[4], $db)."')") or die(mysql_error($db));
}

?>

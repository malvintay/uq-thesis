<?php

require_once 'db.php';

ini_set('display_errors', 'off');

//returns the dataset for given range
function getClicksData($rangeDays = 1, $user_id = FALSE) {

	global $db, $db_table;

	$data = array();
	
	if (is_array($rangeDays)) {
	
		//actual range in days
		$range = (strtotime($rangeDays[1]) - strtotime($rangeDays[0])) / 86400;
	
		//advanced search with date range
		if ($range < 2) {
			//per hour
			$res = mysql_query("SELECT CONCAT(HOUR(`timestamp`), ':00') AS hour, COUNT(*) as total_clicks FROM $db_table WHERE ".($user_id ? "anonID = '$user_id' AND " : '')." `timestamp` BETWEEN '{$rangeDays[0]}' AND '{$rangeDays[1]}' GROUP BY HOUR(`timestamp`) ORDER BY HOUR(`timestamp`);");
			
			while (($row = mysql_fetch_object($res)) !== FALSE) {
				array_push($data, array($row->hour, $row->total_clicks));
			}
			
		} else {
			//per day
			$res = mysql_query("SELECT DATE(`timestamp`) as day, COUNT(*) as total_clicks FROM $db_table WHERE ".($user_id ? "anonID = '$user_id' AND " : '')." `timestamp` BETWEEN '{$rangeDays[0]}' AND '{$rangeDays[1]}' GROUP BY DATE(`timestamp`) ORDER BY DATE(`timestamp`);");
			
			while (($row = mysql_fetch_object($res)) !== FALSE) {
				array_push($data, array($row->day, $row->total_clicks));
			}
			
		}
	
	} else {
		//regular search
	
		if ($rangeDays < 2) {
			//per hour
			$res = mysql_query("SELECT CONCAT(HOUR(`timestamp`), ':00') AS hour, COUNT(*) as total_clicks FROM $db_table WHERE ".($user_id ? "anonID = '$user_id' AND " : '')." `timestamp` BETWEEN NOW() - INTERVAL ".($rangeDays * 24)." HOUR AND NOW() GROUP BY HOUR(`timestamp`) ORDER BY HOUR(`timestamp`);");
			
			while (($row = mysql_fetch_object($res)) !== FALSE) {
				array_push($data, array($row->hour, $row->total_clicks));
			}
			
		} else {
			//per day
			$res = mysql_query("SELECT DATE(`timestamp`) as day, COUNT(*) as total_clicks FROM $db_table WHERE ".($user_id ? "anonID = '$user_id' AND " : '')." `timestamp` BETWEEN NOW() - INTERVAL $rangeDays DAY AND NOW() GROUP BY DATE(`timestamp`) ORDER BY DATE(`timestamp`);");
			
			while (($row = mysql_fetch_object($res)) !== FALSE) {
				array_push($data, array($row->day, $row->total_clicks));
			}
			
		}
		
	}
	
	//we have the grouped dataset, prepare in arrays and pass to trimmer
	//allows getting the ready dataset with just 2 queries and max 48 iterations
	return trimSequences($rangeDays, $user_id, $data);

}

//remove sequence false positives
function trimSequences($rangeDays = 1, $user_id = FALSE, $data) {

	global $db, $db_table;

	$sequenceIndex = array();
	
	if (is_array($rangeDays)) {
	
		//actual range in days
		$range = (strtotime($rangeDays[1]) - strtotime($rangeDays[0])) / 86400;
	
		if ($range < 2) {
			$res = mysql_query("SELECT CONCAT(HOUR(`timestamp`), ':00') AS hour, COUNT(*) / 17 AS sequences 
								FROM $db_table 
								WHERE ".($user_id ? "anonID = '$user_id' 
								AND " : '')." `timestamp` 
								BETWEEN '{$rangeDays[0]}' AND '{$rangeDays[1]}' 
								AND (item = 'Learning Pathway' OR parentitem = 'Learning Pathway') 
								GROUP BY HOUR(`timestamp`)");
			
			while (($row = mysql_fetch_object($res)) !== FALSE) {
				$sequenceIndex[$row->hour] = $row->sequences;
			}
			
		} else {
			$res = mysql_query("SELECT DATE(`timestamp`) AS date, COUNT(*) / 17 AS sequences 
								FROM $db_table 
								WHERE ".($user_id ? "anonID = '$user_id' 
								AND " : '')." `timestamp` 
								BETWEEN '{$rangeDays[0]}' AND '{$rangeDays[1]}' 
								AND (item = 'Learning Pathway' OR parentitem = 'Learning Pathway') 
								GROUP BY DATE(`timestamp`);
			");
			
			while (($row = mysql_fetch_object($res)) !== FALSE) {
				$sequenceIndex[$row->date] = $row->sequences;
			}
		}
	
	} else {
	
		if ($rangeDays < 2) {
			$res = mysql_query("SELECT CONCAT(HOUR(`timestamp`), ':00') AS hour, COUNT(*) / 17 AS sequences 
								FROM $db_table 
								WHERE ".($user_id ? "anonID = '$user_id' 
								AND " : '')." `timestamp` 
								BETWEEN NOW() - INTERVAL ".($rangeDays * 24)." HOUR 
								AND NOW() AND (item = 'Learning Pathway' OR parentitem = 'Learning Pathway') 
								GROUP BY HOUR(`timestamp`)");
			
			while (($row = mysql_fetch_object($res)) !== FALSE) {
				$sequenceIndex[$row->hour] = $row->sequences;
			}
			
		} else {
			$res = mysql_query("SELECT DATE(`timestamp`) AS date, COUNT(*) / 17 AS sequences 
								FROM $db_table 
								WHERE ".($user_id ? "anonID = '$user_id' 
								AND " : '')." `timestamp` 
								BETWEEN NOW() - INTERVAL $rangeDays DAY 
								AND NOW() AND (item = 'Learning Pathway' OR parentitem = 'Learning Pathway') 
								GROUP BY DATE(`timestamp`);
			");
			
			while (($row = mysql_fetch_object($res)) !== FALSE) {
				$sequenceIndex[$row->date] = $row->sequences;
			}
		}
		
	}

	
	//xy = array([hour, date], clicks)
	//subtract sequences
	foreach ($data as &$xy) {
		if (array_search($xy[0], $sequenceIndex)) {
			//we had some sequences in this hour/date, subtract false positives
			$xy[1] = round($xy[1] - $sequenceIndex[$xy[0]] * 17 + $sequenceIndex[$xy[0]]);
		}
	}
	
	return $data;
	
}

//user clicks
function userVsAverage($user_id, $range = 14) {

	global $db, $db_table;
	
	$data = getClicksData($range);
	
	$avg = array();
	
	//we have the data for each hour/day now, get avg by user for each day
	foreach ($data as $xy) {
		$date = $xy[0];
		$clicks = $xy[1];
		$res = mysql_query("SELECT COUNT(DISTINCT anonID) AS total_users FROM $db_table WHERE DATE(`timestamp`) = '$date'");
		
		$res = mysql_fetch_object($res);
		
		$avg[] = array($date, round($clicks / $res->total_users, 2));
		
	}
	
	return $avg;
	
	
}

?>

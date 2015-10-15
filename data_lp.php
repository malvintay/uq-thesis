<?php

require_once 'db.php';

ini_set('display_errors', 'off');

//returns the dataset for given range
function getLPData($rangeDays = 1, $user_id = FALSE) {

	global $db;

	$data = array();
	
	$lp_user = array();
	$lp_avg = array();
	
	$x_axis = array();
	
	if ($user_id) {
		$res = mysql_query("SELECT * FROM quiz_summary WHERE user_id = '$user_id'");
		if (mysql_num_rows($res) == 0) {
			return array(
				'error' => 1,
				'message' => "User id $user_id doesn't exist"
			);
		}
	}
	

	if (is_array($rangeDays)) {
	
		//actual range in days
		$range = (strtotime($rangeDays[1]) - strtotime($rangeDays[0])) / 86400;
	
		//advanced search with date range
		if ($range < 2) {
			//per hour
			$res = mysql_query("SELECT HOUR(ts1) AS hour, CONCAT(HOUR(ts1), ':00') AS hour_preview, total_clicks / users_distinct AS avg_clicks, total_clicks, users_distinct, user_clicks
			FROM 
			(SELECT COUNT(*) AS total_clicks, cd1.`timestamp` AS ts1 FROM clicks_data cd1 WHERE cd1.`timestamp` BETWEEN '{$rangeDays[0]}' AND '{$rangeDays[1]}' AND cd1.item = 'Learning Pathway' GROUP BY HOUR(cd1.`timestamp`)) t1
			LEFT JOIN (SELECT COUNT(*) AS user_clicks, cd2.`timestamp` AS ts2 FROM clicks_data cd2 WHERE cd2.`timestamp` BETWEEN '{$rangeDays[0]}' AND '{$rangeDays[1]}' AND cd2.item = 'Learning Pathway' AND anonID = '$user_id' GROUP BY HOUR(cd2.`timestamp`)) t2 ON HOUR(t1.ts1) = HOUR(t2.ts2)
			LEFT JOIN (SELECT COUNT(DISTINCT anonID) AS users_distinct, cd3.`timestamp` AS ts3 FROM clicks_data cd3 WHERE cd3.`timestamp` BETWEEN '{$rangeDays[0]}' AND '{$rangeDays[1]}' AND cd3.item = 'Learning Pathway' GROUP BY HOUR(cd3.`timestamp`)) t3 ON HOUR(t3.ts3) = HOUR(t1.ts1)
			ORDER BY hour");
			
			while (($row = mysql_fetch_object($res)) !== FALSE) {
				if ($user_id) {
					array_push($lp_user, round($row->user_clicks, 2));
				}
				array_push($lp_avg, round($row->avg_clicks, 2));
				array_push($x_axis, $row->hour_preview);
			}
			
		} else {
			//per day
			$res = mysql_query("SELECT DATE(ts1) AS real_date, HOUR(ts1) AS hour, CONCAT(HOUR(ts1), ':00') AS hour_preview, total_clicks / users_distinct AS avg_clicks, total_clicks, users_distinct, user_clicks
			FROM 
			(SELECT COUNT(*) AS total_clicks, cd1.`timestamp` AS ts1 FROM clicks_data cd1 WHERE cd1.`timestamp` BETWEEN '{$rangeDays[0]}' AND '{$rangeDays[1]}' AND cd1.item = 'Learning Pathway' GROUP BY DATE(cd1.`timestamp`)) t1
			LEFT JOIN (SELECT COUNT(*) AS user_clicks, cd2.`timestamp` AS ts2 FROM clicks_data cd2 WHERE cd2.`timestamp` BETWEEN '{$rangeDays[0]}' AND '{$rangeDays[1]}' AND cd2.item = 'Learning Pathway' AND anonID = '$user_id' GROUP BY DATE(cd2.`timestamp`)) t2 ON DATE(t1.ts1) = DATE(t2.ts2)
			LEFT JOIN (SELECT COUNT(DISTINCT anonID) AS users_distinct, cd3.`timestamp` AS ts3 FROM clicks_data cd3 WHERE cd3.`timestamp` BETWEEN '{$rangeDays[0]}' AND '{$rangeDays[1]}' AND cd3.item = 'Learning Pathway' GROUP BY DATE(cd3.`timestamp`)) t3 ON DATE(t3.ts3) = DATE(t1.ts1)
			ORDER BY DATE(ts1)");
			
			while (($row = mysql_fetch_object($res)) !== FALSE) {
				if ($user_id) {
					array_push($lp_user, round($row->user_clicks, 2));
				}
				array_push($lp_avg, round($row->avg_clicks, 2));
				array_push($x_axis, $row->real_date);
			}
			
		}
	
	} else {
		//regular search
	
		if ($rangeDays < 2) {
			//per hour
			$res = mysql_query("SELECT HOUR(ts1) AS hour, CONCAT(HOUR(ts1), ':00') AS hour_preview, total_clicks / users_distinct AS avg_clicks, total_clicks, users_distinct, user_clicks
			FROM 
			(SELECT COUNT(*) AS total_clicks, cd1.`timestamp` AS ts1 FROM clicks_data cd1 WHERE cd1.`timestamp` BETWEEN NOW() - INTERVAL $rangeDays DAY AND NOW() AND cd1.item = 'Learning Pathway' GROUP BY HOUR(cd1.`timestamp`)) t1
			LEFT JOIN (SELECT COUNT(*) AS user_clicks, cd2.`timestamp` AS ts2 FROM clicks_data cd2 WHERE cd2.`timestamp` BETWEEN NOW() - INTERVAL $rangeDays DAY AND NOW() AND cd2.item = 'Learning Pathway' AND anonID = '$user_id' GROUP BY HOUR(cd2.`timestamp`)) t2 ON HOUR(t1.ts1) = HOUR(t2.ts2)
			LEFT JOIN (SELECT COUNT(DISTINCT anonID) AS users_distinct, cd3.`timestamp` AS ts3 FROM clicks_data cd3 WHERE cd3.`timestamp` BETWEEN NOW() - INTERVAL $rangeDays DAY AND NOW() AND cd3.item = 'Learning Pathway' GROUP BY HOUR(cd3.`timestamp`)) t3 ON HOUR(t3.ts3) = HOUR(t1.ts1)
			ORDER BY hour");
			
			while (($row = mysql_fetch_object($res)) !== FALSE) {
				if ($user_id) {
					array_push($lp_user, round($row->user_clicks, 2));
				}
				array_push($lp_avg, round($row->avg_clicks, 2));
				array_push($x_axis, $row->hour_preview);
			}
			
		} else {
			//per day
			$res = mysql_query("SELECT DATE(ts1) AS real_date, HOUR(ts1) AS hour, CONCAT(HOUR(ts1), ':00') AS hour_preview, total_clicks / users_distinct AS avg_clicks, total_clicks, users_distinct, user_clicks
			FROM 
			(SELECT COUNT(*) AS total_clicks, cd1.`timestamp` AS ts1 FROM clicks_data cd1 WHERE cd1.`timestamp` BETWEEN NOW() - INTERVAL $rangeDays DAY AND NOW() AND cd1.item = 'Learning Pathway' GROUP BY DATE(cd1.`timestamp`)) t1
			LEFT JOIN (SELECT COUNT(*) AS user_clicks, cd2.`timestamp` AS ts2 FROM clicks_data cd2 WHERE cd2.`timestamp` BETWEEN NOW() - INTERVAL $rangeDays DAY AND NOW() AND cd2.item = 'Learning Pathway' AND anonID = '$user_id' GROUP BY DATE(cd2.`timestamp`)) t2 ON DATE(t1.ts1) = DATE(t2.ts2)
			LEFT JOIN (SELECT COUNT(DISTINCT anonID) AS users_distinct, cd3.`timestamp` AS ts3 FROM clicks_data cd3 WHERE cd3.`timestamp` BETWEEN NOW() - INTERVAL $rangeDays DAY AND NOW() AND cd3.item = 'Learning Pathway' GROUP BY DATE(cd3.`timestamp`)) t3 ON DATE(t3.ts3) = DATE(t1.ts1)
			ORDER BY DATE(ts1)");
			
			while (($row = mysql_fetch_object($res)) !== FALSE) {
				if ($user_id) {
					array_push($lp_user, round($row->user_clicks, 2));
				}
				array_push($lp_avg, round($row->avg_clicks, 2));
				array_push($x_axis, $row->real_date);
			}
			
		}
		
	}
	
	
	$data['series'] = array();
	
	$data['series'][0] = new stdClass;
	$data['series'][1] = new stdClass;
	
	$data['series'][0]->name = 'Average';
	$data['series'][1]->name = 'User';
	
	$data['series'][0]->data = $lp_avg;
	$data['series'][1]->data = $lp_user;
	
	$data['x_axis'] = $x_axis;
	
	return $data;

}

?>

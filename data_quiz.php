<?php

require_once 'db.php';

ini_set('display_errors', 'off');

function getQuizSummary($user_id = FALSE) {

	global $db, $db_table;

	$data = array();

	//averages
	$res_avg = mysql_query("SELECT
	SUM(week1) / COUNT(DISTINCT user_id) AS w1_avg,
	SUM(week2) / COUNT(DISTINCT user_id) AS w2_avg,
	SUM(week3) / COUNT(DISTINCT user_id) AS w3_avg,
	SUM(week4) / COUNT(DISTINCT user_id) AS w4_avg,
	SUM(week5) / COUNT(DISTINCT user_id) AS w5_avg,
	SUM(week6) / COUNT(DISTINCT user_id) AS w6_avg
	FROM quiz_summary") or die(mysql_error());
	
	if ($res_avg) {
		$avg = array();
		$res_avg = mysql_fetch_assoc($res_avg);
		
		for ($i = 1; $i < 7; $i++) {
			$avg[] = round($res_avg['w'.$i.'_avg'], 2);
		}
		
	}
	
	//user averages
	if ($user_id != FALSE) {
	
		if (($res = mysql_query("SELECT * FROM quiz_summary WHERE user_id = '$user_id'")) != FALSE) {
			if (mysql_num_rows($res) == 0) {
				//user doesn't exist
				$data['error'] = 1;
				$data['message'] = "User id $user_id doesn't exist";
			} else {
			
				$res_user = mysql_query("SELECT
				SUM(week1) / COUNT(DISTINCT user_id) AS w1_avg,
				SUM(week2) / COUNT(DISTINCT user_id) AS w2_avg,
				SUM(week3) / COUNT(DISTINCT user_id) AS w3_avg,
				SUM(week4) / COUNT(DISTINCT user_id) AS w4_avg,
				SUM(week5) / COUNT(DISTINCT user_id) AS w5_avg,
				SUM(week6) / COUNT(DISTINCT user_id) AS w6_avg
				FROM quiz_summary
				WHERE user_id = '$user_id'
				");
				
				
				if ($res_user) {
					$user = array();
					$res_user = mysql_fetch_assoc($res_user);
					
					for ($i = 1; $i < 7; $i++) {
						$user[] = round($res_user['w'.$i.'_avg'], 2);
					}
					
				}
			
			}
		}
	
	}
	
	$data['series'] = array();
	
	if (isset($avg)) {
		$data['series'][0] = new stdClass;
		$data['series'][0]->name = 'Average';
		$data['series'][0]->data = $avg;
		$data['series'][0]->pointPlacement = 'on';
	}
	
	if (isset($user)) {
		$data['series'][1] = new stdClass;
		$data['series'][1]->name = 'User';
		$data['series'][1]->data = $user;
		$data['series'][1]->pointPlacement = 'on';
	}
	
	return $data;

}

function durationByModule($user_id = FALSE) {
	global $db;
	
	if (! $user_id) {
		$res = mysql_query("SELECT qw.`module`, SUM(duration) AS dur FROM quizz_weekly qw GROUP BY module");
	} else {
	
	
		if (($res = mysql_query("SELECT * FROM quiz_summary WHERE user_id = '$user_id'")) != FALSE) {
			if (mysql_num_rows($res) == 0) {
				//user doesn't exist
				$data['error'] = 1;
				$data['message'] = "User id $user_id doesn't exist";
			} else {
			
				$res = mysql_query("SELECT qw.`module`, SUM(duration) AS dur FROM quizz_weekly qw WHERE user_id = '$user_id' GROUP BY module");
			
			}
		}
	
	}
	
	if ($res) {
	
		$data = array(
			'series' => array()
		);
	
		while (($module = mysql_fetch_object($res)) !== FALSE) {
			$data['series'][] = array($module->module, (int)$module->dur);
		}
		
		return $data;
		
	} else {
		return FALSE;
	}
	
}

function durationByModnum($user_id = FALSE, $modnum = FALSE) {
	global $db;
	
	
	if (! $user_id) {
		$res = mysql_query("SELECT SUBSTR(qw.`module`, 1, 1) AS modnum, qw.`module`, SUM(duration) AS dur FROM quizz_weekly qw".($modnum != FALSE ? " WHERE modnum LIKE '$modnum%'" : '')." GROUP BY modnum ORDER BY modnum");
	} else {
	
		if (($res = mysql_query("SELECT * FROM quizz_weekly WHERE user_id = '$user_id'")) != FALSE) {
		
			if (mysql_num_rows($res) == 0) {
				//user doesn't exist
				$data['error'] = 1;
				$data['message'] = "User id $user_id doesn't exist";
			} else {
			
				$res = mysql_query("SELECT SUBSTR(qw.`module`, 1, 1) AS modnum, qw.`module`, SUM(duration) AS dur FROM quizz_weekly qw WHERE user_id = '$user_id'".($modnum != FALSE ? " AND modnum LIKE '$modnum%'" : '')." GROUP BY modnum ORDER BY modnum");
			
			}
		}
	
	}
	
	if ($res) {
	
		$data = array(
			'series' => array()
		);
	
		while (($module = mysql_fetch_object($res)) !== FALSE) {
			$data['series'][] = array('Module '.$module->modnum, (int)$module->dur);
		}
		
		return $data;
		
	} else {
		return FALSE;
	}
}

// join the 2 above reports into donut data
function getQuizTemporal($user_id) {

	$by_modnum = durationByModnum($user_id);
	$by_module = durationByModule($user_id);
	
	$data_modnum = array();
	$data_module = array();
	
	foreach ($by_module['series'] as $mod_data) {
		$data_module[] = array(
			'name' => $mod_data[0],
			'y' => $mod_data[1]
		);
	}
	
	return array('series' => array(
		array(
			'name' => 'Module numbers',
			'data' => $by_modnum['series'],
			'size' => '60%'
		),
		array(
			'name' => 'Drilldown',
			'data' => $data_module,
			'size' => '40%'
		)
	));

}


function drilldown($modnum, $user_id = FALSE) {

	global $db;

	$data = array();
	
	$grades_user = array();
	$grades_avg = array();
	
	$x_axis = array();

	$res = mysql_query("SELECT module, AVG(grade) AS avg_grade, AVG(attempt) AS avg_attempts FROM quizz_weekly qw WHERE qw.`module` LIKE '$modnum%' GROUP BY module");

	while (($row = mysql_fetch_object($res)) !== FALSE) {
		$x_axis[] = $row->module;
		
		$grade = new stdClass;
		
		$grade->y = round($row->avg_grade, 2);
		$grade->avg_attempts = round($row->avg_attempts, 2);
		
		array_push($grades_avg, $grade);
	}
	
	if ($user_id) {
		$res = mysql_query("SELECT module, AVG(grade) AS avg_grade, MAX(attempt) AS attempts, MAX(grade) AS max_grade FROM quizz_weekly qw WHERE qw.`module` LIKE '$modnum%' AND user_id = '$user_id' GROUP BY module");
		while (($row = mysql_fetch_object($res)) !== FALSE) {
		
			$grade = new stdClass;
			
			$grade->y = round($row->avg_grade, 2);
			$grade->attempts = round($row->attempts, 2);
			$grade->max_grade = round($row->max_grade, 2);
		
			array_push($grades_user, $grade);
		}
	}

	while (($row = mysql_fetch_object($res)) !== FALSE) {
		$x_axis[] = $row->module;
		array_push($grades_avg, round($row->avg_grade, 2));
	}
	
	$data['series'] = array();
	
	$data['series'][0] = new stdClass;
	$data['series'][1] = new stdClass;
	
	$data['series'][0]->name = 'Average grade';
	$data['series'][1]->name = 'User avg grade';
	
	$data['series'][0]->data = $grades_avg;
	$data['series'][1]->data = $grades_user;
	
	
	$data['series'][0]->tooltip = array(
		'pointFormat' => 'Avg. mark <b>{point.y}</b><br>Avg. attempts <b>{point.avg_attempts}</b>'
	);
	
	$data['series'][1]->tooltip = array(
		'pointFormat' => 'Avg. mark <b>{point.y}</b><br>Attempts <b>{point.attempts}</b><br>Grade Obtained <b>{point.max_grade}</b>'
	);
	
	$data['x_axis'] = $x_axis;
	
	return $data;

}

?>

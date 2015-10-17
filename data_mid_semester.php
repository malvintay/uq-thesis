<?php

require_once 'db.php';

ini_set('display_errors', 'off');

function midSemesterPie($user_id = FALSE) {

	global $db;

	$data = array();

	//averages
	$res = mysql_query("SELECT AVG(MCQ) AS MCQ, AVG(part_b) AS part_b, AVG(part_c) AS part_c, AVG(MCQ+part_b+part_c) AS avg_total FROM mid_semester WHERE  deferred = 0 AND withdrawn = 0") or die(mysql_error());
	
	$res = mysql_fetch_object($res);
	
	$data['series'] = array(
		array(
			'name' => 'MCQ',
			'value' => round($res->MCQ, 2),
			'y' => 1,
			'color' => '#74AEE9'
		),
		array(
			'name' => 'B',
			'value' => round($res->part_b, 2),
			'y' => 1,
			'color' => '#74AEE9'
		),
		array(
			'name' => 'C',
			'value' => round($res->part_c, 2),
			'y' => 1,
			'color' => '#74AEE9'
		),
	);
	
	//total average
	$data['average'] = round($res->avg_total, 2);
	
	
	if ($user_id) {

		//actual marks
		
		$res = mysql_query("SELECT MAX(MCQ) AS MCQ, MAX(part_b) AS part_b, MAX(part_c) AS part_c FROM mid_semester WHERE student_id='$user_id' AND deferred = 0 AND withdrawn = 0");
		
		$res = mysql_fetch_object($res);

		$data['series'][0]['user_mark'] = round($res->MCQ, 2);
		$data['series'][1]['user_mark'] = round($res->part_b, 2);
		$data['series'][2]['user_mark'] = round($res->part_c, 2);
		
		$data['user_average'] = round(($res->MCQ + $res->part_b + $res->part_c) / 3, 2);
		$data['user_mark'] = round(($res->MCQ + $res->part_b + $res->part_c), 2);
		
	}
	
	
	return $data;

}

function midSemesterPlot($user_id = FALSE) {
	global $db;
	
	$res = mysql_query("SELECT * FROM mid_semester s RIGHT JOIN (SELECT t1.user_id, SUM(t1.attempts) AS attempts FROM (SELECT user_id, MAX(attempt) AS attempts FROM quizz_weekly qw1 GROUP BY qw1.user_id, qw1.module) t1 GROUP BY t1.user_id) t2 ON t2.user_id = s.student_id");
	
	$data = array();
	
	$data['series'] = array(
		array(
			'data' => array(),
			'name' => 'Students'
		)
	);
	
	while (($row = mysql_fetch_object($res)) !== FALSE) {
		$point = array();
		$point['x'] = (int)$row->attempts;
		$point['y'] = $row->MCQ + $row->part_b + $row->part_c;
		$point['user_id'] = $row->user_id;
		$point['MCQ'] = $row->MCQ;
		$point['B'] = $row->part_b;
		$point['C'] = $row->part_c;
		
		if ($user_id == $point['user_id']) {
			$point['color'] = '#DD790A';
			$user_point = $point;
		} else {
			$data['series'][0]['data'][] = $point;
		}
	}
	
	if ($user_id != FALSE && isset($user_point)) {
		array_unshift($data['series'][0]['data'], $user_point);
	}
	
	return $data;
	
}

?>

<?php

//serves as a controller for getting the data

ini_set('display_errors', 'on');

$advanced = isset($_GET['advanced']) ? $_GET['advanced'] : FALSE;

if ($advanced) {
	if (! isset($_GET['from']) || ! isset($_GET['to'])) {die('[]');}
	$range = array($_GET['from'], $_GET['to']);
}

$output = new stdClass;
$output->error = 0;

switch ($_GET['report_type']) {
	case 'clicks':
	
		if (! $advanced) {
			if (! isset($_GET['range'])) {die('[]');}
			$range = $_GET['range'];
		}
	
		require 'data_clicks.php';
		
		$user_id = ($_GET['userId'] == 'false' || ! $_GET['userId'] ? FALSE : $_GET['userId']);
		
		$data = getClicksData($range, $user_id);
	
		$output->data = $data;
		
		//get user average if user id passed
		if ($user_id) {
			$output->dataAverage = userVsAverage($user_id, $range, $data);
		}
		
		break;
		
	case 'lp':
	
		require 'data_lp.php';
		
		$user_id = ($_GET['userId'] == 'false' || ! $_GET['userId'] ? FALSE : $_GET['userId']);
		$range = $_GET['range'];
		
		$data = getLPData($range, $user_id);
		$output->data = $data;
		
		if ($data['error'] == 1) {
			$output->error = 1;
			$output->data = $data['message'];
		}
	
		break;
		
	case 'quiz_summary':
		
		require 'data_quiz.php';
		
		$user_id = ($_GET['userId'] == 'false' || ! $_GET['userId'] ? FALSE : $_GET['userId']);
		
		$data = getQuizSummary($user_id);
		$output->data = $data;
		
		if ($data['error'] == 1) {
			$output->error = 1;
			$output->data = $data['message'];
		}
		
		break;
		
		
	case 'quiz_temporal':
		
		require 'data_quiz.php';
		
		$user_id = ($_GET['userId'] == 'false' || ! $_GET['userId'] ? FALSE : $_GET['userId']);
		
		$data = durationByModnum($user_id);
		$output->data = $data;
		
		if ($data['error'] == 1) {
			$output->error = 1;
			$output->data = $data['message'];
		}
		
		break;
		
		
	case 'quiz_drilldown':
		
		require 'data_quiz.php';
		
		$user_id = ($_GET['userId'] == 'false' || ! $_GET['userId'] ? FALSE : $_GET['userId']);
		
		$module = $_GET['additional_data']['module'];
		
		$data = drilldown($module, $user_id);
		$output->data = $data;
		
		if ($data['error'] == 1) {
			$output->error = 1;
			$output->data = $data['message'];
		}
		
		break;
}

echo json_encode($output);


?>

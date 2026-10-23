<?php
include 'config.php';
require_once 'functions.php';

date_default_timezone_set("$timezone");
$area_asc = $_GET['asc'] ?? '';
$area_num = $_GET['areanum'] ?? '';
$how_many_days = (int) ($_REQUEST['hdays'] ?? 0);

$today = date("Y-m-d");
$dateminus = date('Y-m-d', strtotime("-$how_many_days days"));

$url = $bmlt_server. "/client_interface/json/?switcher=GetChanges&start_date=" .$dateminus. "&end_date=" .$today. "&service_body_id=" .urlencode($area_num);

// Load the changes before any output so a failed fetch shows a clear message
// instead of a blank 500.
$json = bmlt_get_json($url);
if ($json === null) {
    bmlt_fail($bmlt_server);
}

echo "
	<!DOCTYPE html>
		<html>
			<head>
				<link rel=\"stylesheet\" type=\"text/css\" href=\"assets/css/changes.css\">
			</head>
	<body>
		<H2>" .bmlt_e($area_asc). " CHANGES</H2>
		Changes for last " .$how_many_days. " days from today: " .date("l jS \of F Y h:i A") . "<br>
		<hr>";

echo bmlt_render_changes($json, 'YAP');

echo "END of File<br>BMLT Changes</body></html>";

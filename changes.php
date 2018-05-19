<?php
include 'config.php';

date_default_timezone_set("$timezone");
$area_asc = $_GET['asc'];
$area_num = $_GET['areanum'];
$how_many_days = $_REQUEST['hdays'];

$today=date("Y-m-d");
$dateminus=date('Y-m-d', strtotime("-$how_many_days days"));


//Changes printed on date and time
echo "
	<!DOCTYPE html>
		<html>
			<head>
				<link rel=\"stylesheet\" type=\"text/css\" href=\"changes.css\">
			</head>
	<body>
		<H2>" .$area_asc. " CHANGES</H2>
		Changes for last " .$how_many_days. " days from today: " .date("l jS \of F Y h:i A") . "<br>
		<hr>";

$url = $bmlt_server. "/client_interface/xml/?switcher=GetChanges&start_date=" .$dateminus. "&end_date=" .$today. "&service_body_id=" .$area_num;
// get xml file contents
$xml = simplexml_load_file($url);

// loop begins
foreach($xml->row as $row)
{
// begin new paragraph
echo "<p>";
// show Date
echo "<strong>Date:</strong> ".$row->date_string." - ";
// show Change Type
$change_type=$row->change_type;
	if ($change_type == "comdef_change_type_change") {
		$change_type = "Change";
	}
	if ($change_type == "comdef_change_type_new") {
		$change_type = "New";
	}
	if ($change_type == "comdef_change_type_delete") {
		$change_type = "DELETE";
	}
echo "<strong>Change Type:</strong> ".$change_type."<br/>";
// show Meeting ID
echo "<strong>Meeting (ID) Name:</strong> (".$row->meeting_id.") " .$row->meeting_name."<br/>";
// show User Name
echo "<strong>User Name:</strong> ".$row->user_name."<br/>";
// show Service Body
echo "<strong>Service Body:</strong> ".$row->service_body_name."</br><OL>";
// show details
$details=$row->details;
		// Remove last . at end of details
	$details = preg_replace('/.$/',"",$details);
		// Remove the weird #@-@# from the format codes
	$details = str_replace("#@-@#"," ",$details);
		// protect email . from being replacedwith </br> tag
	$details = preg_replace_callback(
		'/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})/',
			function ($email) {
				return str_replace(".","~DOT~",$email[0]);
				},
				$details
			);
		// Look for Latittude and Longitude, change . to ~DOT~
	$details = preg_replace_callback(
		'/from \"[0-9]+\./',
			function ($matches) {
				return str_replace(".","~DOT~",$matches[0]);
				},
				$details
			);
	$details = preg_replace_callback(
		'/to \"[0-9]+\./',
			function ($matches) {
				return str_replace(".","~DOT~",$matches[0]);
				},
				$details
			);
	$details = preg_replace_callback(
		'/from \"-+[0-9]+\./',
			function ($matches) {
				return str_replace(".","~DOT~",$matches[0]);
				},
				$details
			);
	$details = preg_replace_callback(
		'/to \"-+[0-9]+\./',
			function ($matches) {
				return str_replace(".","~DOT~",$matches[0]);
				},
				$details
			);
		//Change all the . to <LI>
	$details = str_replace(".","<LI>",$details);
		//Change all the ~DOT~ back to .
	$details = str_replace("~DOT~",".",$details);
//	$details = str_replace("from \"-75</br>"," from \"-75.",$details);
echo "<strong>Details:</strong><LI> ".$details."</OL>";
echo "</p> <hr>";
// end paragraph
}
// loop ends

echo "END of File<br>BMLT Changes</body></html>"

?>
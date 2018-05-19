<?php
include 'config.php';

date_default_timezone_set("$timezone");

$area_asc = $_GET['asc'];
$area_num = $_GET['areanum'];

$today=date("Y-m-d");


echo "<!DOCTYPE html>
<html>
<head>
<style>
body { font-family: \"Helvetica Neue\",Arial,sans-serif; font-size: 12px; font-weight: 200; line-height: 1.25;}
body>div {
  width:99%;
  display:inline-table;
  border:dotted;
  text-align:left;
  margin:1em auto;
  vertical-align:top;
  padding:3px
}
.right {
  border-left:dotted;
}
.right p:first-child {
  border-bottom:dotted;
}
.right p {
  padding:0.5em;
  margin:0;
  min-height:40%;
  box-sizing:border-box;
  background:red;
}
</style>
</head>

<body>
<h2>Meeting List Proof Report for ".$area_asc."</h2>

Printed on: " .date("l jS \of F Y h:i A") . "

<h3>Pass this proof report around at your Area Service Committee meeting so that groups can check their listing and make changes.</h3>
";

 function sortBySubkey(&$array, $subkey, $sortType = SORT_ASC) {
			if ( empty( $array ) ) { return; }
			foreach ($array as $subarray) {
				$keys[] = $subarray[$subkey];
			}
			array_multisort($keys, $sortType, $array);
		}

 	 $get_formats = file_get_contents($bmlt_server . "/client_interface/json/?switcher=GetFormats");
	  $formats = json_decode($get_formats, true);
   sortBySubkey($formats, 'key_string');

		 $data_formats = "<table width=\"70%\"><tr><td colspan=\"2\">MEETING FORMATS</td></tr>";
			$countmax = count ( $formats );
			for ( $count = 0; $count < $countmax; $count++ ) {
				$data_formats .= '<tr>';
				$data_formats .= "<td>".$formats[$count]['key_string']."</td>";
				$data_formats .= "<td>".$formats[$count]['name_string']."</td>";
				$count++;
				$data_formats .= "<td>".$formats[$count]['key_string']."</td>";
				$data_formats .= "<td>".$formats[$count]['name_string']."</td>";
				$data_formats .= "</tr>";
			}
			$data_formats .= "</table>";
			echo $data_formats;

//Changes printed on date and time
//echo "<hr>";

$url = $bmlt_server. "/client_interface/xml/?switcher=GetSearchResults&get_used_formats=1&services=".$area_num."&weekdays[]=1&weekdays[]=2&weekdays[]=3&weekdays[]=4&weekdays[]=5&weekdays[]=6&weekdays[]=7&sort_keys=meeting_name";

// get xml file contents
$xml = simplexml_load_file($url);

// loop begins
foreach($xml->row as $row)
{

// begin new paragraph
		echo "<div class=\"list-row\">
					<div class=\"list-left\">
				";
 
// Group Name
		echo "Group: <strong style=\"text-decoration: underline; font-size: larger;\">".$row->meeting_name."</strong>, ";

// Day of the Week
		$weekday_tinyint=$row->weekday_tinyint;
			if ($weekday_tinyint == "1") {
				$weekday_tinyint = "Sunday";
			}
			if ($weekday_tinyint == "2") {
				$weekday_tinyint = "Monday";
			}
			if ($weekday_tinyint == "3") {
				$weekday_tinyint = "Tuesday";
			}
			if ($weekday_tinyint == "4") {
				$weekday_tinyint = "Wednesday";
			}
			if ($weekday_tinyint == "5") {
				$weekday_tinyint = "Thursday";
			}
			if ($weekday_tinyint == "6") {
				$weekday_tinyint = "Friday";
			}
			if ($weekday_tinyint == "7") {
				$weekday_tinyint = "Saturday";
			}
// Print Week Day
		echo "Day:<strong> ".$weekday_tinyint."</strong>, ";

// Start Time
		$st = $row->start_time;
		$start_time = date("g:i A", strtotime($st));
		echo "Start:<strong> ".$start_time."</strong>, ";

// Duration
		$dt = $row->duration_time;
		$duration_time = date("g:i", strtotime($dt));
		echo "Duration (H:M):<strong> ".$duration_time."</strong>
		";
// List ID data
		echo "<small>[ BMLT ID: ".$row->id_bigint." / ";

// List NAWS ID data
		echo "NAWS ID: ".$row->worldid_mixed." / ";

// Published formats
		$formats = $row->formats;
		$formats = str_replace(",",", ",$formats);
		echo "Published Formats: <strong>".$formats."</strong> ]</small><br>";

//Address
		echo "
		        Address:<strong> ".$row->location_text.", ";
		echo " ".$row->location_street.", ";
		echo " ".$row->location_municipality.", ";
		echo " ".$row->location_province.", ";
		echo " ".$row->location_postal_code_1."</strong> ";

// Extra Info
		if (isset($row->location_info)) {
		echo "Extra Location Info:<strong> ".$row->location_info."</strong> / ";
		}	
		
// County
		echo "County:<strong> ".$row->location_sub_province." County</strong><br>
		CHANGES:<br><br><br><br>";
		
//echo "
//		        </div>
//		        <div class=\"list-right\">";
// End Paragraph
echo "	</div>
		</div>";
}

// loop ends

echo "</body>
</html>";
?>
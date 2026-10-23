<?php
include 'config.php';
require_once 'functions.php';

date_default_timezone_set("$timezone");

$area_asc = $_GET['asc'] ?? '';
$area_num = $_GET['areanum'] ?? '';
$recurse = isset($_GET['recurse']) && $_GET['recurse'] == "true" ? "&recursive=1" : "";
$show_unpublished = isset($_GET['unpublished']) ? $_GET['unpublished'] : "false";
$sortby_day = isset($_GET['sortby']) && $_GET['sortby'] == "day";
$today=date("Y-m-d");

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
// Remove parameter from query string

function filter_url($pattern, $url)
{
    return rtrim(preg_replace('~(\?|&)'.$pattern.'=[^&]*~', '$1', $url), "&");
}

if ($sortby_day) {
    $sortby_query = "weekday_tinyint,start_time";
    $sortby_link = "<a href=\"".bmlt_e(filter_url('sortby', $current_url))."\" class=\"no-print\">Sort By Meeting Name</a>";
} else {
    $sortby_query = "meeting_name";
    $sortby_link = "<a href=\"".bmlt_e(filter_url('sortby', $current_url))."&sortby=day\" class=\"no-print\">Sort By Day/Time</a>";
}

if ($show_unpublished == "true") {
    $published_unpublished_query = "&advanced_published=-1";
    $published_unpublished_header = " (Unpublished Meetings Only)";
    $published_unpublished_link = "<a href=\"".bmlt_e(filter_url('unpublished', $current_url))."&unpublished=false\" class=\"no-print\">Show Published</a>";
} elseif ($show_unpublished == "both") {
    $published_unpublished_query = "&advanced_published=0";
    $published_unpublished_header = "";
    $published_unpublished_link = "<a href=\"".bmlt_e(filter_url('unpublished', $current_url))."&unpublished=both\" class=\"no-print\">Show Published</a>";
} else {
    $published_unpublished_query = "&advanced_published=1";
    $published_unpublished_header = "";
    $published_unpublished_link = "<a href=\"".bmlt_e(filter_url('unpublished', $current_url))."&unpublished=true\" class=\"no-print\">Show Unpublished</a>";
}

// Load the formats and meetings before any output so a failed fetch shows a
// clear message instead of a blank 500.
$formats = bmlt_get_json($bmlt_server . "/client_interface/json/?switcher=GetFormats", true);
$url = $bmlt_server. "/client_interface/json/?switcher=GetSearchResults&get_used_formats=1&services=".urlencode($area_num)."&weekdays[]=1&weekdays[]=2&weekdays[]=3&weekdays[]=4&weekdays[]=5&weekdays[]=6&weekdays[]=7&sort_keys=" . $sortby_query . $recurse . $published_unpublished_query;
$json = bmlt_get_json($url);
if ($formats === null || $json === null) {
    bmlt_fail($bmlt_server);
}

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

@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
}
</style>
</head>

<body>".
    $published_unpublished_link.
    "&nbsp;&nbsp;".
    $sortby_link
    ."<h2>Meeting List Proof Report for ".bmlt_e($area_asc).$published_unpublished_header."</h2>

Printed on: " .date("l jS \of F Y h:i A") . "

<h3>Pass this proof report around at your Area Service Committee meeting so that groups can check their listing and make changes.</h3>
";


bmlt_sort_by_subkey($formats, 'key_string');

$data_formats = "<table width=\"70%\"><tr><td colspan=\"2\">MEETING FORMATS</td></tr>";
$countmax = count($formats);
for ($count = 0; $count < $countmax; $count += 2) {
    $data_formats .= '<tr>';
    $data_formats .= "<td>".bmlt_e($formats[$count]['key_string'])."</td>";
    $data_formats .= "<td>".bmlt_e($formats[$count]['name_string'])."</td>";
    if (isset($formats[$count + 1])) {
        $data_formats .= "<td>".bmlt_e($formats[$count + 1]['key_string'])."</td>";
        $data_formats .= "<td>".bmlt_e($formats[$count + 1]['name_string'])."</td>";
    } else {
        $data_formats .= "<td colspan=\"2\"></td>";
    }
    $data_formats .= "</tr>";
}
$data_formats .= "</table>";
echo $data_formats;

//Changes printed on date and time
//echo "<hr>";

// loop begins
foreach ($json->meetings ?? [] as $row) {
// begin new paragraph
    echo "<div class=\"list-row\">
					<div class=\"list-left\">
				";

// Group Name
    $unpublished = "";
    if ($row->published == 0) {
        $unpublished = " (UNPUBLISHED)";
    }
    echo "Group: <strong style=\"text-decoration: underline; font-size: larger;\">".bmlt_e($row->meeting_name).$unpublished."</strong>, ";

// Day of the Week
    echo "Day:<strong> ".bmlt_weekday_name($row->weekday_tinyint)."</strong>, ";

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
    echo "<small>[ BMLT ID: ".bmlt_e($row->id_bigint)." / ";

// List NAWS ID data
    echo "NAWS ID: ".bmlt_e($row->worldid_mixed)." / ";

// Published formats
    $published_formats = str_replace(",", ", ", $row->formats);
    echo "Published Formats: <strong>".bmlt_e($published_formats)."</strong> ]</small><br>";

// Virtual Meeting Additional Info
    if (isset($row->phone_meeting_number) || isset($row->virtual_meeting_link) || isset($row->virtual_meeting_additional_info)) {
        echo "Virtual Info: ";
        if (isset($row->phone_meeting_number)) {
            echo "[Phone: <strong> ".bmlt_e($row->phone_meeting_number)."</strong>] ";
        }
        if (isset($row->virtual_meeting_link)) {
            echo "[Link: <strong> ".bmlt_e($row->virtual_meeting_link)."</strong>] ";
        }
        if (isset($row->virtual_meeting_additional_info)) {
            echo "[Additional Info: <strong> ".bmlt_e($row->virtual_meeting_additional_info)."</strong>]";
        }
        echo "<br>";
    }

//Address
    echo "
		        Address:<strong> ".bmlt_e($row->location_text).", ";
    echo " ".bmlt_e($row->location_street).", ";
    echo " ".bmlt_e($row->location_municipality).", ";
    echo " ".bmlt_e($row->location_province).", ";
    echo " ".bmlt_e($row->location_postal_code_1)."</strong> ";

// Extra Info
    if (isset($row->location_info)) {
        echo "Extra Location Info:<strong> ".bmlt_e($row->location_info)."</strong> / ";
    }

// County
    echo "County:<strong> ".bmlt_e($row->location_sub_province)." County</strong><br>
		CHANGES:<br><br><br><br>";

//echo "
//              </div>
//              <div class=\"list-right\">";
// End Paragraph
    echo "	</div>
		</div>";
}

// loop ends

echo "</body>
</html>";

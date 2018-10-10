<?php
include 'config.php';
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

date_default_timezone_set("$timezone");
$today=date("Y-m-d");
$dateminus=date('Y-m-d', strtotime("-$howmanydays days"));

// show beginning header 
$message = "<H2>" .$service_body_name. " CHANGES</H2>Changes for last $howmanydays day(s) from " .date("l jS \of F Y h:i A") . "<br><hr>";

$url = $bmlt_server. "/client_interface/xml/?switcher=GetChanges&start_date=" .$dateminus. "&end_date=" .$today. "&service_body_id=" .$service_body_id;

// get xml file contents
$xml = simplexml_load_file($url);

if (empty($xml)) {
  // if no changes found do nothing 
}

else {
  // loop begins
  foreach($xml->row as $row) {
    if( strpos( $row->meeting_name, '_YAP_' ) !== false ) {
      // dont show YAP data
    }
  else {   
  // begin new paragraph
  $message .= "<p>";
   
  // show Date
  $message .= "<strong>Date:</strong> ".$row->date_string." - ";
  
  // show Change Type
  $change_type=$row->change_type;
  if ($change_type == "comdef_change_type_change") {	$change_type = "Change"; }
  if ($change_type == "comdef_change_type_delete") { $change_type = "DELETE"; }
  if ($change_type == "comdef_change_type_new") {		$change_type = "New";	}
  $message .= "<strong>Change Type:</strong> ".$change_type."<br/>";
   
  // show Meeting ID
  $message .= "<strong>Meeting (ID) Name:</strong> (".$row->meeting_id.") " .$row->meeting_name."<br/>";
   
  // show User Name
  $message .= "<strong>User Name:</strong> ".$row->user_name."<br/>";
   
  // Show Service Body
  $message .= "<strong>Service Body:</strong> ".$row->service_body_name."</br><OL>";
  
  // show details
  $details=$row->details;
   // remove root_server_uri info
  $details = str_replace(". root_server_uri was added as \"https:\" "," ",$details);
	 	// Remove last . at end of details
	 $details = preg_replace('/.$/',"",$details);
		// Remove the weird #@-@# from the format codes
	 $details = str_replace("#@-@#"," ",$details);
 		// protect email . from being replaced with </br> tag
	 $details = preg_replace_callback(
	 	'/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})/',
			function ($email) {
				return str_replace(".","~DOT~",$email[0]);
				},
				$details
			);
	 	// Look for Latittude and Longitude, change . to ~DOT~ This must happen so we can then find the individual changes and add <li> 
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
   
  // Change all the . to <LI>
 	$details = str_replace(".","<LI>",$details);
   
		//Change all the ~DOT~ back to .
 	$details = str_replace("~DOT~",".",$details);
  //	$details = str_replace("from \"-75</br>"," from \"-75.",$details);
  $message .= "<strong>Details:</strong><LI> ".$details."</OL>";
  $message .= "</p> <hr>";
  // end paragraph
  }
  }
  // loop ends
  $message .= "END of BMLT Changes";
  //echo $message;  // for testing
 
  //Send Email
  $mail = new PHPMailer(true);
  $mail->isSMTP();
  $mail->Host = $smtp_host;
  $mail->SMTPAuth = true;
  $mail->Username = $smtp_username;
  $mail->Password = $smtp_password;
  $mail->SMTPSecure = $smtp_secure;
  if ($smtp_alt_port) {
      $mail->Port = $smtp_alt_port;
  } elseif ($smtp_secure == 'tls') {
      $mail->Port = 587;
  } elseif ($smtp_secure == 'ssl') {
      $mail->Port = 465;
  }
  $mail->setFrom($smtp_from_address, $smtp_from_name);
  $mail->isHTML(true);
  $mail->addAddress($smtp_to_address, $smtp_to_name);
  $mail->Body = $message;
  $mail->Subject = $smtp_email_subject;
  $mail->send();
}
?>
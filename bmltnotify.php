<?php
include 'config.php';
require_once 'functions.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

date_default_timezone_set("$timezone");
$today=date("Y-m-d");
$dateminus=date('Y-m-d', strtotime("-$howmanydays days"));

// show beginning header
$message = "<H2>" .$service_body_name. " CHANGES</H2>Changes for last $howmanydays day(s) from " .date("l jS \of F Y h:i A") . "<br><hr>";

$url = $bmlt_server. "/client_interface/json/?switcher=GetChanges&start_date=" .$dateminus. "&end_date=" .$today. "&service_body_id=" .$notify_service_body_id;

$body = bmlt_http_get($url);

if ($body === false) {
    http_response_code(502);
    echo "Could not reach the BMLT server at " . $bmlt_server;
    exit;
}

$json = json_decode($body);

if (!empty($json)) {
    $message .= bmlt_render_changes($json, '_YAP_');
    $message .= "END of BMLT Changes";

    // Send Email
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_username;
    $mail->Password = $smtp_password;
    $mail->SMTPSecure = $smtp_secure;
    if (isset($smtp_alt_port)) {
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

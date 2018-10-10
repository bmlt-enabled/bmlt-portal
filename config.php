<?php
static $timezone = 'America/New_York';
static $bmlt_server = '';
static $service_body_name = '';
static $service_body_shortname = '';
static $service_body_website = '';
static $service_body_id = '1';  // the BMLT parent service body id number
static $howmanydays = '30';  // this should be set to the same number of days your cron job is set to



//static $from_name = 'Service Body Admin';
//static $from_email = 'service@someemail.org';

static $smtp_to_name = '';
static $smtp_to_address = '';
static $smtp_email_subject = 'Service Body BMLT Changes';

static $smtp_host = '';             // the smtp server
static $smtp_username = '';         // the smtp username
static $smtp_password = '';         // the smtp password
static $smtp_secure = '';           // either ssl (port 486) or more securely tls (port 587)
static $smtp_from_address = '';     // the address where the email will be sent from
static $smtp_from_name = '';        // the label name on the from address

<?php
//
// BMLT Portal configuration.
//
// Fill in the Required settings. Everything else is optional and can be left
// at its default. Service body id numbers come from your BMLT server; see the
// README for how to list them.
//
// Note: the commented-out options below are intentionally left undefined —
// uncommenting one turns the feature on, so only set them when you want them.
//

// ---- Required --------------------------------------------------------------

// Your BMLT root server's main_server URL (should start with https://).
static $bmlt_server = '';

// Timezone for dates shown in the portal.
// Values: https://www.php.net/manual/en/timezones.php
static $timezone = 'America/New_York';

// How your service body is labeled in the portal header.
static $service_body_name = '';       // e.g. 'Carolina Region'
static $service_body_shortname = '';  // e.g. 'CRNA'
static $service_body_website = '';    // e.g. 'https://crna.org'

// ---- Which service bodies to list ------------------------------------------

// Scope the Changes/Proofs lists to the service bodies nested under this parent
// id, at any depth (so Areas under a Metro/sub-region are included). Leave
// empty to list every service body on the server.
static $parent_service_body_id = '';

// Also list the parent service body itself as its own row. Only has an effect
// when $parent_service_body_id is set above.
static $include_parent_service_body_id = false;

// ---- Extra header menu links (optional) ------------------------------------
// Uncomment and set to add links to the top navigation menu.

// static $bmlt_instructional_manual = 'https://example.org/how-to-update-listings.pdf';
// static $bmlt_instructional_video  = 'https://example.org/meetings.mp4';

// ---- Notifier (bmltnotify.php) ---------------------------------------------
// Only needed if you run the email change notifier on a cron schedule.

// Service body id to report changes for (usually your parent/region id).
static $notify_service_body_id = '';

// Number of days of changes to include; match this to your cron interval.
static $howmanydays = '';

// Email recipient and subject.
static $smtp_to_name = '';
static $smtp_to_address = '';
static $smtp_email_subject = 'Service Body BMLT Changes';

// SMTP server settings.
static $smtp_host = '';             // the smtp server
static $smtp_username = '';         // the smtp username
static $smtp_password = '';         // the smtp password
static $smtp_secure = '';           // 'tls' (port 587, preferred) or 'ssl' (port 465)
static $smtp_from_address = '';     // the address the email is sent from
static $smtp_from_name = '';        // the label name on the from address

// Override the SMTP port (otherwise inferred from $smtp_secure: tls=587, ssl=465).
// static $smtp_alt_port = 587;

<?php

// Shared helpers for the BMLT Portal.

// Identifies the portal to the BMLT server in the User-Agent header. Some
// servers and WAFs reject requests that send no (or a default PHP) User-Agent.
define('BMLT_PORTAL_USER_AGENT', 'bmlt-portal/1.2.0 (+https://github.com/bmlt-enabled/bmlt-portal)');

/**
 * Fetch a URL and return the response body, or false on failure.
 *
 * Prefers cURL (follows http -> https redirects and works even when
 * allow_url_fopen is disabled) and falls back to file_get_contents with a
 * stream context when cURL isn't available.
 */
function bmlt_http_get($url)
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => BMLT_PORTAL_USER_AGENT,
        ]);
        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if ($body === false || $status >= 400) {
            return false;
        }
        return $body;
    }

    $context = stream_context_create([
        'http' => [
            'timeout' => 30,
            'follow_location' => 1,
            'max_redirects' => 5,
            'user_agent' => BMLT_PORTAL_USER_AGENT,
        ],
    ]);
    $body = @file_get_contents($url, false, $context);
    return $body === false ? false : $body;
}

/**
 * Fetch a URL and return decoded JSON, or null on any failure (a network
 * error, an empty response, or invalid JSON).
 */
function bmlt_get_json($url, $assoc = false)
{
    $body = bmlt_http_get($url);
    if ($body === false || $body === '') {
        return null;
    }
    $data = json_decode($body, $assoc);
    return json_last_error() === JSON_ERROR_NONE ? $data : null;
}

/**
 * Render a friendly "can't reach the server" page and stop.
 *
 * This is what the portal shows instead of a blank 500 when the BMLT server
 * can't be reached.
 */
function bmlt_fail($bmlt_server)
{
    if (!headers_sent()) {
        http_response_code(502);
    }
    $server = htmlspecialchars($bmlt_server, ENT_QUOTES);
    echo "<!DOCTYPE html><html><head><meta charset=\"utf-8\">"
        . "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">"
        . "<title>BMLT Portal &mdash; Server Unavailable</title>"
        . "<style>body{font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,sans-serif;"
        . "max-width:640px;margin:10vh auto;padding:0 20px;line-height:1.55;color:#22201b}"
        . "h1{font-size:1.3rem}code{background:#f0eee8;padding:1px 5px;border-radius:4px}"
        . "li{margin:.4em 0}</style></head><body>"
        . "<h1>Can&rsquo;t reach the BMLT server</h1>"
        . "<p>The portal couldn&rsquo;t load data from <code>" . $server . "</code>.</p>"
        . "<p>Common causes:</p><ul>"
        . "<li>The <code>\$bmlt_server</code> value in <code>config.php</code> is wrong, "
        . "or needs to start with <code>https://</code>.</li>"
        . "<li>This web server can&rsquo;t make outbound requests "
        . "(<code>allow_url_fopen</code> is off and cURL isn&rsquo;t installed, or a firewall is blocking it).</li>"
        . "<li>The BMLT server is temporarily down.</li>"
        . "</ul></body></html>";
    exit;
}

<?php

// Shared helpers for the BMLT Portal.

// Identifies the portal to the BMLT server in the User-Agent header. Some
// servers and WAFs reject requests that send no (or a default PHP) User-Agent.
define('BMLT_PORTAL_USER_AGENT', 'bmlt-portal/1.3.0 (+https://github.com/bmlt-enabled/bmlt-portal)');

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
 * Escape a value for safe output inside HTML text or a double-quoted attribute.
 */
function bmlt_e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Sort an array of associative rows in place by one of their columns.
 */
function bmlt_sort_by_subkey(array &$rows, string $subkey, int $sortType = SORT_ASC): void
{
    if (empty($rows)) {
        return;
    }
    $keys = array_column($rows, $subkey);
    array_multisort($keys, $sortType, $rows);
}

/**
 * Map a BMLT change_type code to a human-readable label.
 */
function bmlt_change_type_label(string $changeType): string
{
    return match ($changeType) {
        'comdef_change_type_change' => 'Change',
        'comdef_change_type_new' => 'New',
        'comdef_change_type_delete' => 'DELETE',
        default => $changeType,
    };
}

/**
 * Map a BMLT weekday_tinyint (1 = Sunday ... 7 = Saturday) to its day name.
 */
function bmlt_weekday_name($weekday): string
{
    return match ((string) $weekday) {
        '1' => 'Sunday',
        '2' => 'Monday',
        '3' => 'Tuesday',
        '4' => 'Wednesday',
        '5' => 'Thursday',
        '6' => 'Friday',
        '7' => 'Saturday',
        default => (string) $weekday,
    };
}

/**
 * Turn a change's "details" string into an HTML list.
 *
 * BMLT packs several field changes into one string, each ending with a period
 * that sits OUTSIDE the quoted from/to values, e.g.
 *   Street Address was changed from "..." to "...". County was added as "...".
 * We split only on those out-of-quote periods, so periods inside a value
 * (addresses, free-text comments, decimals, coordinates, emails) are kept
 * rather than breaking the line. Each item is HTML-escaped.
 */
function bmlt_format_change_details(string $details): string
{
    // Remove root_server_uri noise and the format-code separator.
    $details = str_replace(". root_server_uri was added as \"https:\" ", " ", $details);
    $details = str_replace("#@-@#", " ", $details);
    // Drop the trailing terminator period so it doesn't make an empty item.
    $details = preg_replace('/\.\s*$/', '', rtrim($details));

    // Split into statements at periods that are outside the quoted values.
    $items = [''];
    $inQuotes = false;
    for ($i = 0, $len = strlen($details); $i < $len; $i++) {
        $char = $details[$i];
        if ($char === '"') {
            $inQuotes = !$inQuotes;
            $items[count($items) - 1] .= $char;
        } elseif ($char === '.' && !$inQuotes) {
            $items[] = '';
        } else {
            $items[count($items) - 1] .= $char;
        }
    }

    return implode('<LI>', array_map('bmlt_e', $items));
}

/**
 * Render a GetChanges response as HTML. Meetings whose name contains
 * $skipNeedle (YAP meetings) are omitted.
 */
function bmlt_render_changes($changes, string $skipNeedle): string
{
    $html = "";
    foreach ($changes as $row) {
        if (strpos($row->meeting_name, $skipNeedle) !== false) {
            continue;
        }
        $html .= "<p>";
        $html .= "<strong>Date:</strong> " . bmlt_e($row->date_string) . " - ";
        $html .= "<strong>Change Type:</strong> " . bmlt_e(bmlt_change_type_label($row->change_type)) . "<br/>";
        $html .= "<strong>Meeting (ID) Name:</strong> (" . bmlt_e($row->meeting_id) . ") " . bmlt_e($row->meeting_name) . "<br/>";
        $html .= "<strong>User Name:</strong> " . bmlt_e($row->user_name) . "<br/>";
        $html .= "<strong>Service Body:</strong> " . bmlt_e($row->service_body_name) . "</br><OL>";
        $html .= "<strong>Details:</strong><LI> " . bmlt_format_change_details($row->details) . "</OL>";
        $html .= "</p> <hr>";
    }
    return $html;
}

/**
 * Build the "Changes" table rows for the portal home page: one row per service
 * body, each linking to its 30- and 90-day change reports.
 */
function bmlt_change_links_rows(array $serviceBodies): string
{
    $html = "";
    foreach ($serviceBodies as $sb) {
        $query = "asc=" . urlencode($sb['name']) . "&areanum=" . urlencode((string) $sb['id']);
        $html .= "<tr><td>" . bmlt_e($sb['name']) . "</td><td>";
        foreach ([30, 90] as $i => $days) {
            $html .= ($i ? " | " : "")
                . "<a href=\"changes.php?" . $query . "&hdays=" . $days
                . "\" class=\"button special\" target=\"_blank\">" . $days . " Days</a>";
        }
        $html .= "</td></tr>";
    }
    return $html;
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

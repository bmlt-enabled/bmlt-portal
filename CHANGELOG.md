## 1.2.0 (October 20, 2026)
* Show a clear message instead of a blank 500 when the BMLT server can't be reached.
* Fetch data via cURL (follows http -> https redirects, works when `allow_url_fopen` is off), falling back to `file_get_contents`.
* Fix bmltnotify.php decoding the request URL instead of the fetched response body.

## 1.1.0 (December 9, 2024)
* Fix for possible undefined array key error.

## 1.0.0 (September 4, 2022)
* Initially versioned release.

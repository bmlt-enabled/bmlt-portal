## 1.3.0 (October 23, 2026)
* List Areas nested under a Metro/sub-region when pointing at a Region, instead of only the Region's direct children (#5).
* Fix change details splitting a single change across multiple list items when a value (address, comment, date) contained a period.
* Fix empty Changes and Proof Reports tables on PHP 8 when no parent service body is configured.
* Fix proof report links applying the "recurse" flag to the wrong service bodies.
* Escape service body names, meeting data, and URL parameters to prevent HTML/markup issues.
* Add a `docker-compose.yml` for local development and fix the Docker image build.
* Remove duplicated code and modernize the build and CI tooling.

## 1.2.0 (October 20, 2026)
* Show a clear message instead of a blank 500 when the BMLT server can't be reached.
* Fetch data via cURL (follows http -> https redirects, works when `allow_url_fopen` is off), falling back to `file_get_contents`.
* Fix bmltnotify.php decoding the request URL instead of the fetched response body.

## 1.1.0 (December 9, 2024)
* Fix for possible undefined array key error.

## 1.0.0 (September 4, 2022)
* Initially versioned release.

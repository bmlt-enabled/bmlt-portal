# BMLT Portal

Additional entry point into the bmlt that offers easy access to meeting changes in the server as well as proof reports meant to be accessed by ASCs for GSRs to go over and 'proof' their meeting data.

# install
download the repository and edit the config.php then put in a folder on your server.

# examples
 * [Carolina Region](http://crna.org/changes) - example shows with manual link option
 * [New England Region](https://nerna.org/changes) - example shows with manual and video link option

# configure
The following options should be configured in the config.php file.

Example settings

static $bmlt_server = 'http://crna.org/main_server';

static $timezone = 'America/New_York';

static $service_body_name = 'Carolina Region';

static $service_body_shortname = 'CRNA';

static $service_body_website = 'http://crna.org';




# Optional configuration values

its now possible to specify a service body parent, this is usefull for service bodies that have multiple regions in them.

* static $parent_service_body_id = '';

When using this feature if you also want to include the parent service body in the list you must add this to your config as well.
* static $include_parent_service_body_id = true;

These will be added as links to main menu if they exist in the config.

* static $bmlt_instructional_manual = 'http://www.crna.org/changes/HowtoUpdateAreaMeetingListings.pdf';

* static $bmlt_instructional_video = 'https://www.dropbox.com/s/of7xevt0o62rgb1/meetings.mp4?dl=0';

# BMLT Notify

Sends an email with all BMLT changes in x amount of days

# install
download the repository and edit the config.php then put on a server and create cron job. the config $homanydays should be set to the same amount of time you set your cron job for.

static $notify_service_body_id = ''; must be set in the config and should probably be the service body parent.

# example
example cron job calls, this would set to call the script at 6pm on sunday

0 18 * * 0 wget -q -O - https://someserver.org/bmltnotify.php >/dev/null 

0 18 * * 0 curl --silent https://someserver.org/bmltnotify.php >/dev/null

# Timezones for config
Eastern ........... America/New_York

Central ........... America/Chicago

Mountain .......... America/Denver

Mountain no DST ... America/Phoenix

Pacific ........... America/Los_Angeles

Alaska ............ America/Anchorage

Hawaii ............ America/Adak

Hawaii no DST ..... Pacific/Honolulu

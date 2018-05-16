# bmlt-portal
Additional entry point into the bmlt that offers easy access to meeting changes in the server as well as proof reports meant to be accessed by ASCs for GSRs to go over and 'proof' their meeting data.

# install
download the repository and edit the config.php then put in a folder on your server.

# examples
 * [Carolina Region](http://crna.org/changes) - example shows with manual link option]
 * [New England Region](https://nerna.org/changes) - example shows with manual and video link option

# configure
The following options should be configured in the config.php file.

Example settings

static $bmlt_server = 'http://crna.org/main_server';

static $timezone = 'America/New_York';

$service_body_name = 'Carolina Region';

static $service_body_shortname = 'CRNA';

static $service_body_website = 'http://crna.org';




These are optional and will be added as links to main menu if they exist in the config.

static $bmlt_instructional_manual = 'http://www.crna.org/changes/HowtoUpdateAreaMeetingListings.pdf';

static $bmlt_instructional_video = 'https://www.dropbox.com/s/of7xevt0o62rgb1/meetings.mp4?dl=0';


# Timezones for config
Eastern ........... America/New_York

Central ........... America/Chicago

Mountain .......... America/Denver

Mountain no DST ... America/Phoenix

Pacific ........... America/Los_Angeles

Alaska ............ America/Anchorage

Hawaii ............ America/Adak

Hawaii no DST ..... Pacific/Honolulu
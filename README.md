# International Space Station (ISS) APRS Plugin

WordPress plugin for International Space Station APRS Tracker.
Requires [ISS APRS API](https://github.com/mkbodanu4/iss-aprs-api) as data source.

## Installation

1. Add plugin to your WordPres installation, activate it
2. Open Settings -> ISS APRS Tracker Plugin page
3. Fill form with URL to [ISS APRS API](https://github.com/mkbodanu4/iss-aprs-api) and API Key.
4. Add shortcode to any page or post.
5. Set ISS TLE data cache file (cache/iss.txt) access permission (chmod) to 777 and add cronjob to refresh that cache data at least once a day 

```
15 0 * * * php /{full path to WordPress installation}/wp-content/plugins/iss-aprs-plugin/cache/reload.php
```
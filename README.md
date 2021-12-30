# Flickr Heatmap
Display your geotagged Flickr photos on a map. To run, include a CSV file called mvjantzen.csv containing the photo data, such as:

> https://mvs202.github.io/flickrheatmap/?csv=mvjantzen

To create the CSV file, run the `create.php` program, using parameters to control which photos to include:

> php create.php 1a23bc4d56789ef01g23h4ij56k7l8m9 2021-01-01 2021-12-31 mvjantzen

You must include these parameters:
* Flickr API key (like 1a23bc4d56789ef01g23h4ij56k7l8m9) — You can get your own key via https://www.flickr.com/services/apps/create/apply
* start date (YYYY-MM-DD)
* end date (YYYY-MM-DD)
* user name or user ID (like 77945684@N00)

There is a limit of 4,000 photos that can be returned for any request. If your request has more, narrow the date range. 
You can then manually combine the results in a text editor.

PHP is bundled with macOS, so it is easy to run `create.php` locally.
1. Open the Terminal app
2. Move to the local flickrheatmap repo folder
```
cd /Users/michael/Documents/GitHub/flickrheatmap
```
3. Run the program
```
php create.php 1a23bc4d56789ef01g23h4ij56k7l8m9 2021-12-01 2021-12-03 mvjantzen
```
4. Copy and paste the output into a CSV file

You can combine the last two steps by redirecting Terminal's output to a file using the right angle bracket:
```
php create.php 1a23bc4d56789ef01g23h4ij56k7l8m9 2021-12-01 2021-12-03 mvjantzen > mvjantzen.csv
```

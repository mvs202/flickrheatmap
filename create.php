<?php
// Creates CSV content to be used by the Flickr Heatmap program.
// Flickr will return at most the first 4,000 results for any given search query; thus you can't go beyond 16 pages.
// More info at https://www.flickr.com/services/api/flickr.photos.search.html
require_once("phpFlickr.php");
if ($argc < 5) {
  echo "Error: missing parameters\r\n";
  echo "Parameters:\r\n";
  echo "    Flickr API key (https://www.flickr.com/services/apps/create/apply)\r\n";
  echo "    start date (YYYY-MM-DD)\r\n";
  echo "    end date (YYYY-MM-DD)\r\n";
  echo "    Flickr user ID\r\n";
  echo "    Flickr user name (optional; will use in URLs if provided)\r\n";
  echo "Example:\r\n";
  echo "    php getcsv.php 1a23bc4d56789ef01g23h4ij56k7l8m9 2021-12-01 2021-12-03 77945684@N00 mvjantzen\r\n";
  exit;
  }
$key = $argv[1];
$start = $argv[2];
$end = $argv[3];
$user = $argv[4];
$user_name = $argv[5];
$f = new phpFlickr($key);
if (isset($user)) { // either user ID, user alias, group ID, or group alias
  if (strpos($user, "@") > 0) { // either user ID or group ID
    $userinfo = $f->people_getInfo($user);
    if ($f->getErrorCode() != false)   // assume group ID
      $criteria["group_id"] = $user;
    else
      $criteria["user_id"] = $user;
    }
  else if ($user == "mvjantzen") {
    $criteria["user_id"] = "77945684@N00";
    $user_name = "mvjantzen";
    }
  else { // either user alias or group alias - alias can contain only letters, numbers, "_" or "-".
    $userinfo = $f->urls_lookupUser('www.flickr.com/photos/' . $user);
    if ($f->getErrorCode() != false) { // look for group alias
      $groupinfo = $f->urls_lookupGroup("www.flickr.com/groups/" . $user);
      if ($f->getErrorCode() != false)
        $failBlurb = $failBlurb . "Could not find user or group alias " . $_GET["user"] . " (" . $f->getErrorMsg() . ")\r\n";
      else
        $criteria["group_id"] = $groupinfo['id'];
      }
    else
      $criteria["user_id"] = $userinfo['id'];
    }
  }
$criteria["extras"] = "owner_name,geo,date_taken";
$criteria["has_geo"] = 1;
$criteria["per_page"] = 500;
$criteria["sort"] = "date-taken-desc";
$criteria["min_taken_date"] = $start;
$criteria["max_taken_date"] = $end . " 23:59:59";  // make end date inclusive
set_time_limit(0); 
echo "lat,lng,url,src,datetaken\r\n";
$photos = null;
$outputPix = null;
$outputPix = array();
$pageno = 1;
do {
  $criteria["page"] = $pageno;
  $photos = $f->photos_search($criteria);
  $pages = (int)$photos['pages'];
  if ($pages > 16) {
    echo $pages . " pages in result; no more than 16 allowed<p>";
    break;
    }
  if ($f->getErrorCode() != false) {
    echo "Problem conducting search: " .  $criteria . " (" . $f->getErrorMsg() . ")\r\n";
    break;
    }
  addPixToArray();
  $pageno++;
  } while ($pageno <= $pages);
foreach ($outputPix as $row) {
  $latlng = $row['lat'] . "," . $row['lng'];
  $date = substr($row['datetaken'], 0, 10);
  echo $latlng . "," . $row['url'] . "," . $row['src'] . "," . $date . "\r\n";
  } 

function addPixToArray() {
  global $f;
  global $outputPix;
  global $photos;
  global $user_name;
  $pix = (array)$photos['photo'];
  foreach ($pix as $photo) {
    $owner = empty($user_name) ? "$photo[owner]" : $user_name;
    $node = array(
      'lat' => $photo['latitude'],
      'lng' => $photo['longitude'],
      'ownername' => $photo['ownername'],
      'url' => "http://flickr.com/photos/$owner/$photo[id]",
      'src' => $f->buildPhotoURL($photo, "small"));
    $node['datetaken'] = substr($photo['datetaken'], 0, 10);  // use only the date (truncate time)
    array_push($outputPix, $node);
    }
  }
?>

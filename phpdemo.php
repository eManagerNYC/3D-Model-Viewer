<?php
/*
* Use PHP variables
*/
$url = 'assets/Project2.rvt.js';

function get_ip() {
    // http://chriswiegman.com/2014/05/getting-correct-ip-address-php/
    //Just get the headers if we can or else use the SERVER global
    if ( function_exists( 'apache_request_headers' ) ) {
      $headers = apache_request_headers();
    } else {
      $headers = $_SERVER;
    }
    //Get the forwarded IP if it exists
    if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
      $the_ip = $headers['X-Forwarded-For'];
    } elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
    ) {
      $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
    } else {
      $the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
    }
  return $the_ip;

function geoCheckIP($ip) {
  //check, if the provided ip is valid
  if(!filter_var($ip, FILTER_VALIDATE_IP)) {
    throw new InvalidArgumentException("IP is not valid");
   }
  //contact ip-server
  $response=@file_get_contents('http://www.netip.de/search?query='.$ip);
  if (empty($response)) {
    throw new InvalidArgumentException("Error contacting Geo-IP-Server");
  }
  //Array containing all regex-patterns necessary to extract ip-geoinfo from page
  $patterns=array();
  $patterns["domain"] = '#Domain: (.*?)&nbsp;#i';
  $patterns["country"] = '#Country: (.*?)&nbsp;#i';
  $patterns["state"] = '#State/Region: (.*?)<br#i';
  $patterns["town"] = '#City: (.*?)<br#i';
  //Array where results will be stored
  $ipInfo=array();
  //check response from ipserver for above patterns
  foreach ($patterns as $key => $pattern) {
    //store the result in array
    $ipInfo[$key] = preg_match($pattern,$response,$value) && !empty($value[1]) ? $value[1] : 'not found';
  }
  /*I've included the substr function for Country to exclude the abbreviation (UK, US, etc..)
  To use the country abbreviation, simply modify the substr statement to:
  substr($ipInfo["country"], 0, 3)
  */
    $ipdata = $ipInfo["town"]. ", ".$ipInfo["state"].", ".substr($ipInfo["country"], 4);
  return $ipdata;
}

$clientloc = geoCheckIP(get_ip());
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/css/jasny-bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>
    <script src=https://mrdoob.github.io/three.js/build/three.min.js ></script>
    <script src=https://mrdoob.github.io/three.js/examples/js/controls/TrackballControls.js ></script>
    <script src=https://mrdoob.github.io/three.js/examples/js/libs/stats.min.js ></script>
    <script src=https://cdnjs.cloudflare.com/ajax/libs/dat-gui/0.5/dat.gui.js></script>
    <script src="assets/sun-position.js" ></script>
    <script src="assets/va3c-viewer.js" ></script>
    <script src="assets/ColladaLoader.js"></script>
    <script src=https://mrdoob.github.io/three.js/examples/js/controls/OrbitControls.js ></script>
 
  </head>

  <body>
<?php
    echo '<div style display="none" >'.$cleintloc.'</div>';
    echo '<div class="row inline trans">

    <div class="col-md-2 padding text-center" style="padding-left:-10px;"><a onclick="resetCamera()">Reset View</a></div>
    <div class="col-md-2 padding text-center"><a onclick="zoomExtents()">Zoom Extents</a></div>
    <div class="col-md-2 padding text-center"><select class="styled-select" onchange="getComboA(this);">
          <option value="New York">New York</option>
          <option value="Sao Paulo">Sao Paulo</option>
          <option value="Paris">Paris</option>
          <option value="Tokyo">Tokyo</option>
          <option value="Moscow">Moscow</option>
      </select></div>
    <div class="col-md-2 padding text-center">Month 1-12<input type="range" name="points" id="month" min="1" max="12" step="1" value="5" onchange="updateLight()"></div>
    <div class="col-md-2 padding text-center">Day 1-31<input type="range" name="points" id="day" min="1" max="31" step="1" value="18" onchange="updateLight()"></div>
    <div class="col-md-2 padding text-center" style="padding-right:-10px;">Hour 0-24<input type="range" name="points" id="hour" min="1" max="24" step="1" value="24" onchange="updateLight()"></div>
<hr>

<script>
      var fname = "'.$url.'";
      init(fname);
      animate();
    </script>
</div>';
?>
  </body>
</html>
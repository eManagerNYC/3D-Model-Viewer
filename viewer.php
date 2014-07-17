<?php 
/*
Plugin Name: 3d Model Viewer
Plugin URI: http://blackreit.com
Description: Model viewer using three.js for CAD files
Author: Matthew M. Emma
Version: 0.1
Author URI: http://www.blackreit.com
Credits: va3c - http://va3c.github.io/
*/
/*-------------------------------------------------------*/
/* Enqueue scripts
/*-------------------------------------------------------*/
function modelscripts() {
  /*Register*/
	wp_register_script( 'threejs', plugins_url('assets/three.min.js', __FILE__), array(), null, false );
  wp_register_script( 'OrbitControls', plugins_url('assets/OrbitControls.js', __FILE__), array(), null, false );
	wp_register_script( 'stats', plugins_url('assets/stats.min.js', __FILE__), array(), null, false );
  wp_register_script( 'ColladaLoader', plugins_url('assets/ColladaLoader.js', __FILE__), array(), null, false );
	wp_register_script( 'va3c-viewer-aa', plugins_url('assets/va3c-viewer-v3aa.js', __FILE__), array(), null, false );
  wp_register_script( 'va3c-viewer-at', plugins_url('assets/va3c-viewer-v3aa.js', __FILE__), array(), null, false );
  wp_register_script( 'va3c-viewer-cc', plugins_url('assets/va3c-viewer-v3aa.js', __FILE__), array(), null, false );
  wp_register_script( 'va3c-viewer-pl', plugins_url('assets/va3c-viewer-v3aa.js', __FILE__), array(), null, false );
  wp_register_script( 'va3c-viewer-fo', plugins_url('assets/va3c-viewer-v3aa.js', __FILE__), array(), null, false );
  wp_register_script( 'va3c-viewer-bu', plugins_url('assets/va3c-viewer-v3aa.js', __FILE__), array(), null, false );
  wp_register_script( 'va3c-viewer-su', plugins_url('assets/va3c-viewer-v3aa.js', __FILE__), array(), null, false );
  wp_register_script( 'firstperson-theo', plugins_url('assets/first-person-controls-theo.js', __FILE__), array(), null, false );
	wp_register_script( 'sun-position', plugins_url('assets/sun-position.js', __FILE__), array(), null, false );
  /* Load em in */
	wp_enqueue_script('threejs');
	wp_enqueue_script('OrbitControls');
	wp_enqueue_script('stats');
	wp_enqueue_script('ColladaLoader');
  wp_enqueue_script('va3c-viewer-aa');
  wp_enqueue_script('va3c-viewer-at');
  wp_enqueue_script('va3c-viewer-cc');
  wp_enqueue_script('va3c-viewer-pl');
  wp_enqueue_script('va3c-viewer-fo');
  wp_enqueue_script('va3c-viewer-bu');
  wp_enqueue_script('va3c-viewer-su');
  wp_enqueue_script('firstperson-theo');
	wp_enqueue_script('sun-position');
}
add_action('wp_enqueue_scripts','modelscripts');
/*-------------------------------------------------------*/
/* Add Clientside Location from IP
/*-------------------------------------------------------*/
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
}

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
/*-------------------------------------------------------*/
/* 3d Modelv viewer
/*-------------------------------------------------------*/
function ModelViewer( $atts ) {
  extract( shortcode_atts( array(
    'url' => plugins_url('assets/Project2.rvt.js'),
    'loc' => 'New York',
    'width' => '100%',
    'height' => '50em'
  ), $atts, 'model' ) );

  if ($loc == 'ip') {
  		$clientloc = geoCheckIP(get_ip());
  }
add_action( 'wp_enqueue_scripts', 'modelscripts' );
echo '<canvas width="'.$width.'" height="'.$height.'"></canvas>';

echo "<script>
// Theo Armour ~ 2014-05-27 ~ MIT License
// every name space used below relates to a JavaScript file with the same name suffix

  var fname = '".$url."';
  var targetList;

  var clock = new THREE.Clock();

  init();
  animate();

  function init() {
    container = document.body.appendChild( document.createElement( 'div' ) );

    V3AA.addCSS();

    V3AA.addHeader();
    V3AA.addMenu();

    V3PL.parsePermalink();
    V3FO.addFileOpen();
    V3AA.addThreeJS();
    V3PL.addPermalinks();
    V3BU.addBundleOpen();
    V3CC.addCameraControls();
    V3AT.addAttributes();
    V3SU.addSunlight();
    V3AA.addAbout();
    V3AA.addFooter();

// if a permalink is found use it, otherwise load the default
    if ( V3PL.url ) {
      V3FO.loadURL( V3PL.url );
    } else {
      V3FO.loadFile( fname );
    }

    document.addEventListener( 'mousemove', onDocumentMouseMove, false );
    document.addEventListener( 'click', onDocumentMouseClick, false );
    window.addEventListener('mouseup', mouseUp, false);
  }

  function animate() {
    requestAnimationFrame( animate );
    renderer.render( scene, camera );
    controls.update( clock.getDelta() );
    stats.update();

    if ( !controls.heightSpeed ) return;
    msg.innerHTML = 'Debug:<br>' +
      'Freeze: ' + controls.freeze + '<br>' +
      'lookSpeed: ' + controls.lookSpeed.toFixed(3) + ' movementSpeed: ' + controls.movementSpeed + '<br>' +
      'lon ' + controls.lon.toFixed(3) + ' lat ' + controls.lat.toFixed(3) + '<br>' +0
      'movementSpeed ' + controls.movementSpeed + ' actualMoveSpeed '  + actualMoveSpeed.toFixed(2) + '<br>' +
    '';
  }

</script>";

}
add_shortcode( 'model', 'ModelViewer' );

function ModelFrame( $atts ) {
  extract( shortcode_atts( array(
    'type' => 'object',
    'width' => '100%',
    'height' => '50em'
  ), $atts, 'va3c' ) );
  $error = '<p>Sorry. This content cannot be rendered.  Stop living in the past and upgrade to <a href="http://www.abetterbrowser.com">a better browser</a></p>';
  /* Load as an object */
  if ($type === 'object'){
    echo '<object data="'.plugins_url('va3c-viewer-html5-r2.html', __FILE__).'" type="text/html" style="width:'.$width.'; height:'.$height.'" class="va3c" id="va3c">'.$error.'</object>';
  }
  if ($type === 'frame'){
    echo '<iframe src="'.plugins_url('va3c-viewer-html5-r2.html', __FILE__).'" type="text/html" style="width:'.$width.'; height:'.$height.'; border: 0" class="va3c" id="va3c">'.$error.'</iframe>';
  }
}
add_shortcode( 'va3c', 'ModelFrame' );

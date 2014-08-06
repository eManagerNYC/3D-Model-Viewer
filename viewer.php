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
  $patterns["domain"] = '#Domain: (.*?) #i';
  $patterns["country"] = '#Country: (.*?) #i';
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
  //wp_register_script( 'Detector', plugins_url('assets/Detector.js', __FILE__), array(), null, false );
	//wp_register_script( 'threejs', plugins_url('assets/three.min.js', __FILE__), array(), null, false );
  //wp_register_script( 'TrackballControls', plugins_url('assets/TrackballControls.js', __FILE__), array(), null, false );
  //wp_enqueue_script('Detector');
  //wp_enqueue_script('threejs');
  //wp_enqueue_script('TrackballControls');
  extract( shortcode_atts( array(
    'url' => plugins_url('assets/WaltHeadLo.js', __FILE__),
    'loc' => 'New York',
    'width' => '800',
    'height' => '600'
  ), $atts, 'model' ) );

  if ($loc == 'ip') {
  		$clientloc = geoCheckIP(get_ip());
  }

  echo '<script type="text/javascript" src='.plugins_url('/assets/Detector.js', __FILE__).' ></script>';
  echo '<script type="text/javascript" src='.plugins_url('/assets/three.min.js', __FILE__).' ></script>';
  echo '<script type="text/javascript" src='.plugins_url('/assets/TrackballControls.js', __FILE__).' ></script>';
  echo '<script type="text/javascript" src='.plugins_url('/assets/stats.min.js', __FILE__).' ></script>';
  echo '<div id="threejs" style="position: relative; border: 2px solid black">';

  echo '<script>
    var renderer, stats, scene, camera, controls;

    init();
    animate();

    function init() {

      var elem, geometry, material, mesh;

      if ( ! Detector.webgl ) {
        renderer = new THREE.CanvasRenderer( { alpha: 1, antialias: true, clearColor: 0xffffff  } );
      } else {
        renderer = new THREE.WebGLRenderer( { alpha: 1, antialias: true, clearColor: 0xffffff } );
      }
      renderer.setSize( '.$width.', '.$height.' );

      stats = new Stats();
      stats.domElement.style.cssText = "position: absolute; right: 0; top: 0; zIndex: 100;";

      elem = document.getElementById( "threejs" );
      elem.appendChild( renderer.domElement );
      elem.appendChild( stats.domElement );
      scene = new THREE.Scene();

      camera = new THREE.PerspectiveCamera( 40, '.($width/$height).', 1, 1000 );
      camera.position.set( 100, 100, 100 );
      controls = new THREE.TrackballControls( camera, renderer.domElement );

      loader = new THREE.JSONLoader();
      loader.load( "'.$url.'", function ( geometry, materials ) {
        material = new THREE.MeshNormalMaterial();
        mesh = new THREE.Mesh( geometry, material );
        scene.add( mesh );
      } );
    }

    function animate() {

      requestAnimationFrame( animate );
      renderer.render( scene, camera );
      controls.update();
      stats.update();
    }

  </script>';
	echo '<div class="controls" style="background: #cccccc; text-align: center; font-weight: bold; padding: 10px; position: absolute; bottom: 0; width: 100%; zIndex: 100">';
    /* check if fontawesome exists */
    if (wp_style_is( 'fontawesome' )) {
      echo '<div class="controls-left" style="float: left; width: 25%"><i class="fa fa-undo fa-2x"></i><br><strong>Rotate [Left Click]</strong></div>
      <div class="controls-center" style="float: left; width: 25%"><i class="fa fa-search-plus fa-2x"></i><br><strong>Zoom [Mouse Wheel]</strong></div>
      <div class="controls-right" style="float: left; width: 25%"><i class="fa fa-arrows fa-2x"></i><br><strong>Pan [Right Click]</strong></div>';
    } else {
    	echo '<div class="controls-left" style="float: left; width: 25%">Left Click<br>to Rotate.</div>
    	<div class="controls-center" style="float: left; width: 25%">Mouse Wheel<br>to Zoom.</div>
    	<div class="controls-right" style="float: left; width: 25%">Right Click<br>to Pan.</div>';
    }
    /* check if bootstrap exists */
    if (wp_style_is( 'bootstrap' )) {
      echo '<div class="controls-download" style="float: left; width: 25%"><a href="'.$url.'" class="btn btn-primary btn-lg" role="button" target="_blank" download>Download Model</a></div>';
    } else {
      echo '<div class="controls-download" style="float: left; width: 25%"><a href="'.$url.'" target="_blank" download>Download Model</a></div>';
    }
	
  echo '</div></div>';
}
add_shortcode( 'model', 'ModelViewer' );

function ModelFrame( $atts ) {
  add_action('wp_enqueue_scripts','modelscripts');
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

<?php 
/*
Plugin Name: 3d Model Viewer
Plugin URI: http://turneremanager.com
Description: Model viewer using three.js for CAD files
Author: Matthew M. Emma & Robert Carmosino
Version: 0.3
Author URI: http://www.turneremanager.com
Credits: va3c - http://va3c.github.io/
*/
/*-------------------------------------------------------*/
/* Enqueue scripts
/*-------------------------------------------------------*/
function modelscripts() {
  wp_register_script('Detector', plugins_url('assets/Detector.js', __FILE__), false);
  wp_register_script('Three', plugins_url('assets/three.min.js', __FILE__), false);
  wp_register_script('TrackballControls', plugins_url('assets/TrackballControls.js', __FILE__), false);
  wp_register_script('Stats', plugins_url('assets/stats.min.js', __FILE__), false);
  wp_register_script('jama-materials', plugins_url('assets/jama-materials.js', __FILE__), false);
  wp_register_script('jama-materials-data', plugins_url('assets/jama-materials-data.js', __FILE__), false);

  wp_enqueue_script('Detector');
  wp_enqueue_script('Three');
  wp_enqueue_script('TrackballControls');
  wp_enqueue_script('Stats');
  wp_enqueue_script('jama-materials');
  wp_enqueue_script('jama-materials-data');
}

if ( shortcode_exists( 'gallery' ) ) {
    add_action( 'wp_enqueue_scripts', 'modelscripts' );
}
/*-------------------------------------------------------*/
/* 3d Modelv viewer
/*-------------------------------------------------------*/
function ModelViewer( $atts ) {
  extract( shortcode_atts( array(
    'url' => plugins_url('assets/rac_basic_sample_project.rvt.js', __FILE__),
    'loc' => 'New York',
    'width' => '800',
    'height' => '600'
  ), $atts, 'model' ) );


  echo '<div id="threejs" style="position: relative; width: '.$width.'; height: '.$height.'; border: 2px solid black">';

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
      renderer.shadowMapEnabled = true;

      stats = new Stats();
      stats.domElement.style.cssText = "position: absolute; right: 0; top: 0; zIndex: 100;";

      elem = document.getElementById( "threejs" );
      elem.appendChild( renderer.domElement );
      elem.appendChild( stats.domElement );

      camera = new THREE.PerspectiveCamera( 40, '.($width/$height).', 1, 100000 );
      camera.position.set( 15000, 15000, 15000 );
      controls = new THREE.TrackballControls( camera, renderer.domElement );

      scene = new THREE.Scene();

      light = new THREE.AmbientLight( 0x888888 );
      scene.add( light );

      light = new THREE.PointLight( 0xffffff, 0.5 );
      scene.add( light );

      light = new THREE.DirectionalLight( 0xffffff );
      light.position.set( 10000, 10000, 10000 ).normalize();
      scene.add( light );
    }

    function loadFile( file ) {
      renderer.setSize( document.getElementById("threejs").clientWidth, document.getElementById("threejs").clientHeight );
      document.getElementById("control-load").style.visibility = "hidden";
      document.getElementById("control-load").style.display = "none";
      document.getElementById("progress").style.removeProperty("visibility");
      document.getElementById("progress").style.removeProperty("display");

      var data = requestFile(file);
      document.getElementById("progressbar").style.width = "25%";

      data = JSON.parse(data);
      document.getElementById("progressbar").style.width = "50%";

      if ( data.metadata === undefined ) { // 2.0
        data.metadata = { type: "Geometry" };
      }
      if ( data.metadata.type === undefined ) { // 3.0
        data.metadata.type = "Geometry";
      }
      if ( data.metadata.version === undefined ) {
        data.metadata.version = data.metadata.formatVersion;
      }

      if ( data.metadata.type.toLowerCase() === "geometry" ) {
        console.log( "found geometry" );

        loader = new THREE.JSONLoader();
        contents = loader.parse( data );

        geometry = contents.geometry;

        if ( contents.materials !== undefined ) {
          console.log( "found geometry", contents.materials );
          if ( contents.materials.length > 1 ) {
            material = new THREE.MeshFaceMaterial( contents.materials );
          } else {
            material = contents.materials[ 0 ];
          }
        } else {
          material = JAMA.materials.NormalSmooth.set();
        }

        var mesh = new THREE.Mesh( geometry, material );
        document.getElementById("progressbar").style.width = "75%";
        scene.add( mesh );
        document.getElementById("progressbar").style.width = "100%";

      } else if ( data.metadata.type.toLowerCase() === "object" ) {

        loader = new THREE.ObjectLoader();
        contents = loader.parse( data );
        document.getElementById("progressbar").style.width = "75%";

        if ( contents instanceof THREE.Scene ) {
          console.log( "found scene" );

          scene.add( contents );
          document.getElementById("progressbar").style.width = "100%";

        } else {
          console.log( "found object", contents );

          scene.add( contents );
          document.getElementById("progressbar").style.width = "100%";

        }
      } else if ( data.metadata.type.toLowerCase() === "scene" ) {
        console.log( "found deprecated");

        // DEPRECATED
        var loader = new THREE.SceneLoader();
        loader.load( bundle.src, function ( contents ) {
          document.getElementById("progressbar").style.width = "75%";
          scene.add( contents );
          document.getElementById("progressbar").style.width = "100%";

        }, "" );
      } else {
        console.log( "found a whoopsie");
        document.getElementById("progressbar").style.width = "100%";
      }
    }

    function requestFile ( fname ) {
      var xmlhttp = new XMLHttpRequest();
      xmlhttp.crossOrigin = "Anonymous"; 
      xmlhttp.open( "GET", fname, false );
      xmlhttp.send( null );
      return xmlhttp.responseText;
    }

    function animate() {

      requestAnimationFrame( animate );
      controls.update();
      renderer.render( scene, camera );
      stats.update();
    }

  </script>';
  echo '<div class="controls" style="background: #cccccc; text-align: center; font-weight: bold; padding: 10px; position: absolute; bottom: 0; width: 100%; zIndex: 100">';
    /* check if fontawesome exists */
    if (wp_style_is( 'fontawesome' )) {
      echo '<div class="controls-left" style="float: left; width: 15%"><i class="fa fa-undo fa-2x"></i><br><strong>Rotate [Left Button]</strong></div>
      <div class="controls-center" style="float: left; width: 15%"><i class="fa fa-search-plus fa-2x"></i><br><strong>Zoom [Mouse Wheel]</strong></div>
      <div class="controls-right" style="float: left; width: 15%"><i class="fa fa-arrows fa-2x"></i><br><strong>Pan [Right Button]</strong></div>';
    } else {
      echo '<div class="controls-left" style="float: left; width: 15%">Left Click<br>to Rotate.</div>
      <div class="controls-center" style="float: left; width: 15%">Mouse Wheel<br>to Zoom.</div>
      <div class="controls-right" style="float: left; width: 15%">Right Click<br>to Pan.</div>';
    }
    /* check if bootstrap exists */
    if (wp_style_is( 'bootstrap' )) {
      echo '<div class="controls-load" style="float: left; width: 25%">';
        echo '<button id="control-load" type="button" class="btn btn-primary btn-lg" onclick="loadFile(\''.$url.'\')">Load Model</button>';
        echo '<div class="progress" id="progress" style="visibility: hidden; display: none"><div class="progress-bar progress-bar-striped active" id="progressbar" role="progressbar" style="width: 0%"><span class="sr-only">Loading</span></div></div>';
      echo '</div>';
      echo '<div class="controls-download" style="float: left; width: 30%"><a id="control-download" type="button" class="btn btn-primary btn-lg" href="'.$url.'" download>Download Model</a></div>';
    } else {
      echo '<div class="controls-load" style="float: left; width: 25%"><button id="control-load" type="button" onclick="loadFile(\''.$url.'\')">Load Model</button></div>';
      echo '<div class="controls-download" style="float: left; width: 30%"><a id="control-download" type="button" href="'.$url.'" download>Download Model</a></div>';
    }
    
  
  echo '</div></div>';
}
add_shortcode( 'model', 'ModelViewer' );

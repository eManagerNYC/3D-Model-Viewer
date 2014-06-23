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
	wp_register_script( 'jquery', plugins_url('assets/jquery-1.10.2.min.js', __FILE__), array(), null, false );
	wp_register_script( 'bootstrap', plugins_url('assets/bootstrap.min.js', __FILE__), array(), null, false );
	wp_register_script( 'jasny', plugins_url('assets/jasny-bootstrap.min.js', __FILE__), array(), null, false );
	wp_register_script( 'threejs', plugins_url('assets/three.min.js', __FILE__), array(), null, false );
	wp_register_script( 'TrackballControls', plugins_url('assets/TrackballControls.js ', __FILE__), array(), null, false );
	wp_register_script( 'stats', plugins_url('assets/stats.min.js', __FILE__), array(), null, false );
	wp_register_script( 'datgui', plugins_url('assets/dat.gui.js', __FILE__), array(), null, false );
	wp_register_script( 'sun-position', plugins_url('assets/sun-position.js', __FILE__), array(), null, false );
	wp_register_script( 'va3c-viewer', plugins_url('assets/va3c-viewer.js', __FILE__), array(), null, false );
	wp_register_script( 'ColladaLoader', plugins_url('assets/ColladaLoader.js', __FILE__), array(), null, false );
	wp_register_script( 'OrbitControls', plugins_url('assets/OrbitControls.js', __FILE__), array(), null, false );
	wp_enqueue_script('jquery');
	wp_enqueue_script('bootstrap');
	wp_enqueue_script('jasny');
	wp_enqueue_script('threejs');
	wp_enqueue_script('TrackballControls');
	wp_enqueue_script('stats');
	wp_enqueue_script('datgui');
	wp_enqueue_script('sun-position');
	wp_enqueue_script('va3c-viewer');
	wp_enqueue_script('ColladaLoader');
	wp_enqueue_script('OrbitControls');
}
add_action( 'wp_enqueue_scripts', 'modelscripts' );
/*-------------------------------------------------------*/
/* 3d Modelv viewer
/*-------------------------------------------------------*/
function ModelViewer( $atts ) {
  extract( shortcode_atts( array(
    'url' => plugins_url('assets/Project2.rvt.js'),
  ), $atts, 'model' ) );

  return '<div class="row inline trans">

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
}
add_shortcode( 'model', 'ModelViewer' );
<?php
/*
* Use PHP variables
*/
$url = 'assets/Project2.rvt.js';
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
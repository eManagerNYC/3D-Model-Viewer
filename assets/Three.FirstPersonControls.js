/**
 * @author mrdoob / http://mrdoob.com/
 * @author alteredq / http://alteredqualia.com/
 * @author paulirish / http://paulirish.com/
 *
 * Modified from default:
 * - Added this.clickMove, which differentiates between mouse-looking and
 *   click-to-move.
 * - Changed camera movement in this.update() to respect wall collisions
 * - Changed this.update() to use this.noFly to disallow going up/down with R/F
 */
var myobject; 
 
THREE.FirstPersonControls = function ( object, domElement ) {
	
	
	this.object = object;
	this.target = new THREE.Vector3( 0, 0, 0 );

	this.domElement = ( domElement !== undefined ) ? domElement : document;

	this.movementSpeed = 2.5;
	this.lookSpeed = 0.01;

	this.noFly = false;
	this.lookVertical = true;
	this.autoForward = false;
	// this.invertVertical = false;

	this.activeLook = true;
	this.clickMove = false;

	this.heightSpeed = false;
	this.heightCoef = 1.0;
	this.heightMin = -1;

	this.constrainVertical = false;
	this.verticalMin = 0;
	this.verticalMax = Math.PI;

	this.autoSpeedFactor = 0.0;

	this.mouseX = 0;
	this.mouseY = 0;

	this.lat = 0;
	this.lon = 0;
	this.phi = 0;
	this.theta = 0;

	this.moveForward = false;
	this.moveBackward = false;
	this.moveLeft = false;
	this.moveRight = false;
	this.freeze = false;

	this.mouseDragOn = false;

	if ( this.domElement === document ) {

		this.viewHalfX = window.innerWidth / 2;
		this.viewHalfY = window.innerHeight / 2;

	} else {

		this.viewHalfX = this.domElement.offsetWidth / 2;
		this.viewHalfY = this.domElement.offsetHeight / 2;
		this.domElement.setAttribute( 'tabindex', -1 );

	}

	/*this.onMouseDown = function ( event ) {
		
		
			
			switch ( event.button ) {
			case 0: this.moveForward = true; break;
		}
	}
	
		this.mouseDragOn = true;

	};

	this.onMouseUp = function ( event ) {
	
			switch ( event.button ) {
			case 0: this.moveForward = false; break;
		}
	}
	
		this.mouseDragOn = false;
	};*/

	this.onKeyDown = function ( event ) {

		switch( event.keyCode ) {

			case 38: /*up*/ this.moveForward = true; break;
			case 87: /*W*/  this.mouseY = event.pageY - this.viewHalfY; break;

			case 37: /*left*/this.mouseX = event.pageX - this.viewHalfX; break;
			case 65: /*A*/ this.moveLeft = true; break;

			case 40: /*down*/ this.moveBackward = true; break;
			case 83: /*S*/ this.mouseY = event.pageY + this.viewHalfY; break;

			case 39: /*right*/this.mouseX = this.mouseX = event.pageX + this.viewHalfX; break;
			case 68: /*D*/ this.moveRight = true; break;

			case 82: /*R*/ this.moveUp = true; break;
			case 70: /*F*/ this.moveDown = true; break;

			case 81: /*Q*/ this.freeze = !this.freeze; break;
			
			

		}

	};

	this.onKeyUp = function ( event ) {

		switch( event.keyCode ) {

			case 38: /*up*/ this.moveForward = false; break;
			case 87: /*W*/  this.mouseY = false; break;

			case 37: /*left*/this.mouseX = false; break; 
			case 65: /*A*/ this.moveLeft = false; break;

			case 40: /*down*/ this.moveBackward = false; break;
			case 83: /*S*/ this.mouseY = false; break;
			
			case 39: /*right*/this.mouseX = false; break; 
			case 68: /*D*/ this.moveRight = false; break;

			case 82: /*R*/ this.moveUp = false; break;
			case 70: /*F*/ this.moveDown = false; break;

		}

	};

	this.update = function( delta ) {
		var actualMoveSpeed = 0;
		
		if ( this.freeze ) {
			
			return;
			
		} else {

			if ( this.heightSpeed ) {

				var y = THREE.Math.clamp( this.object.position.y, this.heightMin, this.heightMax );
				var heightDelta = y - this.heightMin;

				this.autoSpeedFactor = delta * ( heightDelta * this.heightCoef );

			} else {

				this.autoSpeedFactor = 0.0;

			}

			actualMoveSpeed = delta * this.movementSpeed;

			
			var vx = this.object.position.x;
			var vy = this.object.position.y;
			var vz = this.object.position.z;
			
			$("#listener").html(vx + "</br>" + vy + "</br>" + vz);
			
			if ( this.moveForward || ( this.autoForward && !this.moveBackward ) ) {
				this.object.translateZ( - ( actualMoveSpeed + this.autoSpeedFactor ) );
				if(vz < -1145 || vz > 1050 || vx < -1581 || vx > 1761){
					this.object.translateZ( actualMoveSpeed + this.autoSpeedFactor )
					if(vz < -1145) {this.object.position.z = -1144.9999;}
					if(vz > 1050) {this.object.position.z = 1049.9999;}
					if(vx < -1581) {this.object.position.x = -1580.9999;}
					if(vx > 1761) {this.object.position.x = 1760.9999;}
				}
			}
			
			if ( this.moveBackward || ( this.autoBackward && !this.moveBackward ) ) {
				
				this.object.translateZ(actualMoveSpeed + this.autoSpeedFactor);
				if(vz < -1145 || vz > 1050 || vx < -1581 || vx > 1761){
					this.object.translateZ(- (actualMoveSpeed + this.autoSpeedFactor ) );
					if(vz < -1145) {this.object.position.z = -1144.9999;}
					if(vz > 1050) {this.object.position.z = 1049.9999;}
					if(vx < -1581) {this.object.position.x = -1580.9999;}
					if(vx > 1761) {this.object.position.x = 1760.9999;}
				}
			}

			if ( this.moveLeft || ( this.autoBackward && !this.moveBackward ) ) {
				this.object.translateX( - (actualMoveSpeed + this.autoSpeedFactor ) );
				if(vz < -1145 || vz > 1050 || vx < -1581 || vx > 1761){
					this.object.translateX( actualMoveSpeed + this.autoSpeedFactor )
					if(vz < -1145) {this.object.position.z = -1144.9999;}
					if(vz > 1050) {this.object.position.z = 1049.9999;}
					if(vx < -1581) {this.object.position.x = -1580.9999;}
					if(vx > 1761) {this.object.position.x = 1760.9999;}
				}
			}
			if ( this.moveRight ) {
				this.object.translateX( actualMoveSpeed + this.autoSpeedFactor );
				if(vz < -1145 || vz > 1050 || vx < -1581 || vx > 1761){
					this.object.translateX(-(actualMoveSpeed + this.autoSpeedFactor) );
					if(vz < -1145) {this.object.position.z = -1144.9999;}
					if(vz > 1050) {this.object.position.z = 1049.9999;}
					if(vx < -1581) {this.object.position.x = -1580.9999;}
					if(vx > 1761) {this.object.position.x = 1760.9999;}
				}
			}

			
			
			
			
			if (!this.noFly) {
				if ( this.moveUp ) {
					this.object.translateY( actualMoveSpeed );
				}
				if ( this.moveDown ) {
					this.object.translateY( - actualMoveSpeed );
				}
			}

			var actualLookSpeed = delta * this.lookSpeed;

			if ( !this.activeLook ) {

				actualLookSpeed = 0;

			}

			this.lon += this.mouseX * actualLookSpeed;
			if( this.lookVertical ) this.lat -= this.mouseY * actualLookSpeed; // * this.invertVertical?-1:1;

			this.lat = Math.max( - 85, Math.min( 85, this.lat ) );
			this.phi = ( 90 - this.lat ) * Math.PI / 180;
			this.theta = this.lon * Math.PI / 180;

			var targetPosition = this.target,
				position = this.object.position;

			targetPosition.x = position.x + 100 * Math.sin( this.phi ) * Math.cos( this.theta );
			targetPosition.y = position.y + 100 * Math.cos( this.phi );
			targetPosition.z = position.z + 100 * Math.sin( this.phi ) * Math.sin( this.theta );

		}

		var verticalLookRatio = 1;

		if ( this.constrainVertical ) {

			verticalLookRatio = Math.PI / ( this.verticalMax - this.verticalMin );

		}

		this.lon += this.mouseX * actualLookSpeed;
		if( this.lookVertical ) this.lat -= this.mouseY * actualLookSpeed * verticalLookRatio;

		this.lat = Math.max( - 85, Math.min( 85, this.lat ) );
		this.phi = ( 90 - this.lat ) * Math.PI / 180;

		this.theta = this.lon * Math.PI / 180;

		if ( this.constrainVertical ) {

			this.phi = THREE.Math.mapLinear( this.phi, 0, Math.PI, this.verticalMin, this.verticalMax );

		}

		var targetPosition = this.target,
			position = this.object.position;

		targetPosition.x = position.x + 100 * Math.sin( this.phi ) * Math.cos( this.theta );
		targetPosition.y = position.y + 100 * Math.cos( this.phi );
		targetPosition.z = position.z + 100 * Math.sin( this.phi ) * Math.sin( this.theta );

		this.object.lookAt( targetPosition );

	};


	this.domElement.addEventListener( 'contextmenu', function ( event ) { event.preventDefault(); }, false );

	this.domElement.addEventListener( 'mousemove', bind( this, this.onMouseMove ), false );
	this.domElement.addEventListener( 'mousedown', bind( this, this.onMouseDown ), false );
	this.domElement.addEventListener( 'mouseup', bind( this, this.onMouseUp ), false );
	this.domElement.addEventListener( 'keydown', bind( this, this.onKeyDown ), false );
	this.domElement.addEventListener( 'keyup', bind( this, this.onKeyUp ), false );

	function bind( scope, fn ) {

		return function () {
			fn.apply( scope, arguments );

		};

	};
	
	myobject = this;

};

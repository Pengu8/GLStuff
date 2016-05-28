<?php include("core.php"); ?>
<head>
	<title>Avatar Test 2</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
	<style>
	body {
		font-family: Monospace;
		background-color: #FFF;
		color: #fff;
		margin: 0px;
		overflow: hidden;
	}
	#info {
		color: #fff;
		position: absolute;
		top: 10px;
		width: 100%;
		text-align: center;
		z-index: 100;
		display:block;
	}
	#info a, .button { color: #f00; font-weight: bold; text-decoration: underline; cursor: pointer }
	</style>
</head>

<body>
	<script type="x-shader/x-vertex" id="vertex_shader">
	#if NUM_DIR_LIGHTS > 0
	struct DirectionalLight {
		vec3 direction;
		vec3 color;
		int shadow;
		float shadowBias;
		float shadowRadius;
		vec2 shadowMapSize;
	};
	uniform DirectionalLight directionalLights[ NUM_DIR_LIGHTS ];
	#endif
	varying vec3 color;
	varying vec2 vUv;
	void main() {
		float r = directionalLights[0].color.r;
		float g = directionalLights[0].color.g;
		float b = directionalLights[0].color.b;
		color = vec3(r,g,b);
		gl_Position = projectionMatrix * modelViewMatrix * vec4(position , 1.0);
	}
	</script>

	<script type="x-shader/x-fragment" id="fragment_shader">
	uniform vec3 colors;
	uniform sampler2D texture;

	varying vec2 vUv;
	varying vec3 color;
	void main() {
		vec4 tColor = texture2D( texture, vUv );
		//tColor.a = .5;
		gl_FragColor = tColor;
	}
	</script>
	<script src="./JS/three.js"></script>

	<script src="./JS/DDSLoader.js"></script>
	<script src="./JS/MTLLoader.js"></script>
	<script src="./JS/OBJLoader.js"></script>
	<script src="./JS/OrbitControls.js"></script>
	<script src="./JS/Detector.js"></script>
	<script src="./JS/stats.min.js"></script>

	<script>

	var container, stats;
	var camera, scene, renderer;
	var mouseX = 0, mouseY = 0;

	var windowHalfX = window.innerWidth / 2;
	var windowHalfY = window.innerHeight / 2;

	init();
	animate();

	function init() {

		container = document.createElement( 'div' );
		document.body.appendChild( container );
		renderer = new THREE.WebGLRenderer( {
			alpha: true,
			antialias: true
		} );

		renderer.setPixelRatio( window.devicePixelRatio );
		console.log( "Ratio:" +  window.devicePixelRatio );
		console.log( "Aspect Ratio:" + window.innerWidth / window.innerHeight );
		renderer.setSize( 255,300);
		container.appendChild( renderer.domElement );


		camera = new THREE.PerspectiveCamera( 14, 0.8, .2, 1500 );
		camera.position.z = 1;
		camera.position.y = 8;
		camera.updateProjectionMatrix();
		//camera.rotation.x = -1*(180/3.14159265)


		/*+var orbit = new THREE.OrbitControls( camera, renderer.domElement );
		orbit.enableZoom = false;
		orbit.enablePan = false;
		orbit.autoRotate = true;*/
		// scene

		scene = new THREE.Scene();

		var ambient = new THREE.AmbientLight( 0xffffff );
		scene.add( ambient );

		var dirLight = new THREE.DirectionalLight(0xffffff, .41);
		dirLight.position.set(100, 100, 50);
		scene.add(dirLight);


		var light = new THREE.PointLight( 0xf8f8ff, 0.25, 10000 );
		light.position.set( 0, 100,-75);
		scene.add( light );

		//var directionalLight = new THREE.DirectionalLight( 0xf8f8ff );
		//directionalLight.position.set( 0, 0, 1 ).normalize();
		//scene.add( directionalLight );

		// model

		var onProgress = function ( xhr ) {
			if ( xhr.lengthComputable ) {
				var percentComplete = xhr.loaded / xhr.total * 100;
				//console.log( Math.round(percentComplete, 2) + '% downloaded' );
			}
		};

		var onError = function ( xhr ) { };

		THREE.Loader.Handlers.add( /\.dds$/i, new THREE.DDSLoader() );

		var mtlLoader = new THREE.MTLLoader();
		mtlLoader.setBaseUrl( './Avatar/' );
		mtlLoader.setPath( './Avatar/' );
		mtlLoader.load( 'Avatar.mtl', function( materials ) {

			materials.preload();

			var objLoader = new THREE.OBJLoader();
			objLoader.setMaterials( materials );
			objLoader.setPath( './Avatar/' );
			objLoader.load( 'Avatar.obj', function ( object )
			{
				object.alphaTest = 0;
				object.transparent = true;
				//var material = new THREE.MeshBasicMaterial({color: 0x0000ff});
				/*
				var AvatarTexture;
				var AvatarTextureP;

				var loader = new THREE.TextureLoader();

				// load a resource
				loader.load(
				// resource URL
				'./Avatar/TestTexture.png',
				// Function when resource is loaded
				function ( texture ) {
				// do something with the texture
				AvatarTexture = new THREE.MeshPhongMaterial( {
				map: texture,
				shininess:80
			} );
		},
		// Function called when download progresses
		function ( xhr ) {
		console.log( (xhr.loaded / xhr.total * 100) + '% loaded' );
	},
	// Function called when download errors
	function ( xhr ) {
	console.log( 'An error happened' );
}
);
// load a resource
loader.load(
// resource URL
'./Avatar/templatepants.png',
// Function when resource is loaded
function ( texture ) {
// do something with the texture
AvatarTextureP = new THREE.MeshPhongMaterial( {
map: texture,
shininess:80
} );
},
// Function called when download progresses
function ( xhr ) {
console.log( (xhr.loaded / xhr.total * 100) + '% loaded' );
},
// Function called when download errors
function ( xhr ) {
console.log( 'An error happened' );
}
);
*/

var AvatarTexture = new THREE.MeshPhongMaterial( { map: THREE.ImageUtils.loadTexture('./Avatar/TestTexture.png'), shininess: 80} ); //Torso
var AvatarTextureP = new THREE.MeshPhongMaterial( { map: THREE.ImageUtils.loadTexture('./Avatar/templatepants.png'), shininess: 80 } ); //Pants

//var texture = new THREE.Texture( generateTexture( ) ); // texture background is transparent
var texture = THREE.ImageUtils.loadTexture('./Avatar/FaceTestTr.png');
/*var texture;
var notDone = true;
loader.load(
// resource URL
'./Avatar/FaceTestTr.png',
// Function when resource is loaded
function ( tex ) {
texture = tex;
texture.needsUpdate = true;
notDone = false;
}
);
// uniforms
//console.log("Spinning");
//while(notDone);
*/
var uniforms = THREE.UniformsUtils.merge( [
	THREE.UniformsLib[ "lights" ],
	{
		color: { type: "c", value: new THREE.Color( 0x3344ff ) },
		texture: { type: "t", value: texture }
	}



] );
// important

/*var uniforms = {
color: { type: "c", value: new THREE.Color( 0xffffff ) },
texture: { type: "t", value: texture },
transparent: true,
opacity: 0.9,
};*/

// material
var AvatarTextureF = new THREE.ShaderMaterial({
	uniforms        : uniforms,
	vertexShader    : document.getElementById( 'vertex_shader' ).textContent,
	fragmentShader  : document.getElementById( 'fragment_shader' ).textContent,
	transparent: true,
	lights: true
});

var AvatarTextureF = new THREE.MeshPhongMaterial( { map: THREE.ImageUtils.loadTexture('./Avatar/FaceTestTr.png'), shininess: 80,  shading: THREE.SmoothShading, alphaMap: 0x000000} ); //Face

//material.map = AvatarTexture;
object.children[0].material = AvatarTexture;
object.children[1].material = AvatarTextureF;
object.children[1].side = THREE.DoubleSide;
object.children[5].material = AvatarTextureP;
object.children[2].material = AvatarTexture;
object.children[3].material = AvatarTextureP;
object.children[4].material = AvatarTexture;
mesh = object;

checkMesh = function(mesh, child_index) {
	if (
		mesh.geometry.faces.length > 0 &&
		mesh.geometry.vertices.length > 0
	) {
		// do stuff here with the good mesh

		for (var i = 0; i < mesh.children.length; i++)
		if (!checkMesh(mesh.children[i], i))
		i--; // child was removed, so step back

		return true;
	} else // empty mesh! this causes WebGL errors
	{
		if (mesh.parent != null)
		mesh.parent.children.splice(child_index, 1);

		console.log(mesh.name + " has zero faces and/or vertices so it is removed.");
		mesh = null;

		return false;
	}
}

//checkMesh(mesh,0);

//geometry.scale.set(15, 15, 15);
//HEAD: 0
//TORSO: 3
//LARM: 4
//RARM: 5
//LLEG: 1
//RLEG: 2


//object.faceVertexUvs[3] = [];
//object.faceVertexUvs[3][1][0] = [ TorsoFront[0], TorsoFront[1], TorsoFront[3] ];
//object.faceVertexUvs[0][1] = [ bricks[1], bricks[2], bricks[3] ];



//material.map = texture;
//object.children[3].material = material1;
//mesh = object;
//object.side = THREE.DoubleSide;
//object.faceVertexUvs[3][0] = [];
//mesh = new THREE.Mesh(object, AvatarTexture );
//mesh.position.z = -50;
//scene.add( mesh );
//object.rotation.y = -25*(180/3.14159265)
object.rotation.x = -1.25;
object.rotation.y = 0.6;
///object.position.y = 0.6;
scene.add( object );

edges = new THREE.VertexNormalsHelper( object, 2, 0x00ff00, 1 );

scene.add( edges );
}, onProgress, onError );

});



//


document.addEventListener( 'mousemove', onDocumentMouseMove, false );

//

window.addEventListener( 'resize', onWindowResize, false );

}

function onWindowResize() {

	windowHalfX = window.innerWidth / 2;
	windowHalfY = window.innerHeight / 2;

	camera.aspect = 0.8;
	camera.updateProjectionMatrix();

	//renderer.setSize( window.innerWidth, window.innerHeight );

}

function onDocumentMouseMove( event ) {

	mouseX = ( event.clientX - windowHalfX ) / 2;
	mouseY = ( event.clientY - windowHalfY ) / 2;

}

//

function animate() {

	requestAnimationFrame( animate );
	render();

}

function render() {

	//camera.position.x += ( mouseX - camera.position.x ) * .005;
	//camera.position.y += ( - mouseY - camera.position.y ) * .005;

	camera.lookAt( scene.position );

	renderer.render( scene, camera );

}

function saveAsImage() {
	var imgData, imgNode;

	try {
		var strMime = "image/jpeg";
		imgData = renderer.domElement.toDataURL(strMime);

		saveFile(imgData.replace(strMime, strDownloadMime), "test.jpg");

	} catch (e) {
		console.log(e);
		return;
	}

}
</script>

</body>
</html>

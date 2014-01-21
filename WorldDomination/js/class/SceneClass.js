/* 
 *  (c) 2014  Adrien THEVENET <a.thevenet@WD-project.com>
 */

var Scene;
var Scene = Class.extend({
    // Class constructor
    init: function() {
        'use strict';
        // Create a scene, a camera, a light and a WebGL renderer with Three.JS
        this.scene = new THREE.Scene();
        this.scene.fog = new THREE.Fog(0xffffff, 1000, 10000);
        // LIGHTS
        this.camera = new THREE.PerspectiveCamera(50, 0.5 * window.innerWidth / window.innerHeight,1 , 10000);
        this.camera.position.z = 500;
        this.scene.add(this.camera);

        var hemiLight = new THREE.HemisphereLight(0xffffff, 0xffffff, 1.25);
        hemiLight.color.setHSL(0.6, 1, 0.75);
        hemiLight.groundColor.setHSL(0.1, 0.8, 0.7);
        hemiLight.position.y = 1000;
        this.scene.add(hemiLight);

        // SKYDOME
        var vertexShader = document.getElementById('vertexShader').textContent;
        var fragmentShader = document.getElementById('fragmentShader').textContent;
        var uniforms = {
            topColor: {type: "c", value: new THREE.Color(0x0077ff)},
            bottomColor: {type: "c", value: new THREE.Color(0xffffff)},
            offset: {type: "f", value: 400},
            exponent: {type: "f", value: 0.6}
        }
        uniforms.topColor.value.copy(hemiLight.color);

        this.scene.fog.color.copy(uniforms.bottomColor.value);

        var skyGeo = new THREE.SphereGeometry(10000, 32, 15);
        var skyMat = new THREE.ShaderMaterial({vertexShader: vertexShader, fragmentShader: fragmentShader, uniforms: uniforms, side: THREE.BackSide});

        var sky = new THREE.Mesh(skyGeo, skyMat);
        this.scene.add(sky);

        // RENDERER
        this.renderer = new THREE.WebGLRenderer({antialias: true, alpha: false});
        this.renderer.setClearColor(this.scene.fog.color, 1);
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        this.renderer.gammaInput = true;
        this.renderer.gammaOutput = true;
        this.renderer.physicallyBasedShading = true;

        // Define the container for the renderer
        this.container = jQuery('#scene');

        // Create the user's character
        this.user = new UserClass({
            color: 0x7A43B6
        });
        this.scene.add(this.user.mesh);

        // Create the "world" : a 3D representation of the place we'll be putting our character in
        this.world = new WorldClass({
            color: 0xF5F5F5
        });
        var scene = this.scene;
        var batiment = new THREE.OBJMTLLoader();
        batiment.load('objects/qg/qg.obj', 'objects/qg/qg.mtl', function(object) {
            object.position.set(0, 0, 380);
            object.scale.set(100, 100, 100);
            scene.add(object);
        });
         
        var scene = this.scene;
        //scene.add(this.world.obj);
        console.log(this.world);
        console.log(this.world.QG);
        console.log(this.world.mesh);
        console.log('t');
        this.scene.add(this.world.obj);
        this.scene.add(this.world.mesh);

        // Define the size of the renderer
        this.setAspect();

        // Insert the renderer in the container
        this.container.prepend(this.renderer.domElement);

        // Set the camera to look at our user's character
        this.setFocus(this.user.mesh);

        // Start the events handlers
        this.setControls();
    },
    // Event handlers
    setControls: function() {
        'use strict';
        // Within jQuery's methods, we won't be able to access "this"
        var user = this.user,
                // State of the different controls
                controls = {
                    left: false,
                    up: false,
                    right: false,
                    down: false
                };
        // When the user presses a key 
        jQuery(document).keydown(function(e) {
            var prevent = true;
            // Update the state of the attached control to "true"
            switch (e.keyCode) {
                case 37:
                    controls.left = true;
                    break;
                case 38:
                    controls.up = true;
                    break;
                case 39:
                    controls.right = true;
                    break;
                case 40:
                    controls.down = true;
                    break;
                default:
                    prevent = false;
            }
            // Avoid the browser to react unexpectedly
            if (prevent) {
                e.preventDefault();
            } else {
                return;
            }
            // Update the character's direction
            user.setDirection(controls);
        });
        // When the user releases a key
        jQuery(document).keyup(function(e) {
            var prevent = true;
            // Update the state of the attached control to "false"
            switch (e.keyCode) {
                case 37:
                    controls.left = false;
                    break;
                case 38:
                    controls.up = false;
                    break;
                case 39:
                    controls.right = false;
                    break;
                case 40:
                    controls.down = false;
                    break;
                default:
                    prevent = false;
            }
            // Avoid the browser to react unexpectedly
            if (prevent) {
                e.preventDefault();
            } else {
                return;
            }
            // Update the character's direction
            user.setDirection(controls);
        });
        // On resize
        jQuery(window).resize(function() {
            // Redefine the size of the renderer
            Scene.setAspect();
        });
    },
    // Defining the renderer's size
    setAspect: function() {
        'use strict';
        // Fit the container's full width
        var w = this.container.width(),
                // Fit the initial visible area's height
                h = jQuery(window).height() - this.container.offset().top - 20;
        // Update the renderer and the camera
        this.renderer.setSize(w, h);
        this.camera.aspect = w / h;
        this.camera.updateProjectionMatrix();
    },
    // Updating the camera to follow and look at a given Object3D / Mesh
    setFocus: function(object) {
        'use strict';
        this.camera.position.set(object.position.x, object.position.y + 128, object.position.z - 256);
        this.camera.lookAt(object.position);
    },
    // Update and draw the scene
    frame: function() {
        'use strict';
        // Run a new step of the user's motions
        this.user.motion();
        // Set the camera to look at our user's character
        this.setFocus(this.user.mesh);
        // And draw !
        this.renderer.render(this.scene, this.camera);
    }
});
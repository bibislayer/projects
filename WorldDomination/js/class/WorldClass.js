/* 
 *  (c) 2014  Adrien THEVENET <a.thevenet@WD-project.com>
 */

var WorldClass = Class.extend({
    // Class constructor
    init: function(args) {
        'use strict';
        // Set the different geometries composing the room
        var obstacles = [
            new THREE.CubeGeometry(64, 64, 64)
        ];
        var initColor = new THREE.Color(0x497f13);
        var initTexture = THREE.ImageUtils.generateDataTexture(1, 1, initColor);
        var groundMaterial = new THREE.MeshPhongMaterial({color: 0xffffff, specular: 0x111111, map: initTexture});
        var groundTexture = THREE.ImageUtils.loadTexture("textures/terrain/grasslight-big.jpg", undefined, function() {
            groundMaterial.map = groundTexture;
        });
        groundTexture.wrapS = groundTexture.wrapT = THREE.RepeatWrapping;
        groundTexture.repeat.set(25, 25);
        groundTexture.anisotropy = 16;
        var that = this;
        // Set the "world" modelisation object
        this.mesh = new THREE.Object3D();
        // Set and add the ground
        this.ground = new THREE.Mesh(new THREE.PlaneGeometry(20000, 20000), groundMaterial);
        this.ground.rotation.x = -Math.PI / 2;
        this.mesh.add(this.ground);
        var batiment = new THREE.OBJMTLLoader();
        this.QG;
        var that = this;
        batiment.load('objects/qg/qg.obj', 'objects/qg/qg.mtl', function(object) {
            object.position.set(0, 0, 0);
            object.scale.set(100, 100, 100);
            that.QG = object;
        });
    }
});
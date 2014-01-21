function $(id) {

    return document.getElementById(id);

}

function handle_update(result, pieces) {

    refreshSceneView(result);
    //renderer.initWebGLObjects( result.scene );

    var m, material, count = 0;

    for (m in result.materials) {

        material = result.materials[ m ];
        if (!(material instanceof THREE.MeshFaceMaterial)) {

            if (!material.program) {

                console.log(m);
                renderer.initMaterial(material, result.scene.__lights, result.scene.fog);

                count += 1;
                if (count > pieces) {

                    //console.log("xxxxxxxxx");
                    break;

                }

            }

        }

    }

}
function onWindowResize() {

    windowHalfX = window.innerWidth / 2;
    windowHalfY = window.innerHeight / 2;

    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();

    renderer.setSize(window.innerWidth, window.innerHeight);

}

function setButtonActive(id) {

    $("start").style.backgroundColor = "green";

}

function onStartClick() {

    $("progress").style.display = "none";

    camera = loaded.currentCamera;
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();

    scene = loaded.scene;

}

function onDocumentMouseMove(event) {

    mouseX = (event.clientX - windowHalfX);
    mouseY = (event.clientY - windowHalfY);

}

function createLoadScene() {

    var result = {
        scene: new THREE.Scene(),
        camera: new THREE.PerspectiveCamera(65, window.innerWidth / window.innerHeight, 1, 1000)

    };

    result.camera.position.z = 100;

    var object, geometry, material, light, count = 500, range = 200;

    material = new THREE.MeshLambertMaterial({color: 0xffffff});
    geometry = new THREE.CubeGeometry(5, 5, 5);

    for (var i = 0; i < count; i++) {

        object = new THREE.Mesh(geometry, material);

        object.position.x = (Math.random() - 0.5) * range;
        object.position.y = (Math.random() - 0.5) * range;
        object.position.z = (Math.random() - 0.5) * range;

        object.rotation.x = Math.random() * 6;
        object.rotation.y = Math.random() * 6;
        object.rotation.z = Math.random() * 6;

        object.matrixAutoUpdate = false;
        object.updateMatrix();

        result.scene.add(object);

    }

    result.scene.matrixAutoUpdate = false;

    light = new THREE.PointLight(0xffffff);
    result.scene.add(light);

    light = new THREE.DirectionalLight(0x111111);
    light.position.x = 1;
    result.scene.add(light);

    return result;

}

//

function animate() {

    requestAnimationFrame(animate);

    render();
    stats.update();

}

function render() {

    camera.position.x += (mouseX - camera.position.x) * .001;
    camera.position.y += (-mouseY - camera.position.y) * .001;

    camera.lookAt(scene.position);

    renderer.render(scene, camera);

}

// Scene explorer user interface

function toggle(id) {

    var scn = $("section_" + id).style,
            btn = $("plus_" + id);

    if (scn.display == "block") {

        scn.display = "none";
        btn.innerHTML = "[+]";

    }
    else {

        scn.display = "block";
        btn.innerHTML = "[-]";

    }

}

function createToggle(label) {
    return function() {
        toggle(label)
    }
}
;

function refreshSceneView(result) {

    $("section_exp").innerHTML = generateSceneView(result);

    var config = ["obj", "geo", "mat", "tex", "lit", "cam"];

    for (var i = 0; i < config.length; i++)
        $("plus_" + config[i]).addEventListener('click', createToggle(config[i]), false);

}

function generateSection(label, id, objects) {

    var html = "";

    html += "<h3><a id='plus_" + id + "' href='#'>[+]</a> " + label + "</h3>";
    html += "<div id='section_" + id + "' class='part'>";

    for (var o in objects) {

        html += o + "<br/>";

    }
    html += "</div>";

    return html;

}

function generateSceneView(result) {

    var config = [
        ["Objects", "obj", result.objects],
        ["Geometries", "geo", result.geometries],
        ["Materials", "mat", result.materials],
        ["Textures", "tex", result.textures],
        ["Lights", "lit", result.lights],
        ["Cameras", "cam", result.cameras]
    ];

    var html = "";

    for (var i = 0; i < config.length; i++)
        html += generateSection(config[i][0], config[i][1], config[i][2]);

    return html;

}
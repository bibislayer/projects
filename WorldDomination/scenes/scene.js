{

"metadata" :
{
	"formatVersion" : 3.2,
	"type"          : "scene",
	"sourceFile"    : "scene.blend",
	"generatedBy"   : "Blender 2.65 Exporter",
	"objects"       : 29,
	"geometries"    : 3,
	"materials"     : 3,
	"textures"      : 0
},

"urlBaseType" : "relativeToScene",


"objects" :
{
	"Monkey.007" : {
		"geometry"  : "geo_Monkey",
		"groups"    : [  ],
		"material"  : "Material.002",
		"position"  : [ -0.712512, 2.115, -8.09784 ],
		"rotation"  : [ 0.0245137, 0.21586, 0.075485 ],
		"quaternion": [ 0.0162409, 0.107176, 0.0388305, 0.993349 ],
		"scale"     : [ 1, 1, 1 ],
		"visible"       : true,
		"castShadow"    : false,
		"receiveShadow" : false,
		"doubleSided"   : false
	},

	"Cube.005" : {
		"geometry"  : "geo_Cube",
		"groups"    : [  ],
		"material"  : "Material",
		"position"  : [ -1.00097, 1.31948, -8.75555 ],
		"rotation"  : [ -1.5708, 0, 0 ],
		"quaternion": [ -0.707107, 0, 0, 0.707107 ],
		"scale"     : [ 6.13, 0.07, 1.69 ],
		"visible"       : true,
		"castShadow"    : false,
		"receiveShadow" : false,
		"doubleSided"   : false
	},

	"Plane" : {
		"geometry"  : "geo_Plane",
		"groups"    : [  ],
		"material"  : "Material.001",
		"position"  : [ 0, -4.04787e-09, 0.11826 ],
		"rotation"  : [ -1.5708, 0, 0 ],
		"quaternion": [ -0.707107, 0, 0, 0.707107 ],
		"scale"     : [ 7.92279, 7.92279, 7.92279 ],
		"visible"       : true,
		"castShadow"    : false,
		"receiveShadow" : false,
		"doubleSided"   : false
	},


	"default_light" : {
		"type"       : "DirectionalLight",
		"direction"  : [ 0, 1, 1 ],
		"color"      : 16777215,
		"intensity"  : 0.80
	},

	"default_camera" : {
		"type"  : "PerspectiveCamera",
		"fov"   : 60.000000,
		"aspect": 1.333000,
		"near"  : 1.000000,
		"far"   : 10000.000000,
		"position": [ 0, 0, 10 ],
		"target"  : [ 0, 0, 0 ]
	}
},


"geometries" :
{
	"geo_Cube" : {
		"type" : "ascii",
		"url"  : "scene.Cube.js"
	},

	"geo_Monkey" : {
		"type" : "ascii",
		"url"  : "scene.Monkey.js"
	},

	"geo_Plane" : {
		"type" : "ascii",
		"url"  : "scene.Plane.js"
	}
},


"materials" :
{
	"Material" : {
		"type": "MeshLambertMaterial",
		"parameters": { "color": 10688800, "opacity": 1, "blending": "NormalBlending" }
	},

	"Material.001" : {
		"type": "MeshLambertMaterial",
		"parameters": { "color": 2401086, "opacity": 1, "blending": "NormalBlending" }
	},

	"Material.002" : {
		"type": "MeshLambertMaterial",
		"parameters": { "color": 10711076, "opacity": 1, "blending": "NormalBlending" }
	}
},


"transform" :
{
	"position"  : [ 0, 0, 0 ],
	"rotation"  : [ -1.5708, 0, 0 ],
	"scale"     : [ 1, 1, 1 ]
},

"defaults" :
{
	"bgcolor" : [ 0, 0, 0 ],
	"bgalpha" : 1.000000,
	"camera"  : "default_camera"
}

}

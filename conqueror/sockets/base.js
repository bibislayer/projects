// Connection db
var mongo = require('mongodb');
var monk = require('monk');
var db = monk('localhost:27017/conqueror');
var cookie = require('cookie');
var session = require('express-session');
var sessionStore = new session.MemoryStore();
var cookieParser = require('cookie-parser');
var async = require('async');
var ObjectID = require('mongodb').ObjectID;

var turnTime = 30000;

function is_int(value) {
	if ((parseFloat(value) == parseInt(value)) && !isNaN(value)) {
		return true;
	} else {
		return false;
	}
}

function upStatsBuilding(building){
	var newCitizenCost = newGoldCost = newCitizenMore = newHappinessMore = newGoldPerTurn = '';
	if(building.type == "cityHall"){
		newCitizenCost = Math.round((building.citizenMore + building.level) * 0.1);
		newGoldPerTurn = Math.round((building.goldPerTurn + building.level) * 0.2);
		newGoldCost = Math.round((building.goldCost + building.level) * 0.3);
	}
	if(building.type == "house"){
		newCitizenMore = Math.round((building.citizenMore + building.level) * 0.2);
		newGoldCost = Math.round((building.goldCost + building.level) * 0.3);
		newGoldPerTurn = Math.round((building.goldPerTurn + building.level) * 0.2);
	}
	if(building.type == "tavern"){
		newCitizenCost = Math.round((building.citizenMore + building.level) * 0.2);
		newHappinessMore = Math.round((building.happinessMore + building.level) * 0.2);
		newGoldCost = Math.round((building.goldCost + building.level) * 0.3);
	}
	if(building.type == "idol"){
		newHappinessMore = Math.round((building.happinessMore + building.level) * 0.2);
		newGoldCost = Math.round((building.goldCost + building.level) * 0.3);
	}
	if (newGoldPerTurn != '')
		building.goldPerTurn += newGoldPerTurn;
	if (newCitizenMore != '')
		building.citizenMore += newCitizenMore;
	if (newHappinessMore != '')
		building.happinessMore += newHappinessMore;

	if (newCitizenCost != '')
		building.citizenCost += newCitizenCost;
	if (newGoldCost != '')
		building.goldCost += newGoldCost;

	return building;
}

var buildingCollection = db.get('buildingCollection');
var userBuildingCollection = db.get('buildingUserCollection');
var userCollection = db.get('userCollection');
var timerGame;

module.exports = function(io) {
	// Active sockets by session
	var connections = {};
	io.on('connection', function(client) {
		try {
			for (key in connections) {
			    if('undefined' == typeof connections[key].user)
					delete connections[key];
			}
			var data = client.handshake || client.request;
			if (!data.headers.cookie) {
				console.log('Missing cookie headers');
				return;
			}
			//console.log('cookie header ( %s )', JSON.stringify(data.headers.cookie));
			var cookies = cookie.parse(data.headers.cookie);
			//console.log(cookies);
			//console.log('cookies parsed ( %s )', JSON.stringify(cookies));
			if (!cookies['sid']) {
				console.log('Missing cookie ' + 'sid');
				return;
			}
			var sid = cookieParser.signedCookie(cookies['sid'], 'quabulaneaulac');
			if (!sid) {
				console.log('Cookie signature is not valid');
				return;
			}
			console.log('session ID ( %s )', sid);
			if ('undefined' == typeof connections[sid] || 'undefined' == typeof connections[sid].user) {
				connections[sid] = {
					"length" : 0
				};
			} else {
				async.series([
				function(callback) {
					buildingCollection.find({}, "-_id", function(e, buildings) {
						client.emit('initGame', {
							username : connections[sid].user.login,
							resources : {
								gold : connections[sid].user.gold,
								citizen : connections[sid].user.citizen,
								happiness : connections[sid].user.happiness,
								goldPerTurn : connections[sid].user.goldPerTurn
							},
							buildings : connections[sid].user.buildings,
							buildingsList: buildings
						});
						client.broadcast.emit('alert', {
							msg: connections[sid].user.login + " vient de se connecter.",
							type: "success"
						});
						callback();
					});
				},
				function(callback) {
					console.log('timer 1');
					launchGame();
					callback();
				}],
				// optional callback
				function() {
					//console.log(session);
				});
			}
			// Add connection to pool
			connections[sid][client.id] = client;
			connections[sid].length++;

			data.sid = sid;
			sessionStore.get(sid, function(err, session) {
				if (err)
					return err;
				if (!session)
					return new Error('session not found');
				data.session = session;
				next();
			});
		} catch (err) {
			console.error(err.stack);
			return new Error('Internal server error');
		}
		client.on('login', function(data) {
			async.series([
			function(callback) {
				console.log(data);
				if (data.login != null && data.password != null) {
					userCollection.findOne({
						login : data.login,
						password : data.password
					}, "-password", function(e, user) {
						if (user) {
							console.log('logged');
							connections[sid].user = user;
							client.broadcast.emit('alert', {
								msg: user.login + " vient de se connecter.",
								type: "success"
							});
							callback();
						}
					});
				}
			},
			function(callback) {
				buildingCollection.find({}, "-_id", function(e, buildings) {
					client.emit('initGame', {
						username : connections[sid].user.login,
						resources : {
							gold : connections[sid].user.gold,
							citizen : connections[sid].user.citizen,
							happiness : connections[sid].user.happiness,
							goldPerTurn : connections[sid].user.goldPerTurn
						},
						buildings : connections[sid].user.buildings,
						buildingsList: buildings
					});
					client.broadcast.emit('alert', {
						msg: connections[sid].user.login + " vient de se connecter.",
						type: "success"
					});
					callback();
				});
			},
			function(callback) {
				console.log('timer 2');
				launchGame(connections);
				callback();
			}],
			// optional callback
			function() {
				//console.log(session);
			});
		});
		client.on('addBuilding', function(datas) {
			if ('undefined' == typeof connections[sid] || 'undefined' == typeof connections[sid].user) {
				return;
			}
			var nbTypeItem = 0;
			connections[sid].user.buildings.forEach(function(building) {
				if (building.type == 'cityHall')
					lvlCityHall = building.level;
				if (building.type == datas.type)
					nbTypeItem++;
			});
			buildingCollection.findOne({
				type : datas.type
			}, "-_id", function(e, building) {
				if (nbTypeItem >= Math.round(building.nbMax * Math.pow(1.1, lvlCityHall))) {
					client.emit('alert', {
						msg: "Vous êtes limité à " + Math.round(building.nbMax * Math.pow(1.1, lvlCityHall)) + " batiment de ce type. Améliorer votre hdv pour pouvoir construire plus de batiment du même type !",
						type: "warning"
					});
					return;
				}
				building.level = 1;
				var objectId = new ObjectID();
				building._id = objectId;
				var gold = connections[sid].user.gold - building.goldCost;
				var citizen = (connections[sid].user.citizen - building.citizenCost) + building.citizenMore;
				var happiness = (connections[sid].user.happiness - building.citizenMore) + building.happinessMore;

				if (gold < 0 || citizen < 0 || happiness < 0) {
					if (gold < 0)
						client.emit('alert', {
							msg: "Or insuffisant (" + building.goldCost + "/" + connections[sid].user.gold + "). Vas farmer sale noob !",
							type: "warning"
						});
					if (citizen < 0)
						client.emit('alert', {
							msg: "Nombre de citoyen insuffisant (" + building.citizenCost + "/" + connections[sid].user.citizen + "). Vas farmer sale noob !",
							type: "warning"
						});
					if (happiness < 0)
						client.emit('alert', {
							msg: "Bonheur insuffisant (" + building.citizenCost - building.happinessMore + "/" + connections[sid].user.happiness + "). Vas farmer sale noob !",
							type: "warning"
						});
					return;
				}
				connections[sid].user.buildings.push(building);
				connections[sid].user.goldPerTurn += building.goldPerTurn;
				userCollection.findAndModify({
					_id : connections[sid].user._id
				}, {
					$set : {
						"gold" : gold,
						"citizen" : citizen,
						"happiness" : happiness,
						"goldPerTurn" : connections[sid].user.goldPerTurn,
						"buildings" : connections[sid].user.buildings
					}
				}, function(err, user) {
					connections[sid].user.gold = gold;
					connections[sid].user.citizen = citizen;
					connections[sid].user.happiness = happiness;

					client.emit('addBuilding', {
						resources : {
							gold : gold,
							citizen : citizen,
							happiness : happiness,
							goldPerTurn : connections[sid].user.goldPerTurn
						},
						building : building
					});
				});
			});
		});
		client.on('upBuilding', function(datas) {
			if ('undefined' == typeof connections[sid] || 'undefined' == typeof connections[sid].user) {
				return;
			}
			var newCitizenCost = newGoldPerTurn = newGoldCost = 
			connections[sid].user.buildings.forEach(function(building) {
				if (building._id == datas._id) {

					var newLevel = building.level + 1;
					building.level = newLevel;
					building = upStatsBuilding(building);

					var gold = connections[sid].user.gold - building.goldCost;
					var citizen = (connections[sid].user.citizen - building.citizenCost) + building.citizenMore;
					var happiness = (connections[sid].user.happiness - building.citizenMore) + building.happinessMore;

					if (gold < 0 || citizen < 0 || happiness < 0) {
						if (gold < 0)
							client.emit('alert', {
								msg: "Or insuffisant (" + building.goldCost + "/" + connections[sid].user.gold + "). Vas farmer sale noob !",
								type: "warning"
							});
						if (citizen < 0)
							client.emit('alert', {
								msg: "Nombre de citoyen insuffisant (" + building.citizenCost + "/" + connections[sid].user.citizen + "). Vas farmer sale noob !",
								type: "warning"
							});
						if (happiness < 0)
							client.emit('alert', {
								msg: "Bonheur insuffisant (" + building.happinessCost + "/" + connections[sid].user.happiness + "). Vas farmer sale noob !",
								type: "warning"
							});
						return;
					}
					connections[sid].user.gold = gold;
					connections[sid].user.citizen = citizen;
					connections[sid].user.happiness = happiness;
					
					connections[sid].user.goldPerTurn -= building.goldPerTurn/1.2;
					connections[sid].user.goldPerTurn += building.goldPerTurn;

					//console.log(session.user.buildings);
					userCollection.findAndModify({
						_id : connections[sid].user._id
					}, {
						$set : {
							"gold" : gold,
							"citizen" : citizen,
							"happiness" : happiness,
							"goldPerTurn" : Math.round(connections[sid].user.goldPerTurn),
							"buildings" : connections[sid].user.buildings
						}
					}, function(err, user) {
						client.emit('upBuilding', {
							resources : {
								gold : gold,
								citizen : citizen,
								happiness : happiness,
								goldPerTurn : Math.round(connections[sid].user.goldPerTurn)
							},
							building : building
						});
					});
				}
			});
		});
		client.on('sendMsg', function(datas) {
			if ('undefined' == typeof connections[sid] || 'undefined' == typeof connections[sid].user) {
				return;
			}
			client.broadcast.emit('msg_receive', {
							msg : datas.msg,
							user : connections[sid].user.login
						});
		});
		client.on('resetGame', function() {
			if ('undefined' == typeof connections[sid] || 'undefined' == typeof connections[sid].user) {
				return;
			}
			buildingCollection.findOne({type : 'cityHall'}, "-_id", function(e, building) {
				building.level = 1;
				var objectId = new ObjectID();
				building._id = objectId;
				userCollection.findAndModify({
					_id : connections[sid].user._id
				}, {
					$set : {
						"gold" : 100000000,
						"citizen" : 10,
						"happiness" : 100,
						"goldPerTurn" : 10,
						"buildings" : [building]
					}
				}, function(err, user) {
					clearInterval(connections[sid].user.timer);
					connections[sid] = {};
					client.emit('reseted');
				});
			});
		});
		client.on('disconnect', function(data) {
			if ('undefined' == typeof connections[sid] || 'undefined' == typeof connections[sid].user)
				return;
			client.broadcast.emit('alert', {
				msg: connections[sid].user.login + " vient de se déconnecter.",
				type: "success"
			});
			console.log('disconect '+connections[sid].user.login);
			clearInterval(connections[sid].user.timer);
			connections[sid] = {};
			
		});
		function launchGame(){
			if (!connections[sid].user)
				return;
			var timer = turnTime;
			connections[sid].user.timer = setInterval(function() {
				if (is_int(timer / 1000)) {
					client.emit('timerDecrement', {'stats':{'nbPlayerOnline' : Object.keys(connections).length}});
					buildingCollection.find({}, "-_id", function(e, buildings) {
						buildings.forEach(function(building) {
							if (building.goldCost <= connections[sid].user.gold && building.citizenCost <= connections[sid].user.citizen && building.citizenMore <= connections[sid].user.happiness)
								client.emit('activeBuildButton', building.type);
							else
								client.emit('desactiveBuildButton', building.type);
						});
					});
				}
				if (timer == 0) {
					timer = turnTime;
					var gold = parseInt(connections[sid].user.gold) + parseInt(connections[sid].user.goldPerTurn);
					userCollection.findAndModify({
						_id : connections[sid].user._id
					}, {
						$set : {
							"gold" : gold,
							"citizen" : connections[sid].user.citizen,
							"happiness" : connections[sid].user.happiness,
							"goldPerTurn" : connections[sid].user.goldPerTurn
						}
					}, function(err, doc) {
						connections[sid].user.gold = gold;
						client.emit('editOr', {
							or : connections[sid].user.gold,
						});
					});
				}
				timer -= 100;
			}, 100);
		}
	});
	io.on('disconnect', function(client) {
		client.broadcast.emit('alert', {
			msg: user.login + " vient de se déconnecter.",
			type: "success"
		});
		console.log('disconect '+connections[sid].user.login);
		// Is this socket associated to user session ?
		var userConnections = connections[sid];
		clearInterval(connections[sid].user.timer);
		if (userConnections.length && userConnections[client.id]) {
			// Forget this socket
			userConnections.length--;
			delete userConnections[client.id];
		}
	});
};

var socket = io.connect();

socket.on('initGame', function(datas) {
	console.log('init game');
	$('#login').hide();
	$('#citoyenCount').html(formatNumber(datas.resources.citizen));
	$('#bonheurCount').html(formatNumber(datas.resources.happiness));
	$('#orCount').html(formatNumber(datas.resources.gold));
	$('#goldPerRound').html(formatNumber(datas.resources.goldPerTurn));
	$('#connectMessage').html('Vous êtes connecté en tant que : ' + datas.username);
	datas.buildingsList.forEach(function(item) {
		if(item.type != 'cityHall'){
			var html = '<div class="item panel panel-default text-center" data-type="' + item.type + '" data-id="' + item._id + '">';
			html += '<div class="item-infos" style="display:none;"><div style="padding: 0px;" class="col-xs-12 col-md-12"><p>Limite de construction : <span class="lvlMax">'+item.nbMax+'</span></p><div class="col-xs-6 col-md-6">';
			if (item.citizenMore)
				html += '<p>+' + formatNumber(item.citizenMore) + ' <img src="/images/citizen.png" width="24" title="citoyen" alt="citoyen" /></p>';
			if (item.happinessMore)
				html += '<p>+' + formatNumber(item.happinessMore) + ' <img src="/images/happiness.png" width="16" title="bonheur" alt="bonheur" /></p>';
			if (item.goldPerTurn)
				html += '<p>+' + formatNumber(item.goldPerTurn) + ' <img src="/images/gold.png" width="24" title="or" alt="or" /></p>';
			html += '</div>';
			html += '<div class="col-xs-6 col-md-6">';
			if (item.citizenCost)
				html += '<p>-' + formatNumber(item.citizenCost) + ' <img src="/images/citizen.png" width="24" title="citoyen" alt="citoyen" /></p>';
			if (item.happinessCost)
				html += '<p>-' + formatNumber(item.happinessCost) + ' <img src="/images/happiness.png" width="16" title="bonheur" alt="bonheur" /></p>';
			if (item.goldCost)
				html += '<p>-' + formatNumber(item.goldCost) + ' <img src="/images/gold.png" width="24" title="or" alt="or" /></p>';
			html += '</div></div>';
			html += '<div class="col-xs-12 col-md-12"><button data-type="' + item.type + '", style="margin:5px;" class="btn btn-default addBuilding"> Construire</button></div></div>';
			html += '<div class="col-xs-12 col-md-12"><img src="/images/' + item.type + '1.png" class="buildingImg" width="110px" title="' + item.type + '" alt="' + item.type + '" /></div>';
			html += '</div>';
			$('#buildingsList').append(html);
		}
	});
	//affichage
	$('#game,#userRessources,#chat_window_1,#showBuildingsList').show();
	$('#buildingsList').slideDown('slow');
	$('#showBuildingsList').animate({
		bottom : "118px",
	}, 600);
	datas.buildings.forEach(function(item) {
		if ($("p[data-id=" + item._id + "]").length == 0) {
			addBuilding(item);
		}
	});
});
socket.on('addBuilding', function(datas) {
	$('#citoyenCount').html(formatNumber(datas.resources.citizen));
	$('#bonheurCount').html(formatNumber(datas.resources.happiness));
	$('#orCount').html(formatNumber(datas.resources.gold));
	$('#goldPerRound').html(formatNumber(datas.resources.goldPerTurn));
	addBuilding(datas.building);
});
socket.on('upBuilding', function(datas) {
	$('#citoyenCount').html(formatNumber(datas.resources.citizen));
	$('#bonheurCount').html(formatNumber(datas.resources.happiness));
	$('#orCount').html(formatNumber(datas.resources.gold));
	$('#goldPerRound').html(formatNumber(datas.resources.goldPerTurn));

	var nb = checkNbSkin(datas.building.type, datas.building.level);
	
	$('div[data-id=' + datas.building._id + ']').find('.buildingImg').attr('src', 'images/' + datas.building.type + '' + nb + '.png');

	$('div[data-id=' + datas.building._id + ']').find('.level').html(datas.building.level);
	$('div[data-id=' + datas.building._id + ']').find('.goldPerTurn').html(formatNumber(Math.round(datas.building.goldPerTurn)));

	$('div[data-id=' + datas.building._id + ']').find('.citizenCost').html(formatNumber(datas.building.citizenCost));
	$('div[data-id=' + datas.building._id + ']').find('.happinessCost').html(formatNumber(datas.building.happinessCost));
	$('div[data-id=' + datas.building._id + ']').find('.goldCost').html(formatNumber(datas.building.goldCost));

	$('div[data-id=' + datas.building._id + ']').find('.citizenMore').html(formatNumber(Math.round(datas.building.citizenMore)));
	$('div[data-id=' + datas.building._id + ']').find('.happinessMore').html(formatNumber(Math.round(datas.building.happinessMore)));

	$('div[data-id=' + datas.building._id + ']').find('.goldPerTurnUp').html(formatNumber(Math.round(datas.building.goldPerTurn*1.3)));
	$('div[data-id=' + datas.building._id + ']').find('.citizenMoreUp').html(formatNumber(Math.round(datas.building.citizenMore*1.2)));
	$('div[data-id=' + datas.building._id + ']').find('.happinessMoreUp').html(formatNumber(Math.round(datas.building.happinessMore*1.2)));
});
socket.on('timerDecrement', function(datas) {
	$('#nbPlayerOnline').html('Nombre de joueur connecté : '+datas.stats.nbPlayerOnline);
	$('#timer').html(parseInt($('#timer').html()) - 1);
});
socket.on('editOr', function(datas) {
	$('#orCount').html(datas.or);
	$('#timer').html(30);
});
socket.on('activeBuildButton', function(data) {
	$('button[data-type=' + data + ']').removeAttr('disabled');
});
socket.on('desactiveBuildButton', function(data) {
	$('button[data-type=' + data + ']').attr('disabled', 'true');
});
socket.on('alert', function(data) {
	if (data.type == 'success') {
		var html = '<div class="row msg_container base_receive"><div class="col-md-2 col-xs-2 avatar">';
		html += '<img src="http://www.bitrebels.com/wp-content/uploads/2011/02/Original-Facebook-Geek-Profile-Avatar-1.jpg" class=" img-responsive ">';
		html += '</div><div class="col-md-10 col-xs-10"><div class="messages msg_receive">';
		html += '<p>' + data.msg + '</p><time datetime="' + data.time + '">System • 51 min</time>';
		html += '</div></div></div>';
		$('.msg_container_base').append(html);
		$(".msg_container_base").animate({
			scrollTop : $(".msg_container_base").prop('scrollHeight')
		});
	}
	$('#alertContent').parent().removeClass('alert-warning', 'alert-success');
	$('#alertContent').parent().addClass('alert-' + data.type);
	$('#alertContent').html(data.msg);
	$('.alert').slideDown('slow');
	setTimeout(function() {
		$('.alert').slideUp('slow');
	}, 3000);
});
socket.on('msg_receive', function(data) {
	document.title = data.user + ' Vous à envoyé un message.';
	var html = '<div class="row msg_container base_receive"><div class="col-md-2 col-xs-2 avatar">';
	html += '<img src="http://www.bitrebels.com/wp-content/uploads/2011/02/Original-Facebook-Geek-Profile-Avatar-1.jpg" class=" img-responsive ">';
	html += '</div><div class="col-md-10 col-xs-10"><div class="messages msg_receive">';
	html += '<p>' + data.msg + '</p><time datetime="' +getCurrentDate()+ '">' + data.user + ' • ' +getCurrentDate('text')+ '</time>';
	html += '</div></div></div>';
	$('.msg_container_base').append(html);
	$(".msg_container_base").animate({
		scrollTop : $(".msg_container_base").prop('scrollHeight')
	});
});
socket.on('reseted', function() {
	socket.emit('disconnect');
	location.reload();
});
socket.on('disconnected', function() {
	location.reload();
});
window.addEventListener('beforeunload', function(e) {
	(e || window.event).returnValue = "test";
  	return "test";
});
window.addEventListener('unload', function(event) {
	socket.emit('disconnect');
});

function checkNbSkin(type, level){
	var nb = level;
	if (nb >= 7)
		nb = 7;
	if (nb >= 11)
		nb = 11;
	if (nb >= 17)
		nb = 17;
	if (nb >= 21)
		nb = 21;
	if (type == 'idol' && nb > 3)
		nb = 3;
	if (type == 'tavern' && nb > 6)
		nb = 6;
	if (type == 'house' && nb > 7)
		nb = 7;
	if (type == 'sawmill' && nb > 9)
		nb = 9;
	if (type == 'barrack') {
		if (nb >= 1)
			nb = 1;
		if (nb >= 3)
			nb = 3;
		if (nb >= 6)
			nb = 6;
		if (nb >= 8)
			nb = 8;
		if (nb >= 13)
			nb = 13;
		if (nb >= 16)
			nb = 16;
		if (nb >= 21)
			nb = 21;
	}
	return nb;
}

function addBuilding(item) {
	var citizenMoreUp = happinessMoreUp = goldPerTurnUp = 0;
	var nb = checkNbSkin(item.type, item.level);

	citizenMoreUp = Math.round(item.citizenMore*1.2);
	happinessMoreUp = Math.round(item.happinessMore*1.2);
	goldPerTurnUp =  Math.round(item.goldPerTurn*1.3);
		
	var html = '<div style="margin-right:5px;padding: 0px;" class="col-xs-5 col-md-3 col-lg-2 panel panel-default" data-type="' + item.type + '" data-id="' + item._id + '">';
	html += '<div class="col-md-12 col-lg-7" style="text-align:center;">';
	html += '<img src="/images/' + item.type + '' + nb + '.png" class="buildingImg img-responsive" title="' + item.type + '" alt="' + item.type + '" /><p style="text-align:center">Level <span class="level">' + item.level + '</span></p>';
	//html += '<button data-id="'+item._id+'" class="remove"> X </button>';
	html += '</div>';
	html += '<div class="col-md-12 col-lg-5" style="position:relative;top:15%;text-align:center;">';
	if (item.citizenMore)
		html += '<p><span style="vertical-align: middle;" class="citizenMore">' + formatNumber(item.citizenMore) + '</span> <img src="/images/citizen.png" width="24" title="citoyen" alt="citoyen" /></p>';
	if (item.happinessMore)
		html += '<p><span style="vertical-align: middle;" class="happinessMore">' + formatNumber(item.happinessMore) + '</span> <img src="/images/happiness.png" width="16" title="bonheur" alt="bonheur" /></p>';
	if (item.goldPerTurn)
		html += '<p><span style="vertical-align: middle;" class="goldPerTurn">' + formatNumber(item.goldPerTurn) + '</span> <img src="/images/gold.png" width="24" title="or" alt="or" /></p>';
	html += '<div class="container_infos" id="' + item._id + '" style="display:none;"><p>Coût : </p>';
	if (item.goldCost)
		html += '<p><span class="goldCost">' + formatNumber(Math.round(item.goldCost*1.3)) + '</span> <img src="/images/gold.png" width="16" title="or" alt="or" /></p>';
	if (item.citizenCost)
		html += '<p><span class="citizenCost">' + formatNumber(Math.round(item.citizenCost*1.2)) + '</span> <img src="/images/citizen.png" width="16" title="citoyen" alt="citoyen" /></p>';
	if (item.happinessCost)
		html += '<p><span class="happinessCost">' + formatNumber(Math.round(item.happinessCost*1.2)) + '</span> <img src="/images/happiness.png" width="12" title="bonheur" alt="bonheur" /></p>';
	html += '<p>Bonus : </p>';
	if (item.citizenMore)
		html += '<p><span class="citizenMoreUp">'+formatNumber(citizenMoreUp)+'</span> <img src="/images/citizen.png" width="16" title="citoyen" alt="citoyen" /></p>';
	if (item.happinessMore)
		html += '<p> <span class="happinessMoreUp">'+formatNumber(happinessMoreUp)+'</span> <img src="/images/happiness.png" width="12" title="bonheur" alt="bonheur" /></p>';
	if (item.goldPerTurn)
		html += '<p> <span class="goldPerTurnUp">'+formatNumber(goldPerTurnUp)+'</span> <img src="/images/gold.png" width="16" title="or" alt="or" /></p>';
	html += '</div><button class="up infos" data-id="' + item._id + '">Up</button>';
	html += '</div></div></div>';

	$('#buildingList').append(html);
}

function getCurrentDate(type){
	var date = new Date;
	var jour = date.getDate();
	var mois = date.getMonth() + 1;
	if (mois < 10)
		mois = "0" + mois;
	var annee = date.getFullYear();
	h = date.getHours();
	if (h < 10)
		h = "0" + h;
	m = date.getMinutes();
	if (m < 10)
		m = "0" + m;
	s = date.getSeconds();
	if (s < 10)
		s = "0" + s;
	if(type == 'text')
		return 'Le ' + jour + '-' + mois + '-' + annee + ' à ' + h + ':' + m + ':' + s;
	else
		return jour + '-' + mois + '-' + annee + ' ' + h + ':' + m + ':' + s;
}

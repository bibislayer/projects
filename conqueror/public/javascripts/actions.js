$(document).ready(function() {
	$('body').on('click', 'button#sendLogin', function(event) {
		event.preventDefault();
		socket.emit('login', {
			login : $('input[name=login]').val(),
			password : $('input[name=password]').val()
		});
	});

	$('body').on('mouseenter', '.panel-body, #buildingsList', function() {
		$(this).find('.infos').popover({
			html : true,
			trigger : 'hover',
			content : function() {
				return $(this).prev().html();
			}
		});
	}).on('click', 'button.addBuilding', function() {
		socket.emit('addBuilding', {
			type : $(this).attr('data-type')
		});
	}).on('click', 'button.up', function() {
		socket.emit('upBuilding', {
			_id : $(this).attr('data-id')
		});
	}).on('focus', '#btn-input', function(event) {
		document.title = 'Conqueror test nodeJs.';
		return true;
	}).on('keydown', '#btn-input', function(event) {
		if (event.which == 13 || event.keyCode == 13) {
			$('#btn-chat').trigger('click');
			return true;
		}
		return true;
	}).on('click', '#btn-chat', function() {
		if ($('#btn-input').val().length > 0) {
			socket.emit('sendMsg', {
				msg : $('#btn-input').val(),
			});
			var html = '<div class="row msg_container base_sent"><div class="col-md-10 col-xs-10"><div class="messages msg_sent">';
			html += '<p>' + $('#btn-input').val() + '</p><time datetime="' + getCurrentDate() + '">Moi • ' + getCurrentDate('text') + '</time></div></div>';
			html += '<div class="col-md-2 col-xs-2 avatar">';
			html += '<img src="http://www.bitrebels.com/wp-content/uploads/2011/02/Original-Facebook-Geek-Profile-Avatar-1.jpg" class=" img-responsive ">';
			html += '</div></div>';
			$('.msg_container_base').append(html);
			$('#btn-input').val('');
			$(".msg_container_base").animate({
				scrollTop : $(".msg_container_base").prop('scrollHeight')
			});
		}
	}).on('click', '#showBuildingsList', function() {
		if($('#buildingsList').is(":visible")){
			$('#buildingsList').slideUp('slow');
			$('#showBuildingsList').animate({
				bottom : "0px",
			}, 600);
		}else{
			$('#buildingsList').slideDown('slow');
			$('#showBuildingsList').animate({
				bottom : "118px",
			}, 600);
		}
	})
	.on('click', '#resetGame', function() {
		socket.emit('resetGame');
	})
	.on('mouseenter', '#buildingsList .item', function() {
		$('.item-infos').hide();
		$(this).find('.item-infos').show();
		$(this).css('box-shadow', "0px 0px 5px 2px rgb(76,158,217)");
	})
	.on('mouseleave', '#buildingsList .item', function() {
		$('.item-infos').hide();
		$(this).css('box-shadow', "none");
	})
	.on('click', ':not(.container)', function() {
		if('undefined' != typeof $(this).attr('id') && 
			$(this).attr('id') != 'showBuildingsList' && 
				$(this).attr('id') != 'buildingsList'){
			$('#buildingsList').slideUp('slow');
			$('#showBuildingsList').animate({
				bottom : "0px",
			}, 600);
		}
	});
});
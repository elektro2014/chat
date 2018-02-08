$(document).ready(function(){
	//Si el usuario quiere dejar la sesión
	$("#exit").click(function(){
		var exit = confirm("¿Estas seguro que te quieres pirar de aqui?");
		if(exit==true){window.location = 'index.php?logout=true';}
	});

	$("#submitmsg").click(function(){
		sendMessage();
	});

	function sendMessage(){
		var clientmsg = $('.emojionearea-editor').text();

		if(clientmsg.length == 0)
			return;

		$.post("post.php", {text: clientmsg});

		$('.emojionearea-editor').text('');
	}

	function loadLog(){
		var oldscrollHeight = $("#chatbox").attr("scrollHeight"); //La altura del scroll antes de la petición
		oldscrollHeight -= 20;
		
		$.ajax({
			url: "log.html",
			cache: false,
			success: function(html){
				$("#chatbox").html(html); //Inserta el log de char en el div #chatbox
				//Auto-scroll
				var newscrollHeight = $("#chatbox").attr("scrollHeight");
				newscrollHeight -= 20; //La altura del scroll después del pedido
				if(newscrollHeight > oldscrollHeight){
					$("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll hacia el fondo del div
				}
			},
		});
	}

	$("#usermsg").emojioneArea({
		events: {
			keyup: function(editor, event){
				if(event.which == 13)
					sendMessage();
			}
		}
	});

	setInterval (loadLog, 1000);
});
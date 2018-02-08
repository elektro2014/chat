<?php
	session_start();

	function loginForm(){
		echo '<div id="loginform">
			<form action="index.php" method="post">
				<p>¿Quien ere?:</p>
				<label for="username">Nombre:</label>
				<input type="text" name="username" id="username" />
				<input type="submit" name="enter" id="enter" value="Palante" />
			</form>
		</div>';
	}

	if(isset($_GET['logout'])){
		//Mensaje simple de salida
		$fp = fopen("log.html", 'a');
		fwrite($fp, "<div class='msgln'><i>El/la señor/a ". $_SESSION['username'] ." sa morio.</i><br></div>");
		fclose($fp);

		session_destroy();
		header("Location: /chat"); //Redirige al usuario
	}

	if(isset($_POST['enter'])){
		if($_POST['username'] != ""){
			$_SESSION['username'] = stripslashes(htmlspecialchars($_POST['username']));

			$fp = fopen("log.html", 'a');
			fwrite($fp, "<div class='msgln'><i>El/la señor/a ". $_SESSION['username'] ." sa unio.</i><br></div>");
			fclose($fp);
		} else{
			echo '<span class="error">Introduce tu nombre, no se quien ere</span>';
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<title>Chat – Better than Slack</title>
			<link type="text/css" rel="stylesheet" href="style.css" />
			<link rel="stylesheet" href="lib/emojionearea.min.css">
			<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
			<script type="text/javascript" src="lib/emojionearea.min.js"></script>
		</head>

	<?php
		if(!isset($_SESSION['username'])){
			loginForm();
		} else{
	?>
			<body>
				<div id="wrapper">
					<div id="menu">
						<p class="welcome">Hola que ase, <b><?php echo $_SESSION['username']; ?></b></p>
						<p class="logout"><a id="exit" href="#">Salir</a></p>

						<div style="clear:both"></div>
					</div>

					<div id="chatbox">
						<?php
							if(file_exists("log.html") && filesize("log.html") > 0){
								$handle = fopen("log.html", "r");
								$contents = fread($handle, filesize("log.html"));
								fclose($handle);

								echo $contents;
							}
						?>
					</div>

					<form name="message" action="">
						<input type="text" name="usermsg" id="usermsg" size="63" />
						<input name="submitmsg" type="submit"  id="submitmsg" value="Mandar" />
					</form>
				</div>

				<script type="text/javascript">
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
				</script>
			</body>
	</html>
	<?php
		}
	?>
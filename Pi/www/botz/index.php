<? include "general.php"; ?>
<!DOCTYPE HTML>
<html>
 <head>
  <title>Botz</title>
  <meta name="Generator" content="Goldcoast Techspace">
  <meta name="Author" content="Goldcoast Techspace">
  <meta name="viewport" content="user-scalable=1.0,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="format-detection" content="telephone=no">
  <style>
	.body { 
		margin:0px;
		padding:0px;
	}
	input { height:130%;}
  </style>
 </head>
 <script type="text/javascript">
   function blockMove() {
      event.preventDefault() ;
}
</script>

<body ontouchmove="blockMove()">
 <iframe id="cJax" name="cJax" border=0 frameborder=0 width=0 height=0></iframe>
	 <script>
		var ServerComm = document.getElementById('cJax');
	 </script>
  <table width="100%" height="100%">
  <tr>
	<td>
		<center>
		<img src="images/botz.png"><br><br>
		<?
		if($PLAYER_ID==""){
			//NOT A PLAYER
			?>
			<strong>Crack the Code</strong><br>
			<table border=1>
				<tr>
					<td align="center" onclick="doPress(1);" style="width:90px;height:90px;cursor:pointer;">1</td>
					<td align="center" onclick="doPress(2);" style="width:90px;height:90px;cursor:pointer;">2</td>
					<td align="center" onclick="doPress(3);" style="width:90px;height:90px;cursor:pointer;">3</td>
				</tr>
			</table>
			<br>
			<table>
				<tr>
					<td><div id="c1">?</div></td>
					<td><div id="c2">?</div></td>
					<td><div id="c3">?</div></td>
				</tr>
			</table>
			<script>
				var theCode = 0;
				var code_1 = 0;
				var code_2 = 0;
				var code_3 = 0;
				var codeCount = 0;

				function doPress(inNum){
					if(codeCount==0){code_1=inNum;document.getElementById('c1').innerHTML=inNum;}
					if(codeCount==1){code_2=inNum;document.getElementById('c2').innerHTML=inNum;}
					if(codeCount==2){code_3=inNum;document.getElementById('c3').innerHTML=inNum;doGuess();}
					codeCount++;
				}

				function doGuess(){
					ServerComm.src = "botz.php?guess="+c1.innerHTML+c2.innerHTML+c3.innerHTML;
				}
			</script>
			<?
			if($_GET['msg']!=''){
				?><br><br>
					<div id="msg" style="color:red;font-size:18pt;">FAIL</div>
					<script>
						setTimeout("doClear();",2000);
						function doClear(){
							document.getElementById('msg').innerHTML="";
						}
					</script>
				<?
			}
		 }else{
		?>
			You are Player<br>
			<h2><? echo $PLAYER_ID; ?></h2><br>
			<br>
			<table>
			<tr>
				<td>Command:</td>
				<td width=150><div id="commandDisplay"></div></td>
			</tr>
			</table>
			<? if(1==2){ ?>
				<table>
					<tr>
						<td width="60"></td>
						<td width="60" height="50" align="center" onclick="doCMDAjax('F');" style="cursor:pointer;">Forward</td>
						<td width="60"></td>
					</tr>
					<tr>
						<td align="center" height="50" onclick="doCMDAjax('L');" style="cursor:pointer;">LEFT</td>
						<td align="center" height="50" onclick="doCMDAjax('S');" style="cursor:pointer;">STOP</td>
						<td align="center" height="50" onclick="doCMDAjax('R');" style="cursor:pointer;">RIGHT</td>
					</tr>
					<tr>
						<td width="60"></td>
						<td width="60" height="50" align="center" onclick="doCMDAjax('B');" style="cursor:pointer;">Back</td>
						<td width="60"></td>
					</tr>
				</table>
			<? }else{ ?>
				<table>
					<tr>
						<td width="60"></td>
						<td width="60" height="50" ><input type="button" value="FORWARD" onclick="doCMDAjax('F');"></td>
						<td width="60"></td>
					</tr>
					<tr>
						<td align="center" height="50"><input type="button" value="LEFT" onclick="doCMDAjax('L');"></td>
						<td align="center" height="50"><input type="button" value="STOP" onclick="doCMDAjax('S');"></td>
						<td align="center" height="50"><input type="button" value="RIGHT" onclick="doCMDAjax('R');"></td>
					</tr>
					<tr>
						<td width="60"></td>
						<td width="60" height="50" ><input type="button" value="BACK" onclick="doCMDAjax('B');"></td>
						<td width="60"></td>
					</tr>
				</table>
			<? } ?>
			<br>
			<br>
			<a onclick="doLeave();"  target="cJax" style="border:1px solid black; width:60px; height:40px;">Leave game</a>
			<script>
				var LastCommand = "";
				var useAudio = false;
				var snd_start = document.createElement('audio');
				var snd_end = document.createElement('audio');
				var snd_back = document.createElement('audio');
				var snd_forward = document.createElement('audio');
				var snd_left = document.createElement('audio');
				var snd_right = document.createElement('audio');
				var snd_stop = document.createElement('audio');
				
				if(useAudio){
					snd_start.setAttribute('src', 'sounds/gamebegin.ogg');
					snd_end.setAttribute('src', 'sounds/gameover.ogg');
					snd_back.setAttribute('src', 'sounds/back.ogg');
					snd_forward.setAttribute('src', 'sounds/forward.ogg');
					snd_left.setAttribute('src', 'sounds/left.ogg');
					snd_right.setAttribute('src', 'sounds/right.ogg');
					snd_stop.setAttribute('src', 'sounds/stop.ogg');
				}

				var xmlhttp;	
				if (window.XMLHttpRequest){
					// code for IE7+, Firefox, Chrome, Opera, Safari
					xmlhttp=new XMLHttpRequest();
				}else{
					// code for IE6, IE5
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}


				function doLeave(){
					snd_end.play();
					setTimeout("actuallyLeave();",500);
				}
				function actuallyLeave(){
					ServerComm.src = "botz.php?command=leave";
				}

				document.onkeypress =  zx;
				function zx(e){
					var charCode = (typeof e.which == "number") ? e.which : e.keyCode
					if(charCode==119){doCMDAjax('F');}
					if(charCode==120){doCMDAjax('B');}
					if(charCode==97){doCMDAjax('L');}
					if(charCode==100){doCMDAjax('R');}
					if(charCode==115){doCMDAjax('S');}
				}


				function doCMDAjax(str){
					if(LastCommand != str){
						xmlhttp.onreadystatechange=function(){}
						xmlhttp.open("GET","botz.php?command="+str,true);
						xmlhttp.send();

						if(str=="F"){
							theCommand = "forward";
							document.getElementById('commandDisplay').innerHTML=theCommand;
							if(useAudio){snd_forward.play();}
						} 
						if(str=="B"){
							theCommand = "back";
							document.getElementById('commandDisplay').innerHTML=theCommand;
							if(useAudio){snd_back.play();}
						} 
						if(str=="L"){
							theCommand = "left";
							document.getElementById('commandDisplay').innerHTML=theCommand;
							if(useAudio){snd_left.play();}
						} 
						if(str=="R"){
							theCommand = "right";
							document.getElementById('commandDisplay').innerHTML=theCommand;
							if(useAudio){snd_right.play();}
						} 
						if(str=="S"){
							theCommand = "stop";
							document.getElementById('commandDisplay').innerHTML=theCommand;
							if(useAudio){snd_stop.play();}
						} 
					}
					LastCommand = str;
				}

				snd_start.play();
			</script>
		<?
		 }
		?>
		</center>
	</td>
  </tr>
  </table>
	 
 </body>
</html>
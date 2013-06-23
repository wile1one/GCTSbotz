<? include "general.php"; ?>
<!DOCTYPE HTML>
<html>
 <head>
  <title>Botz</title>
  <meta name="Generator" content="Goldcoast Techspace">
  <meta name="Author" content="Goldcoast Techspace">
  <style>
	.body { 
		margin:0px;
		padding:0px;
	}
  </style>
 </head>
 <body>
 <iframe id="cJax" name="cJax" border=0 frameborder=0 width=0 height=0></iframe>
	 <script>
		var ServerComm = document.getElementById('cJax');
	 </script>
  <table width="100%" height="100%" border=0>
	  <tr>
		<td>
			<center>
				<img src="images/botz.png"><br>
				ADMIN<br>
			</center>
		</td>
	  </tr>
	  <tr>
		<td>
			<br><br>
			<center>
			<table>
			<tr>
				<td valign=top width=200>
					<input type="button" value=" START game " onclick="doStart();"><br>
					<br>
					<input type="button" value=" END GAME" onclick="doEnd();"><br>
					<br>
				</td>
				<td valign=top>
					<table>
						<tr>
							<td>Game ON</td>
							<td><div id="game_on">-</div></td>
						</tr>
						<tr>
							<td>Time</td>
							<td><div id="game_time">-</div></td>
						</tr>
						<tr>
							<td>Plays</td>
							<td><div id="game_plays">-</div></td>
						</tr>
					</table>
					<br>
					<table>
					<tr>
						<td>Player</td>
						<td>Code</td>
						<td>Enabled</td>
						<td>Score</td>
						<td></td>
					</tr>
					<?
					for($j=1; $j<11; $j++){
					?>
					<tr>
						<td><? echo $j; ?></td>
						<td><div id="p<? echo $j; ?>_code">-</div></td>
						<td><div id="p<? echo $j; ?>_enabled">-</div></td>
						<td><div id="p<? echo $j; ?>_score">-</div></td>
						<td><input type="button" value=" End Game " onclick="doPlayerEnd(<? echo $j; ?>);"></td>
					</tr>
					<? } ?>
					</table>
					<br><br>
					<input type="button" value=" Refresh " onclick="updateStats();">
				</td>
			</tr>
			</table>
			</center>
		</td>
	  </tr>
  </table>
  <script>
	function updateStats(){
		clear_screen();
		ServerComm.src='server.php?command=update';
	}

	function doStart(){
		ServerComm.src='server.php?command=start';
	}

	function doEnd(){
		ServerComm.src='server.php?command=end';
	}


	function setEnabled(inID, invalue){
		document.getElementById('p'+inID+'_enabled').innerHTML = invalue;
	}

	function setCode(inID, invalue){
		document.getElementById('p'+inID+'_code').innerHTML = invalue;
	}

	function setScore(inID, invalue){
		document.getElementById('p'+inID+'_score').innerHTML = invalue;
	}

	function setGameStat(inGame, inTime, inPlays){
		document.getElementById('game_on').innerHTML = inGame;
		document.getElementById('game_time').innerHTML = inTime;
		document.getElementById('game_plays').innerHTML = inPlays;
	}

	function clear_screen(){
		document.getElementById('game_on').innerHTML = '-';
		document.getElementById('game_time').innerHTML = '-';
		document.getElementById('game_plays').innerHTML = '-';
		for( i=1; i<10; i++){
			document.getElementById('p'+i+'_enabled').innerHTML = '-';
			document.getElementById('p'+i+'_code').innerHTML = '-';
			document.getElementById('p'+i+'_score').innerHTML = '-';
		}
	}
  </script>
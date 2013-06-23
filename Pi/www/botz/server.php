<?
include "general.php";




if($_GET['command']=='start'){
	game_start();
	$_GET['command']='update';
}

if($_GET['command']=='end'){
	game_end();
	$_GET['command']='update';
}


if($_GET['command']=='update'){
	$theGame = gameStats();
	while($row = mysql_fetch_assoc($theGame)){
		?>
		<script>
			parent.setEnabled(<? echo $row['p_player_id']; ?>,<? echo $row['p_enabled']; ?>);
			parent.setCode(<? echo $row['p_player_id']; ?>,<? echo $row['p_code']; ?>);
			parent.setScore(<? echo $row['p_player_id']; ?>,<? echo $row['p_score']; ?>);
		</script>
		<?
	}
	?>
	<script>
		parent.setGameStat(<? echo $GAME_ON; ?>,<? echo $GAME_TIME; ?>,<? echo $GAME_PLAYS; ?>);
	</script>
	<?
}


?>
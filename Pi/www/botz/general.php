<?
session_start();

//Databse connection
$conn = mysql_connect("127.0.0.1", "root", "X5c6v7b8") or die ('Error connecting to mysql');
mysql_select_db("Botz");

//Set default variables
$GAME_ON = 0;
$GAME_TIME = 0;
$GAME_PLAYS = 0;
$PLAYERS[10]="";
$PLAYER_ID = $_SESSION['PLAYER_ID'];

if(($_SESSION['PLAYER_ID']!='')&&(isGameOn()==0)){
	?><script>document.location='logout.php';</script><?
	die();
}

// ************************************************************************************************
//Game functions
// ************************************************************************************************

function gameStats(){
	$SQL = "SELECT * FROM game where id=1";
	$result = mysql_query($SQL) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	if($row){
		$GAME_ON = $row['game_on'];
		$GAME_TIME = $row['game_started'];
		$GAME_PLAYS = $row['game_plays'];
	}else{
		$GAME_ON = 0;
		$GAME_TIME = "";
		$GAME_PLAYS = 0;
	}
	$SQL = "SELECT * FROM players";
	$result = mysql_query($SQL) or die(mysql_error());
	return $result;
}

function game_start(){
	$SQL = "update game set game_on=1, game_started=".time().", game_plays=game_plays+1 where id=1";
	$result = mysql_query($SQL) or die(mysql_error());
	return $result;
}

function game_end(){
	$SQL = "update game set game_on=0 where id=1";
	$result = mysql_query($SQL) or die(mysql_error());

	$SQL = "delete from players";
	$result = mysql_query($SQL) or die(mysql_error());

	return $result;
}

function leave_game($player_id){
	$SQL = "delete from players where p_player_id=".$player_id;
	$result = mysql_query($SQL) or die(mysql_error());
	return $result;
}

function crack_code($in_code){ // returns FALSE for fail and Player ID for win
	$SQL = "insert into players (p_code, p_session_id) values (".mysql_real_escape_string(sanity($in_code)).",'".session_id()."')";
	$result = mysql_query($SQL);
	$last_id = mysql_insert_id();
	echo $SQL."<br>";
	echo "last_id=[".$last_id."]<br>";
	if($last_id!=''){
		$SQL = "select p_id from players";
		$result = mysql_query($SQL) or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		$rowCount = mysql_num_rows($result);
		
		$PLAYER_ID = $rowCount;
		$_SESSION['PLAYER_ID'] = $PLAYER_ID;

		$SQL = "update players set p_player_id=".$PLAYER_ID." where p_id=".$last_id;
		$result = mysql_query($SQL) or die(mysql_error());
	}
	return $last_id;
}

function isGameOn(){
	$SQL = "select game_on from game";
	$result = mysql_query($SQL) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	return $row['game_on'];
}

// ************************************************************************************************
// Built in functions
// ************************************************************************************************

function robot_command($PlayerID, $inCommand){
	$SQL = "update control set direction='".$inCommand."' where id=".$PlayerID;
	$result = mysql_query($SQL) or die(mysql_error());
	return $result;
}

function sanity($inData){ //Sanitize string for DB query - nasty version ;)
	$inData = str_replace("'","",$inData);
	$inData = str_replace("/","",$inData);
	$inData = str_replace("\\","",$inData);
	$inData = str_replace("(","",$inData);
	$inData = str_replace(")","",$inData);
	return $inData;
}
?>
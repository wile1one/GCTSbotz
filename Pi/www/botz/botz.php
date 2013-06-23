<?
include "general.php";

if($_GET['guess']!=''){
	$meee = crack_code($_GET['guess']);
	if($meee==null){
		?><script>parent.location='index.php?msg=fail';</script><?
	}else{
		?><script>parent.location='index.php';</script><?
	}
}

if($_GET['command']!=''){
	switch ($_GET['command']) {
		case "leave":
			robot_command($PLAYER_ID,"S");
			leave_game($PLAYER_ID);
			$PLAYER_ID = "";
			$_SESSION['PLAYER_ID']="";
			?><script>parent.location='index.php';</script><?
			break;
		default:
			echo robot_command($PLAYER_ID,$_GET['command']);
			break;
	}
}
?>
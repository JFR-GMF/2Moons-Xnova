<?php

define('INSIDE'  , true);
define('INSTALL' , false);

$rocketnova_root_path = './';
include($rocketnova_root_path . 'extension.inc');
include($rocketnova_root_path . 'common.' . $phpEx);

includeLang('alliance');

global $user;

$parse['ally_name'] = $user['ally_name'];
$parse['ally_id'] = $user['ally_id'];

$ally_id = doquery("SELECT `ally_owner` FROM {{table}} WHERE `id` = '". $user['ally_id'] ."'", "alliance");  
$id = mysql_fetch_array($ally_id);

####################
# Anzeige der BNDs #
####################

$query = doquery("SELECT `pakt_owner_id_1`,`pakt_owner_id_2`,`pakt_name_1`,`pakt_name_2` FROM {{table}} WHERE (`pakt_owner_id_1` = '". $user['ally_id'] ."' OR `pakt_owner_id_2` = '". $user['ally_id'] ."') AND `pakt` = '1' AND `akzeptiert` = '0'", "diplo");

$i = 0;
while ($u = mysql_fetch_array($query)) {
	if($user['ally_id'] == $u['pakt_owner_id_1']){
		$parse['bnd'] = "<tr>".
		"<td class=b colspan=3><a href=alliance.php?mode=ainfo&a=" . $u['pakt_owner_id_2'] . " ><center><b><font color=lime>" . $u['pakt_name_2'] . "</font></b></center></a></td>".
		"<td class=b colspan=3><center><a href=alliance_diplo.php?delete=". $u['pakt_owner_id_2'] .">".$lang['ALLIANCE_DELETE']."</a></center></td></tr>";
	}else{
		$parse['bnd'] = "<tr>".
		"<td class=b colspan=3><a href=alliance.php?mode=ainfo&a=" . $u['pakt_owner_id_1'] . " ><center><b><font color=lime>" . $u['pakt_name_1'] . "</font></b></center></a></td>".
		"<td class=b colspan=3><center><a href=alliance_diplo.php?delete=". $u['pakt_owner_id_1'] .">".$lang['ALLIANCE_DELETE']."</a></center></td></tr>";
	}
	$i++;
}

####################
# Anzeige der NAPs #
####################
		
$query2 = doquery("SELECT `pakt_owner_id_1`,`pakt_owner_id_2`,`pakt_name_1`,`pakt_name_2` FROM {{table}} WHERE ((`pakt_owner_id_1` = '". $user['ally_id'] ."' OR `pakt_owner_id_2` = '". $user['ally_id'] ."') AND `pakt` = '2'  AND `akzeptiert` = '0')", "diplo");

$i = 0;
while ($u = mysql_fetch_array($query2)) {
	if($user['ally_id'] == $u['pakt_owner_id_1']){
		$parse['nap'] .= "<tr>".
		"<td class=b colspan=3><a href=alliance.php?mode=ainfo&a=" . $u['pakt_owner_id_2'] . " ><center><b><font color=skyblue>" . $u['pakt_name_2'] . "</font></b></center></a></td>".
		"<td class=b colspan=3><center><a href=alliance_diplo.php?delete=". $u['pakt_owner_id_2'] .">".$lang['ALLIANCE_DELETE']."</a></center></td></tr>";
	}else{
		$parse['nap'] .= "<tr>".
		"<td class=b colspan=3><a href=alliance.php?mode=ainfo&a=" . $u['pakt_owner_id_1'] . " ><center><b><font color=skyblue>" . $u['pakt_name_1'] . "</font></b></center></a></td>".
		"<td class=b colspan=3><center><a href=alliance_diplo.php?delete=". $u['pakt_owner_id_1'] .">".$lang['ALLIANCE_DELETE']."</a></center></td></tr>";
	}
	$i++;
}

#############################
# Anzeige der KRIEGEs Pakte #
#############################
		
$query3 = doquery("SELECT `pakt_owner_id_1`,`pakt_owner_id_2`,`pakt_name_1`,`pakt_name_2` FROM {{table}} WHERE (`pakt_owner_id_1` = '". $user['ally_id'] ."' OR `pakt_owner_id_2` = '". $user['ally_id'] ."') AND `pakt` = '3'  AND `akzeptiert` = '0'", "diplo");

$i = 0;
while ($u = mysql_fetch_array($query3)) {
	if($user['ally_id'] == $u['pakt_owner_id_1']){
		$parse['war'] .= "<tr>".
		"<td class=b colspan=3><a href=alliance.php?mode=ainfo&a=" . $u['pakt_owner_id_2'] . " ><center><b><font color=red>" . $u['pakt_name_2'] . "</font></b></center></a></td>".
		"<td class=b colspan=3><center><a href=alliance_diplo.php?delete=". $u['pakt_owner_id_2'] .">".$lang['ALLIANCE_DELETE']."</a></center></td></tr>";
	}else{
		$parse['war'] .= "<tr>".
		"<td class=b colspan=3><a href=alliance.php?mode=ainfo&a=" . $u['pakt_owner_id_1'] . " ><center><b><font color=red>" . $u['pakt_name_1'] . "</font></b></center></a></td>".
		"<td class=b colspan=3><center><a href=alliance_diplo.php?delete=". $u['pakt_owner_id_1'] .">".$lang['ALLIANCE_DELETE']."</a></center></td></tr>";
	}
	$i++;
}

######################################
# Abfrage ob Spieler Leader der Ally #
######################################

if($user['id'] == $id['ally_owner']) {		

	################################
	# Erstellen einer Pakt Anfrage #
	################################

	if(isset($_GET['add'])) {
		if(!isset($_POST['search'])){
		
			###########################
			# Suche des Pakt Partners #
			###########################
			
			$parse['zurueck'] = "<a href=alliance_diplo.php>".$lang['ALLIANCE_BACK']."</a>";	
			$page = parsetemplate(gettemplate('alliance_diplo_search'), $parse);
			display($page);
			
		}elseif($_POST['search'] == "step2"){
		
			#####################################
			# Weiter Einstellungen für den Pakt #
			#####################################
			
			$ally = doquery("SELECT `ally_name`,`id` FROM {{table}} WHERE ally_name LIKE '%" . $_POST['ally_search'] . "%' LIMIT 30", "alliance");  
 			while ($c = mysql_fetch_array($ally)) {
				if($c['ally_name'] != $parse['ally_name']){
					$parse['allys'] .= "<option value='".$c['id']."' selected>". $c['ally_name'] ."</option>";
				}
			}
			$parse['zurueck'] = "<a href=alliance_diplo.php?add=1>Zur&uuml;ck</a>";
			$parse['zurueck_dip_manag'] = "<a href=alliance_diplo.php>".$lang['ALLIANCE_BACK']."</a>";
						
			$page = parsetemplate(gettemplate('alliance_diplo_add'), $parse);
			display($page);
			
		}elseif((isset($_POST['pakt'])) && (isset($_POST['ally'])) && ($_POST['search'] == "step3")){
		
			#############################################
			# Eintrag des Paktes in die DB und			#
			# Senden einer Nachricht an den Ally Leader	#
			#############################################
			
			$vorhanden = doquery("SELECT * FROM {{table}} WHERE".
			"(`pakt_owner_id_1` = '" . $user['ally_id'] . "' AND `pakt_owner_id_2` = '" . $_POST['ally'] . "')".
			"OR (`pakt_owner_id_1` = '" . $_POST['ally'] . "' AND `pakt_owner_id_2` = '". $user['ally_id'] ."')", "diplo");
	   		$test = mysql_fetch_array($vorhanden);
	   		if($test == ""){
				if(($_POST['pakt'] <= 0) or ($_POST['pakt'] >= 4)){
					AdminMessage ('<meta http-equiv="refresh" content="1; url=alliance_diplo.php">'.$lang['ALLIANCE_ERROR_NOPACT'], $lang['ALLIANCE_ERROR']);
					echo "test";
				}else{
					$ally_id = doquery("SELECT * FROM {{table}} WHERE `id` = '". $_POST['ally'] ."'", "alliance");  
					$id = mysql_fetch_array($ally_id);
					$zeitpunkt = time();
		   
					doquery("INSERT INTO {{table}} SET
					`pakt_owner_id_1` = '" . $parse['ally_id'] . "',
					`pakt_owner_id_2` = '" . $_POST['ally'] . "',
					`pakt_name_1` = '" . $parse['ally_name'] . "',
					`pakt_name_2` = '" . $id['ally_name'] . "',
					`pakt` = '" . $_POST['pakt'] . "',
					`akzeptiert` = '1',
					`zeitpunkt` = '" . $zeitpunkt . "'
										 ", "diplo");
							 
					##########################################
					# Senden einer Nachricht für die Anfrage #
					##########################################						 
					$Owner = $id['ally_owner'];
					$Sender = $user['id'];
					$From    = "Allianz <font color=\"red\">".$parse['ally_name']."</font>";
					$Subject = $lang['ALLIANCE_PACT_SUBJECT'];
	

					if($_POST['pakt'] == 1){
						$Message = str_replace('##alliance##', $user['ally_name'], $lang['ALLIANCE_PACT_ALLY_TEXT'])."<a href=alliance_diplo.php?accept=". $parse['ally_id'] ."><b>".$lang['ALLIANCE_PACT_ACCEPT']."</b></a>";
					}elseif($_POST['pakt'] == 2) {
						$Message = str_replace('##alliance##', $user['ally_name'], $lang['ALLIANCE_PACT_NEUTRAL_TEXT'])."<a href=alliance_diplo.php?accept=". $parse['ally_id'] ."><b>".$lang['ALLIANCE_PACT_ACCEPT']."</b></a>";
					} elseif ($_POST['pakt'] == 3) {
						$Message = str_replace('##alliance##', $user['ally_name'], $lang['ALLIANCE_PACT_WAR_TEXT'])."<a href=alliance_diplo.php?accept=". $parse['ally_id'] ."><b>".$lang['ALLIANCE_PACT_ACCEPT']."</b></a>";
					}
	
					SendSimpleMessage ( $Owner, $Sender, '', 3, $From, $Subject, $Message);

					AdminMessage ('<meta http-equiv="refresh" content="1; url=alliance_diplo.php">'.$lang['ALLIANCE_PACT_SUCCESS'], $lang['ALLIANCE_PACT_SUCCESS_TITLE']);
				}
			}else{
				AdminMessage ('<meta http-equiv="refresh" content="1; url=alliance_diplo.php">'.$lang['ALLIANCE_PACT_FAILED'], $lang['ALLIANCE_ERROR']);
			}
		}
	}
	
#######################
# Anfragen bearbeiten #
#######################

	if(isset($_GET['anfragen'])) {
		$akzeptiert = doquery("SELECT `pakt_owner_id_1`, `pakt_owner_id_2`, `pakt_name_1`, `pakt_name_2`, `pakt`, `zeitpunkt` FROM {{table}} " .
		" WHERE (`pakt_owner_id_1` = '". $user['ally_id'] ."' OR `pakt_owner_id_2` = '". $user['ally_id'] ."') AND (`akzeptiert` = '1')", "diplo");
		while ($c = mysql_fetch_array($akzeptiert)) {
			if($c['pakt_owner_id_1'] == $user['ally_id']){
				$parse['anfragen_own'] .= "<tr >".
					"<td class=\"b\"><b>". $c['pakt_name_2'] ."</b></td>".
					"<td class=\"b\"><b>".str_replace('##date##', date("d.m.y",$c['zeitpunkt']), str_replace('##time##', date("H:i:s",$c['zeitpunkt']), $lang['ALLIANCE_PACT_STATUS']))."</b></td>".
					"<td class=\"b\" colspan=\"2\"><a href=alliance_diplo.php?delete=". $c['pakt_owner_id_2'] ."&how=2>".$lang['ALLIANCE_DELETE']."</a></td></tr>";
			}else{
				$parse['anfragen'] 		.="<tr>".
					"<td class=\"b\"><a href=alliance_diplo.php?accept=". $c['pakt_owner_id_1'] ."><b>". $c['pakt_name_1'] ."</b></a></td>".
					"<td class=\"b\"><b>".str_replace('##date##', date("d.m.y",$c['zeitpunkt']), str_replace('##time##', date("H:i:s",$c['zeitpunkt']), $lang['ALLIANCE_PACT_STATUS']))."</b></td>".
					"<td class=\"b\"><a href=alliance_diplo.php?accept=". $c['pakt_owner_id_1'] .">".$lang['ALLIANCE_PACT_ACCEPT']."</a></td>".
					"<td class=\"b\"><a href=alliance_diplo.php?delete=". $c['pakt_owner_id_1'] ."&how=1>".$lang['ALLIANCE_DELETE']."</a></td></tr>";
			}
		}
		$parse['zurueck'] = "<a href=alliance_diplo.php>".$lang['ALLIANCE_BACK']."</a>";
		$page = parsetemplate(gettemplate('alliance_diplo_anfragen'), $parse);
		display($page);
	}
	
#######################
# Anfrage Akzeptieren #
#######################

	if(isset($_GET['accept'])) {
	
		doquery("UPDATE {{table}} SET `akzeptiert` = '0' WHERE `pakt_owner_id_1` = '". $_GET['accept'] ."' AND `pakt_owner_id_2` =". $user['ally_id'] ."", "diplo");  
		
		$From    = "Allianz <font color=\"red\">".$id['ally_name']."</font>";
		$Subject = $lang['ALLIANCE_PACT_SUBJECT'];
		$Message = str_replace('##alliance##', $user['ally_name'], $lang['ALLIANCE_PACT_ACCEPTED_TEXT']);
		$Message = mysql_escape_string(strip_tags($Message));
		
		##############################################
		# Senden einer RM an die partner Arbeit,wenn #
		# der Pakt zwichen denn Ally geschlossen ist #
		##############################################
		
		$sq = doquery("SELECT id,username FROM {{table}} WHERE ally_id='" . $_GET['accept'] . "'", "users");
		while ($u = mysql_fetch_array($sq)) {
			doquery("INSERT INTO {{table}} SET
			`message_owner`='".$u['id']."',
			`message_sender`='".$user['id']."' ,
			`message_time`='" . time() . "',
			`message_type`='2',
			`message_from`='".$From."',
			`message_subject`='".$Subject."',
			`message_text`='".$Message."'
			", "messages");
		}
		doquery("UPDATE {{table}} SET `new_message`=new_message+1 WHERE ally_id='" . $_GET['accept'] . "'", "users");
		doquery("UPDATE {{table}} SET `mnl_alliance`=mnl_alliance+1 WHERE ally_id='" . $_GET['accept'] . "'", "users");
		
		#######################################
		# Senden einer RM an die eigene Ally, #
		# wenn der Pakt geschlossen wird      #
		#######################################
		
		$ally_id_part = doquery("SELECT `ally_name` FROM {{table}} WHERE `id` = '". $_GET['accept'] ."'", "alliance");  
		$id_part = mysql_fetch_array($ally_id_part);
		
		$Message_owne = str_replace('##alliance##', $id_part['ally_name'], $lang['ALLIANCE_PACT_ACCEPTED_TEXT']);
		$Message_owne = mysql_escape_string(strip_tags($Message_owne));
		
		$sq = doquery("SELECT id,username FROM {{table}} WHERE ally_id='" . $parse['ally_id'] . "'", "users");
		while ($u = mysql_fetch_array($sq)) {
			doquery("INSERT INTO {{table}} SET
			`message_owner`='".$u['id']."',
			`message_sender`='".$user['id']."' ,
			`message_time`='" . time() . "',
			`message_type`='2',
			`message_from`='".$From."',
			`message_subject`='".$Subject."',
			`message_text`='".$Message_owne."'
			", "messages");
		}
		doquery("UPDATE {{table}} SET `new_message`=new_message+1 WHERE ally_id='" . $parse['ally_id'] . "'", "users");
		doquery("UPDATE {{table}} SET `mnl_alliance`=mnl_alliance+1 WHERE ally_id='" . $parse['ally_id'] . "'", "users");
		
		AdminMessage ('<meta http-equiv="refresh" content="1; url=alliance_diplo.php">'.$lang['ALLIANCE_PACT_SUCCESS'], $lang['ALLIANCE_PACT_SUCCESS_TITLE']);
		
	}

	################
	# Pakt Löschen #
	################

	if(isset($_GET['delete'])) {
		doquery("DELETE FROM {{table}} ".
		"WHERE (`pakt_owner_id_1` = '". $user['ally_id'] ."' AND `pakt_owner_id_2` = '". $_GET['delete'] ."') ".
		"OR (`pakt_owner_id_1` = '". $_GET['delete'] ."' AND `pakt_owner_id_2` = '". $user['ally_id'] ."')", "diplo");  
		
		$ally_id = doquery("SELECT * FROM {{table}} WHERE `id` = '". $_GET['delete'] ."'", "alliance");  
		$id = mysql_fetch_array($ally_id);
		
		$Owner 	 = $id['ally_owner'];
		$Sender  = $user['id'];
		$From    = "Allianz <font color=\"red\">".$id['ally_name']."</font>";
		$Subject = $lang['ALLIANCE_PACT_REJECTED_SUBJECT'];
		if(isset($_GET['delete']) && ($_GET['how'] == 1)){
			$Message = str_replace('##alliance##', $user['ally_name'], $lang['ALLIANCE_PACT_REJECTED_TEXT']);
			SendSimpleMessage ( $Owner, $Sender, '', 3, $From, $Subject, $Message);
		}elseif(isset($_GET['delete']) && ($_GET['how'] == 2)){
			$Message = str_replace('##alliance##', $user['ally_name'], $lang['ALLIANCE_PACT_CANCELED']);
			SendSimpleMessage ( $Owner, $Sender, '', 3, $From, $Subject, $Message);
		}else{
			
			###############################
			# Bei Paktlöschung RM an      #
			# alle Member der patner Ally #
			###############################
			
			$Message = str_replace('##alliance##', $user['ally_name'], $lang['ALLIANCE_PACT_DELETED']);
			$Message = mysql_escape_string(strip_tags($Message));

			$sq = doquery("SELECT id,username FROM {{table}} WHERE ally_id='" . $_GET['delete'] . "'", "users");
			while ($u = mysql_fetch_array($sq)) {
				doquery("INSERT INTO {{table}} SET
				`message_owner`='".$u['id']."',
				`message_sender`='".$user['id']."' ,
				`message_time`='" . time() . "',
				`message_type`='2',
				`message_from`='".$From."',
				`message_subject`='".$Subject."',
				`message_text`='".$Message."'
				", "messages");
			}
			doquery("UPDATE {{table}} SET `new_message`=new_message+1 WHERE ally_id='".$_GET['delete']."'", "users");
			doquery("UPDATE {{table}} SET `mnl_alliance`=mnl_alliance+1 WHERE ally_id='".$_GET['delete']."'", "users");
			
			#######################################
			# Senden einer RM an die eigene Ally, #
			# wenn der Pakt gelöscht wird         #
			#######################################
			
			$ally_id_part = doquery("SELECT `ally_name` FROM {{table}} WHERE `id` = '". $_GET['accept'] ."'", "alliance");  
			$id_part = mysql_fetch_array($ally_id_part);
			
			$Message_owne = str_replace('##alliance##', $id_part['ally_name'], $lang['ALLIANCE_PACT_DELETED']);
			$Message_owne = mysql_escape_string(strip_tags($Message_owne));
			
			$sq = doquery("SELECT id,username FROM {{table}} WHERE ally_id='" . $parse['ally_id'] . "'", "users");
			while ($u = mysql_fetch_array($sq)) {
				doquery("INSERT INTO {{table}} SET
				`message_owner`='".$u['id']."',
				`message_sender`='".$user['id']."' ,
				`message_time`='" . time() . "',
				`message_type`='2',
				`message_from`='".$From."',
				`message_subject`='".$Subject."',
				`message_text`='".$Message_owne."'
				", "messages");
			}
			doquery("UPDATE {{table}} SET `new_message`=new_message+1 WHERE ally_id='" . $parse['ally_id'] . "'", "users");
			doquery("UPDATE {{table}} SET `mnl_alliance`=mnl_alliance+1 WHERE ally_id='" . $parse['ally_id'] . "'", "users");
				
		}
			
		AdminMessage ('<meta http-equiv="refresh" content="1; url=alliance_diplo.php">'.$lang['ALLIANCE_PACT_DELETION_SUCCESS'], $lang['ALLIANCE_PACT_SUCCESS_TITLE']);

	}

	#############################
	# Anzeige des Leader-panels #
	#############################

	$parse['admin'] = "
		<tr>
			<td class=c colspan=4><b>".$lang['ALLIANCE_LEADER_PANEL']."</b></td>
		</tr>
		<tr>
			<td class=b colspan=2><b><a href=alliance_diplo.php?add=". $user['ally_id'] ."><center>".$lang['ALLIANCE_LEADER_ADD_NEW']."</a></td>
			<td class=b colspan=2><a href=alliance_diplo.php?anfragen=". $user['ally_id'] ."><center>".$lang['ALLIANCE_LEADER_EDIT']."</a></center></b></td>
		</tr>";
} else {
	$parse['admin'] = "";
}
$parse['zurueck_ally'] = "<a href=alliance.php?mode=admin&edit=ally>".$lang['ALLIANCE_BACK']."</a>";

$page = parsetemplate(gettemplate('alliance_diplo'), $parse);
display($page);
?>
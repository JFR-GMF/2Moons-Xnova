<?php

##############################################################################
# *																			 #
# * XG PROYECT																 #
# *  																		 #
# * @copyright Copyright (C) 2008 - 2009 By lucky from Xtreme-gameZ.com.ar	 #
# *																			 #
# *																			 #
# *  This program is free software: you can redistribute it and/or modify    #
# *  it under the terms of the GNU General Public License as published by    #
# *  the Free Software Foundation, either version 3 of the License, or       #
# *  (at your option) any later version.									 #
# *																			 #
# *  This program is distributed in the hope that it will be useful,		 #
# *  but WITHOUT ANY WARRANTY; without even the implied warranty of			 #
# *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the			 #
# *  GNU General Public License for more details.							 #
# *																			 #
##############################################################################

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$xgp_root = '../';
include($xgp_root . 'extension.inc.php');
include($xgp_root . 'common.' . $phpEx);

if ($user['authlevel'] < 3) die(message ($lang['not_enough_permissions']));

	$parse = $lang;

	extract($_GET);

	if (isset($delete))
		doquery("DELETE FROM {{table}} WHERE `error_id`=$delete", 'errors');
	elseif ($deleteall == 'yes')
		doquery("TRUNCATE TABLE {{table}}", 'errors');

	$query = doquery("SELECT * FROM {{table}}", 'errors');

	$i = 0;

	while ($u = mysql_fetch_array($query))
	{
		$i++;

		$parse['errors_list'] .= "

		<tr><td width=\"25\">". $u['error_id'] ."</td>
		<td width=\"170\">". $u['error_type'] ."</td>
		<td width=\"230\">". date('d/m/Y h:i:s', $u['error_time']) ."</td>
		<td width=\"95\"><a href=\"?delete=". $u['error_id'] ."\" border=\"0\"><img src=\"../styles/images/r1.png\" border=\"0\"></a></td></tr>
		<tr><td colspan=\"4\" class=b>".  nl2br($u['error_text'])."</td></tr>";
	}

	$parse['errors_list'] .= "<tr><th class=b colspan=5>". $i . $lang['er_errors'] ."</th></tr>";

	display(parsetemplate(gettemplate('adm/errors_body'), $parse), false, '', true, false);

?>
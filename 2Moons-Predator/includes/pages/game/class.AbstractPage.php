<?php

/**
 *  2Moons
 *  Copyright (C) 2012 Jan Kröpke
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package 2Moons
 * @author Jan Kröpke <info@2moons.cc>
 * @copyright 2012 Jan Kröpke <info@2moons.cc>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPLv3 License
 * @version 1.7.3 (2013-05-19)
 * @info $Id: class.AbstractPage.php 2643 2013-03-26 17:13:31Z slaver7 $
 * @link http://2moons.cc/
 */

abstract class AbstractPage 
{
	protected $tplObj;
	protected $ecoObj;
	protected $window;
	protected $disableEcoSystem = false;
	
	protected function __construct() {
		
		if(!AJAX_REQUEST)
		{
			$this->setWindow('full');
			if(!$this->disableEcoSystem)
			{
				$this->ecoObj	= new ResourceUpdate();
				$this->ecoObj->CalcResource();
			}
			$this->initTemplate();
		} else {
			$this->setWindow('ajax');
		}
	}
	
	protected function initTemplate() {
		if(isset($this->tplObj))
			return true;
			
		$this->tplObj	= new template;
		list($tplDir)	= $this->tplObj->getTemplateDir();
		$this->tplObj->setTemplateDir($tplDir.'game/');
		return true;
	}
	
	protected function setWindow($window) {
		$this->window	= $window;
	}
		
	protected function getWindow() {
		return $this->window;
	}
	
	protected function getQueryString() {
		$queryString	= array();
		$page			= HTTP::_GP('page', '');
		
		if(!empty($page)) {
			$queryString['page']	= $page;
		}
		
		$mode			= HTTP::_GP('mode', '');
		if(!empty($mode)) {
			$queryString['mode']	= $mode;
		}
		
		return http_build_query($queryString);
	}
	
	protected function getCronjobsTodo()
	{
		require_once 'includes/classes/Cronjob.class.php';
		
		$this->tplObj->assign_vars(array(	
			'cronjobs'		=> Cronjob::getNeedTodoExecutedJobs()
		));
	}
	
	protected function getNavigationData() 
    {
		global $PLANET, $LNG, $USER, $THEME, $resource, $reslist;
//PlanetMenu
    		if(isset($USER['PLANETS']))
        	$USER['PLANETS']	= getPlanets($USER);
		
		if($PLANET[$resource[43]] > 0) {
			$this->tplObj->loadscript("gate.js");
		}
		
		$this->tplObj->loadscript("topnav.js");
		$this->tplObj->loadscript("planetmenu.js");
    		$this->phpself	= "?page=".HTTP::_GP('page', '')."&amp;mode=".HTTP::_GP('mode', '');
			
		$PlanetSelect	= array();
		$Scripttime 	= array();
		
		if(isset($USER['PLANETS'])) {
			$USER['PLANETS']	= getPlanets($USER);
		}
		
		foreach($USER['PLANETS'] as $CurPlanetID => $PlanetQuery)
		{
			if(!empty($PlanetQuery['b_building_id']))
        	{
            	$QueueArray                    	= unserialize($PlanetQuery['b_building_id']);
            	foreach($QueueArray as $ListIDArray)
            	{
                	if($ListIDArray[3] > TIMESTAMP)
                    	$Scripttime[$PlanetQuery['id']][]	= $ListIDArray[3] - TIMESTAMP;
            	}
        	}
        	$Planetlist[$PlanetQuery['id']]	= array(
            	'url'    		=> $this->phpself."&amp;cp=".$PlanetQuery['id'],
            	'name'    		=> $PlanetQuery['name'].(($PlanetQuery['planet_type'] == 3) ? " (".$LNG['fcm_moon'].")":""),
            	'image'   		 => $PlanetQuery['image'],
            	'galaxy'   		 => $PlanetQuery['galaxy'],
            	'system'   		 => $PlanetQuery['system'],
            	'planet'			=> $PlanetQuery['planet'],
            	'ptype'  		  => $PlanetQuery['planet_type'],
        	);
			$PlanetSelect[$PlanetQuery['id']]	= $PlanetQuery['name'].(($PlanetQuery['planet_type'] == 3) ? " (" . $LNG['fcm_moon'] . ")":"")." [".$PlanetQuery['galaxy'].":".$PlanetQuery['system'].":".$PlanetQuery['planet']."]";
		}
		
		$resourceTable	= array();
		$resourceSpeed	= Config::get('resource_multiplier');
		foreach($reslist['resstype'][1] as $resourceID)
		{
			$resourceTable[$resourceID]['name']			= $resource[$resourceID];
			$resourceTable[$resourceID]['current']		= $PLANET[$resource[$resourceID]];
			$resourceTable[$resourceID]['max']			= $PLANET[$resource[$resourceID].'_max'];
			if($USER['urlaubs_modus'] == 1 || $PLANET['planet_type'] != 1)
			{
				$resourceTable[$resourceID]['production']	= $PLANET[$resource[$resourceID].'_perhour'];
			}
			else
			{
				$resourceTable[$resourceID]['production']	= $PLANET[$resource[$resourceID].'_perhour'] + Config::get($resource[$resourceID].'_basic_income') * $resourceSpeed;
			}
		}

		foreach($reslist['resstype'][2] as $resourceID)
		{
			$resourceTable[$resourceID]['name']			= $resource[$resourceID];
			$resourceTable[$resourceID]['used']			= $PLANET[$resource[$resourceID].'_used'];
			$resourceTable[$resourceID]['max']			= $PLANET[$resource[$resourceID]];
		}

		foreach($reslist['resstype'][3] as $resourceID)
		{
			$resourceTable[$resourceID]['name']			= $resource[$resourceID];
			$resourceTable[$resourceID]['current']		= $USER[$resource[$resourceID]];
		}
// Mise en place comteur pour le bonus


		if($USER['bonus_attente_time'] == 0){
      
      		$BonusTemps = 0;
		$secsBonus = 0;
    
    }

		if($USER['bonus_attente_time'] < TIMESTAMP && $USER['bonus_attente_time'] != 0){


			$time = 0;
			$GLOBALS['DATABASE']->query("UPDATE `".USERS."` SET `bonus_attente_time` = ". $time .";");

		}

		if ($USER['bonus_attente_time'] > TIMESTAMP){

			$secsBonus = $USER['bonus_attente_time'] - TIMESTAMP;

		}

		// fin de la Mise en place comteur pour le bonus

		$themeSettings	= $THEME->getStyleSettings();
		
		$this->tplObj->assign_vars(array(	
			'PlanetSelect'		=> $PlanetSelect,
			'PlanetMenu'   	=> $Planetlist,
        		'show_planetmenu'   => $LNG['show_planetmenu'],
        		'Scripttime'    	=> json_encode($Scripttime), 
			'is_pmenu'        	=> $USER['settings_planetmenu'],
			'new_message' 		=> $USER['messages'],
			'vacation'			=> $USER['urlaubs_modus'] ? _date($LNG['php_tdformat'], $USER['urlaubs_until'], $USER['timezone']) : false,
			'delete'			=> $USER['db_deaktjava'] ? sprintf($LNG['tn_delete_mode'], _date($LNG['php_tdformat'], $USER['db_deaktjava'] + (Config::get('del_user_manually') * 86400)), $USER['timezone']) : false,
			'darkmatter'		=> $USER['darkmatter'],
			'current_pid'		=> $PLANET['id'],
			'image'				=> $PLANET['image'],
			'resourceTable'		=> $resourceTable,
			'shortlyNumber'		=> $themeSettings['TOPNAV_SHORTLY_NUMBER'],
			'closed'			=> !Config::get('game_disable'),
			'hasBoard'			=> filter_var(Config::get('forum_url'), FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED),
			'hasAdminAccess'	=> isset($_SESSION['admin_login']),
		));
	}
	
	protected function getPageData() 
    {
		global $USER, $THEME;
		
		if($this->getWindow() === 'full') {
			$this->getNavigationData();
			$this->getCronjobsTodo();
		}
		
		$dateTimeServer		= new DateTime("now");
		if(isset($USER['timezone'])) {
			try {
				$dateTimeUser	= new DateTime("now", new DateTimeZone($USER['timezone']));
			} catch (Exception $e) {
				$dateTimeUser	= $dateTimeServer;
			}
		} else {
			$dateTimeUser	= $dateTimeServer;
		}
		
        $this->tplObj->assign_vars(array(
            'vmode'				=> $USER['urlaubs_modus'],
			'authlevel'			=> $USER['authlevel'],
			'userID'			=> $USER['id'],
			'bodyclass'			=> $this->getWindow(),
            'game_name'			=> Config::get('game_name'),
            'uni_name'			=> Config::get('uni_name'),
			'ga_active'			=> Config::get('ga_active'),
			'ga_key'			=> Config::get('ga_key'),
			'debug'				=> Config::get('debug'),
			'VERSION'			=> Config::get('VERSION'),
			'date'				=> explode("|", date('Y\|n\|j\|G\|i\|s\|Z', TIMESTAMP)),
			'REV'				=> substr(Config::get('VERSION'), -4),
			'Offset'			=> $dateTimeUser->getOffset() - $dateTimeServer->getOffset(),
			'queryString'		=> $this->getQueryString(),
			'themeSettings'		=> $THEME->getStyleSettings(),
		));
	}
	
	protected function printMessage($Message, $fullSide = true, $redirect = NULL) {
		$this->tplObj->assign_vars(array(
			'mes'		=> $Message,
		));
		
		if(isset($redirect)) {
			$this->tplObj->gotoside($redirect[0], $redirect[1]);
		}
		
		if(!$fullSide) {
			$this->setWindow('popup');
		}
		
		$this->display('error.default.tpl');
	}
	
	protected function save() {		
		if(isset($this->ecoObj)) {
			$this->ecoObj->SavePlanetToDB();
		}
	}
	
	protected function display($file) {
		global $THEME, $LNG;
		
		$this->save();
		
		if($this->getWindow() !== 'ajax') {
			$this->getPageData();
		}
		
		$this->tplObj->assign_vars(array(
            'lang'    		=> $LNG->getLanguage(),
            'dpath'			=> $THEME->getTheme(),
			'scripts'		=> $this->tplObj->jsscript,
			'execscript'	=> implode("\n", $this->tplObj->script),
			'basepath'		=> PROTOCOL.HTTP_HOST.HTTP_BASE,
		));

		$this->tplObj->assign_vars(array(
			'LNG'			=> $LNG,
		), false);
		
		$this->tplObj->display('extends:layout.'.$this->getWindow().'.tpl|'.$file);
		exit;
	}
	
	protected function sendJSON($data) {
		$this->save();
		echo json_encode($data);
		exit;
	}
	
	protected function redirectTo($url) {
		$this->save();
		HTTP::redirectTo($url);
		exit;
	}
}
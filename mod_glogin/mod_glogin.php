<?php

/*
 * @package    Glogin
 * @subpackage Plugins
 * @license    GNU/GPL
 *
 * Copyright 2017 Voxinteractif Inc. 
 
 *    This file is part of Glogin.

    Glogin is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Glogin is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Glogin.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die;

$app  = JFactory::getApplication();
$doc 	= JFactory::getDocument();

$plugin = JPluginHelper::getPlugin('authentication', 'glogin');

$user = JFactory::getUser();


if(isset($_POST['isGlogin']) && !$user->id)
{
	if($app->isClient('site'))
	{
		$arr_credentials['username'] = $_POST['username'];
		$arr_credentials['password'] = $_POST['password'];
	}
	else 
	{
		$arr_credentials['username'] = $_POST['username'];
		$arr_credentials['password'] = $_POST['passwd'];
	}
	
	
	JPluginHelper::importPlugin('authentication', 'glogin');
	$dispatcher = JEventDispatcher::getInstance();
	$results = new stdClass();
	$dispatcher->trigger('gloginUserAuthenticate', array($arr_credentials,'',&$results));
	$_POST['isGlogin'] = null;
	echo $results->msg;
}

$user = JFactory::getUser();

if(!$plugin) 
{
	JError::raiseWarning( 100, 'Glogin is disable or not installed.' );
}
else if ($user->id) 
{
	require JModuleHelper::getLayoutPath('mod_login', $params->get('layout', 'default_logout'));
}
else 
{
	$GloginParams = new JRegistry($plugin->params);
	JLoader::register('ModLoginHelper', JPATH_SITE.'/modules/mod_login/helper.php');
	
	$doc->addScript("https://apis.google.com/js/platform.js");
	$doc->addCustomTag("<meta name=\"google-signin-client_id\" content=\"".$GloginParams['gClientId']."\">");
	$doc->addCustomTag("<meta name=\"google-signin-scope\" content=\"profile email\">");
	
	if($app->isClient('site'))
	{
		$params->def('greeting', 0);
		$type = 'login';
		$return = ModLoginHelper::getReturnUrl($params, $type);
		$task = 'user.login';
		$option = 'com_users';
		$passwordName = 'password';
	}
	else 
	{
		$return = ModLoginHelper::getReturnUri();
		$task = 'login';
		$option = 'com_login';
		$passwordName = 'passwd';
	}
	
	$theFormId = 'gloginForm';
	 
	$theHTML = '<div class="g-signin2" data-onsuccess="onSignIn" data-theme="dark"></div>
					<div class="glogin_error"><p></p></div>
					<form id="'.$theFormId.'" style="display:none;" action="'.JUri::getInstance().'" method="post">
					<input type="hidden" name="isGlogin" value="1">
					'.JHtml::_('form.token').'
					</form>';
					
	$theHTML .= '<script>
					function onSignIn(googleUser)  {
					var Gform = document.getElementById("'.$theFormId.'");
					var node;
					var profile = googleUser.getBasicProfile();
					node = document.createElement("input");
					node.setAttribute("type", "hidden");
					node.setAttribute("name", "username");
					node.setAttribute("value", profile.getEmail());
					Gform.appendChild(node);
					node = document.createElement("input");
					node.setAttribute("type", "hidden");
					node.setAttribute("name", "'.$passwordName.'");
					node.setAttribute("value", googleUser.getAuthResponse().id_token);
					Gform.appendChild(node);
					googleUser.disconnect();
					Gform.submit();
					}
					</script>';
	 
	echo $theHTML;
}
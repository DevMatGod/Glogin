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
 use Joomla\CMS\User\UserHelper;
 use Joomla\CMS\User\User;
 
 class plgAuthenticationGlogin extends JPlugin
 {
 	/**
     * This method should handle authentication from the Glogin module.
     *
     * @access    public
     * @param     array     $credentials    Array holding the user credentials ('username' and 'password')
     * @param     array     $options        Array of extra options
     * @param     object    $response       Authentication response object
     * @return    boolean
     * @since 1.5
     */
     
	function onUserAuthenticate( $credentials, $options, &$response)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		
		/* Checking if everything is all right */
		$response->type = "Glogin";
		$response->status = JAuthentication::STATUS_FAILURE; // Will return Failure by default.
		
		if($jinput->get('isGlogin', false) === false)
		{
			$response->msg = 'User not using Glogin.';
			return;
		}
		if(!(isset($credentials['password'])))
		{
			$response->msg = 'No token send';
			return;
		}
		
		// Sending data to google and reciving confirmation
		$payload = $this->checkGoogleAccount($credentials['password']);
		
		if(!($payload))
		{
			$response->msg = 'Google did not validated the credentials.';
			return;
		}
		
		// Validate the Domain.
		
		$plugin = JPluginHelper::getPlugin('authentication', 'glogin');
     	$pluginParams = new JRegistry($plugin->params);
     	$domainList = ($pluginParams->get('domainGroup'));
		
		$domainOption = [];
		$domainOption['isOk'] = false;
   	
		foreach($domainList as $currentDomain => $values) 
		{
			if($values->domain == $payload['hd'])
			{
				$domainOption['isOk'] = true;
				$domainOption['domain'] = $values->domain;
				$domainOption['group'] = $values->userGroup;
				$domainOption['newUser'] = isset($values->newUser);
			}
  		}

		if(!($domainOption['isOk']))
		{
			$response->msg = 'Domain is not valide.';
			return;
		}
		
  		// Getting user
  		$user = $this->getUser($payload['email']);

  		if(!($user->id)) $user = $this->createUser($payload,$domainOption); // User does't exist. Trying to create one.
  		if(!($user->id))
		{
			$response->msg = 'User could not be created.';
			return;
		}
  		
		// Everything worked and the user can login
		$response->email    = $user->email;
		$response->fullname = $user->name;
		$response->id 		  = $user->id;
		$response->status = JAuthentication::STATUS_SUCCESS;
		
		if (JFactory::getApplication()->isClient('administrator'))
		{
			$response->language = $user->getParam('admin_language');
		}
		else
		{
			$response->language = $user->getParam('language');
		}
		
		return; // This is the end.

 }
     	
     	private function checkGoogleAccount($googleToken) {
     		$googleApiInstalled = glob(JPATH_LIBRARIES."/google-api-php-client-*",GLOB_ONLYDIR);
     		
     		if(sizeof($googleApiInstalled) > 0)
     		{
     			arsort($googleApiInstalled);
     			require_once($googleApiInstalled[0]."/vendor/autoload.php");
     			$client = new Google_Client();
				$client->setScopes(array('https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/userinfo.profile'));
				return $client->verifyIdToken($googleToken);
     		}
     		else 
     		{
     			return false;
     		}
     	}
     	
     	private function getUser($username) {
     		$theUserId = UserHelper::getUserId($username);
			if($theUserId) return User::getInstance($theUserId);
	
			//Check if user is using a other username then email (User as been created without glogin or was edited)  		
  			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
			    ->select($db->quoteName('id'))
			    ->from($db->quoteName('#__users'))
			    ->where($db->quoteName('email') . ' = ' . $db->quote($username));
			    
			$db->setQuery($query);
			$id = $db->loadResult();
			if ($theUser = User::getInstance($id)) return $theUser;
			else return false;
     	}	
     	
     	function createUser($thePayload, $domainOption) {     		
			jimport('joomla.user.helper');
			
			if(!($domainOption['newUser'])) return false;
			
			$password = UserHelper::genRandomPassword();
			$user = User::getInstance();
			$user->set('id',null);
			$user->set('password',UserHelper::hashPassword($password));
			$user->set('username',$thePayload['email']);
			$user->set('email', $thePayload['email']);
			$user->set('name', $thePayload['name']);
			$user->set('groups',$domainOption['group']); 
			
			$plugin = JPluginHelper::getPlugin('authentication', 'glogin');
			$params = new JRegistry($plugin->params);
		
			$tempUser = JFactory::getUser($params['suid']);
			$session = JFactory::getSession();
			$session->set('user', $tempUser);
			$userIsSave = $user->save();
			$session->set('user', null);

			if ($userIsSave) return User::getInstance($user->id);
			else return false; // User could not be created.
		}
		
		function gloginUserAuthenticate($credentials, $options, &$response)
		{
			$this->onUserAuthenticate($credentials, $options, $response);
			if($response->status == JAuthentication::STATUS_SUCCESS)
			{
				$tempUser = User::getInstance($response->id);
				$session = JFactory::getSession();
				$session->set('user', $tempUser);
				header('Location: '.JUri::getInstance());
			}
		}
 }
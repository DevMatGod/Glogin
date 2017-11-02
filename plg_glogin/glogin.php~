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
 //jimport('joomla.plugin.plugin');
 //require_once JPATH_LIBRARIES.'/googleAPI/vendor/autoload.php';
 
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
     
     
     
     
		function onUserAuthenticate( $credentials, $options, &$response ){
			$theDomain;
			$theDomain['isOk'] = false;			
    		$plugin = JPluginHelper::getPlugin('authentication', 'glogin');
     		$pluginParams = new JRegistry($plugin->params);
     		$domainList = ($pluginParams->get('domainGroup'));
     		$response->type = "Glogin";
     		
     		if(isset($options['googleToken'])) {
	     		$payload = $this->checkGoogleAccount($options['googleToken']);
	     		if($payload){
	     			
					foreach($domainList as $currentDomain => $values) 
					{
						if($values->domain == $payload['hd'])
						{
							$theDomain['isOk'] = true;
							$theDomain['domain'] = $values->domain;
							$theDomain['group'] = $values->userGroup;
							$theDomain['newUser'] = isset($values->newUser);
						}
	     			}
	     			$result = $this->getUser($payload['email']);	
					if($theDomain['isOk']) {
						$user = new stdClass(); 			
						if(is_null($result))
						{
							$user = $this->createUser($payload,$theDomain['group'],$theDomain['newUser']);
						}
						else
						{
							$user->ok = true;
							$user->msg = "User Already Exist";
							$user->id = $result;
						}
						
						if(!$user->ok)
						{
							$response->status = JAuthentication::STATUS_FAILURE;
	     					$response->error_message = $user->msg;
	   					
						}
						else 
						{
							$session = JFactory::getSession();
							$theUser = JFactory::getUser($user->id);
							$session->set('user', $theUser);
							$response->status = JAuthentication::STATUS_SUCCESS;
							
						}
					}
					else 
					{
						$response->status = JAuthentication::STATUS_FAILURE;
	     				$response->error_message = "The email does not have the permission to access this web page.";
					}
	     		}
	     		else
	     		{
	     			$response->status = JAuthentication::STATUS_FAILURE;
	     			$response->error_message = "Bad username or password";
	     		}
	     	
	     	}
     		
     	}
     	
     	function onAjaxGlogin()
     	{
     		if(JSession::checkToken()) //Check if the user is using a valid token.
     		{ 
     			jimport( 'joomla.user.authentication');
     			$post = JFactory::getApplication()->input->post->getArray();
     			$auth = JAuthentication::getInstance();
		      $credentials = array( 'username' => '', 'password' => '' );
		      $options = array('googleToken' => $post['googleToken']);
		      $response = $auth->authenticate($credentials, $options);
     			return $response;
     		}
     		else 
     		{
     			$response = new stdClass();
     			$response->status = 11;
     			$response->error_message = "Invalid Security Token";
     			return JFactory::getConfig();
     		}
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
     	
     	private function getUser($email) {
     		$db = JFactory::getDbo();
			$query    = $db->getQuery(true)
    		->select('id')
    		->from('#__users')
    		->where('email=' . $db->quote($email));
			$db->setQuery($query);
			return $db->loadResult();
     	}
     	
     	function createUser($thePayload, $groups, $newUser) {     		
			jimport('joomla.user.helper');
			
			$theResponse = new stdClass();
			$theResponse->ok = false;
			$theResponse->msg = "Can not create new user for this Domain.";
			$theResponse->id = 0;
			
			if(!($newUser)) return $theResponse;
			
			$password = JUserHelper::genRandomPassword();
			$user = \JUser::getInstance();
			$user->set('id',null);
			$user->set('password',\JUserHelper::hashPassword($password));
			$user->set('username',$thePayload['email']);
			$user->set('email', $thePayload['email']);
			$user->set('name', $thePayload['name']);
			$user->set('usertype', 'deprecated');
		
			if (!$user->save())
			{
				$theResponse->ok = false;
				$theResponse->msg = "Could not save the new User";
				return $theResponse;
			}
			else {
				$userId = JUserHelper::getUserId($thePayload['email']);
				for($i = 0; $i < count($groups); $i++)
				{
					$userGroup = new stdClass();
					$userGroup->user_id = $userId;
					$userGroup->group_id = $groups[$i];
					$result[$i] = JFactory::getDbo()->insertObject('#__user_usergroup_map', $userGroup);
				}				
				$theResponse->ok = true;
				$theResponse->msg = "User Created";
				$theResponse->id = $userId;
				return $theResponse;
			}
		}
 }
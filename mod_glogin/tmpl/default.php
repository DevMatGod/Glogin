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
JHtml::_('behavior.keepalive');
$theFormId = 'gloginForm';
 
$theHTML = '<div class="g-signin2" data-onsuccess="onSignIn" data-theme="dark"></div>
				<div class="glogin_error"><p></p></div>
				<form id="'.$theFormId.'" style="display:none;" action="'.JRoute::_('index.php', true, $params->get('usesecure', 0)).'" method="post">
				<input type="hidden" name="option" value="com_login">
				<input type="hidden" name="task" value="login">
				<input type="hidden" name="isGlogin" value="1">
				'.JHtml::_('form.token').'
				<input type="hidden" name="return" value="'.$return.'"/>
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
				node.setAttribute("name", "passwd");
				node.setAttribute("value", googleUser.getAuthResponse().id_token);
				Gform.appendChild(node);
				googleUser.disconnect();
				Gform.submit();
				}
				</script>
				';
 
echo $theHTML;
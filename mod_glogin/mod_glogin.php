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
 
require_once dirname(__FILE__) . '/helper.php';
JHtml::_('jquery.framework');

$plugin = JPluginHelper::getPlugin('authentication', 'glogin');
$params = new JRegistry($plugin->params);

JFactory::getDocument()->addScript("https://apis.google.com/js/platform.js");
JFactory::getDocument()->addCustomTag("<meta name=\"google-signin-client_id\" content=\"".$params['gClientId']."\">");
JFactory::getDocument()->addCustomTag("<meta name=\"google-signin-scope\" content=\"profile email\">");


$user = JFactory::getUser();
if ($user->id) $layout = 'logout';
else $layout = 'default';

require JModuleHelper::getLayoutPath('mod_glogin', $params->get('layout', $layout));


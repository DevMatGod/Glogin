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
 $adminEnable = JFactory::getConfig()->get('shared_session');
 if($adminEnable)
 {
 	require(JPATH_ROOT.'/modules/mod_glogin/mod_glogin.php');
 }
 
 else 
 {
 	require JModuleHelper::getLayoutPath('mod_glogin', $params->get('layout', 'noShareSession'));
 }


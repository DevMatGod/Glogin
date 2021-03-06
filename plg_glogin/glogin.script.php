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
 
class plgauthenticationGloginInstallerScript
{
	/**
	 * Method to install the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	function install($parent) 
	{
		//echo '<p>The module has been installed like a boss</p>';
	}

	/**
	 * Method to uninstall the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		//echo '<p>The module has been uninstalled</p>';
	}

	/**
	 * Method to update the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	function update($parent) 
	{
		//echo '<p>The module has been updated to version' . $parent->get('manifest')->version . '</p>';
	}

	/**
	 * Method to run before an install/update/uninstall method
	 * $parent is the class calling this method
	 * $type is the type of change (install, update or discover_install)
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		$googleApiInstalledPath = glob(JPATH_LIBRARIES."/google-api-php-client-*",GLOB_ONLYDIR);
		$googleApiInstalled = (sizeof($googleApiInstalledPath) > 0);
		
		if($type == "install" && !$googleApiInstalled)
		{
			?>
			<div class="alert">
			<h3><p>This module need the latest version of google-api-php-client to be install. You can <a href="https://github.com/google/google-api-php-client/releases" target="_blank">Download the latest version here.</a></p></h3>
			<h2>How to install</h2>
			<ol>
				<li><a href="https://github.com/google/google-api-php-client/releases" target="_blank">Download the latest version here.</a></li>
				<li>Unzip the folder.</li>
				<li>Copy the content to <?php echo JPATH_LIBRARIES; ?></li>
			</ol>
			</div>
			
			<?php
		}
		
	}

	/**
	 * Method to run after an install/update/uninstall method
	 * $parent is the class calling this method
	 * $type is the type of change (install, update or discover_install)
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
	}
}
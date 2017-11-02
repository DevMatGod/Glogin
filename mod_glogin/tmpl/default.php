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
 ?>
 
 <div class="g-signin2" data-onsuccess="onSignIn" data-theme="dark"></div>
 <div class="glogin_error"><p></p></div>
    <script>
      function onSignIn(googleUser)  {
        // Useful data for your client-side scripts:
        var profile = googleUser.getBasicProfile();
		  var gloginError = jQuery(".glogin_error p");
        // The ID token you need to pass to your backend:
        var theData = {option:"com_ajax", plugin:"glogin", format:"json", group:"authentication", <?php echo "\"".JSession::getFormToken()."\"";?>:1, googleToken:googleUser.getAuthResponse().id_token};
        
         
        
        var ajaxRequest = jQuery.ajax({
        		url: "/index.php",
  				method: "POST",
  				data: theData
			});
			
			ajaxRequest.done(function(data) {
				var theObject = data.data[0];
				if (theObject.status == 1)
				{
					location.reload();
				} 
				else {
					gloginError.text(theObject.error_message);
				}
			});
			
			ajaxRequest.fail(function(data) {
				gloginError.text("something went wrong if the problem persists contact the site administrator");
			});
			
			ajaxRequest.always(function() {
				googleUser.disconnect();
			});


      } 
    </script>
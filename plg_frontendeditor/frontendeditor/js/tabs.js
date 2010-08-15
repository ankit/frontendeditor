/**
* @version		$Id: tabs.js 418 2009-06-03 17:40:47Z ankit.ahuja $
* @copyright	Copyright (C) 2009 Open Source Matters. All rights reserved.
* @author 		Ankit Ahuja ( as part of Google Summer Of Code 2009 )
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

var tabSwitch = function( number )
{
	$$('.tab_content').setStyle('display','none');
	$('content_'+number).setStyle('display','block');
	var tabs = $$('.fr_tabs');
	tabs.removeClass('active');
	var len = tabs.length;
	for(var i=0; i < len; i++)
	{
		if(tabs[i].rel == number)
		{
			tabs[i].addClass('active');
			break;
		}
	}
}
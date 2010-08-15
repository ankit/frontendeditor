/**
* @version		$Id: menuitem.js 418 2009-06-03 17:40:47Z ankit.ahuja $
* @copyright	Copyright (C) 2009 Open Source Matters. All rights reserved.
* @author 		Ankit Ahuja ( as part of Google Summer Of Code 2009 )
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/*
 * JavaScript to perform editing of Menu Item Titles
 */

var JMenuitem = new Class({
	/* Initialize editing */
	initialize: function(el){
		this.el = el;
		this.id = this.getId();
		this.span_el = this.el.getElement('a').getElement('span');
		this.title = this.span_el.get('html');
	},
	/* Get menuitem id */
	getId: function(){
		var str = this.el.get('class');
		if(str)
		{
			return JEdit.removePrefix(str,'item');
		}
	},
	/* Enable editing for menuitem title */
	enableEdit: function(){
		this.span_el.set('html','');
		var span = new Element('span',{'class':'editable','html':this.title});
		this.span_el.adopt(span);
		this.createEvent(span);
	},
	/* Create onclick event for menuitem title */
	createEvent: function(el){
		var a_el = el.getParent();
		if(a_el)
		{
			a_el.addEvent('click',function(e){
				if(JEdit.isToggleOn)
				{
					e.stop();
				}
			});
		}
		el.addEvent('click',function(e,id){
			if(JEdit.isToggleOn)
			{
				e.stop();
				JEdit.createForm(this.getParent(),this.get('html'),id,"menuitem");
			}
		}.bindWithEvent(el,this.id));
	}
});

var JPage = new Class({
	/* Initialize editing */
	initialize: function(){
		this.el = $(document.body).getElement(fr_pageTitleSelector);
		this.id = this.getId();
		this.title = this.getTitle();
	},
	/* Get page id */
	getId: function(){
		var id = $$('span.fr_component_id').get('id').toString();
		if(id)
		{
			return JEdit.removePrefix(id,'component.');
		}
		else
		{
			return null;
		}
	},
	/* Get page title */
	getTitle: function(){
		if(this.el)
		{
			return this.el.get('html').toString().trim()
		}
	},
	/* Enable edit for page title */
	enableEdit: function(){
		if(this.id && this.el)
		{
			this.el.set('html','');
			var span = new Element('span',{'class':'editable','html':this.title});
			this.el.adopt(span);
			this.createEvent(span);
		}
	},
	/* Create onclick event for page title */
	createEvent: function(el){
		el.addEvent('click',function(e,id){
			if(JEdit.isToggleOn)
			{
				e.stop();
				JEdit.createForm(this.getParent(fr_pageTitleSelector),this.get('html'),id,"page");
			}
		}.bindWithEvent(el,this.id));
	}
});


JEdit.extend({
	
	/* Initialize editing for menuitems */
	initEditingForMenuItems: function(){
		//For Menu Item Titles
		var menus = $(document.body).getElements('ul.menu');
		menus.each(function(menu){
			var menu_items = menu.getElements('li');
			menu_items.each(function(el){
				var menuitem = new JMenuitem(el);
				menuitem.enableEdit();
			});
		});
	},
	
	/* Initialize editing for page title */
	initEditingForPageTitles: function(){
		if(this.isDefined("fr_articleSelector"))
		{
			if(fr_pageTitleSelector != fr_articleSelector)
			{
				var page = new JPage();
				page.enableEdit();
			}
		}
		else
		{
			var page = new JPage();
			page.enableEdit();
		}
	},

	/* Set page title on save */
	setPageTitle: function(value){
		document.title = value;
	}

});
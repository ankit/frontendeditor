/**
* @version		$Id: module.js 418 2009-06-03 17:40:47Z ankit.ahuja $
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
 * JavaScript to peform editing of Modules
 */
var JModule = new Class({
	/* Initialize editing */
	initialize: function(el){
		this.el = el;
		this.id = this.getId();
		this.title = this.getTitle();
	},
	/* Get module id */
	getId: function(){
		var str = this.el.get('id');
		if(str)
		{
			return JEdit.removePrefix(str,'module.');
		}
	},
	/* Get module title */
	getTitle: function(){
		return this.el.get('name');
	},
	/* Enable editing of module */
	enableEdit: function(){
		var children = this.el.getElements('');
		var len = children.length;
		var element;
		for(var i = 0;i < len; i ++)
		{
			element = children[i];

			if(JEdit.unfilterInput(element.get('html').toLowerCase()) == this.title.toLowerCase())
			{
				var span = new Element('span',{'class':'editable','html':element.get('html')});
				element.set('html','');
				element.adopt(span);
				this.createEvent(span);
				break;
			}
		}
	},
	/* Create onclick event for module title */
	createEvent: function(el){
		el.addEvent('click',function(e,id){
			if(JEdit.isToggleOn)
			{
				e.stop();
				JEdit.createForm(this.getParent(),this.get('html'),id,"module");
			}
		}.bindWithEvent(el,this.id));
	},
	/* Get order of module */
	getOrder: function(){
		var container = this.el.getParent();
		return container.getElements('.mod-edit').indexOf(this.el);
	},
	/* Get module position */
	getPosition: function(){
		return JEdit.removePrefix(this.el.getParent().get('class'),'frpos.');
	},
	/* Create loading icon */
	createLoadIcon: function(){
		this.loadicon = new Element('span',{'html':"<img src='"+fr_imagedir+"ajax-loader.gif' title='Saving...' >",'class':'loading-icon'});
		this.loadicon.inject(this.el,'before');
	},
	/* Revert module to original position */
	revertPosition: function(position,order){
		var parent;
		var module;
		var temp = this.el.clone(true,true)
		if(this.getPosition() == position)
		{
			parent = this.el.getParent();
			module = parent.getElements('.mod-edit')[order];
			if(module)
			{
				if(order < this.getOrder())
				{
					temp.injectBefore(module);
				}
				else
				{
					temp.injectAfter(module);
				}
			}
		}
		else
		{
			var modules = $$('.mod-edit');
			var len = modules.length;
			for(var i = 0;i < len; i++ )
			{
				if(modules[i].getParent().get('class') == position)
				{
					parent = modules[i].getParent();
					break;
				}
			}
			module = parent.getElements('.mod-edit')[order];
			if(module)
			{
				temp.injectBefore(module);
			}
		}
		if(!module)
		{
			parent.adopt(temp);
		}
		this.el.destroy();
		this.el = temp;
		var span = this.el.getElement('span.editable');
		this.createEvent(span);
		JEdit.setTitleHover(span,true);
		JDraggables.serialize(false, function(el,index){
			return el.set('alt',index);
		});
	}
	
});

JEdit.extend({
	/* Initialize editing for modules */
	initEditingForModules: function(){
		var modules = $(document.body).getElements('.mod-edit');
		modules.each(function(el){
			var module = new JModule(el);
			module.enableEdit();
		});
		if(fr_dragdrop)
		{
			this.initDragDrop();
		}
	},
	/* Reload document after module save */
	refreshDoc: function(){
		var href = document.URL;
		var req = new Request.HTML({
			url:href,
			onRequest:function(){},
			onComplete:function(responseTree,responseElements, responseHTML, responseJavaScript)
			{
				JEdit.closeBox();	
				$(document.body).setStyle('opacity',0);
				var children = $(document.body).getChildren();
				var tree_length = responseTree.length;
				for(var i=0;i<tree_length;i++)
				{
					var item = responseTree[i];
					if(item.innerHTML)
					{
						children.each(function(child){
							if(child.get('tag') == item.get('tag') && child.get('name') == item.get('name') && child.get('id') == item.get('id') && child.get('class') == item.get('class'))
							{
									child.innerHTML = item.innerHTML;
							}
						});
					}
				}
				/* Reinitialize and enable editing */
				JEdit.init();
				JEdit.enable();
				$(document.body).setStyle('opacity',1);
			}
		});
		req.send();
	},
	/* Get ordering for a position */
	getOrdering: function(module){
		var container = module.getParent();
		var modules = container.getElements('.mod-edit');
		var order = new Array();
		modules.each(function(el){
			var module = new JModule(el);
			order.push(module.id);
		});
		order.join(',');
		return order;
	},
	/* Check if the order of modules in a position is changed */
	isOrderChanged: function(){
		var modules = $$('.mod-edit');
		var order = new Array();
		modules.each(function(el)
		{
			order.push(el.get('alt'));
		});
		JDraggables.serialize(false, function(el,index){
			return el.set('alt',index);
		});
		var new_order = new Array();
		modules.each(function(el)
		{
			new_order.push(el.get('alt'));
		});
		if(order.join(',') == new_order.join(','))
		{return false;}
		else
		{return true;}
	}

});
/**
* @version		$Id: dragndrop.js 418 2009-06-03 17:40:47Z ankit.ahuja $
* @copyright	Copyright (C) 2009 Open Source Matters. All rights reserved.
* @author 		Ankit Ahuja ( as part of Google Summer Of Code 2009 )
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

var JDraggables;
var old_pos;
var old_order;

JEdit.extend({
	/* Initialize drag & drop */
	initDragDrop: function(){
		var modules = $$('.mod-edit');
		var lists = new Array();
		var drag_no = 0;
		modules.each(function(el){
			var parent = el.getParent();
			var flag = true;
			var len = lists.length;
			if(parent.get('class').indexOf('frpos.') != -1)
			{
				for(var i=0;i<len;i++)
				{
					if(lists[i] == '#'+parent.get('id'))
					{
						flag=false;
						break;
					}
				}
				if(flag)
				{
					if(parent.get('id'))
					{
						lists.push('#'+parent.get('id'));
					}
					else
					{
						var drag_id = 'drag-'+drag_no;
						drag_no += 1;
						parent.set('id',drag_id);
						lists.push('#'+parent.get('id'));
					}
				}		
				flag = true;
			}
		});
		var len = lists.length;
		var lists_str = lists;
		lists_str.join(",");
		
		JDraggables = new Sortables(lists_str,{
			clone:false,
			revert:true,
			opacity:1,
			onStart:function(el){
				el.addClass('fr_drag');
				var module = new JModule(el);
				old_pos = module.getPosition();
				old_order = module.getOrder();
			},
			onComplete:function(el){
				el.removeClass('fr_drag');
				if(JEdit.isOrderChanged())
				{	
					JEdit.disableDragDrop();
					var module = new JModule(el);
					var orderlist = JEdit.getOrdering(el);
					var href = "index.php?option=com_frontendeditor&c=module&task=saveOrder&format=raw&position="+module.getPosition()+"&cid="+module.id+"&order="+orderlist+"&"+fr_jtoken+"=1";
					var req = new Request({
						method:'post',
						url:href,
						onRequest:function()
						{
							module.createLoadIcon();
						},
						onComplete:function(response){
							module.loadicon.destroy();
							if(response!=1)
							{
								alert("Module could not be re-positioned!");
								module.revertPosition(old_pos,old_order);
							}
							JEdit.enableDragDrop();
						}
					});
					req.send();
				}
			}
		});

		// Get rid of items that are not modules i.e. do not have class 'mod-edit'
		lists.each(function(list_id){
			var parent = $$(list_id);
			parent.getChildren().each(function(el){
				if(!el.hasClass('mod-edit'))
				{
					JDraggables.removeItems(el);
				}
			});
		});

		JDraggables.serialize(false, function(el,index){
			return el.set('alt',index);
		});
		JDraggables.detach();
	},

	/* Enable drag & drop of modules */
	enableDragDrop: function(){
		$$('.mod-edit').each(function(el){
			if(el.getParent().get('class').indexOf('frpos.') != -1)
			{
				if(!el.hasClass('fr_draggable'))
				{
					el.addClass('fr_draggable');
				}
			}
		});
		JDraggables.attach();
	},

	/* Disable drag & drop of modules */
	disableDragDrop: function(){
		$$('.mod-edit').each(function(el){
 			if(el.getParent().get('class').indexOf('frpos.') != -1)
			{
				el.removeClass('fr_draggable');
			}
		});
		JDraggables.detach();
	}

});
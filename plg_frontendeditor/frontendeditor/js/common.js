/**
* @version		$Id: common.js 418 2009-06-03 17:40:47Z ankit.ahuja $
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
 * Common JavaScript for Frontend Editor
 */

/* List of global variables declared in plugin 
 * fr_toggle: default toggle value
 * fr_articleSelector: the selector for article titles
 * fr_pageTitleSelector: the selector for page title
 * fr_jtoken: token value for forms
 * fr_imagedir: Image dir of the plugin
 * fr_dragdrop: Boolean value to indicate if drag/drop should be enabled
 * fr_coloredit: Whether template should be allowed to set hover color using #fr_title
 * fr_alias: Whether the alias should also be set on article save
*/

/* When DOM is ready */
window.addEvent('domready',function(){
	
	// Initialize frontend editing
	JEdit.init();
	
	// Initialize toggle value
	JEdit.initToggle();
	
	// Initialize shortcut key
	JEdit.initShortcutKey();
		
	//Preload images	
	JEdit.preloadImages();
});

/* Contains all edit methods */
var JEdit = {
	
	isToggleOn:0,
	
	/* Initialize editing */
	init: function(){
		if(this.isDefined("initEditingForArticles", "JEdit"))
		{
			this.initEditingForArticles();
		}
		if(this.isDefined("initEditingForMenuItems", "JEdit"))
		{
			this.initEditingForMenuItems();
		}
		if(this.isDefined("initEditingForModules", "JEdit"))
		{
			this.initEditingForModules();
		}
		if(this.isDefined("initEditingForPageTitles", "JEdit"))
		{
			this.initEditingForPageTitles();
		}
		this.initPopups();
	},
	
	/* Get rid of editing form if it exists on click elsewhere in document */
	docClickEvent: function(e){
		if($('fr_editform'))
		{	
			if($(e.target).get('class') != 'fr_textfield')
			{
				$('fr_cancelbutton').fireEvent('click');
			}
		}
	},
	
	/* Enable editing */
	enable: function(){
		this.enableTitles();
		var toggle = $('fr_toggle');
		if(toggle)
		{
			toggle.addClass('enabled');
			toggle.set('html','turn <u>e</u>diting off');
		}	
		//To make the edit icons visible
		this.showEditIcons();
		this.setTitleHover($$('span.editable'),true);
		if(this.isDefined("initEditingForModules", "JEdit") && this.isDefined("fr_dragdrop"))
		{
			if(fr_dragdrop)
			{
				this.enableDragDrop();
			}
		}
		document.addEvent('click',JEdit.docClickEvent);
		this.isToggleOn = 1;
	},
	
	/* Disable editing */
	disable: function(){
		this.disableTitles();
		/* If a title is being edited, cancel it first */
		if((form = $('fr_editform')))
		{
			$('fr_cancelbutton').fireEvent('click');	
		}

		var toggle = $('fr_toggle');
		if(toggle)
		{
			toggle.removeClass('enabled');
			toggle.set('html','turn <u>e</u>diting on');
		} 
	 	/* Hide edit icons */
		this.hideEditIcons();
		if(title = $$('.fr_title'))
		{
			title.removeClass('fr_title');
		}
		this.setTitleHover($$('span.editable'),false);
		if(this.isDefined("initEditingForModules","JEdit") && this.isDefined("fr_dragdrop"))
		{
			if(fr_dragdrop)
			{
				this.disableDragDrop();
			}
		}
		$(document).removeEvent('click',JEdit.docClickEvent);
		this.isToggleOn = 0;
	},
	
	/* Toggle editing */
	toggle: function(){
		if(this.isToggleOn)
		{
			this.disable();
		}
		else
		{
			this.enable();
		}
		this.setCookie();
	},
	
	/* Filter the input and replace html tags, & etc. with encoded form */
	filterInput: function(input){
		/* To replace < with &lt; and > with &gt; respectively */
		input = input.replace(/</g, "&lt;").replace(/>/g, "&gt;");
		/* To replace & with &amp; */
		input = input.replace(/&/g, "&amp;");
		return input;
	},
	
	/* Unfilter the input and replace encoded form with html tags, &, etc. */
	unfilterInput:function(input){
		/* To replace &lt; with < and &gt; with > respectively */
		input = input.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
		/* To replace &amp; with & */
		input = input.replace(/&amp;/g, "&");
		return input;
	},
	
	/* Destroy edit-in-place form and display text */ 
	displayTitle: function(element,value,type){
		var parent = element.getParent();

		/* Check to see if a link exists. If yes, re-enable it */
		var a_el = element.getParent('a');
		if(a_el)
		{
			a_el.removeClass('fr_hoverClass');
			a_el.set('href',a_el.get('rel'));
			a_el.set('rel',null);
		}
		/* Destroy form element */
		element.destroy();

		/* Create span.editable element */
		var span = new Element('span',{'class':'editable','html':value});
		span.inject(parent,'top');	

		/* Add hover styling to span */
		this.setTitleHover(span,true)
		
		/* Enable editing for span */
		this.enableEditOfTitle(span);
		
		/* If it is a module or menuitem title, enable drag & drop */
		if(type == "module" || type == "menuitem")
		{
			if(this.isDefined("fr_dragdrop"))
			{
				if(fr_dragdrop)
				{
					JEdit.enableDragDrop();	
				}
			}
		}
	},
	
	/* Enable editing for a title */
	enableEditOfTitle: function(el){
		var parent;
		/* if it is a menu-item title*/
		if(el.getParent('ul.menu'))
		{
			var menuitem = new JMenuitem(el.getParent('li'));
			menuitem.createEvent(el);
		}
		/* if it is a module title*/
		else if(parent = el.getParent('.mod-edit'))
		{
			var module = new JModule(parent);
			module.createEvent(el);
		}
		/* if it is a page title */
		else if(this.isDefined("fr_pageTitleSelector"))
		{
			if(el.getParent(fr_pageTitleSelector))
			{
				if(this.isDefined("fr_articleSelector"))
				{
					if(fr_pageTitleSelector != fr_articleSelector)
					{
						var page = new JPage();
						page.createEvent(el);
						return true;
					}
				}
				else
				{
					var page = new JPage();
					page.createEvent(el);
					return true;
				}
			}

		}
		/* if it is an article title */
		if(this.isDefined("fr_articleSelector"))
		{
			if(el.getParent(fr_articleSelector))
			{
				var article = new JArticle(el);
				article.createEvent(el);
			}
		}
	},
	
	/* Send AJAX Request to save title */
	saveTitle: function(element,newvalue,oldvalue,id,type){
		if(newvalue == oldvalue)
		{
			this.displayTitle(element,oldvalue,type);
			return;
		}
		// Disable editing while request is being sent
		this.isToggleOn = 0;

		var parent = element.getParent();
		var onCompleteExec = function(responseTree, responseElements, responseHTML, responseJavaScript)
		{
			JEdit.destroyLoadingIcon(element);
			if(responseHTML!="false" && responseHTML && !responseTree[1])
			{
				if(type == "article")
				{
					JEdit.saveArticleTitleInDOM(element,responseHTML,oldvalue);
				}
				if(type == "menuitem" && task == "savepagetitle")
				{
					JEdit.setPageTitle(responseHTML);
				}
				JEdit.displayTitle(element,responseHTML,type);
			}
			else
			{
				alert('Error! Could not change title');
				JEdit.displayTitle(element,oldvalue,type);
			}
			JEdit.isToggleOn = 1;
		};
		var task;
		if(type=='page')
		{
			task = "savepagetitle";
			type = 'menuitem';
		}
		else
		{
			task = "savetitle";
		}
		var href = "index.php?option=com_frontendeditor&c="+type+"&task="+task+"&format=raw&"+fr_jtoken+"=1";
		if(type == "article")
		{
			href += "&alias="+fr_alias;
		}
		var req = new Request.HTML({
			method:'post',
			url:href,
			data:{'id':id,'value':newvalue},
			onRequest:function(){
				JEdit.displayLoadingIcon(element);
			},
			onComplete: onCompleteExec,
			evalResponse:true
		});
		req.send();
	},
	
	/* Create form for edit-in-place */
	createForm: function(parent,value,id,type){
		/* If another title is being edited, destroy that form first */
		if($('fr_editform'))
		{
			$('fr_cancelbutton').fireEvent('click');
		}
		
		/* If it is a module or menuitem title, disable drag and drop */
		if(type == "module" || type == "menuitem")
		{
			if(this.isDefined("fr_dragdrop"))
			{
				if(fr_dragdrop)
				{
					this.disableDragDrop();
				}
			}
		}
		var span = parent.getElement('span.editable');
		var a_element = span.getParent('a');
		/* Destroy span.editable */
		span.destroy();
		/* Create a new form */
		var form =  new Element('form',{'id':'fr_editform'});
		value = this.unfilterInput(value);
		/* Create text-field */
		var input = new Element('input',{'type':'text','class':'fr_textfield','value':value});
		/* Set the parent's font style */
		this.setTextStyle(input,parent);
		var width = parent.getScrollSize().x;
		if(width)
		{
			input.setStyles({'width':width - 50});
		}
		form.adopt(input);
		if(a_element)
		{
			/* Disable the link */
			a_element.set('rel',a_element.get('href'));
			a_element.set('href',null);
			/* Add class to prevent unwanted link style */
			a_element.addClass('fr_hoverClass');
		}

		var br = new Element('br');
		form.adopt(br);

		/* Create submit and cancel buttons */
		input = new Element('input',{'type':'submit','class':'fr_button submit','id':'fr_submitbutton','value':'Save'});
		form.adopt(input);	

		input = new Element('input',{'type':'button','class':'fr_button cancel','id':'fr_cancelbutton','value':'Cancel'});
		form.adopt(input);	
		
		form.inject(parent,'top');

		input = form.getElement('input');
		input.focus();	

		/* Add event for cancel button */
		$('fr_cancelbutton').addEvent('click',function(e){
			if(JEdit.isToggleOn)
			{
				if(e)
				{
					e.stop();
				}
				JEdit.displayTitle(form,value,type);
			}
		});

		/* Add event for form submit */
		$$('.fr_textfield').addEvent('keydown',function(e){
			if(e.key == 'enter')
			{
				e.stop();
				$('fr_submitbutton').fireEvent('click');
			}
		});

		$('fr_submitbutton').addEvent('click',function(e){
			if(JEdit.isToggleOn)
			{
				if(e)
				{
					e.stop();
				}
				var form = $('fr_editform');
				var input = form.getElement('input');
				/* Filter input text */
				var filteredTitle = JEdit.filterInput(input.get('value'));
				/* Check to make sure input field is not empty */
				if(filteredTitle)
				{
					JEdit.saveTitle(form,filteredTitle,value,id,type);
				}
				else
				{
					alert(type.charAt(0).toUpperCase()+type.slice(1)+" must have a title!");
					input.set('value',value);
				}			
			}
		});

		/* Cancel edit on 'esc' press */
		input.addEvent('keydown',function(e){
			if(JEdit.isToggleOn)
			{
				if(e.key == "esc")
				{
					$('fr_cancelbutton').fireEvent('click');
				}
			}
		});
	},
	
	/* Apply parent's style to element */
	setTextStyle: function(input,parent){
		if(window.opera || navigator.userAgent.indexOf('MSIE')!=-1)
		{
			return;
		}
		var style = parent.getStyles('font-family','font-style','font-weight',
			'font-variant','font-size','text-transform','letter-spacing');
		input.setStyles(style);
	},
	
	/* Initialize toggle */
	initToggle: function(){
		var html;
		// Check if cookie exists. If yes, read the value into isToggleOn
		if(Cookie.read('site_editing_toggle'))
		{
		 	var toggleCookie = Cookie.read('site_editing_toggle');
			if(toggleCookie == "true")
			{
				this.isToggleOn = 1;
			}
			else
			{
				this.isToggleOn = 0;
			}
		}
		else
		{
			this.isToggleOn = fr_toggle;
		}
		if(this.isToggleOn)
		{
			html = "turn <u>e</u>diting off";
		}
		else
		{
			html = "turn <u>e</u>diting on";
		}
		var bar = new Element('div',{'id':'fr_togglebar'});
		var toggle = new Element('div',{'id':'fr_toggle','html':html});
		bar.setStyles({'position':'fixed',
		'top':0
		});
		bar.adopt(toggle);
		bar.inject($(document.body),'top');
		toggle.addEvent('click',function(){
			JEdit.toggle();
		});
		if(this.isToggleOn)
		{
			this.enable();
		}
		this.setCookie();
	},
	
	/* Display loading icon while saving title */
	displayLoadingIcon: function(element){
		/* fr_imagedir is global var containing location of plugin */
		var new_el = new Element('span',{'html':"<img src='"+fr_imagedir+"ajax-loader.gif' title='Saving...' >",'class':'fr_loadingicon'});
		element.adopt(new_el);
	},
	
	/* Destroy the loading icon after save */
	destroyLoadingIcon: function(element){
	 	element.getElement('span.fr_loadingicon').destroy();
	},
	
	/* Remove title attribute of span.editable elements */
	disableTitles: function(){
		var span_elements = $(document.body).getElements('span.editable');
		span_elements.each(function(element){
			element.set('title',null);
		});
	},
	
	/* Add title attributes to span.editable elements */
	enableTitles: function(){
		var span_elements = $(document.body).getElements('span.editable');
		span_elements.each(function(element){
			element.set('title','click to edit');
		});
	},
	
	/* Set toggle cookie */
	setCookie: function(){
		var toggleCookie = Cookie.write('site_editing_toggle',this.isToggleOn ? "true":"false",{
			path:'/',
			duration: 0
		});
	},
	
	/* Show edit icons */
	showEditIcons: function(){
		$$('.fr_editicon').setStyles({'display':'inline'});
	},
	
	/* Hide edit icons */
	hideEditIcons: function(){
		$$('.fr_editicon').setStyles({'display':'none'});
	},
	
	/* Initialize modal popup */
	initPopups: function(){
		SqueezeBox.initialize();
		SqueezeBox.assign($$('.fr_editicon'),{
			parse:'rel',
			closableByBckClick:false,
			closeBtn: false,
			closableByEsc: false
		});
	},
	
	/* Event to take place when a modal popup is opened */
	onModalOpen: function(){
		$('fr_togglebar').setStyle('display','none');
	},
	
	/* Close modal popup */
	closeBox: function(){
		$('fr_togglebar').setStyle('display','block');
		SqueezeBox.close();
		/* return focus to parent document */
		window.focus();
	},
	
	/* Display loading icon in modal popup or in place of article */
	loadingIcon: function(el){
		if(el)
		{
			var loading_el = new Element('span',{'html':"<img src='"+fr_imagedir+"ajax-loader.gif' title='Saving...' >",'class':'fr_loadingicon'});
			loading_el.inject(el,'before');
		}
		else
		{
			SqueezeBox.toggleLoading(true);
		}
	},
	
	/* To add/remove fr_title class from element(s) */
	setTitleHover: function(elements, toggle){
		var onEnter = function(e){
			this.addClass('fr_title');
			if(!fr_coloredit)
			{
				this.set('id','fr_title');
			}
		}
		var onLeave = function(e){
			this.removeClass('fr_title');
			if(!fr_coloredit)
			{
				this.set('id',null);
			}
		}	
		if(toggle)
		{
			elements.addEvent('mouseenter',onEnter);
			elements.addEvent('mouseleave',onLeave);
		}
		else
		{
			elements.removeEvents('mouseenter',onEnter);
			elements.removeEvents('mouseleave',onLeave);
		}
	},
	
	/* Preload images */
	preloadImages: function(){
		// Preloading save and close images
		var save_img = new Image();
		save_img.src = fr_imagedir+"check_small.png";
		var close_img = new Image();
		close_img.src = fr_imagedir+"cross_small.png";
	},
	
	/* Initialize shortcut key 'e' to toggle editing */
	initShortcutKey: function(){
		// Add event for shortcut key 'e'
		document.addEvent('keypress',function(e){
			if(e.key == 'e')
			{	
				var target = $(e.target);
				if(target && target!=document)
				{
					var tag = target.get('tag');
					if(tag != 'input' && tag != 'textarea')
					{
						e.stop();
						JEdit.toggle();
					}
				}
				else
				{
					e.stop();
					JEdit.toggle();
				}
			}
		});
	},
	
	removePrefix: function(str, prefix) {
		return str.substring(str.indexOf(prefix)+prefix.length,str.length+1);
	},
	isDefined: function(variable,object){
		object = (typeof object == "undefined") ? 'window': object;
		return (typeof(eval(object)[variable])!= 'undefined');
	},
	extend: function(properties) {
		return $extend(this, properties);
	}
};
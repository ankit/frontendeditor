/**
* @version		$Id: article.js 418 2009-06-03 17:40:47Z ankit.ahuja $
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
 * JavaScript to perform editing of Article titles
*/

var ret = null;
/** 
 * @class
 */
var JArticle = new Class({
	/* Initialize editing */
	initialize: function(el){
		this.el = el;
		this.a_el = this.el.getElement('a');
		this.parent = this.a_el ? this.a_el: this.el;
		this.title = this.getTitle();
		this.id = this.getId();
	},
	/* Get article id */
	getId: function(){
		if(this.title)
		{
			var parent = this.el.getParent();
			while(parent)
			{
				var spans = parent.getElements('.fr_article_id');
				var len = spans.length;
				for(var i=0;i<len;i++)
				{
					var span = spans[i];
					if(span.get('title') == JEdit.unfilterInput(this.title))
					{
						return JEdit.removePrefix(span.get('id'),'article.');
					}
				}
				parent = parent.getParent();
			}
		}
		return null;
	},
	/* Get article title */
	getTitle: function(){
		return this.parent.get('html').trim();
	},
	/* Enable editing */
	enableEdit: function(){
		this.parent.set('html','');
		var span = new Element('span',{'class':'editable','html':this.title});
		this.parent.adopt(span);
		this.createEvent(span);
		this.createIcon();
	},
	/* Create article edit icon */
	createIcon: function(){
		var href= 'index.php?view=article&task=edit&ret='+ret+'&tmpl=component&id='+this.id;
		var link = new Element('a',{'class':'fr_editicon','href':href,'html':'',"rel":"{ handler:'iframe', iframePreload:true, size:{x:800,y:590}, sizeLoading:{x:800,y:590}, onOpen: JEdit.onModalOpen }"});
		this.el.adopt(link);
	},
	/* Create onclick event for article title */
	createEvent: function(el){
		var a_el = el.getParent('a');
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
				JEdit.createForm(this.getParent(),this.get('html'),id,"article");
			}
		}.bindWithEvent(el,this.id));
	}
});


JEdit.extend({
	/* Initialize editing for articles */
	initEditingForArticles: function(){

		/* Get the encoded URL */
		ret = this.base64_encode(document.URL);	

		/* Get all elements containing article titles */
		var articles = $$(fr_articleSelector);
		articles.each(function(el){
			var article = new JArticle(el);
			article.enableEdit();
		});

		/* Create edit icons for articles whose titles are disabled */
		var articles = $$(".fr_article_id[rel=0]");
		this.createArticleIcon(articles);

		/* Hack to get rid of current edit icons */
		this.hideDefaultArticleEditIcons();
	},
	
	/* Create edit icons for article(s) whose titles are not visible */
	createArticleIcon: function(articles){
		articles.each(function(article){
			var a_id = article.get('id');
			var href= 'index.php?view=article&task=edit&ret='+ret+'&tmpl=component&id='+JEdit.removePrefix(a_id,'article.');
			var link = new Element('a',{'class':'fr_editicon','href':href,'html':'',"rel":"{ handler:'iframe', iframePreload:true, size:{x:800,y:590}, sizeLoading:{x:800,y:590}, onOpen: JEdit.onModalOpen }"});
			link.inject(article.getParent(),'before');
		});
	},
	
	/* Update the title value in the span element containing article id */
	saveArticleTitleInDOM: function(element,value,oldvalue)
	{
		var parent = element.getParent();
		var flag = 1;
		while(parent && flag==1)
		{
			var span_elements = parent.getElements('span');
			if(span_elements)
			{
				span_elements.each(function(span){
					if(flag==1)
					{
						var span_id = span.get('id');
						if(span_id)
						{
							if(span_id.substring(0,7) == 'article' && span.get('title') == oldvalue)
							{
								span.set('title',value);
								flag=0;
							}
						}
					}
				});
			}
			parent = parent.getParent();
		}
	},
	
	/* Load the article after save */
	loadArticle: function(id){
		this.closeBox();
		var parent = $('article.'+id).getParent();
		this.loadingIcon(parent);
		parent.setStyles({'opacity':0});
		var href;
		var uri = document.URL;
		href = uri;
		var req = new Request.HTML({
			url:href,
			onRequest:function(){
			},
			onComplete:function(responseTree,responseElements, responseHTML, responseJavaScript){
				var top_el = responseElements[0];
				var parent;
				var html;
				var entire_page = false;
				// Check if article is visible
				var span_el = responseElements.filter(function(el,index){
					return el.get('id') == 'article.'+id;
				});
				if(span_el != "")
				{
					html = span_el.getParent().get('html');
					parent = $('article.'+id).getParent();
				}
				else
				{
					var _class = top_el.get('class');
					html = top_el.getParent().get('html');
					parent = $$('.'+_class).getParent();
					entire_page = true;
				}
				parent.set('html',html);
				if(entire_page)
				{
					this.initEditingForArticles();
					if(JEdit.isDefined("initEditingForPageTitles","JEdit"))
					{
						JEdit.initEditingForPageTitles();
					}
				}
				else
				{
					// Enable editing for only the edited article
					var articles = $('article.'+id).getParent().getElements(fr_articleSelector);
					articles.each(function(el){
						var article = new JArticle(el);
						article.enableEdit();
					});
					// For articles with titles not shown
					JEdit.createArticleIcon($('article.'+id).getParent().getElements('.fr_edit_icon[rel=0]'));
					JEdit.showEditIcons();
					JEdit.hideDefaultArticleEditIcons();
					parent = $('article.'+id).getParent();
				}
				JEdit.initPopups();
				JEdit.setTitleHover($$('span.editable'),true);
				JEdit.enableTitles();
				parent.getPrevious().destroy();
				parent.fade('in');
			}
		});
		req.send();
	},
	
	/* Hide default edit icons for articles */
	hideDefaultArticleEditIcons: function(){
		$$('.contentpaneopen img[alt=edit]').setStyle('display','none');
		$$(fr_editIconSelector).setStyle('display','none');
		$$('.contentpaneopen_edit img[alt=edit]').setStyle('display','none');
	},
	
	base64_encode: function( data ) {
	    // http://kevin.vanzonneveld.net
	    // +   original by: Tyler Akins (http://rumkin.com)
	    // +   improved by: Bayron Guevara
	    // +   improved by: Thunder.m
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // +   bugfixed by: Pellentesque Malesuada
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // -    depends on: utf8_encode
	    // *     example 1: base64_encode('Kevin van Zonneveld');
	    // *     returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='

	    // mozilla has this native
	    // - but breaks in 2.0.0.12!
	    //if (typeof this.window['atob'] == 'function') {
	    //    return atob(data);
	    //}

	    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0, ac = 0, enc="", tmp_arr = [];

	    if (!data) {
	        return data;
	    }

	    data = this.utf8_encode(data+'');

	    do { // pack three octets into four hexets
	        o1 = data.charCodeAt(i++);
	        o2 = data.charCodeAt(i++);
	        o3 = data.charCodeAt(i++);

	        bits = o1<<16 | o2<<8 | o3;

	        h1 = bits>>18 & 0x3f;
	        h2 = bits>>12 & 0x3f;
	        h3 = bits>>6 & 0x3f;
	        h4 = bits & 0x3f;

	        // use hexets to index into b64, and append result to encoded string
	        tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
	    } while (i < data.length);

	    enc = tmp_arr.join('');

	    switch( data.length % 3 ){
	        case 1:
	            enc = enc.slice(0, -2) + '==';
	        break;
	        case 2:
	            enc = enc.slice(0, -1) + '=';
	        break;
	    }

	    return enc;
	},

	utf8_encode: function( argString ) {
	    // http://kevin.vanzonneveld.net
	    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // +   improved by: sowberry
	    // +    tweaked by: Jack
	    // +   bugfixed by: Onno Marsman
	    // +   improved by: Yves Sucaet
	    // +   bugfixed by: Onno Marsman
	    // *     example 1: utf8_encode('Kevin van Zonneveld');
	    // *     returns 1: 'Kevin van Zonneveld'

	    var string = (argString+'').replace(/\r\n/g, "\n").replace(/\r/g, "\n");

	    var utftext = "";
	    var start, end;
	    var stringl = 0;

	    start = end = 0;
	    stringl = string.length;
	    for (var n = 0; n < stringl; n++) {
	        var c1 = string.charCodeAt(n);
	        var enc = null;

	        if (c1 < 128) {
	            end++;
	        } else if((c1 > 127) && (c1 < 2048)) {
	            enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
	        } else {
	            enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
	        }
	        if (enc !== null) {
	            if (end > start) {
	                utftext += string.substring(start, end);
	            }
	            utftext += enc;
	            start = end = n+1;
	        }
	    }

	    if (end > start) {
	        utftext += string.substring(start, string.length);
	    }

	    return utftext;
	}
});
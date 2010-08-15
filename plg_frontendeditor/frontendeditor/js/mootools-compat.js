/* 
 * JavaScript to add compatibility for any mootools 1.11 code 
 * via http://www.dwightjack.com/diary/2009/06/07/use-mootools-122-in-joomla-15/
 */

Native.implement([Element, Document], {

	getElementsByClassName: function(className){
		return this.getElements('.' + className);
	},

	getElementsBySelector: function(selector){
		return this.getElements(selector);
	}

});

Elements.implement({

	filterByTag: function(tag){
		return this.filter(tag);
	},

	filterByClass: function(className){
		return this.filter('.' + className);
	},

	filterById: function(id){
		return this.filter('#' + id);
	},

	filterByAttribute: function(name, operator, value){
		return this.filter('[' + name + (operator || '') + (value || '') + ']');
	}

});

var $E = function(selector, filter){
	return ($(filter) || document).getElement(selector);
};

var $ES = function(selector, filter){
	return ($(filter) || document).getElements(selector);
};
Class.empty = $empty;

//legacy .extend support

Class.prototype.extend = function(properties){
	properties.Extends = this;
	return new Class(properties);
};
window.extend = document.extend = function(properties){
	for (var property in properties) this[property] = properties[property];
};

window[Browser.Engine.name] = window[Browser.Engine.name + Browser.Engine.version] = true;

window.ie = window.trident;
window.ie6 = window.trident4;
window.ie7 = window.trident5;
$A = function(iterable, start, length){
	if (Browser.Engine.trident && $type(iterable) == 'collection'){
		start = start || 0;
		if (start < 0) start = iterable.length + start;
		length = length || (iterable.length - start);
		var array = [];
		for (var i = 0; i < length; i++) array[i] = iterable[start++];
		return array;
	}
	start = (start || 0) + ((start < 0) ? iterable.length : 0);
	var end = ((!$chk(length)) ? iterable.length : length) + start;
	return Array.prototype.slice.call(iterable, start, end);
};

(function(){
	var natives = [Array, Function, String, RegExp, Number];
	for (var i = 0, l = natives.length; i < l; i++) natives[i].extend = natives[i].implement;
})();
Event.keys = Event.Keys;
Element.extend = Element.implement;

Elements.extend = Elements.implement;

Element.implement({

	getFormElements: function(){
		return this.getElements('input, textarea, select');
	},

	replaceWith: function(el){
		el = $(el);
		this.parentNode.replaceChild(el, this);
		return el;
	},

	removeElements: function(){
		return this.dispose();
	}

});

Element.alias({'dispose': 'remove', 'getLast': 'getLastChild'});

Element.implement({

	getText: function(){
		return this.get('text');
	},

	setText: function(text){
		return this.set('text', text);
	},

	setHTML: function(){
		return this.set('html', arguments);
	},

	getHTML: function(){
		return this.get('html');
	},

	getTag: function(){
		return this.get('tag');
	}

});
Element.implement({

	setOpacity: function(op){
		return this.set('opacity', op);
	}

});
Fx.implement({

	custom: function(from, to){
		return this.start(from, to);
	},

	clearTimer: function(){
		return this.cancel();
	},

	stop: function(){
		return this.cancel();
	}

});

Fx.Base = Fx;
Fx.Styles = Fx.Morph;

Element.implement({

	effects: function(options){
		return new Fx.Morph(this, options);
	}

});
Fx.Scroll.implement({

	scrollTo: function(y, x){
		return this.start(y, x);
	}

});
Fx.Style = function(element, property, options){
	return new Fx.Tween(element, $extend({property: property}, options));
};

Element.implement({

	effect: function(property, options){
		return new Fx.Tween(this, $extend({property: property}, options));
	}

});
Array.implement({

	copy: function(start, length){
		return $A(this, start, length);
	}

});

Array.alias({erase: 'remove', combine: 'merge'});
Function.extend({

	bindAsEventListener: function(bind, args){
		return this.create({'bind': bind, 'event': true, 'arguments': args});
	}

});

Function.empty = $empty;
Hash.alias({getKeys: 'keys', getValues: 'values', has: 'hasKey', combine: 'merge'});
var Abstract = Hash;
Object.toQueryString = Hash.toQueryString;

var XHR = new Class({

	Extends: Request,

	options: {
		update: false
	},

	initialize: function(url, options){
		this.parent(options);
		this.url = url;
	},

	request: function(data){
		return this.send(this.url, data || this.options.data);
	},

	send: function(url, data){
		if (!this.check(arguments.callee, url, data)) return this;
		return this.parent({url: url, data: data});
	},

	success: function(text, xml){
		text = this.processScripts(text);
		if (this.options.update) $(this.options.update).empty().set('html', text);
		this.onSuccess(text, xml);
	},

	failure: function(){
		this.fireEvent('failure', this.xhr);
	}

});

var Ajax = XHR;
JSON.Remote = new Class({

	options: {
		key: 'json'
	},

	Extends: Request.JSON,

	initialize: function(url, options){
		this.parent(options);
		this.onComplete = $empty;
		this.url = url;
	},

	send: function(data){
		if (!this.check(arguments.callee, data)) return this;
		return this.parent({url: this.url, data: {json: Json.encode(data)}});
	},

	failure: function(){
		this.fireEvent('failure', this.xhr);
	}

});
Cookie.set = function(key, value, options){
	return new Cookie(key, options).write(value);
};

Cookie.get = function(key){
	return new Cookie(key).read();
};

Cookie.remove = function(key, options){
	return new Cookie(key, options).dispose();
};

/**
 * Some other custom compatibility codes...
 */
Element.implement({

	getValue: function(){
		return this.get('value');
	},
	getSelected: function(){
		return new Elements($(this).getElements('option').filter(function(option){
			return option.selected;
		}));
	}
});
Json = new Hash ({
	evaluate : function (string,secure) {
		return JSON.decode(string,(secure || false));
	},
	toString : function (object) {
		return JSON.encode(object);
	}
});

window.onDomReady = function(fn){
   return this.addEvent('domready', fn);
};
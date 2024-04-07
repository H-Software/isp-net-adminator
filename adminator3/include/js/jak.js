/**
 * @overview kod pro beh stranky
 * @version 1.0
 * @author aichi
 */


/**
 * inicializacni metoda navesujici objekt dynamickeho preklikavani na menu
 */
function initializeTabs(idMenu, contentBox) {
	var a = SZN.gEl(idMenu).getElementsByTagName('a');
	for (var i = 0; i < a.length; i++) {
		new SZN.DynamicTab(a[i], contentBox);
	}
}

/**
 * trida DynamicTab je navesena na kazdy odkaz v menu
 * a zajistuje dynamicke preklikavani
 */
SZN.DynamicTab = SZN.ClassMaker.makeClass({
	NAME: "DynamicTab",
	VERSION: "1.0",
	CLASS: "class"
});

/**
 * konstruktor kdy je nabindovana metoda click na onClick polozek v menu
 */
SZN.DynamicTab.prototype.$constructor = function(element, contentBox) {
	this.ec = [];

	this.element = SZN.gEl(element);
	this.contentBox = SZN.gEl(contentBox);
	this.ec.push(SZN.Events.addListener(this.element, 'click', this, 'click', false, true));
	this.ec.push(SZN.Events.addListener(window, 'unload', this, '$destructor', false, true));
	
	//pokud menime ajaxem zalozky, musime dynamicky i volat merici kod
	this.netMonitorCounter = new SZN.NetMonitorCounter();
	//promenna do ktere si ukladam jakou stranku uzivatel pres XHR chtel a az bude zobrazena aktivuji prislusny kod
	this.loadedXHRUrl = 'def'; 
}

SZN.DynamicTab.prototype.$destructor = function() {
	for (var i=0;i<this.ec.length;i++) {
		SZN.Events.removeListener(this.ec[i]);
	}
}

/**
 * metoda objektu je volana pri kliknuti na polozku menu
 */
SZN.DynamicTab.prototype.click = function(e, elm){
	SZN.Events.cancelDef(e);
	var li = this.element.parentNode.parentNode.getElementsByTagName('li');
	for(var i = 0; i < li.length; i++) {
		SZN.Dom.removeClass(li[i], 'selected');
	}
	SZN.Dom.addClass(this.element.parentNode, 'selected');
	
	//var url = this.element.href + 'ajax.html';
	var url = '/ajax.html?page=' + this._getPath(this.element.href).directory;
	
	this.loadedXHRUrl = this._getPath(this.element.href).directory;
	
	var rq = new SZN.HTTPRequest();
	rq.setMethod("get");
	rq.setFormat("txt");
	rq.setMode("async");
	rq.send(url,this,"_response");
}

/**
 * metoda je volana pri navratu z httpRequestu
 */
SZN.DynamicTab.prototype._response = function(txt){
	SZN.Dom.clear(this.contentBox);
	this.contentBox.innerHTML = txt;
	this.netMonitorCounter.makeHit(this.loadedXHRUrl);
}

/**
 * vraci cestu mezi prvnim a poslednim lomitkem
 */
SZN.DynamicTab.prototype._getPath = function(url) {
	return parseUri(url);
}






SZN.NetMonitorCounter = SZN.ClassMaker.makeClass({
	NAME : 'NetMonitorCounter',
	VERSION : '1.0',
	CLASS : 'class'
});

SZN.NetMonitorCounter.prototype.COUNTER_TYPE = {
	// dynamicke zalozky
	'/'  			: 'coiVJM_uZWenFcgwHikmlvUHfdqd1uNAxfdfvUEWsbX.p7', // home page
	'/download/'	: 'zIibI.grwJQCnEDVudEKOadFHZGgHyLwoVxPO5rLgSH.i7', // download
	'/example/'		: 'zIg1xfgrVS0C.JhpOa3ukqdFfXCgH_M0axnlOHgi2az.97', // example rozcestnik
	'/manual/' 		: 'coiVhs_uZa4NHyib7wxO7.UH356d1jtM8CeYUj4sftn.P7', // manual
	def 			: false												// NIC
};

SZN.NetMonitorCounter.prototype.NetMonitorCounter = function(){

};

SZN.NetMonitorCounter.prototype.$destructor = function(){
	for(var i in this){
		this[i] = null;
	}
};

SZN.NetMonitorCounter.prototype._getType = function(type){
	return (typeof this.COUNTER_TYPE[type] != 'undefined') ? this.COUNTER_TYPE[type] : this.COUNTER_TYPE['def'];
};

SZN.NetMonitorCounter.prototype.makeHit = function(countType){
	var tp = this._getType(countType);
	if(tp && (typeof(pp_gemius_hit) == "function")){
		pp_gemius_hit(tp);
	}
};











/*
parseUri 1.2.1
(c) 2007 Steven Levithan <stevenlevithan.com>
MIT License
*/

function parseUri (str) {
var	o   = parseUri.options,
	m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
	uri = {},
	i   = 14;

while (i--) uri[o.key[i]] = m[i] || "";

uri[o.q._name] = {};
uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
	if ($1) uri[o.q._name][$1] = $2;
});

return uri;
};

parseUri.options = {
strictMode: false,
key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
q:   {
	_name:   "queryKey",
	parser: /(?:^|&)([^&=]*)=?([^&]*)/g
},
parser: {
	strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
	loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
}
};

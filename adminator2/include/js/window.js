/*
Licencováno pod MIT Licencí

© 2008 Seznam.cz, a.s.

Tímto se uděluje bezúplatná nevýhradní licence k oprávnění užívat Software,
časově i místně neomezená, v souladu s příslušnými ustanoveními autorského zákona.

Nabyvatel/uživatel, který obdržel kopii tohoto softwaru a další přidružené 
soubory (dále jen „software“) je oprávněn k nakládání se softwarem bez 
jakýchkoli omezení, včetně bez omezení práva software užívat, pořizovat si 
z něj kopie, měnit, sloučit, šířit, poskytovat zcela nebo zčásti třetí osobě 
(podlicence) či prodávat jeho kopie, za následujících podmínek:

- výše uvedené licenční ujednání musí být uvedeno na všech kopiích nebo 
podstatných součástech Softwaru.

- software je poskytován tak jak stojí a leží, tzn. autor neodpovídá 
za jeho vady, jakož i možné následky, ledaže věc nemá vlastnost, o níž autor 
prohlásí, že ji má, nebo kterou si nabyvatel/uživatel výslovně vymínil.



Licenced under the MIT License

Copyright (c) 2008 Seznam.cz, a.s.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

/**
 * @overview window
 * @version 1.0
 * @author zara
*/   

SZN.Window = SZN.ClassMaker.makeClass({
	NAME: "Window",
	VERSION: "1.0",
	CLASS: "class"
});
/**
 * @class Okenko se stinem, neboli prosta tabulka s deviti prvky
 * @name SZN.Window
 * @param {Object} optObj asociativni pole parametru, muze obsahovat tyto hodnoty:
 *	 <ul>
 *		<li><em>imagePath</em> - cesta k obrazkum</li>
 *		<li><em>imageFormat</em> - pripona obrazku (png/gif/jpg)</li>
 *		<li><em>sizes</em> - pole ctyr velikosti okraju, dle hodinovych rucicek</li>
 *   <ul>
 * @constructor
 */
SZN.Window.prototype.$constructor = function(optObj) {
	this.options = {
		imagePath:"/img/shadow-",
		imageFormat:"png",
		sizes:[6,6,6,6]
	}
	for (var p in optObj) { this.options[p] = optObj[p]; }

	/**
	 * @field content vnitrni bunka, do ktere se da pridavat dalsi obsah
	 */
	this.content = SZN.cEl("div",false,"window-content",{position:"relative"});;
	/**
	 * @field vnejsi prvek
	 */
	this.container = false;
	this._buildDom();
}

/**
 * @method  Tvorba DOM stromu.
 */
SZN.Window.prototype._buildDom = function() {
	var imageNames = [
		["lt","t","rt"],
		["l","","r"],
		["lb","b","rb"]
	]
	this.container = SZN.cEl("div",false,"window-container",{position:"relative",zIndex:10});
	var table = SZN.cEl("table",false,false,{borderCollapse:"collapse",position:"relative"});
	var tbody = SZN.cEl("tbody");
	SZN.Dom.append([table,tbody],[this.container,table]);
	
	for (var i=0;i<3;i++) {
		var tr = SZN.cEl("tr");
		tbody.appendChild(tr);
		for (var j=0;j<3;j++) {
			var td = SZN.cEl("td");
			td.style.padding = "0px";
			td.style.margin = "0px";
			if (i == 1 && j == 1) { 
				td.appendChild(this.content); 
			}
			var im = imageNames[i][j];
			
			if (im) { /* image */
				var path = this.options.imagePath + im + "." + this.options.imageFormat;
				
				//hujer pridal
				var bname = navigator.appName
				
				//puvodni if (SZN.Browser.klient == "ie" && this.options.imageFormat.match(/png/i)) {
				if (bname == "ie" && this.options.imageFormat.match(/png/i)) {
					td.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+path+"',sizingMethod='scale')";
				} else {
					td.style.backgroundImage = "url("+path+")";
				} /* not ie */
			}
			
			/* dimensions */
			if (i == 0) { td.style.height = this.options.sizes[0]+"px"; }
			if (i == 2) { td.style.height = this.options.sizes[2]+"px"; }
			if (j == 0) { td.style.width = this.options.sizes[3]+"px"; }
			if (j == 2) { td.style.width = this.options.sizes[1]+"px"; }
			if (j == 1 && i != 1) { td.style.width = "auto"; }
			
			tr.appendChild(td);
		} /* for all columns */
	} /* for all rows */
}

/**
 * @method Explicitni desktruktor. Smaze vsechny vlastnosti.
 */
SZN.Window.prototype.$destructor = function() {
	for (var p in this) { this[p] = null; }
}

/**
 * Ukazani okna
 */
SZN.Window.prototype.show = function() {
	//this.container.style.visibility = "visible";
	this.container.style.display = "";
}

/**
 * Schovani okna
 */
SZN.Window.prototype.hide = function() {
	//this.container.style.visibility = "hidden";
	this.container.style.display = "none";
}


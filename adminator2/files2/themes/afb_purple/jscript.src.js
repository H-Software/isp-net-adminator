//position the footer at the bottom
function setFooter() {
	if (document.height) {
		var dheight = document.height;
	} else {
		var dheight = document.body.offsetHeight;
	}
	var cheight = xClientHeight();
	var footer = xGetElementById("footer");
	
	BrowserDetect.init();
	
	if (footer) {
		if (cheight > dheight) {
			//repoisition!	
			footer.style.position = "absolute";
			if (BrowserDetect.browser == 'Explorer' && BrowserDetect.version == 6 && xGetElementById('sidemenu')) {
				footer.style.top = (cheight-21) + "px";
			} else {
				footer.style.top = (cheight-38) + "px";
			}
			footer.style.left = "0";
			footer.style.visibility = "visible";
		}
		footer.style.visibility = "visible";
	}
	return null;
}

function setMainContent() {
	if (document.height) {
		var dheight = document.height;
	} else {
		var dheight = document.body.offsetHeight;
	}
	
	var cheight = xClientHeight();
//	alert ("dheight: " + dheight + ", cheight: " + cheight);
	if (cheight > (dheight)) {
		//repoisition!
		
		var side = xGetElementById("sidemenu");
		if (side) {
			side.style.height = (cheight-169) + "px";
		}
		var main = xGetElementById("main");
		if (main) {
			var theclass = ' '+main.className+' ';
			if (theclass.indexOf(' wide ') == -1) {
				main.style.height = (cheight-179) + "px";
			} else {
				main.style.height = (cheight-164) + "px";
			}
		}
	}
	return null;
}

function setLogin() {
	if (document.height) {
		var dheight = document.height;
	} else {
		var dheight = document.body.offsetHeight;
	}
	
	var cheight = xClientHeight();		
	var login = xGetElementById("login");
	if (login) {
		login.style.marginTop = ((cheight/2)-220) + "px";
	}
	return null;
}

function windowload() {
	var result = setFooter();
	var result = setMainContent();
	var result = setLogin();

	var s = xGetElementById('shares');
	if (s) {
		s.style.display = 'block';
	}
	return null;
}


/* ********************************************************************************************
UNFUNCTIONAL CODE - gets run asap.
******************************************************************************************** */
var crap= addEvent(window, "load", windowload, false);



var BrowserDetect = {
	init: function () {
		this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
		this.version = this.searchVersion(navigator.userAgent)
			|| this.searchVersion(navigator.appVersion)
			|| "an unknown version";
		this.OS = this.searchString(this.dataOS) || "an unknown OS";
	},
	searchString: function (data) {
		for (var i=0;i<data.length;i++)	{
			var dataString = data[i].string;
			var dataProp = data[i].prop;
			this.versionSearchString = data[i].versionSearch || data[i].identity;
			if (dataString) {
				if (dataString.indexOf(data[i].subString) != -1)
					return data[i].identity;
			}
			else if (dataProp)
				return data[i].identity;
		}
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index == -1) return;
		return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
	},
	dataBrowser: [
		{ 	string: navigator.userAgent,
			subString: "OmniWeb",
			versionSearch: "OmniWeb/",
			identity: "OmniWeb"
		},
		{
			string: navigator.vendor,
			subString: "Apple",
			identity: "Safari"
		},
		{
			prop: window.opera,
			identity: "Opera"
		},
		{
			string: navigator.vendor,
			subString: "iCab",
			identity: "iCab"
		},
		{
			string: navigator.vendor,
			subString: "KDE",
			identity: "Konqueror"
		},
		{
			string: navigator.userAgent,
			subString: "Firefox",
			identity: "Firefox"
		},
		{
			string: navigator.vendor,
			subString: "Camino",
			identity: "Camino"
		},
		{		// for newer Netscapes (6+)
			string: navigator.userAgent,
			subString: "Netscape",
			identity: "Netscape"
		},
		{
			string: navigator.userAgent,
			subString: "MSIE",
			identity: "Explorer",
			versionSearch: "MSIE"
		},
		{
			string: navigator.userAgent,
			subString: "Gecko",
			identity: "Mozilla",
			versionSearch: "rv"
		},
		{ 		// for older Netscapes (4-)
			string: navigator.userAgent,
			subString: "Mozilla",
			identity: "Netscape",
			versionSearch: "Mozilla"
		}
	],
	dataOS : [
		{
			string: navigator.platform,
			subString: "Win",
			identity: "Windows"
		},
		{
			string: navigator.platform,
			subString: "Mac",
			identity: "Mac"
		},
		{
			string: navigator.platform,
			subString: "Linux",
			identity: "Linux"
		}
	]

};

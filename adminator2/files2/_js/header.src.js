/*
  This little peice of ingenious code builds on the protype to add trim() functionality to strings,
	thus trimming whitespace from either the left, right, or both sides of the string.
	
	example of usage:
	var sOriginal=' text ';
	sTrim = sOriginal.trim();
	sLTrim=sOriginal.ltrim();
	sRTrim=sOriginal.rtrim();
	
	After execution: 
	sOriginal is ' text ' 
	sTrim is 'text' 
	sLTrim is 'text ' 
	sRTrim is ' text'
	
	Found this years ago on the web - can't remember where..
*/
String.prototype.trim=function(){ 
	return this.replace(/^\s*|\s*$/g,'');
}
String.prototype.ltrim=function(){
	return this.replace(/^\s*/g,'');
}
String.prototype.rtrim=function(){
	return this.replace(/\s*$/g,'');
}



function smartClassName(existing_classname,new_class,replaced_class) {
	//check if browser is compatible
	if (!document.getElementsByTagName) {
		return null;
	}
	if (!existing_classname) {
		existing_classname = ' ';
	}
	//check if vars defined
	if (!existing_classname || !new_class || typeof existing_classname != "string" || typeof new_class != "string") {
		//alert("Incorrect number of parameters for smartClassName function, or parameters are not strings");
		return null;
	}
	
	//code to change and replace strings
	existing_classname = ' ' + existing_classname.replace(/^\s*|\s*$/g,'') + ' ';
	new_class = new_class.replace(/^\s*|\s*$/g,'');
	
	var new_classname = existing_classname;
	if (replaced_class && existing_classname.indexOf(' ' + replaced_class + ' ') != -1 && typeof replaced_class == "string") {
		//found something to replace! or in this case, remove.
		new_classname = existing_classname.replace(' ' + replaced_class.replace(/^\s*|\s*$/g,'') + ' ',' ');
	}
	//add class
	//check if not already there
	if (new_classname.indexOf(' ' + new_class + ' ') == -1) {
		//not found, add it
		new_classname = new_classname + new_class;
	}
	//return the changed text!
	return new_classname.replace(/^\s*|\s*$/g,''); //trimmed whitespace
}



function searchUp(elm,findElm) {
	//this function searches the dom tree upwards for the findElm node starting from elm.
	//check if elm is reference
	if(typeof(elm) == 'string') {
		elm = xGetElementById(elm);
	}
	//search up
	//get the parent findElm
	while (elm.nodeName.toLowerCase() != findElm && elm.nodeName.toLowerCase() != 'body')
		elm = elm.parentNode;
	return elm;
} /* end searchUp function */



/*
This function asks the user a quesiton, and based on the answer, directs the user to a new url.

question - string, question to present to user
onyes - string, url or empty 
onno - string, url or empty
*/
function AskQuestion(question,onyes,onno) {
	var where_to = confirm(question);
	if (where_to == true) {
		if (onyes && onyes != "") {
			window.location=onyes;
		}
	} else {
		if (onno && onno != "") {
			window.location=onno;
		}
	}
}


function addslashes(str) {
	str=str.replace(/\\/g,'\\\\');
	str=str.replace(/\'/g,'\\\'');
	str=str.replace(/\"/g,'\\"');
	str=str.replace(/\0/g,'\\0');
	return str;
}

function stripslashes(str) {
	str=str.replace(/\\\\/g,'\\');
	str=str.replace(/\\'/g,'\'');
	str=str.replace(/\\"/g,'"');
	str=str.replace(/\\0/g,'\0');
	return str;
}

function addEvent (elm, evtype, fn, usecapture) {
	if (elm.addEventListener) {
		elm.addEventListener(evtype, fn, usecapture);
		return true;
	} else if (elm.attachEvent) {
		var r = elm.attachEvent("on" + evtype, fn);
		return r;
	} else {
		elm["on" + evtype] = fn;
	}
}
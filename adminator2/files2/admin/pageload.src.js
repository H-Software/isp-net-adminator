var filetouse = '_adminajax.php';

//poisition the footer at the bottom
function setFooter() {
	if (document.height) {
		var dheight = document.height;
	} else {
		var dheight = document.body.offsetHeight;
	}
	
	var cheight = xClientHeight();
//	alert ("dheight: " + dheight + ", cheight: " + cheight);
	var footer = xGetElementById("footer");
	if (footer) {
		if (cheight > (dheight)) {
			//repoisition!	
			footer.style.position = "absolute";
			footer.style.top = (cheight-38) + "px";
			footer.style.left = "0";
			footer.style.visibility = "visible";
		}
		footer.style.visibility = "visible";
	}
	return null;
}

//expand divs in main contents to height of the page.
function setMainContent() {
	if (document.height) {
		var dheight = document.height;
	} else {
		var dheight = document.body.offsetHeight;
	}
	
	var cheight = xClientHeight();
	if (cheight > (dheight)) {
		//reposition!
		var side = xGetElementById("sidemenu");
		if (side) {
			side.style.height = (cheight-210) + "px";
		}
		var main = xGetElementById("main");
		if (main) {
			main.style.height = (cheight-220) + "px";
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
	
	//result_affirmative
	//Fat.fade_all();
	return null;
}

function SaveEdit(executing_input,field_id,aref) {
	var text = executing_input.value;
	var thefield = xGetElementById(field_id);
	if (thefield) {
		xInnerHtml(thefield,text);
		xInnerHtml(aref,'<img src="admin/images/edit.gif" border="0" width="16" height="16" alt="Edit" />');
		//save with ajax.
		var requests = thefield.getAttribute('rel') + xInnerHtml(thefield);
		getUrl(filetouse,cbSaveSetting,requests);
	} else {
		alert('Could not save because of a browser error.');
	}
}

function EditField (executing_link,field_id) {
	var thefield = xGetElementById(field_id);
	if (thefield) {
		var text = xInnerHtml(thefield);
		xInnerHtml(thefield,'<input id="temp_'+field_id+'" type="text" value="'+text.trim()+'" onblur="SaveEdit(this,\''+field_id+'\',\''+executing_link+'\');" style="width:98%;" />');
		//now focus the field
		target = xGetElementById('temp_'+field_id);
		target.select();
		xInnerHtml(executing_link,'<img src="admin/images/save.gif" border="0" width="16" height="16" alt="Save" />');
	} else {
		alert('Could not edit because of a browser error.');
	}
}


function cbSaveSetting(xml) {
	//callback
	if (CheckResultIsOk(xml)) {
		
	}
}

/* ********************************************************************************************
UNFUNCTIONAL CODE - gets run asap.
******************************************************************************************** */
var crap= addEvent(window, "load", windowload, false);
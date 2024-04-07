var filetouse = '_ajax.php';

// ---------------------------------------------------------------------------------------------
// ------------- SARRISSA FUNCTIONS -----------------
function getUrl(url,fn,requests) {
	//assume xml
	var xmlhttp = new XMLHttpRequest();
	var theurl = url;
	if (requests) {
		theurl = url+requests;
	}
	//alert(theurl);
	xmlhttp.open("GET", theurl, true);
	// if needed set header information 
	// using the setRequestHeader method
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {
			fn(xmlhttp.responseXML);
		}
	};
	xmlhttp.send('');
}
// ------------- END SARRISSA FUNCTIONS -----------------
// ---------------------------------------------------------------------------------------------


//----------------------------------------------------------------------------------------------------------------------
// CheckResultIsOk
//----------------------------------------------------------------------------------------------------------------------
function CheckResultIsOk(xml) {
	if (!xml) {
		//alert ('Error: XML was empty');
		return false;
	}
	
	if (xml.getElementsByTagName('error_code')[0] && Math.abs(xml.getElementsByTagName('error_code')[0].firstChild.data.trim()) > 0) {
		//error detected
		alert('Error ' + xml.getElementsByTagName('error_code')[0].firstChild.data + ': ' + xml.getElementsByTagName('error')[0].firstChild.data.trim());
		return false;
	} else {
		return true; //no errors
	}
}


//----------------------------------------------------------------------------------------------------------------------
// ExpandFolder
//----------------------------------------------------------------------------------------------------------------------
function ExpandFolder(elm,share_id,path) {
	if (!path) {
		var path = '/';
	}
	
	if (typeof(elm) == 'string') {
		elm = xGetElementById(elm);
	}
	if (elm) {
		//get the parent li
		elm = searchUp(elm,'li');
		
		//check a's rel tag for information about retrieved.
		if (elm.getAttribute('rel') && elm.getAttribute('rel') == 'retrieved') {
			//already retrieved! simply show or hide the child UL div(s)
			for (i=0;i<elm.childNodes.length;i++) {
				if (elm.childNodes[i].nodeName.toLowerCase() == 'ul') {
					//found it, so we remove it.
					if (!elm.childNodes[i].style.display || elm.childNodes[i].style.display == 'block') {
						elm.childNodes[i].style.display = 'none';
						elm.className = smartClassName(elm.className,'closed','open');
						//rerender parent ul
						var pul = searchUp(elm,'ul');
						pul.style.display = 'none';
						pul.style.display = 'block';
					} else {
						elm.childNodes[i].style.display = 'block';
						elm.className = smartClassName(elm.className,'open');
					}
				}
			}	//end for
			
			return true;
		}
		
		//delete any sub ul's., and create one for loading img..
		for (i=0;i<elm.childNodes.length;i++) {
			if (elm.childNodes[i].nodeName.toLowerCase() == 'ul') {
				//found it, so we remove it.
				elm.removeChild(elm.childNodes[i]);
			}
		}	//end for
		
		//now add loading img
		//show the waiting stuff.
		new_ul = document.createElement('ul');
		new_ul.className = 'waiting';
		new_li = document.createElement('li');
		new_tn = document.createTextNode('Loading...');
		//appendages
		new_li.appendChild(new_tn);
		new_ul.appendChild(new_li);
		elm.appendChild(new_ul);
		elm.className = smartClassName(elm.className,'open');
		//fetch xml
		var requests = '?getfolder&elm='+elm.id+'&share='+share_id+'&path='+path;
		getUrl(filetouse,cbExpandFolder,requests);
		elm.setAttribute('rel','retrieved');
	}
}


//----------------------------------------------------------------------------------------------------------------------
// cbExpandFolder
//----------------------------------------------------------------------------------------------------------------------
function cbExpandFolder(xml) {
	if (!CheckResultIsOk(xml)) {
		return false;
	}
	
	//check elm node
	if (xml.getElementsByTagName('elm')[0]) {
		var elm = xml.getElementsByTagName('elm')[0].firstChild.data.trim();
	} else {
		return false;
	}
	
	//check share node
	if (xml.getElementsByTagName('share')[0]) {
		var share_id = xml.getElementsByTagName('share')[0].firstChild.data.trim();
	} else {
		return false;
	}
	
	//check dir
	if (xml.getElementsByTagName('dir_path')[0]) {
		var dir_path = xml.getElementsByTagName('dir_path')[0].firstChild.data.trim();
	} else {
		var dir_path = '/';
	}

	//check perms
	var perm_r = false;
	var perm_dl = false;
	var perm_ul = false;
	var perm_del = false;
	if (xml.getElementsByTagName('perm_r')[0] && xml.getElementsByTagName('perm_r')[0].firstChild.data.trim() == 'yes') {
		perm_r = true;
	}
	if (xml.getElementsByTagName('perm_r')[0] && xml.getElementsByTagName('perm_dl')[0].firstChild.data.trim() == 'yes') {
		perm_dl = true;
	}
	if (xml.getElementsByTagName('perm_r')[0] && xml.getElementsByTagName('perm_ul')[0].firstChild.data.trim() == 'yes') {
		perm_ul = true;
	}
	if (xml.getElementsByTagName('perm_r')[0] && xml.getElementsByTagName('perm_del')[0].firstChild.data.trim() == 'yes') {
		perm_del = true;
	}
	
	//get directories
	var folders = xml.getElementsByTagName('directory');
	var folder_html = '';
	for (i=0;i<folders.length;i++) {
		//values
		if (folders[i].getElementsByTagName('name')[0]) {
			var name = folders[i].getElementsByTagName('name')[0].firstChild.data.trim();
			if (folders[i].getElementsByTagName('path') && folders[i].getElementsByTagName('path')[0]) {
				var path = folders[i].getElementsByTagName('path')[0].firstChild.data.trim();
			}
			var ul = '';
			if (perm_ul) {
				ul = '<span class="upload"><a href="index.php?upload&share='+share_id+'&path='+path+'" title="Upload files to '+name+'">Upload</a></span>';
			}
			if (folders[i].getElementsByTagName('dir_id') && folders[i].getElementsByTagName('dir_id')[0]) {
				var dir_id = folders[i].getElementsByTagName('dir_id')[0].firstChild.data.trim();
				if (path) {
					//folder_html = folder_html + '<li class="folder"><a href="javascript:void(null);" onclick="ExpandFolder(\''+elm+'\',\''+share_id+'\',\''+path+'\');">'+name+'</a></li>';
					folder_html = folder_html + '<li class="folder" id="'+dir_id+'"><a href="javascript:void(null);" onclick="ExpandFolder(this,\''+share_id+'\',\''+path+'\');">'+name+'</a> '+ul+'</li>';
				} else {
					folder_html = folder_html + '<li class="folder" id="">'+name+' '+ul+'</li>';
				}
			}
		} //if
	} //for
	
	//get files
	var files = xml.getElementsByTagName('file');
	var file_html = '';
	for (i=0;i<files.length;i++) {
		//values
		if (files[i].getElementsByTagName('name')[0]) {
			var name = files[i].getElementsByTagName('name')[0].firstChild.data.trim();
			if (files[i].getElementsByTagName('link') && files[i].getElementsByTagName('link')[0]) {
				var path = files[i].getElementsByTagName('link')[0].firstChild.data.trim();
			}
			if (files[i].getElementsByTagName('size') && files[i].getElementsByTagName('size')[0]) {
				var size = files[i].getElementsByTagName('size')[0].firstChild.data.trim();
			} else {
				var size = 'NA';
			}
			if (files[i].getElementsByTagName('type') && files[i].getElementsByTagName('type')[0]) {
				var type = files[i].getElementsByTagName('type')[0].firstChild.data.trim();
			} else {
				var type = '';
			}
			var del_link = '';
			if (files[i].getElementsByTagName('file_id') && files[i].getElementsByTagName('file_id')[0]) {
				var file_id = files[i].getElementsByTagName('file_id')[0].firstChild.data.trim();
				if (perm_del) {
					del_link = '<span class="delete" id="del_'+file_id+'"><a href="javascript:void(null);" onclick="askDelete(this,\''+share_id+'\',\''+addslashes(dir_path)+'\',\''+addslashes(name)+'\');">Delete</a></span>';
				}
			}
			if (path) {
				file_html = file_html + '<li class="file '+type+'"><a href="'+path+'">'+name+'</a> <span class="size">'+size+'</span>'+del_link+'</li>';
			} else {
				file_html = file_html + '<li class="file '+type+'">'+name+' <span class="size">'+size+'</span>'+del_link+'</li>';
			}
		} //if
	} //for
	
	
	
	//get elm's parent li
	var parentLI = searchUp(elm,'li');
	if (!parentLI) {
		return false;
	}
	
	//remove loading ul.
	for (i=0;i<parentLI.childNodes.length;i++) {
		if (parentLI.childNodes[i].nodeName.toLowerCase() == 'ul') {
			//found it, so we remove it.
			parentLI.removeChild(parentLI.childNodes[i]);
		}
	}	//end for
	
	//output folders
	var ul = document.createElement('ul');
	if (folder_html.length > 0 || file_html.length > 0) {
		//create a ul, and append folder_html to it
		xInnerHtml(ul,folder_html+file_html);
	} else if (!perm_r) {
		xInnerHtml(ul,'<li class="folder empty">You do not have permissions to view the contents of this directory</li>');
	} else {
		xInnerHtml(ul,'<li class="folder empty">Folder is empty</li>');
	}
	//append ul to li.
	parentLI.appendChild(ul);
	
} //end cbExpandFolder function

//----------------------------------------------------------------------------------------------------------------------
// askDelete
//----------------------------------------------------------------------------------------------------------------------

function askDelete(elm,share,path,file) {
	//alert(share+' -- '+path+' -- '+file);
	var a = confirm('Do you really want to delete "'+stripslashes(file)+'" ?');
	if (a == true) {
		DeleteFile(elm,share,path,file);
	}
}

//----------------------------------------------------------------------------------------------------------------------
// DeleteFile
//----------------------------------------------------------------------------------------------------------------------

function DeleteFile(elm,share,path,file) {
	if (!elm || !share || !path || !file) {
		alert('Delete Failed - JS Error 198');
		return false;
	}
	
	if (typeof(elm) == 'string') {
		elm = xGetElementById(elm);
	}
	
	var parentSpan = searchUp(elm,'span');
	//change class of elm to waiting...
	parentSpan.className = smartClassName(parentSpan.className,'waiting');
	
	var requests = '?deletefile&elm='+parentSpan.id+'&share='+share+'&path='+path+'&file='+file;
	getUrl(filetouse,cbDeleteFile,requests);
		
} //end DeleteFile function

//----------------------------------------------------------------------------------------------------------------------
// cbDeleteFile
//----------------------------------------------------------------------------------------------------------------------
function cbDeleteFile(xml) {
	if (!CheckResultIsOk(xml)) {
		return false;
	}
	
	//check elm node
	if (xml.getElementsByTagName('elm')[0]) {
		var elm = xml.getElementsByTagName('elm')[0].firstChild.data.trim();
	} else {
		return false;
	}
	
	theelm = xGetElementById(elm);
	if (theelm) {	
		//get the parent li and remove it from the ul.
		var parentLI = searchUp(theelm,'li');
		parentLI.parentNode.removeChild(parentLI);
	}
	
} //end cbDeleteFile function

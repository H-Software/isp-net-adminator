
/** Odeslání XMLHttp požadavku
* @param Function funkce zajišťující obsluhu při změně stavu požadavku, dostane parametr s XMLHttp objektem
* @param string GET|POST|...
* @param string URL požadavku
* @param string tělo zprávy
* @param object předané hlavičky ve tvaru { 'hlavička': 'obsah' }
* @return boolean true v případě úspěchu, false jinak
*/
function send_xmlhttprequest(state_change, method, url, content, headers) {

  var xmlhttp;
  try{
    xmlhttp = new XMLHttpRequest();
  }
  catch(e){//pro případ starší verze prohlížeče
    var MSXmlVerze = new Array('MSXML2.XML.Http.6.0','MSXML2.XML.Http.5.0','MSXML2.XML.Http.4.0','MSXML2.XML.Http.3.0','MSXML2.XML.Http.2.0','Microsoft.XML.Http');
    for(var i = 0; i < MSXmlVerze.lenght; i ++){
      try{
        xmlhttp = new ActiveXObject(MSXmlVerze[i]);
      }catch(e){
        //vzniklou chybu ignoruji a pokračuji nastavením další verze
      }
    }
  }
       
    if (!xmlhttp) {
        return false;
    }
    xmlhttp.open(method, url);
    xmlhttp.onreadystatechange = function () {
        state_change(xmlhttp);
    };
    headers = headers || {};
    headers['X-Requested-With'] = headers['X-Requested-With'] || 'XMLHttpRequest';
    for (var key in headers) {
        xmlhttp.setRequestHeader(key, headers[key]);
    }
    xmlhttp.send(content);
    return true;
}

function restart_item() {
    // odeslání požadavku na aktualizaci dat
    if(!send_xmlhttprequest(restart_obsluha, 'GET', 'work_rpc.php?item=1')){
        return false;
    }
    
    var akce_probiha;
    
    akce_probiha = '<div style="color: blue; float: left;" >Požadovaná akce probíhá...</div>';
    akce_probiha = akce_probiha + '<img src="img2/ajax-load-1-0.gif" alt="akce probiha" border="0" >';
    
    var new_el = document.createElement('div');
    new_el.setAttribute('id','my'+item+'div');
    new_el.innerHTML = akce_probiha;
     
    document.getElementById('stav').appendChild(new_el);
    document.getElementById('work-vyberte-akci').parentNode.removeChild(document.getElementById('work-vyberte-akci'));
    document.getElementById('work-ok').parentNode.removeChild(document.getElementById('work-ok'));
    
    return true;
}

function restart_obsluha(xmlhttp) 
{
    if (xmlhttp.readyState == 4) 
    {
        // aktualizace odpovědí na základě aktuálního stavu
        var odpovedi = xmlhttp.responseXML.getElementsByTagName('odpoved');

	//od-skrtnem checkbox
	document.getElementById('item' + odpovedi[0].firstChild.data).checked=false;
	
	//zmenime hlasku v boxu ze akce je provedena
	var akce_provedena = '<div class="work-complete" id=\'work-ok\' >Akce provedena.</div>';
	
        document.getElementById('restart-stav').innerHTML = akce_provedena;
	
	//vypisem stav jednotlivych skriptu	
	var rs = document.createElement('div');
	rs.setAttribute('id','rs' + odpovedi[0].firstChild.data);
	rs.innerHTML = odpovedi[1].firstChild.data;
     
	document.getElementById('odpoved1').appendChild(rs);
	
	//smazem log ze souboru
	document.getElementById('odpoved-file').parentNode.removeChild(document.getElementById('odpoved-file'));

	//dodame aktualni log ze skriptu
	var odp2 = document.createElement('div');
	odp2.setAttribute('id','log' + odpovedi[0].firstChild.data);
	odp2.innerHTML = odpovedi[2].firstChild.data;
	
	document.getElementById('odpoved2').appendChild(odp2);
    }
}

